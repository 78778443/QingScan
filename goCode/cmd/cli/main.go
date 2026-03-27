package main

import (
	"encoding/json"
	"flag"
	"fmt"
	"log"
	"os"

	"qingscan/internal/config"
	"qingscan/internal/database"
	"qingscan/internal/result"
	"qingscan/internal/scanner"
)

var (
	// 命令行参数
	target     = flag.String("t", "", "扫描目标 (URL/IP/域名)")
	scannerName = flag.String("s", "", "扫描器名称 (nmap, nuclei, sqlmap, etc.)")
	toolsPath  = flag.String("p", "/opt/qingscan/tools", "工具目录路径")
	list       = flag.Bool("list", false, "列出所有可用的扫描器")
	check      = flag.String("check", "", "检查指定扫描器是否可用")
	save       = flag.Bool("save", false, "保存结果到数据库")

	// 扫描选项
	optLevel     = flag.Int("level", 2, "SQLMap扫描级别")
	optRisk      = flag.Int("risk", 1, "SQLMap风险级别")
	optThreads   = flag.Int("threads", 4, "并发线程数")
	optBatch     = flag.Bool("batch", true, "批量模式")
	optSeverity  = flag.String("severity", "critical,high,medium,low,info", "Nuclei严重程度")
	optPorts     = flag.String("ports", "1-10000", "Nmap端口范围")
	optOutput    = flag.String("o", "", "输出文件")
	optJSON      = flag.Bool("json", false, "JSON输出")
	optVerbose   = flag.Bool("v", false, "详细输出")
)

func main() {
	flag.Usage = func() {
		fmt.Fprintf(os.Stderr, "QingScan CLI - 扫描器命令行工具\n\n")
		fmt.Fprintf(os.Stderr, "用法: %s [选项]\n\n", os.Args[0])
		flag.PrintDefaults()
		fmt.Fprintf(os.Stderr, "\n支持的扫描器:\n")
		fmt.Fprintf(os.Stderr, "  nmap       - 端口扫描\n")
		fmt.Fprintf(os.Stderr, "  nuclei     - POC漏洞扫描\n")
		fmt.Fprintf(os.Stderr, "  sqlmap     - SQL注入扫描\n")
		fmt.Fprintf(os.Stderr, "  xray       - Web漏洞扫描\n")
		fmt.Fprintf(os.Stderr, "  dirmap     - 目录扫描\n")
		fmt.Fprintf(os.Stderr, "  crawlergo  - URL爬虫\n")
		fmt.Fprintf(os.Stderr, "  whatweb    - 指纹识别\n")
		fmt.Fprintf(os.Stderr, "  rad        - 浏览器爬虫\n")
		fmt.Fprintf(os.Stderr, "  hydra      - 暴力破解\n")
		fmt.Fprintf(os.Stderr, "  semgrep    - 代码扫描\n")
		fmt.Fprintf(os.Stderr, "  vulmap     - POC扫描\n")
		fmt.Fprintf(os.Stderr, "  dismap     - 指纹扫描\n")
	}
	flag.Parse()

	// 设置日志级别
	if !*optVerbose {
		log.SetOutput(os.NewFile(0, ""))
	}

	// 初始化数据库（如果需要保存结果）
	var dbInitialized bool
	if *save {
		cfg, err := config.Load(".")
		if err != nil {
			log.Printf("警告: 配置加载失败，将使用默认配置: %v", err)
			cfg = &config.Config{
				Database: config.DatabaseConfig{
					Host:     "127.0.0.1",
					Port:     3306,
					User:     "root",
					Password: "root",
					DBName:   "qingscan_test",
					MaxIdle:  10,
					MaxOpen:  100,
				},
			}
		}
		if err := database.Init(&cfg.Database); err != nil {
			log.Printf("警告: 数据库连接失败，将不保存结果到数据库: %v", err)
		} else {
			dbInitialized = true
			log.Println("数据库连接成功")
		}
	}

	// 注册所有扫描器
	scanner.RegisterAllScanners(*toolsPath)

	// 列出扫描器
	if *list {
		listScanners()
		return
	}

	// 检查扫描器
	if *check != "" {
		checkScanner(*check)
		return
	}

	// 运行扫描
	if *target == "" || *scannerName == "" {
		fmt.Fprintf(os.Stderr, "错误: -t 和 -s 参数是必需的\n\n")
		flag.Usage()
		os.Exit(1)
	}

	runScan(dbInitialized)
}

