package main

import (
	"flag"
	"fmt"
	"log"

	"github.com/gin-gonic/gin"

	"qingscan/internal/config"
	"qingscan/internal/database"
	"qingscan/internal/handler"
	"qingscan/internal/middleware"
	"qingscan/internal/queue"
)

var (
	configPath string
	port       int
	mode       string
)

func init() {
	flag.StringVar(&configPath, "c", ".", "Config file path")
	flag.IntVar(&port, "p", 8080, "Server port")
	flag.StringVar(&mode, "m", "debug", "Server mode")
	flag.Parse()
}

func main() {
	// 加载配置
	cfg, err := config.Load(configPath)
	if err != nil {
		log.Printf("Failed to load config, using defaults: %v", err)
		cfg = &config.Config{
			Server: config.ServerConfig{
				Port:         port,
				Mode:         mode,
				ReadTimeout:  60,
				WriteTimeout: 60,
			},
			Database: config.DatabaseConfig{
				Host:     "localhost",
				Port:     3306,
				User:     "root",
				Password: "root",
				DBName:   "qingscan",
			},
		}
	}

	// 如果命令行指定了端口，覆盖配置
	if port != 8080 {
		cfg.Server.Port = port
	}
	if mode != "debug" {
		cfg.Server.Mode = mode
	}

	// 初始化数据库
	if err := database.Init(&cfg.Database); err != nil {
		log.Printf("Warning: Database connection failed: %v", err)
		// 继续运行，数据库连接可能稍后恢复
	}

	// 初始化Redis队列
	if err := queue.InitRedis(&cfg.Redis); err != nil {
		log.Printf("Warning: Redis connection failed: %v", err)
		// 继续运行，Redis连接可能稍后恢复
	} else {
		queue.InitAsynq(&cfg.Redis)
	}

	// 设置 Gin 模式
	if cfg.Server.Mode == "release" {
		gin.SetMode(gin.ReleaseMode)
	}

	// 创建路由
	r := gin.Default()

	// 中间件
	r.Use(middleware.CORS())
	r.Use(middleware.Recovery())
	r.Use(gin.Logger())

	// 静态文件
	r.Static("/static", "./static")
	r.StaticFile("/favicon.ico", "./static/favicon.ico")

	// 获取数据库实例
	db := database.GetDB()

	// 创建 Handler
	userHandler := handler.NewUserHandler(db)
	taskHandler := handler.NewTaskHandler(db)
	assetHandler := handler.NewAssetHandler(db)
	appHandler := handler.NewAppHandler(db)
	pluginHandler := handler.NewPluginHandler(db)

	// API 路由
	api := r.Group("/api")
	{
		// 公开接口
		api.POST("/login", userHandler.Login)
		api.POST("/register", userHandler.Register)

		// 需要认证的接口
		auth := api.Group("")
		auth.Use(middleware.AuthMiddleware())
		{
			// 用户相关
			auth.GET("/user", userHandler.GetCurrentUser)
			auth.PUT("/user", userHandler.UpdateUser)
			auth.POST("/user/password", userHandler.ChangePassword)

			// 任务相关
			auth.POST("/tasks", taskHandler.CreateTask)
			auth.GET("/tasks", taskHandler.ListTasks)
			auth.GET("/tasks/:id", taskHandler.GetTask)
			auth.POST("/tasks/:id/start", taskHandler.StartTask)
			auth.POST("/tasks/:id/stop", taskHandler.StopTask)
			auth.DELETE("/tasks/:id", taskHandler.DeleteTask)
			auth.PUT("/tasks/:id/progress", taskHandler.UpdateTaskProgress)

			// 资产相关
			auth.GET("/hosts", assetHandler.ListHosts)
			auth.GET("/hosts/:id", assetHandler.GetHost)
			auth.POST("/hosts", assetHandler.CreateHost)
			auth.PUT("/hosts/:id", assetHandler.UpdateHost)
			auth.DELETE("/hosts/:id", assetHandler.DeleteHost)

			auth.GET("/domains", assetHandler.ListDomains)
			auth.GET("/domains/:id", assetHandler.GetDomain)
			auth.POST("/domains", assetHandler.CreateDomain)
			auth.DELETE("/domains/:id", assetHandler.DeleteDomain)

			auth.GET("/ports", assetHandler.ListPorts)
			auth.POST("/ports", assetHandler.CreatePort)
			auth.DELETE("/ports/:id", assetHandler.DeletePort)

			auth.GET("/urls", assetHandler.ListURLs)
			auth.POST("/urls", assetHandler.CreateURL)
			auth.DELETE("/urls/:id", assetHandler.DeleteURL)

			// 漏洞相关
			auth.GET("/vulnerabilities", assetHandler.ListVulnerabilities)
			auth.GET("/vulnerabilities/:id", assetHandler.GetVulnerability)
			auth.POST("/vulnerabilities", assetHandler.CreateVulnerability)
			auth.PUT("/vulnerabilities/:id", assetHandler.UpdateVulnerability)
			auth.DELETE("/vulnerabilities/:id", assetHandler.DeleteVulnerability)
			auth.GET("/vulnerabilities/stats", assetHandler.GetVulnStats)

			// 统计
			auth.GET("/stats/assets", assetHandler.GetAssetStats)

			// 应用相关
			auth.GET("/apps", appHandler.ListApps)
			auth.GET("/apps/:id", appHandler.GetApp)
			auth.POST("/apps", appHandler.CreateApp)
			auth.PUT("/apps/:id", appHandler.UpdateApp)
			auth.DELETE("/apps/:id", appHandler.DeleteApp)
			auth.POST("/apps/:id/scan", appHandler.StartScan)

			// 插件相关
			auth.GET("/plugins", pluginHandler.ListPlugins)
			auth.GET("/plugins/:id", pluginHandler.GetPlugin)
			auth.POST("/plugins", pluginHandler.CreatePlugin)
			auth.PUT("/plugins/:id", pluginHandler.UpdatePlugin)
			auth.DELETE("/plugins/:id", pluginHandler.DeletePlugin)

			// 工具相关
			auth.GET("/tools", pluginHandler.ListTools)
			auth.GET("/tools/:name/check", pluginHandler.CheckTool)
		}

		// 管理员接口
		admin := auth.Group("/admin")
		admin.Use(middleware.AdminMiddleware())
		{
			admin.GET("/users", userHandler.ListUsers)
			admin.DELETE("/users/:id", userHandler.DeleteUser)
		}
	}

	// 模板视图
	r.LoadHTMLGlob("view/*")

	// 页面路由
	r.GET("/", func(c *gin.Context) {
		c.HTML(200, "index.html", gin.H{
			"title": "QingScan",
		})
	})

	r.GET("/login", func(c *gin.Context) {
		c.HTML(200, "login.html", nil)
	})

	r.GET("/tasks", func(c *gin.Context) {
		c.HTML(200, "tasks.html", nil)
	})

	r.GET("/vulns", func(c *gin.Context) {
		c.HTML(200, "vulns.html", nil)
	})

	r.GET("/hosts", func(c *gin.Context) {
		c.HTML(200, "index.html", gin.H{
			"title": "资产管理",
		})
	})

	r.GET("/apps", func(c *gin.Context) {
		c.HTML(200, "index.html", gin.H{
			"title": "应用管理",
		})
	})

	r.GET("/tools", func(c *gin.Context) {
		c.HTML(200, "index.html", gin.H{
			"title": "工具配置",
		})
	})

	// 健康检查
	r.GET("/health", func(c *gin.Context) {
		c.JSON(200, gin.H{
			"status": "ok",
		})
	})

	// 启动服务器
	addr := fmt.Sprintf(":%d", cfg.Server.Port)

	log.Printf("Server starting on %s", addr)
	log.Printf("Mode: %s", cfg.Server.Mode)

	if err := r.Run(addr); err != nil {
		log.Fatalf("Failed to start server: %v", err)
	}
}
