package model

import (
	"time"

	"gorm.io/gorm"
)

// App represents web application for scanning
type App struct {
	ID          uint           `gorm:"primarykey" json:"id"`
	CreatedAt   time.Time      `json:"created_at"`
	UpdatedAt   time.Time      `json:"updated_at"`
	DeletedAt   gorm.DeletedAt `gorm:"index" json:"-"`
	Name        string         `gorm:"size:100;not null" json:"name"`
	URL         string         `gorm:"size:500;not null" json:"url"`
	Domain      string         `gorm:"size:100" json:"domain"`
	IP          string         `gorm:"size:50" json:"ip"`
	Port        int            `json:"port"`
	Scheme      string         `gorm:"size:10" json:"scheme"`
	Status      int            `gorm:"default:1" json:"status"` // 1:启用 0:禁用
	Fingerprint string         `gorm:"size:100" json:"fingerprint"`
	WebServer   string         `gorm:"size:100" json:"web_server"`
	Framework   string         `gorm:"size:100" json:"framework"`
	Language    string         `gorm:"size:50" json:"language"`
	Title       string         `gorm:"size:200" json:"title"`
	Remark      string         `gorm:"size:500" json:"remark"`
}

func (App) TableName() string {
	return "app"
}

// Plugin represents scanning plugin
type Plugin struct {
	ID          uint           `gorm:"primarykey" json:"id"`
	CreatedAt   time.Time      `json:"created_at"`
	UpdatedAt   time.Time      `json:"updated_at"`
	DeletedAt   gorm.DeletedAt `gorm:"index" json:"-"`
	Name        string         `gorm:"size:100;not null" json:"name"`
	Slug        string         `gorm:"size:100;unique;not null" json:"slug"`
	Type        string         `gorm:"size:20;not null" json:"type"` // scanner, poc, tool
	Command     string         `gorm:"size:500" json:"command"`
	Description string         `gorm:"type:text" json:"description"`
	Author      string         `gorm:"size:50" json:"author"`
	Version     string         `gorm:"size:20" json:"version"`
	Source      string         `gorm:"size:50" json:"source"` // local, store
	Status      int            `gorm:"default:1" json:"status"` // 1:启用 0:禁用
	InstallPath string         `gorm:"size:255" json:"install_path"`
	Config      string         `gorm:"type:text" json:"config"` // JSON配置
}

func (Plugin) TableName() string {
	return "plugin"
}

// ToolConfig represents tool configuration
type ToolConfig struct {
	ID          uint           `gorm:"primarykey" json:"id"`
	CreatedAt   time.Time      `json:"created_at"`
	UpdatedAt   time.Time      `json:"updated_at"`
	DeletedAt   gorm.DeletedAt `gorm:"index" json:"-"`
	Name        string         `gorm:"size:50;unique;not null" json:"name"`
	Path        string         `gorm:"size:255" json:"path"`
	Version     string         `gorm:"size:50" json:"version"`
	Status      int            `gorm:"default:1" json:"status"` // 1:已安装 0:未安装
	InstallCmd  string         `gorm:"size:500" json:"install_cmd"`
	CheckCmd    string         `gorm:"size:255" json:"check_cmd"`
}

func (ToolConfig) TableName() string {
	return "project_tools"
}
