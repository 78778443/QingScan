package workflow

import (
	"encoding/json"
	"fmt"
	"log"
	"os"
	"os/exec"
	"strings"
	"time"

	"gorm.io/gorm"

	"qingscan/internal/model"
)

// WorkflowService 工作流服务
type WorkflowService struct {
	db *gorm.DB
}

// NewWorkflowService 创建工作流服务
func NewWorkflowService(db *gorm.DB) *WorkflowService {
	return &WorkflowService{db: db}
}

// GetDB 获取数据库实例
func (s *WorkflowService) GetDB() *gorm.DB {
	return s.db
}

// ========== 工具管理 ==========

// GetTools 获取所有可用工具
func (s *WorkflowService) GetTools(toolType string) ([]model.ScanTool, error) {
	var tools []model.ScanTool
	query := s.db.Where("enabled = ?", 1)
	if toolType != "" {
		query = query.Where("type = ?", toolType)
	}
	if err := query.Order("category, name").Find(&tools).Error; err != nil {
		return nil, err
	}
	return tools, nil
}

// GetToolByID 根据ID获取工具
func (s *WorkflowService) GetToolByID(id uint) (*model.ScanTool, error) {
	var tool model.ScanTool
	if err := s.db.First(&tool, id).Error; err != nil {
		return nil, err
	}
	return &tool, nil
}

// GetToolByName 根据名称获取工具
func (s *WorkflowService) GetToolByName(name string) (*model.ScanTool, error) {
	var tool model.ScanTool
	if err := s.db.Where("name = ?", name).First(&tool).Error; err != nil {
		return nil, err
	}
	return &tool, nil
}

// CreateTool 创建工具
func (s *WorkflowService) CreateTool(tool *model.ScanTool) error {
	return s.db.Create(tool).Error
}

// UpdateTool 更新工具
func (s *WorkflowService) UpdateTool(id uint, updates map[string]interface{}) error {
	return s.db.Model(&model.ScanTool{}).Where("id = ?", id).Updates(updates).Error
}

// ========== 目标管理 ==========

// CreateTarget 创建目标
func (s *WorkflowService) CreateTarget(target *model.ScanTarget) error {
	return s.db.Create(target).Error
}

// GetTarget 获取目标
func (s *WorkflowService) GetTarget(id uint) (*model.ScanTarget, error) {
	var target model.ScanTarget
	if err := s.db.First(&target, id).Error; err != nil {
		return nil, err
	}
	return &target, nil
}

// ListTargets 获取目标列表
func (s *WorkflowService) ListTargets(page, pageSize int, userID uint) ([]model.ScanTarget, int64, error) {
	var targets []model.ScanTarget
	var total int64

	query := s.db.Model(&model.ScanTarget{})
	if userID > 0 {
		query = query.Where("user_id = ?", userID)
	}

	query.Count(&total)
	offset := (page - 1) * pageSize
	if err := query.Offset(offset).Limit(pageSize).Order("id DESC").Find(&targets).Error; err != nil {
		return nil, 0, err
	}
	return targets, total, nil
}

// DeleteTarget 删除目标
func (s *WorkflowService) DeleteTarget(id uint) error {
	return s.db.Delete(&model.ScanTarget{}, id).Error
}

// ========== 任务管理 ==========

// CreateTask 创建扫描任务
func (s *WorkflowService) CreateTask(task *model.ScanTask) error {
	return s.db.Create(task).Error
}

// GetTask 获取任务
func (s *WorkflowService) GetTask(id uint) (*model.ScanTask, error) {
	var task model.ScanTask
	if err := s.db.First(&task, id).Error; err != nil {
		return nil, err
	}
	return &task, nil
}

// ListTasks 获取任务列表
func (s *WorkflowService) ListTasks(targetID uint, status int, page, pageSize int) ([]model.ScanTask, int64, error) {
	var tasks []model.ScanTask
	var total int64

	query := s.db.Model(&model.ScanTask{})
	if targetID > 0 {
		query = query.Where("target_id = ?", targetID)
	}
	if status >= 0 {
		query = query.Where("status = ?", status)
	}

	query.Count(&total)
	offset := (page - 1) * pageSize
	if err := query.Offset(offset).Limit(pageSize).Order("id DESC").Find(&tasks).Error; err != nil {
		return nil, 0, err
	}
	return tasks, total, nil
}

