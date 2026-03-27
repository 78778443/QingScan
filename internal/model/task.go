package model

import (
	"time"

	"gorm.io/gorm"
)

type Task struct {
	ID          uint           `gorm:"primarykey" json:"id"`
	CreatedAt   time.Time      `json:"created_at"`
	UpdatedAt   time.Time      `json:"updated_at"`
	DeletedAt   gorm.DeletedAt `gorm:"index" json:"-"`
	Name        string         `gorm:"size:100;not null" json:"name"`
	Type        string         `gorm:"size:20;not null" json:"type"` // host, web, code
	Target      string         `gorm:"size:500;not null" json:"target"`
	Tools       string         `gorm:"size:500" json:"tools"` // 使用的工具，逗号分隔
	Status      int            `gorm:"default:0" json:"status"` // 0:待执行 1:执行中 2:完成 3:失败
	Progress    int            `gorm:"default:0" json:"progress"` // 0-100
	ResultCount int            `gorm:"default:0" json:"result_count"`
	UserID      uint           `gorm:"not null" json:"user_id"`
	StartTime   *time.Time     `json:"start_time"`
	EndTime     *time.Time     `json:"end_time"`
	ErrorMsg    string         `gorm:"type:text" json:"error_msg"`
}

func (Task) TableName() string {
	return "task_scan"
}

type TaskHostScan struct {
	ID        uint           `gorm:"primarykey" json:"id"`
	CreatedAt time.Time      `json:"created_at"`
	UpdatedAt time.Time      `json:"updated_at"`
	DeletedAt gorm.DeletedAt `gorm:"index" json:"-"`
	TaskID    uint           `gorm:"not null;index" json:"task_id"`
	Host      string         `gorm:"size:100;not null" json:"host"`
	Port      string         `gorm:"size:20" json:"port"`
	Protocol  string         `gorm:"size:20" json:"protocol"`
	Status    int            `gorm:"default:0" json:"status"`
	Result    string         `gorm:"type:text" json:"result"`
}

func (TaskHostScan) TableName() string {
	return "task_host_scan"
}
