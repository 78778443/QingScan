package worker

import (
	"context"
	"encoding/json"
	"fmt"
	"log"
	"regexp"
	"strings"
	"time"

	"github.com/hibiken/asynq"
	"gorm.io/gorm"

	"qingscan/internal/model"
	"qingscan/internal/queue"
	"qingscan/internal/repository"
	"qingscan/internal/scanner"
)

// TaskProcessor 任务处理器
type TaskProcessor struct {
	taskRepo *repository.TaskRepository
	db       *gorm.DB
}

// NewTaskProcessor 创建任务处理器
func NewTaskProcessor(taskRepo *repository.TaskRepository, db *gorm.DB) *TaskProcessor {
	return &TaskProcessor{
		taskRepo: taskRepo,
		db:       db,
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

	// 工具路径
	toolsPath := "/opt/qingscan/tools"

	// 注册扫描器
	scanner.RegisterAllScanners(toolsPath)
	scanManager := scanner.GetScanManager()

	// 检查 nmap 是否可用
	nmapScanner := scanManager.Get("nmap")
	if nmapScanner == nil {
		log.Printf("Nmap scanner not found, using default path")
		nmapScanner = scanner.NewNmapScanner(toolsPath + "/nmap")
		scanManager.Register(nmapScanner)
	}

	// 更新进度
	queue.UpdateTaskProgress(payload.TaskID, 30, 0)

	// 运行 nmap 扫描
	options := map[string]interface{}{
		"ports":          "1-1000",
		"service_version": true,
	}

	result, err := scanManager.RunScan("nmap", payload.Target, options)
	if err != nil {
		log.Printf("Nmap scan failed: %v", err)
	} else if result.Success {
		log.Printf("Nmap scan completed: %d ports found", len(result.Results.(string)))
		// 保存端口结果到数据库
		p.savePortResults(payload.TaskID, payload.Target, result.Results.(string))
	}

	queue.UpdateTaskProgress(payload.TaskID, 100, 0)

	return nil
}

// savePortResults 保存端口扫描结果到数据库
func (p *TaskProcessor) savePortResults(taskID uint, host, result string) {
	// 查找或创建主机记录
	var h model.Host
	query := p.db.Where("ip = ?", host)
	if err := query.First(&h).Error; err != nil {
		// 创建新主机
		h = model.Host{
			IP:     host,
			Status: 1,
		}
		p.db.Create(&h)
	}

	// 解析 nmap 文本输出，提取端口信息
	// 格式示例：22/tcp   open  ssh
	portRegex := regexp.MustCompile(`(\d+)/(tcp|udp)\s+(\w+)\s+(.*)`)
	lines := strings.Split(result, "\n")
	savedCount := 0

	for _, line := range lines {
		matches := portRegex.FindStringSubmatch(line)
		if len(matches) < 5 {
			continue
		}

		portNum := matches[1]
		protocol := matches[2]
		state := matches[3]
		service := matches[4]

		// 检查是否已存在
		var existing model.Port
		query := p.db.Where("host = ? AND port = ? AND protocol = ?", host, portNum, protocol)
		if err := query.First(&existing).Error; err == nil {
			continue
		}

		// 创建端口记录
		port := model.Port{
			HostID:   h.ID,
			Host:     host,
			Port:     portNum,
			Protocol: protocol,
			State:    state,
			Service:  strings.Fields(service)[0],
		}

		if len(strings.Fields(service)) > 1 {
			port.Version = strings.Join(strings.Fields(service)[1:], " ")
		}

		if err := p.db.Create(&port).Error; err != nil {
			log.Printf("Failed to save port: %v", err)
		} else {
			savedCount++
		}
	}

	// 更新主机的端口数量
	p.db.Model(&h).Update("port_count", savedCount)

	log.Printf("Saved %d ports to database for host %s", savedCount, host)
}

// handleWebScanTask 处理Web扫描任务
func (p *TaskProcessor) handleWebScanTask(payload *queue.TaskPayload) error {
	queue.UpdateTaskProgress(payload.TaskID, 10, 0)

	// 工具路径
	toolsPath := "/opt/qingscan/tools"

	// 注册扫描器
	scanner.RegisterAllScanners(toolsPath)
	scanManager := scanner.GetScanManager()

	// 根据配置的扫描工具执行扫描
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
			result, err := scanManager.RunScan("nuclei", payload.Target, options)
			if err != nil {
				log.Printf("Nuclei scan failed: %v", err)
			} else if result.Success {
				log.Printf("Nuclei scan completed")
				p.saveVulnResults(payload.TaskID, "nuclei", result.Results.(string))
				resultCount++
			}

		case "dirmap":
			options := map[string]interface{}{
				"threads": 10,
			}
			result, err := scanManager.RunScan("dirmap", payload.Target, options)
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
		result, err := scanManager.RunScan("nuclei", payload.Target, options)
		if err != nil {
			log.Printf("Nuclei scan failed: %v", err)
		} else if result.Success {
			log.Printf("Nuclei scan completed")
			p.saveVulnResults(payload.TaskID, "nuclei", result.Results.(string))
			resultCount++
		}
	}

	queue.UpdateTaskProgress(payload.TaskID, 100, 0)

	return nil
}

// saveVulnResults 保存漏洞扫描结果到数据库
func (p *TaskProcessor) saveVulnResults(taskID uint, tool, result string) {
	// 解析 nuclei JSON 结果
	lines := strings.Split(result, "\n")
	savedCount := 0

	for _, line := range lines {
		line = strings.TrimSpace(line)
		if line == "" || (!strings.HasPrefix(line, "{") && !strings.HasPrefix(line, "[")) {
			continue
		}

		var vulnData struct {
			Info struct {
				Name        string `json:"name"`
				Severity    string `json:"severity"`
				Description string `json:"description"`
			} `json:"info"`
			MatchedAt string `json:"matched-at"`
			Extractor string `json:"extractor"`
			Template  string `json:"template"`
			Type      string `json:"type"`
		}

		if err := json.Unmarshal([]byte(line), &vulnData); err != nil {
			continue
		}

		if vulnData.Info.Name == "" {
			continue
		}

		// 检查是否已存在
		var existing model.Vulnerability
		query := p.db.Where("name = ? AND target = ?", vulnData.Info.Name, vulnData.MatchedAt)
		if err := query.First(&existing).Error; err == nil {
			// 已存在，跳过
			continue
		}

		// 创建漏洞记录
		vuln := model.Vulnerability{
			Name:        vulnData.Info.Name,
			Target:      vulnData.MatchedAt,
			Type:        vulnData.Type,
			Severity:    vulnData.Info.Severity,
			Status:      0,
			Tool:        tool,
			Poc:         vulnData.Template,
			Description: vulnData.Info.Description,
		}

		if err := p.db.Create(&vuln).Error; err != nil {
			log.Printf("Failed to save vulnerability: %v", err)
		} else {
			savedCount++
		}
	}

	log.Printf("Saved %d vulnerabilities to database for task %d", savedCount, taskID)
}

// handleCodeScanTask 处理代码扫描任务
func (p *TaskProcessor) handleCodeScanTask(payload *queue.TaskPayload) error {
	queue.UpdateTaskProgress(payload.TaskID, 10, 0)

	// TODO: 调用实际的扫描器执行扫描
	// 1. semgrep 代码扫描
	// 2. codeql 代码分析
	// 3. fortify 代码审计

	queue.UpdateTaskProgress(payload.TaskID, 50, 0)

	// 保存扫描结果
	// ...

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