func listScanners() {
	m := scanner.GetScanManager()
	scanners := m.List()

	fmt.Println("可用的扫描器:")
	fmt.Println("-------------")
	for _, name := range scanners {
		s := m.Get(name)
		if s != nil {
			fmt.Printf("  %-15s 版本: %s\n", name, s.Version())
		}
	}
}

func checkScanner(name string) {
	m := scanner.GetScanManager()
	s := m.Get(name)

	if s == nil {
		fmt.Printf("错误: 未知扫描器 '%s'\n", name)
		os.Exit(1)
	}

	fmt.Printf("检查扫描器: %s\n", name)
	fmt.Printf("  版本: %s\n", s.Version())
	fmt.Printf("  路径: %s\n", s.Name())

	if err := s.Check(); err != nil {
		fmt.Printf("  状态: 不可用 - %v\n", err)
		os.Exit(1)
	}

	fmt.Println("  状态: 可用")
}

func runScan(dbInitialized bool) {
	m := scanner.GetScanManager()
	s := m.Get(*scannerName)

	if s == nil {
		fmt.Printf("错误: 未知扫描器 '%s'\n", *scannerName)
		os.Exit(1)
	}

	// 检查扫描器
	if err := s.Check(); err != nil {
		fmt.Printf("错误: %s 不可用 - %v\n", *scannerName, err)
		os.Exit(1)
	}

	fmt.Printf("使用扫描器: %s\n", *scannerName)
	fmt.Printf("目标: %s\n", *target)

	// 构建选项
	options := buildOptions()

	// 运行扫描
	scanResult, err := s.Run(*target, options)
	if err != nil {
		fmt.Printf("扫描错误: %v\n", err)
		os.Exit(1)
	}

	// 保存结果到数据库
	if *save && dbInitialized {
		saveToDatabase(scanResult)
	}

	// 输出结果
	outputResult(scanResult)
}

func buildOptions() map[string]interface{} {
	options := make(map[string]interface{})

	switch *scannerName {
	case "sqlmap":
		options["level"] = *optLevel
		options["risk"] = *optRisk
		options["batch"] = *optBatch
		options["threads"] = *optThreads
	case "nuclei":
		options["severity"] = *optSeverity
		options["concurrency"] = *optThreads
	case "nmap":
		options["ports"] = *optPorts
		options["threads"] = *optThreads
	case "crawlergo", "rad":
		options["threads"] = *optThreads
	case "whatweb":
		options["concurrency"] = *optThreads
	case "hydra":
		options["threads"] = *optThreads
	case "semgrep":
		options["format"] = "json"
	}

	if *optOutput != "" {
		options["output"] = *optOutput
	}

	return options
}

func outputResult(result *scanner.ScanResult) {
	fmt.Println("\n============ 扫描结果 ============")
	fmt.Printf("扫描器: %s\n", result.Scanner)
	fmt.Printf("目标: %s\n", result.Target)
	fmt.Printf("耗时: %dms\n", result.Duration)
	fmt.Printf("状态: %s\n", func() string {
		if result.Success {
			return "成功"
		}
		return "失败"
	}())

	if result.Error != "" {
		fmt.Printf("错误: %s\n", result.Error)
	}

	if *optJSON {
		data, _ := json.MarshalIndent(result, "", "  ")
		fmt.Println("\n" + string(data))
	} else if result.Results != nil {
		switch v := result.Results.(type) {
		case string:
			fmt.Println("\n输出:")
			fmt.Println(v)
		default:
			data, _ := json.MarshalIndent(v, "", "  ")
			fmt.Println("\n输出:")
			fmt.Println(string(data))
		}
	}
}

// saveToDatabase 保存扫描结果到数据库
func saveToDatabase(scanResult *scanner.ScanResult) {
	db := database.GetDB()
	if db == nil {
		log.Println("数据库未初始化")
		return
	}

	// 创建结果服务
	rs := result.NewResultService(db)

	// 转换结果为字符串
	var outputStr string
	if scanResult.Results != nil {
		switch v := scanResult.Results.(type) {
		case string:
			outputStr = v
		default:
			data, _ := json.Marshal(v)
			outputStr = string(data)
		}
	}

	// 保存结果
	count, err := rs.SaveScanResult(0, scanResult.Scanner, scanResult.Target, outputStr)
	if err != nil {
		log.Printf("保存结果失败: %v", err)
		return
	}

	log.Printf("成功保存 %d 条结果到数据库", count)
}
