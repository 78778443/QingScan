package migrate

import (
	"fmt"
	"log"

	"gorm.io/driver/mysql"
	"gorm.io/gorm"

	"qingscan/internal/model"
)

// Config 数据库配置
type Config struct {
	Host     string
	Port     int
	User     string
	Password string
	DBName   string
}

func (c Config) DSN() string {
	return fmt.Sprintf("%s:%s@tcp(%s:%d)/%s?charset=utf8mb4&parseTime=True&loc=Local&multiStatements=true",
		c.User, c.Password, c.Host, c.Port, c.DBName)
}

// Migrator 迁移器
type Migrator struct {
	db *gorm.DB
}

// NewMigrator 创建迁移器
func NewMigrator(cfg Config) (*Migrator, error) {
	db, err := gorm.Open(mysql.Open(cfg.DSN()), &gorm.Config{
		Logger: log.Default(),
	})
	if err != nil {
		return nil, err
	}
	return &Migrator{db: db}, nil
}

// Migrate 执行迁移
func (m *Migrator) Migrate() error {
	models := []interface{}{
		&model.User{},
		&model.Task{},
		&model.TaskHostScan{},
		&model.Host{},
		&model.Domain{},
		&model.Port{},
		&model.URL{},
		&model.Vulnerability{},
		&model.App{},
		&model.Plugin{},
		&model.ToolConfig{},
	}

	for _, model := range models {
		if err := m.db.AutoMigrate(model); err != nil {
			return fmt.Errorf("failed to migrate %T: %w", model, err)
		}
		log.Printf("Migrated: %T", model)
	}

	return nil
}

// Seed 插入种子数据
func (m *Migrator) Seed() error {
	// 检查是否已有管理员用户
	var count int64
	m.db.Model(&model.User{}).Count(&count)

	if count == 0 {
		// 创建默认管理员用户 (密码: admin123)
		admin := model.User{
			Username: "admin",
			Password: "$2a$10$N9qo8uLOickgx2ZMRZoMy.MQDq3VYD0pKQz5VY5qNQTSVYD1RQJIG",
			Nickname: "Administrator",
			Email:    "admin@qingscan.local",
			Status:   1,
			Role:     "admin",
		}
		m.db.Create(&admin)
		log.Println("Created admin user (admin/admin123)")
	}

	// 初始化默认扫描工具
	var toolCount int64
	m.db.Model(&model.ToolConfig{}).Count(&toolCount)

	if toolCount == 0 {
		tools := []model.ToolConfig{
			{Name: "nmap", Status: 0, InstallCmd: "apt install nmap"},
			{Name: "nuclei", Status: 0, InstallCmd: "go install github.com/projectdiscovery/nuclei/v3@latest"},
			{Name: "xray", Status: 0, InstallCmd: "download from https://github.com/chaitin/xray/releases"},
			{Name: "crawlergo", Status: 0, InstallCmd: "go install github.com/9bie/sec/crawlergo@latest"},
			{Name: "dirmap", Status: 0, InstallCmd: "git clone https://github.com/H4ckForJob/dirmap.git"},
			{Name: "whatweb", Status: 0, InstallCmd: "apt install whatweb"},
			{Name: "sqlmap", Status: 0, InstallCmd: "git clone https://github.com/sqlmapproject/sqlmap.git"},
			{Name: "hydra", Status: 0, InstallCmd: "apt install hydra"},
			{Name: "semgrep", Status: 0, InstallCmd: "pip install semgrep"},
		}

		for _, tool := range tools {
			m.db.Create(&tool)
		}
		log.Println("Initialized tool configurations")
	}

	return nil
}

// GetDB 获取数据库实例
func (m *Migrator) GetDB() *gorm.DB {
	return m.db
}