// UpdateTaskStatus 更新任务状态
func (s *WorkflowService) UpdateTaskStatus(id uint, status int, progress int) error {
	updates := map[string]interface{}{
		"status":   status,
		"progress": progress,
	}
	if status == 1 { // 执行中
		now := time.Now()
		updates["start_time"] = &now
	} else if status == 2 || status == 3 { // 完成或失败
		now := time.Now()
		updates["end_time"] = &now
	}
	return s.db.Model(&model.ScanTask{}).Where("id = ?", id).Updates(updates).Error
}

// ========== 执行扫描 ==========

// RunScan 执行扫描
func (s *WorkflowService) RunScan(taskID uint) error {
	// 获取任务信息
	task, err := s.GetTask(taskID)
	if err != nil {
		return fmt.Errorf("获取任务失败: %w", err)
	}

	// 获取工具信息
	tool, err := s.GetToolByID(task.ToolID)
	if err != nil {
		return fmt.Errorf("获取工具失败: %w", err)
	}

	// 获取目标信息
	target, err := s.GetTarget(task.TargetID)
	if err != nil {
		return fmt.Errorf("获取目标失败: %w", err)
	}

	// 更新任务状态为执行中
	s.UpdateTaskStatus(taskID, 1, 0)

	// 构建命令
	cmd, args := s.buildCommand(tool, target, task.Params)

	log.Printf("执行扫描: %s %s", cmd, strings.Join(args, " "))

	// 执行命令
	execCmd := exec.Command(cmd, args...)
	execCmd.Env = append(os.Environ(), "TERM=dumb")

	output, err := execCmd.CombinedOutput()
	outputStr := string(output)

	// 解析结果并保存
	resultCount := s.parseAndSaveResults(taskID, task.TargetID, tool, outputStr)

	// 更新任务状态
	if err != nil {
		s.UpdateTaskStatus(taskID, 3, 100) // 失败
		s.db.Model(&model.ScanTask{}).Where("id = ?", taskID).Update("error_msg", err.Error())
	} else {
		s.UpdateTaskStatus(taskID, 2, 100) // 完成
		s.db.Model(&model.ScanTask{}).Where("id = ?", taskID).Update("result_count", resultCount)
	}

	return err
}

// buildCommand 构建扫描命令
func (s *WorkflowService) buildCommand(tool *model.ScanTool, target *model.ScanTarget, params string) (string, []string) {
	var args []string

	// 解析默认参数
	var toolParams map[string]interface{}
	if params != "" {
		json.Unmarshal([]byte(params), &toolParams)
	}

	// 根据工具类型构建命令
	switch tool.Name {
	case "sqlmap":
		args = []string{"-u", target.URL}
		// 添加参数
		if level, ok := toolParams["level"].(float64); ok {
			args = append(args, "--level", fmt.Sprintf("%.0f", level))
		}
		if risk, ok := toolParams["risk"].(float64); ok {
			args = append(args, "--risk", fmt.Sprintf("%.0f", risk))
		}
		args = append(args, "--batch", "--smart")

	case "nmap":
		args = []string{}
		if ports, ok := toolParams["ports"].(string); ok {
			args = append(args, "-p", ports)
		}
		args = append(args, target.URL)

	case "nuclei":
		args = []string{}
		if severity, ok := toolParams["severity"].(string); ok {
			args = append(args, "-severity", severity)
		}
		args = append(args, "-u", target.URL, "-json")

	default:
		// 默认使用 -u 参数
		args = []string{"-u", target.URL}
	}

	return tool.Path, args
}

