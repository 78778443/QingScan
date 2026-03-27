package worker

import (
	"context"
	"encoding/json"
	"fmt"
	"log"
	"path/filepath"
	"strings"
	"time"

	"github.com/hibiken/asynq"
	"gorm.io/gorm"

	"qingscan/internal/config"
	"qingscan/internal/queue"
	"qingscan/internal/repository"
	"qingscan/internal/result"
	"qingscan/internal/scanner"
)

// TaskProcessor 任务处理器
type TaskProcessor struct {
	taskRepo        *repository.TaskRepository
	db              *gorm.DB
	containerRunner *scanner.ContainerRunner
	toolConfigs     []*config.ToolConfig
	toolsPath       string
	resultService   *result.ResultService
}

// NewTaskProcessor 创建任务处理器
func NewTaskProcessor(taskRepo *repository.TaskRepository, db *gorm.DB, toolsPath string) *TaskProcessor {
	// 初始化容器运行器
	containerRunner := scanner.NewContainerRunner(toolsPath)

	// 初始化结果服务
	resultService := result.NewResultService(db)

	// 加载工具配置
	var toolConfigs []*config.ToolConfig
	configDir := filepath.Join(toolsPath, "..", "config", "tools")
	imagesDir := filepath.Join(toolsPath, "images")

	loader := config.NewToolLoader(configDir, toolsPath, imagesDir)
	if tools, err := loader.LoadTools(); err == nil {
		toolConfigs = tools
		log.Printf("Loaded %d tool configs", len(toolConfigs))
	} else {
		log.Printf("Failed to load tool configs: %v", err)
	}

	// 加载工具镜像（容器模式）
	if len(toolConfigs) > 0 {
		results := containerRunner.LoadToolImages(toolConfigs)
		for toolName, err := range results {
			log.Printf("Failed to load tool %s: %v", toolName, err)
		}
	}

	return &TaskProcessor{
		taskRepo:        taskRepo,
		db:              db,
		containerRunner: containerRunner,
		toolConfigs:     toolConfigs,
		toolsPath:       toolsPath,
		resultService:   resultService,
	}
}

// RegisterHandlers 注册任务处理器
func (p *TaskProcessor) RegisterHandlers(mux *asynq.ServeMux) {
	// 注册扫描任务处理器
	mux.HandleFunc("scan", p.handleScanTask)
	// 注册主机扫描任务
	mux.HandleFunc("scan:host", p.handleHostScan)
	// 注册Web扫描任务
	mux.HandleFunc("scan:web", p.handleWebScan)
	// 注册代码扫描任务
	mux.HandleFunc("scan:code", p.handleCodeScan)
}

// GetToolConfig 根据工具名称获取配置
func (p *TaskProcessor) GetToolConfig(toolName string) *config.ToolConfig {
	for _, tc := range p.toolConfigs {
		if tc.Name == toolName {
			return tc
		}
	}
	return nil
}

// RunTool 运行工具（自动选择容器或本地模式）
func (p *TaskProcessor) RunTool(toolName, target string, options map[string]interface{}) (*scanner.ScanResult, error) {
	// 优先从配置获取工具配置
	toolConfig := p.GetToolConfig(toolName)

	if toolConfig != nil {
		// 使用容器运行器（支持容器和本地模式）
		return p.containerRunner.RunTool(toolConfig, target, options)
	}

	// 回退到旧的 ScanManager 方式（兼容旧代码）
	scanner.RegisterAllScanners(p.toolsPath)
	scanManager := scanner.GetScanManager()
	scannerObj := scanManager.Get(toolName)
	if scannerObj == nil {
		return nil, fmt.Errorf("scanner not found: %s", toolName)
	}
	return scannerObj.Run(target, options)
}

// handleScanTask 处理扫描任务
func (p *TaskProcessor) handleScanTask(ctx context.Context, task *asynq.Task) error {
	var payload queue.TaskPayload
	if err := json.Unmarshal(task.Payload(), &payload); err != nil {
		return fmt.Errorf("failed to unmarshal task payload: %w", err)
	}

	log.Printf("Processing task: TaskID=%d, Type=%s, Target=%s", payload.TaskID, payload.Type, payload.Target)

	// 更新任务状态为执行中
	now := time.Now()
	p.taskRepo.StartTask(payload.TaskID, &now)

	// 根据类型分发到具体处理函数
	var err error
	switch payload.Type {
	case "host":
		err = p.handleHostScanTask(&payload)
	case "web":
		err = p.handleWebScanTask(&payload)
	case "code":
		err = p.handleCodeScanTask(&payload)
	default:
		err = fmt.Errorf("unknown task type: %s", payload.Type)
	}

	if err != nil {
		log.Printf("Task failed: TaskID=%d, Error=%s", payload.TaskID, err.Error())
		p.taskRepo.FailTask(payload.TaskID, err.Error())
		queue.FailTask(payload.TaskID, err.Error())
		return err
	}

	// 完成任务
	p.taskRepo.CompleteTask(payload.TaskID)
	queue.CompleteTask(payload.TaskID, 0)

	log.Printf("Task completed: TaskID=%d", payload.TaskID)
	return nil
}

// handleHostScanTask 处理主机扫描任务
func (p *TaskProcessor) handleHostScanTask(payload *queue.TaskPayload) error {
	// 更新进度
	queue.UpdateTaskProgress(payload.TaskID, 10, 0)

	// 更新进度
	queue.UpdateTaskProgress(payload.TaskID, 30, 0)

	// 运行 nmap 扫描（自动选择容器或本地模式）
	options := map[string]interface{}{
		"ports":           "1-1000",
		"service_version": true,
	}

	result, err := p.RunTool("nmap", payload.Target, options)
	if err != nil {
		log.Printf("Nmap scan failed: %v", err)
	} else if result.Success {
		if resultStr, ok := result.Results.(string); ok {
			log.Printf("Nmap scan completed: %d chars output", len(resultStr))
			// 保存端口结果到数据库
			p.savePortResults(payload.TaskID, payload.Target, resultStr)
		}
	}

	queue.UpdateTaskProgress(payload.TaskID, 100, 0)

	return nil
}

