package model

import (
	"time"

	"gorm.io/gorm"
)

// ScanTool 扫描工具表
type ScanTool struct {
	ID          uint           `gorm:"primarykey" json:"id"`
	CreatedAt   time.Time      `json:"created_at"`
	UpdatedAt   time.Time      `json:"updated_at"`
	DeletedAt   gorm.DeletedAt `gorm:"index" json:"-"`
	Name        string         `gorm:"size:50;unique;not null" json:"name"` // 工具名称，如 sqlmap, nuclei
	DisplayName string         `gorm:"size:100" json:"display_name"`         // 显示名称
	Type        string         `gorm:"size:20;not null" json:"type"`        // blackbox, whitebox, asset
	Category    string         `gorm:"size:50" json:"category"`             // 漏洞扫描, 端口扫描, 爬虫等
	Path        string         `gorm:"size:255" json:"path"`                 // 工具路径
	Command     string         `gorm:"size:500" json:"command"`              // 命令模板，如 sqlmap -u {target}
	Params      string         `gorm:"type:text" json:"params"`              // JSON格式的默认参数
	Enabled     int            `gorm:"default:1" json:"enabled"`            // 是否启用
	Description string         `gorm:"type:text" json:"description"`        // 工具描述
	Version     string         `gorm:"size:20" json:"version"`              // 版本
}

func (ScanTool) TableName() string {
	return "scan_tool"
}

// ScanTarget 扫描目标表
type ScanTarget struct {
	ID          uint           `gorm:"primarykey" json:"id"`
	CreatedAt   time.Time      `json:"created_at"`
	UpdatedAt   time.Time      `json:"updated_at"`
	DeletedAt   gorm.DeletedAt `gorm:"index" json:"-"`
	Name        string         `gorm:"size:100;not null" json:"name"`       // 目标名称
	URL         string         `gorm:"size:500;not null" json:"url"`         // 目标URL
	Domain      string         `gorm:"size:100" json:"domain"`              // 域名
	IP          string         `gorm:"size:50" json:"ip"`                   // IP
	Type        string         `gorm:"size:20;not null" json:"type"`        // web, host, code
	Status      int            `gorm:"default:1" json:"status"`             // 1:启用 0:禁用
	Remark      string         `gorm:"size:500" json:"remark"`              // 备注
	UserID      uint           `gorm:"not null" json:"user_id"`             // 创建者
}

func (ScanTarget) TableName() string {
	return "scan_target"
}

// ScanTask 扫描任务表（关联目标和工具）
type ScanTask struct {
	ID          uint           `gorm:"primarykey" json:"id"`
	CreatedAt   time.Time      `json:"created_at"`
	UpdatedAt   time.Time      `json:"updated_at"`
	DeletedAt   gorm.DeletedAt `gorm:"index" json:"-"`
	TargetID    uint           `gorm:"not null;index" json:"target_id"`     // 目标ID
	ToolID      uint           `gorm:"not null" json:"tool_id"`              // 工具ID
	ToolName    string         `gorm:"size:50" json:"tool_name"`             // 工具名称
	Status      int            `gorm:"default:0" json:"status"`               // 0:待执行 1:执行中 2:已完成 3:失败
	Progress    int            `gorm:"default:0" json:"progress"`            // 0-100
	Params      string         `gorm:"type:text" json:"params"`              // 运行参数 JSON
	ResultCount int            `gorm:"default:0" json:"result_count"`        // 结果数量
	ErrorMsg    string         `gorm:"type:text" json:"error_msg"`          // 错误信息
	StartTime   *time.Time     `json:"start_time"`                         // 开始时间
	EndTime     *time.Time     `json:"end_time"`                           // 结束时间
	UserID      uint           `gorm:"not null" json:"user_id"`             // 创建者
}

func (ScanTask) TableName() string {
	return "scan_task"
}

// ScanResult 扫描结果表
type ScanResult struct {
	ID          uint           `gorm:"primarykey" json:"id"`
	CreatedAt   time.Time      `json:"created_at"`
	UpdatedAt   time.Time      `json:"updated_at"`
	DeletedAt   gorm.DeletedAt `gorm:"index" json:"-"`
	TaskID      uint           `gorm:"not null;index" json:"task_id"`       // 任务ID
	TargetID    uint           `gorm:"not null;index" json:"target_id"`     // 目标ID
	ToolID      uint           `gorm:"not null" json:"tool_id"`             // 工具ID
	ToolName    string         `gorm:"size:50" json:"tool_name"`            // 工具名称
	Name        string         `gorm:"size:200" json:"name"`                // 漏洞/结果名称
	Target      string         `gorm:"size:500" json:"target"`              // 目标地址
	Type        string         `gorm:"size:50" json:"type"`                // 结果类型
	Severity    string         `gorm:"size:20" json:"severity"`             // critical, high, medium, low, info
	Status      int            `gorm:"default:0" json:"status"`             // 0:待确认 1:确认 2:误报 3:已修复
	RawOutput   string         `gorm:"type:text" json:"raw_output"`        // 原始输出
	Description string         `gorm:"type:text" json:"description"`       // 描述
	Request     string         `gorm:"type:text" json:"request"`           // 请求包
	Response    string         `gorm:"type:text" json:"response"`           // 响应包
}

func (ScanResult) TableName() string {
	return "scan_result"
}

// LLMAnalysis LLM分析结果表
type LLMAnalysis struct {
	ID          uint           `gorm:"primarykey" json:"id"`
	CreatedAt   time.Time      `json:"created_at"`
	UpdatedAt   time.Time      `json:"updated_at"`
	DeletedAt   gorm.DeletedAt `gorm:"index" json:"-"`
	TargetID    uint           `gorm:"not null;index" json:"target_id"`     // 目标ID
	TaskID      uint           `gorm:"index" json:"task_id"`                 // 任务ID（可选）
	ToolName    string         `gorm:"size:50" json:"tool_name"`             // 工具名称
	Summary     string         `gorm:"type:text" json:"summary"`             // 总结
	Analysis    string         `gorm:"type:text" json:"analysis"`            // 详细分析
	RiskLevel   string         `gorm:"size:20" json:"risk_level"`            // 风险等级
	VulnCount   int            `gorm:"default:0" json:"vuln_count"`         // 漏洞数量
	Recommendations string     `gorm:"type:text" json:"recommendations"`     // 修复建议
	RawResults  string         `gorm:"type:text" json:"raw_results"`         // 原始结果JSON
	LLMModel    string         `gorm:"size:50" json:"llm_model"`            // 使用的LLM模型
	Prompt      string         `gorm:"type:text" json:"prompt"`              // 使用的提示词
}

func (LLMAnalysis) TableName() string {
	return "llm_analysis"
}