// parseAndSaveResults 解析并保存结果
func (s *WorkflowService) parseAndSaveResults(taskID, targetID uint, tool *model.ScanTool, output string) int {
	result := model.ScanResult{
		TaskID:    taskID,
		TargetID:  targetID,
		ToolID:    tool.ID,
		ToolName:  tool.Name,
		RawOutput: output,
	}

	// 简单解析 - 根据工具类型解析
	switch tool.Name {
	case "sqlmap":
		result.Type = "sql"
		if strings.Contains(output, "is vulnerable") {
			result.Name = "SQL Injection"
			result.Severity = "critical"
			result.Description = "发现SQL注入漏洞"
		} else if strings.Contains(output, "Parameter:") {
			result.Name = "Potential SQL Injection"
			result.Severity = "high"
			result.Description = "可能存在SQL注入"
		} else {
			result.Name = "Scan Completed"
			result.Severity = "info"
			result.Description = "扫描完成，未发现漏洞"
		}
	case "nmap":
		result.Type = "port"
		result.Name = "Port Scan Result"
		result.Severity = "info"
		result.Description = "端口扫描完成"
	case "nuclei":
		result.Type = "vuln"
		result.Name = "Vulnerability Scan"
		result.Severity = "info"
		result.Description = "漏洞扫描完成"
	default:
		result.Type = "scan"
		result.Name = "Scan Result"
		result.Severity = "info"
		result.Description = "扫描完成"
	}

	// 保存结果
	if err := s.db.Create(&result).Error; err != nil {
		log.Printf("保存结果失败: %v", err)
	}

	return 1
}

// ========== LLM分析 ==========

// AnalyzeResults LLM分析扫描结果
func (s *WorkflowService) AnalyzeResults(targetID uint, taskID uint, toolName string) (*model.LLMAnalysis, error) {
	// 获取扫描结果
	var results []model.ScanResult
	query := s.db.Where("target_id = ?", targetID)
	if taskID > 0 {
		query = query.Where("task_id = ?", taskID)
	}
	if toolName != "" {
		query = query.Where("tool_name = ?", toolName)
	}
	if err := query.Find(&results).Error; err != nil {
		return nil, err
	}

	// 获取目标信息
	target, _ := s.GetTarget(targetID)

	// 生成分析（这里使用模拟的LLM）
	analysis := s.simulateLLMAnalysis(results, target)

	// 保存分析结果
	analysis.TargetID = targetID
	if taskID > 0 {
		analysis.TaskID = taskID
	}
	analysis.ToolName = toolName

	if err := s.db.Create(analysis).Error; err != nil {
		return nil, err
	}

	return analysis, nil
}

// simulateLLMAnalysis 模拟LLM分析（实际应该调用OpenAI/Claude）
func (s *WorkflowService) simulateLLMAnalysis(results []model.ScanResult, target *model.ScanTarget) *model.LLMAnalysis {
	// 统计漏洞
	critical := 0
	high := 0
	medium := 0
	low := 0

	for _, r := range results {
		switch r.Severity {
		case "critical":
			critical++
		case "high":
			high++
		case "medium":
			medium++
		case "low":
			low++
		}
	}

	// 确定风险等级
	riskLevel := "低危"
	if critical > 0 {
		riskLevel = "严重"
	} else if high > 0 {
		riskLevel = "高危"
	} else if medium > 0 {
		riskLevel = "中危"
	}

	// 生成分析
	summary := fmt.Sprintf("目标 %s 扫描完成，共发现 %d 个结果", target.URL, len(results))
	analysis := fmt.Sprintf("统计结果: 严重 %d 个，高危 %d 个，中危 %d 个，低危 %d 个", critical, high, medium, low)

	recommendations := ""
	if critical > 0 || high > 0 {
		recommendations = "1. 建议立即修复高危和严重漏洞\n"
	}
	recommendations += "2. 建议定期进行安全扫描\n"
	recommendations += "3. 建议加强输入验证和输出编码"

	// 原始结果JSON
	rawResults, _ := json.Marshal(results)

	return &model.LLMAnalysis{
		Summary:         summary,
		Analysis:        analysis,
		RiskLevel:       riskLevel,
		VulnCount:       len(results),
		Recommendations: recommendations,
		RawResults:      string(rawResults),
		LLMModel:        "mock-gpt-4",
	}
}

