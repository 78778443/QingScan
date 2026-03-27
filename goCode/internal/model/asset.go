package model

import (
	"time"

	"gorm.io/gorm"
)

// Host represents asset host information
type Host struct {
	ID          uint           `gorm:"primarykey" json:"id"`
	CreatedAt   time.Time      `json:"created_at"`
	UpdatedAt   time.Time      `json:"updated_at"`
	DeletedAt   gorm.DeletedAt `gorm:"index" json:"-"`
	IP          string         `gorm:"size:50;index" json:"ip"`
	MAC         string         `gorm:"size:50" json:"mac"`
	Hostname    string         `gorm:"size:100" json:"hostname"`
	OS          string         `gorm:"size:100" json:"os"`
	OSVersion   string         `gorm:"size:100" json:"os_version"`
	Status      int            `gorm:"default:1" json:"status"` // 1:存活 0:down
	ISP         string         `gorm:"size:50" json:"isp"`
	Country     string         `gorm:"size:50" json:"country"`
	Province    string         `gorm:"size:50" json:"province"`
	City        string         `gorm:"size:50" json:"city"`
	Longitude   string         `gorm:"size:50" json:"longitude"`
	Latitude    string         `gorm:"size:50" json:"latitude"`
	PortCount   int            `gorm:"default:0" json:"port_count"`
	VulnCount   int            `gorm:"default:0" json:"vuln_count"`
	Tags        string         `gorm:"size:255" json:"tags"`
	Remark      string         `gorm:"size:500" json:"remark"`
}

func (Host) TableName() string {
	return "asm_host"
}

// Domain represents domain information
type Domain struct {
	ID          uint           `gorm:"primarykey" json:"id"`
	CreatedAt   time.Time      `json:"created_at"`
	UpdatedAt   time.Time      `json:"updated_at"`
	DeletedAt   gorm.DeletedAt `gorm:"index" json:"-"`
	Domain      string         `gorm:"size:100;index" json:"domain"`
	Type        string         `gorm:"size:20" json:"type"` // primary, subdomain
	Source      string         `gorm:"size:50" json:"source"`
	IP          string         `gorm:"size:100" json:"ip"`
	CNAME       string         `gorm:"size:100" json:"cname"`
	Port        string         `gorm:"size:100" json:"port"`
	Status      int            `gorm:"default:1" json:"status"`
	Title       string         `gorm:"size:200" json:"title"`
	Server      string         `gorm:"size:100" json:"server"`
	Remark      string         `gorm:"size:500" json:"remark"`
}

func (Domain) TableName() string {
	return "asm_domain"
}

// Port represents port information
type Port struct {
	ID          uint           `gorm:"primarykey" json:"id"`
	CreatedAt   time.Time      `json:"created_at"`
	UpdatedAt   time.Time      `json:"updated_at"`
	DeletedAt   gorm.DeletedAt `gorm:"index" json:"-"`
	HostID      uint           `gorm:"index" json:"host_id"`
	Host        string         `gorm:"size:50;index" json:"host"`
	Port        string         `gorm:"size:10;index" json:"port"`
	Protocol    string         `gorm:"size:20" json:"protocol"`
	State       string         `gorm:"size:20" json:"state"`
	Service     string         `gorm:"size:50" json:"service"`
	Version     string         `gorm:"size:100" json:"version"`
	Banner      string         `gorm:"size:500" json:"banner"`
}

func (Port) TableName() string {
	return "asm_host_port"
}

// URL represents web URL information
type URL struct {
	ID          uint           `gorm:"primarykey" json:"id"`
	CreatedAt   time.Time      `json:"created_at"`
	UpdatedAt   time.Time      `json:"updated_at"`
	DeletedAt   gorm.DeletedAt `gorm:"index" json:"-"`
	URL         string         `gorm:"size:500;index" json:"url"`
	Host        string         `gorm:"size:100;index" json:"host"`
	Domain      string         `gorm:"size:100;index" json:"domain"`
	Scheme      string         `gorm:"size:10" json:"scheme"`
	Method      string         `gorm:"size:10" json:"method"`
	Path        string         `gorm:"size:255" json:"path"`
	Query       string         `gorm:"size:500" json:"query"`
	StatusCode  int            `json:"status_code"`
	Title       string         `gorm:"size:200" json:"title"`
	Length      int            `json:"length"`
	Fingerprint string         `gorm:"size:100" json:"fingerprint"`
	Source      string         `gorm:"size:50" json:"source"`
}

func (URL) TableName() string {
	return "asm_urls"
}

// Vulnerability represents vulnerability information
type Vulnerability struct {
	ID          uint           `gorm:"primarykey" json:"id"`
	CreatedAt   time.Time      `json:"created_at"`
	UpdatedAt   time.Time      `json:"updated_at"`
	DeletedAt   gorm.DeletedAt `gorm:"index" json:"-"`
	Name        string         `gorm:"size:200;index" json:"name"`
	Target      string         `gorm:"size:500" json:"target"` // URL或IP
	Type        string         `gorm:"size:50;index" json:"type"` // sql,xss,rce等
	Severity    string         `gorm:"size:20" json:"severity"` // critical,high,medium,low,info
	Status      int            `gorm:"default:0" json:"status"` // 0:待确认 1:已确认 2:误报 3:已修复
	Tool        string         `gorm:"size:50" json:"tool"` // nuclei,xray等
	Poc         string         `gorm:"size:100" json:"poc"`
	Description string         `gorm:"type:text" json:"description"`
	Solution    string         `gorm:"type:text" json:"solution"`
	Request     string         `gorm:"type:text" json:"request"`
	Response    string         `gorm:"type:text" json:"response"`
	Remark      string         `gorm:"size:500" json:"remark"`
}

func (Vulnerability) TableName() string {
	return "asm_vulnerability_summary"
}