// savePortResults 保存端口扫描结果到数据库
func (p *TaskProcessor) savePortResults(taskID uint, host, result string) {
	// 使用统一结果服务解析并保存
	count, err := p.resultService.SaveScanResult(taskID, "nmap", host, result)
	if err != nil {
		log.Printf("Save nmap results failed: %v", err)
	} else {
		log.Printf("Saved %d nmap results", count)
	}
}

// handleWebScanTask 处理Web扫描任务
func (p *TaskProcessor) handleWebScanTask(payload *queue.TaskPayload) error {
	queue.UpdateTaskProgress(payload.TaskID, 10, 0)

	// 根据配置的扫描工具执行扫描（自动选择容器或本地模式）
	tools := strings.Split(payload.Tools, ",")
	resultCount := 0

	for _, tool := range tools {
		tool = strings.TrimSpace(tool)
		if tool == "" {
			continue
		}

		queue.UpdateTaskProgress(payload.TaskID, 30, 0)

		switch tool {
		case "nuclei":
			options := map[string]interface{}{
				"severity": "critical,high,medium,low",
			}
			result, err := p.RunTool("nuclei", payload.Target, options)
			if err != nil {
				log.Printf("Nuclei scan failed: %v", err)
			} else if result.Success {
				log.Printf("Nuclei scan completed")
				if resultStr, ok := result.Results.(string); ok {
					p.saveVulnResults(payload.TaskID, "nuclei", resultStr)
				}
				resultCount++
			}

		case "dirmap":
			options := map[string]interface{}{
				"threads": 10,
			}
			result, err := p.RunTool("dirmap", payload.Target, options)
			if err != nil {
				log.Printf("Dirmap scan failed: %v", err)
			} else if result.Success {
				log.Printf("Dirmap scan completed")
				resultCount++
			}

		default:
			log.Printf("Unknown tool: %s", tool)
		}
	}

	// 如果没有指定工具，默认运行 nuclei
	if len(tools) == 0 || (len(tools) == 1 && tools[0] == "") {
		options := map[string]interface{}{
			"severity": "critical,high,medium,low",
		}
		result, err := p.RunTool("nuclei", payload.Target, options)
		if err != nil {
			log.Printf("Nuclei scan failed: %v", err)
		} else if result.Success {
			log.Printf("Nuclei scan completed")
			if resultStr, ok := result.Results.(string); ok {
				p.saveVulnResults(payload.TaskID, "nuclei", resultStr)
			}
			resultCount++
		}
	}

	queue.UpdateTaskProgress(payload.TaskID, 100, 0)

	return nil
}

// saveVulnResults 保存漏洞扫描结果到数据库
func (p *TaskProcessor) saveVulnResults(taskID uint, tool, result string) {
	// 使用统一结果服务解析并保存
	count, err := p.resultService.SaveScanResult(taskID, tool, "", result)
	if err != nil {
		log.Printf("Save %s results failed: %v", tool, err)
	} else {
		log.Printf("Saved %d %s results", count, tool)
	}
}

// handleCodeScanTask 处理代码扫描任务
func (p *TaskProcessor) handleCodeScanTask(payload *queue.TaskPayload) error {
	queue.UpdateTaskProgress(payload.TaskID, 10, 0)

	// 根据配置的扫描工具执行扫描（自动选择容器或本地模式）
	tools := strings.Split(payload.Tools, ",")
	resultCount := 0

	for _, tool := range tools {
		tool = strings.TrimSpace(tool)
		if tool == "" {
			continue
		}

		queue.UpdateTaskProgress(payload.TaskID, 30, 0)

		// 根据不同工具设置不同选项
		var options map[string]interface{}
		switch tool {
		case "semgrep":
			options = map[string]interface{}{
				"mode": "auto",
			}
		case "codeql":
			options = map[string]interface{}{}
		case "fortify":
			options = map[string]interface{}{}
		default:
			options = map[string]interface{}{}
		}

		result, err := p.RunTool(tool, payload.Target, options)
		if err != nil {
			log.Printf("%s scan failed: %v", tool, err)
		} else if result.Success {
			log.Printf("%s scan completed", tool)
			resultCount++
		}
	}

	// 如果没有指定工具，默认运行 semgrep
	if len(tools) == 0 || (len(tools) == 1 && tools[0] == "") {
		options := map[string]interface{}{
			"mode": "auto",
		}
		result, err := p.RunTool("semgrep", payload.Target, options)
		if err != nil {
			log.Printf("Semgrep scan failed: %v", err)
		} else if result.Success {
			log.Printf("Semgrep scan completed")
			resultCount++
		}
	}

	queue.UpdateTaskProgress(payload.TaskID, 100, 0)

	return nil
}

// 以下是占位实现
func (p *TaskProcessor) handleHostScan(ctx context.Context, task *asynq.Task) error {
	return p.handleScanTask(ctx, task)
}

func (p *TaskProcessor) handleWebScan(ctx context.Context, task *asynq.Task) error {
	return p.handleScanTask(ctx, task)
}

func (p *TaskProcessor) handleCodeScan(ctx context.Context, task *asynq.Task) error {
	return p.handleScanTask(ctx, task)
}