// GetAnalysis 获取分析结果
func (s *WorkflowService) GetAnalysis(targetID uint) ([]model.LLMAnalysis, error) {
	var analyses []model.LLMAnalysis
	if err := s.db.Where("target_id = ?", targetID).Order("id DESC").Find(&analyses).Error; err != nil {
		return nil, err
	}
	return analyses, nil
}

// ========== 数据统计 ==========

// Stats 统计数据
type Stats struct {
	Targets       int64 `json:"targets"`
	Tasks         int64 `json:"tasks"`
	CompletedTasks int64 `json:"completed_tasks"`
	Results       int64 `json:"results"`
	Analyses      int64 `json:"analyses"`
}

// GetStats 获取统计数据
func (s *WorkflowService) GetStats(userID uint) (*Stats, error) {
	var stats Stats

	// 目标数量
	s.db.Model(&model.ScanTarget{}).Where("user_id = ?", userID).Count(&stats.Targets)

	// 任务数量
	s.db.Model(&model.ScanTask{}).Count(&stats.Tasks)

	// 完成的任务
	s.db.Model(&model.ScanTask{}).Where("status = ?", 2).Count(&stats.CompletedTasks)

	// 扫描结果数量
	s.db.Model(&model.ScanResult{}).Count(&stats.Results)

	// LLM分析数量
	s.db.Model(&model.LLMAnalysis{}).Count(&stats.Analyses)

	return &stats, nil
}

// ========== 初始化默认工具 ==========

// InitDefaultTools 初始化默认工具
func (s *WorkflowService) InitDefaultTools() error {
	tools := []model.ScanTool{
		{
			Name:        "sqlmap",
			DisplayName: "SQLMap",
			Type:        "blackbox",
			Category:    "漏洞扫描",
			Path:        "sqlmap",
			Command:     "sqlmap -u {target}",
			Params:      `{"level": 2, "risk": 1, "batch": true}`,
			Enabled:     1,
			Description: "SQL注入检测工具",
		},
		{
			Name:        "nmap",
			DisplayName: "Nmap",
			Type:        "asset",
			Category:    "端口扫描",
			Path:        "nmap",
			Command:     "nmap {target}",
			Params:      `{"ports": "1-10000"}`,
			Enabled:     1,
			Description: "端口扫描工具",
		},
		{
			Name:        "nuclei",
			DisplayName: "Nuclei",
			Type:        "blackbox",
			Category:    "漏洞扫描",
			Path:        "nuclei",
			Command:     "nuclei -u {target}",
			Params:      `{"severity": "critical,high,medium"}`,
			Enabled:     1,
			Description: "POC漏洞扫描工具",
		},
		{
			Name:        "dirmap",
			DisplayName: "DirMap",
			Type:        "blackbox",
			Category:    "目录扫描",
			Path:        "dirmap",
			Command:     "dirmap -u {target}",
			Params:      `{"threads": 10}`,
			Enabled:     1,
			Description: "目录扫描工具",
		},
		{
			Name:        "crawlergo",
			DisplayName: "Crawlergo",
			Type:        "blackbox",
			Category:    "爬虫",
			Path:        "crawlergo",
			Command:     "crawlergo {target}",
			Params:      `{"threads": 4}`,
			Enabled:     1,
			Description: "URL爬取工具",
		},
		{
			Name:        "whatweb",
			DisplayName: "WhatWeb",
			Type:        "asset",
			Category:    "指纹识别",
			Path:        "whatweb",
			Command:     "whatweb {target}",
			Params:      `{"aggression": 1}`,
			Enabled:     1,
			Description: "指纹识别工具",
		},
	}

	for _, tool := range tools {
		// 检查是否已存在
		var existing model.ScanTool
		if err := s.db.Where("name = ?", tool.Name).First(&existing).Error; err != nil {
			if err == gorm.ErrRecordNotFound {
				if err := s.db.Create(&tool).Error; err != nil {
					log.Printf("创建工具失败: %s, %v", tool.Name, err)
				}
			}
		}
	}

	return nil
}
