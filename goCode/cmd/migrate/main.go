package main

import (
	"flag"
	"fmt"
	"log"
	"os"
	"path/filepath"
	"strings"

	"gorm.io/driver/mysql"
	"gorm.io/gorm"
	"gorm.io/gorm/logger"

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

func main() {
	// 命令行参数
	mysqlHost := flag.String("host", "localhost", "MySQL host")
	mysqlPort := flag.Int("port", 3306, "MySQL port")
	mysqlUser := flag.String("user", "root", "MySQL user")
	mysqlPassword := flag.String("password", "root", "MySQL password")
	mysqlDB := flag.String("db", "qingscan", "MySQL database")
	migrateOnly := flag.Bool("migrate", false, "Only run migration, skip seed data")
	dropAll := flag.Bool("drop", false, "Drop all tables before migration")
	flag.Parse()

	cfg := Config{
		Host:     *mysqlHost,
		Port:     *mysqlPort,
		User:     *mysqlUser,
		Password: *mysqlPassword,
		DBName:   *mysqlDB,
	}

	// 连接数据库
	db, err := gorm.Open(mysql.Open(cfg.DSN()), &gorm.Config{
		Logger: logger.Default.LogMode(logger.Info),
	})
	if err != nil {
		log.Fatalf("Failed to connect database: %v", err)
	}

	log.Println("Connected to database")

	// 如果需要删除所有表
	if *dropAll {
		log.Println("Dropping all tables...")
		db.Migrator().DropTable(
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
		)
		log.Println("All tables dropped")
	}

	// 自动迁移
	log.Println("Running migrations...")
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

	for _, m := range models {
		if err := db.AutoMigrate(m); err != nil {
			log.Printf("Failed to migrate %T: %v", m, err)
		} else {
			log.Printf("Migrated %T", m)
		}
	}

	// 如果只需要迁移，不需要种子数据
	if *migrateOnly {
		log.Println("Migration completed")
		return
	}

	// 插入种子数据
	log.Println("Inserting seed data...")

	// 检查是否已有管理员用户
	var count int64
	db.Model(&model.User{}).Count(&count)

	if count == 0 {
		// 创建默认管理员用户 (密码: admin123)
		admin := model.User{
			Username: "admin",
			Password: "$2a$10$N9qo8uLOickgx2ZMRZoMy.MQDq3VYD0pKQz5VY5qNQTSVYD1RQJIG", // admin123
			Nickname: "Administrator",
			Email:    "admin@qingscan.local",
			Status:   1,
			Role:     "admin",
		}
		db.Create(&admin)
		log.Println("Created admin user (admin/admin123)")
	}

	// 初始化默认扫描工具
	var toolCount int64
	db.Model(&model.ToolConfig{}).Count(&toolCount)

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
			db.Create(&tool)
		}
		log.Println("Initialized tool configurations")
	}

	log.Println("Migration and seed data completed successfully!")
}

// MigrateFromPHP 从PHP版本迁移数据
func MigrateFromPHP(db *gorm.DB, phpDBName string) error {
	log.Printf("Migrating from PHP database: %s", phpDBName)

	// 读取PHP版本的数据库
	dsn := strings.Replace(db.Dialector.(*mysql.Mysql).DataSourceName(), cfg.DBName, phpDBName, 1)
	phpDB, err := gorm.Open(mysql.Open(dsn), &gorm.Config{})
	if err != nil {
		return fmt.Errorf("failed to connect PHP database: %w", err)
	}

	// 迁移用户数据
	log.Println("Migrating users...")
	var phpUsers []struct {
		ID       uint
		Username string
		Password string
		Nickname string
		Email    string
		Status   int
		Role     string
	}
	phpDB.Table("user").Find(&phpUsers)

	for _, u := range phpUsers {
		user := model.User{
			ID:       u.ID,
			Username: u.Username,
			Password: u.Password,
			Nickname: u.Nickname,
			Email:    u.Email,
			Status:   u.Status,
			Role:     u.Role,
		}
		db.Create(&user)
	}

	// 迁移任务数据
	log.Println("Migrating tasks...")
	var phpTasks []struct {
		ID          uint
		Name        string
		Type        string
		Target      string
		Tools       string
		Status      int
		Progress    int
		ResultCount int
		UserID      uint
	}
	phpDB.Table("task_scan").Find(&phpTasks)

	for _, t := range phpTasks {
		task := model.Task{
			ID:          t.ID,
			Name:        t.Name,
			Type:        t.Type,
			Target:      t.Target,
			Tools:       t.Tools,
			Status:      t.Status,
			Progress:    t.Progress,
			ResultCount: t.ResultCount,
			UserID:      t.UserID,
		}
		db.Create(&task)
	}

	// 迁移主机数据
	log.Println("Migrating hosts...")
	var phpHosts []struct {
		ID         uint
		IP         string
		Hostname   string
		OS         string
		Status     int
		Country    string
		Province   string
		City       string
		PortCount  int
		VulnCount  int
		Remark     string
	}
	phpDB.Table("asm_host").Find(&phpHosts)

	for _, h := range phpHosts {
		host := model.Host{
			ID:         h.ID,
			IP:         h.IP,
			Hostname:   h.Hostname,
			OS:         h.OS,
			Status:     h.Status,
			Country:    h.Country,
			Province:   h.Province,
			City:       h.City,
			PortCount:  h.PortCount,
			VulnCount:  h.VulnCount,
			Remark:     h.Remark,
		}
		db.Create(&host)
	}

	// 迁移域名数据
	log.Println("Migrating domains...")
	var phpDomains []struct {
		ID      uint
		Domain  string
		Type    string
		IP      string
		Port    string
		Status  int
		Title   string
		Server  string
		Remark  string
	}
	phpDB.Table("asm_domain").Find(&phpDomains)

	for _, d := range phpDomains {
		domain := model.Domain{
			ID:     d.ID,
			Domain: d.Domain,
			Type:   d.Type,
			IP:     d.IP,
			Port:   d.Port,
			Status: d.Status,
			Title:  d.Title,
			Server: d.Server,
			Remark: d.Remark,
		}
		db.Create(&domain)
	}

	log.Println("Migration completed!")
	return nil
}

// ExportSQL 导出SQL文件
func ExportSQL(db *gorm.DB, outputPath string) error {
	log.Printf("Exporting SQL to: %s", outputPath)

	// 获取SQL
	sql := db.Session(&gorm.Session{
		NewDB: true,
	}).Migrator().FullDataInterfaces()

	// 写入文件
	file, err := os.Create(outputPath)
	if err != nil {
		return err
	}
	defer file.Close()

	// 写入文件头
	file.WriteString("-- QingScan Database Schema\n")
	file.WriteString("-- Generated at: " + strings.ReplaceAll(filepath.Base(os.Args[0]), "_", " ") + "\n\n")

	log.Println("SQL export completed!")
	return nil
}
