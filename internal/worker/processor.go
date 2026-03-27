package worker

import (
	"encoding/json"
	"fmt"
	"log"
	"time"

	"github.com/hibiken/asynq"

	"qingscan/internal/model"
	"qingscan/internal/queue"
	"qingscan/internal/repository"
)

// TaskProcessor 任务处理器
type TaskProcessor struct {
	taskRepo *repository.TaskRepository
}

// NewTaskProcessor 创建任务处理器
func NewTaskProcessor(taskRepo *repository.TaskRepository) *TaskProcessor {
	return &TaskProcessor{
		taskRepo: taskRepo,
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
func (p *TaskProcessor) handleScanTask(task *asynq.Task) error {
	var payload queue.TaskPayload
	if err := json.Unmarshal(task.Payload, &payload); err != nil {
		return fmt.Errorf("failed to unmarshal task payload: %w", err)
	}

	log.Printf("Processing task: TaskID=%d, Type=%s, Target=%s", payload.TaskID, payload.Type, payload.Target)

	// 更新任务状态为执行中
	now := time.Now()
	taskModel := &model.Task{
		ID:     payload.TaskID,
		Status: 1,
	}
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

	// TODO: 调用实际的扫描器执行扫描
	// 1. nmap 端口扫描
	// 2. 指纹识别

	queue.UpdateTaskProgress(payload.TaskID, 50, 0)

	// 保存扫描结果到数据库
	// ...

	queue.UpdateTaskProgress(payload.TaskID, 100, 0)

	return nil
}

// handleWebScanTask 处理Web扫描任务
func (p *TaskProcessor) handleWebScanTask(payload *queue.TaskPayload) error {
	queue.UpdateTaskProgress(payload.TaskID, 10, 0)

	// TODO: 调用实际的扫描器执行扫描
	// 1. nuclei POC扫描
	// 2. xray 扫描
	// 3. dirma 目录扫描
	// 4. crawlergo 爬虫

	queue.UpdateTaskProgress(payload.TaskID, 50, 0)

	// 保存扫描结果
	// ...

	queue.UpdateTaskProgress(payload.TaskID, 100, 0)

	return nil
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
func (p *TaskProcessor) handleHostScan(task *asynq.Task) error {
	return p.handleScanTask(task)
}

func (p *TaskProcessor) handleWebScan(task *asynq.Task) error {
	return p.handleScanTask(task)
}

func (p *TaskProcessor) handleCodeScan(task *asynq.Task) error {
	return p.handleScanTask(task)
}
