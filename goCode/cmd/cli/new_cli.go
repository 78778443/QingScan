package main

import (
	"flag"
	"fmt"
	"log"
	"os"

	"gorm.io/driver/mysql"
	"gorm.io/gorm"
	"gorm.io/gorm/logger"

	"qingscan/internal/model"
	"qingscan/internal/workflow"
)

// CLI命令行工具
var (
	flagTarget   = flag.String("t", "", "扫描目标URL")
	flagTool     = flag.String("s", "", "扫描工具名称 (sqlmap, nmap, nuclei等)")
	flagList     = flag.Bool("list", false, "列出所有可用工具")
	flagInit     = flag.Bool("init", false, "初始化工具列表")
	flagAnalyze  = flag.Bool("analyze", false, "执行LLM分析")
	flagTasks    = flag.Bool("tasks", false, "查看任务列表")
	flagResults  = flag.Bool("results", false, "查看扫描结果")
	flagParams   = flag.String("p", "", "工具参数 (JSON格式)")
)

func main() {
	flag.Parse()

	// 连接数据库
	db, err := connectDB()
	if err != nil {
		log.Fatalf("数据库连接失败: %v", err)
	}

	// 创建工作流服务
	ws := workflow.NewWorkflowService(db)

	// 初始化工具
	if *flagInit {
		if err := ws.InitDefaultTools(); err != nil {
			log.Fatalf("初始化工具失败: %v", err)
		}
		fmt.Println("工具初始化完成!")
		listTools(ws)
		return
	}

	// 列出工具
	if *flagList {
		listTools(ws)
		return
	}

	// 列出任务
	if *flagTasks {
		listTasks(ws)
		return
	}

	// 列出结果
	if *flagResults {
		listResults(ws)
		return
	}

	// 执行扫描流程
	if *flagTarget != "" && *flagTool != "" {
		runFullWorkflow(ws)
		return
	}

	// 执行LLM分析
	if *flagAnalyze {
		runAnalysis(ws)
		return
	}

	// 显示帮助
	showHelp()
}

func connectDB() (*gorm.DB, error) {
	dsn := "root:root@tcp(127.0.0.1:3306)/qingscan_test?charset=utf8mb4&parseTime=True&loc=Local"
	db, err := gorm.Open(mysql.Open(dsn), &gorm.Config{
		Logger: logger.Default.LogMode(logger.Info),
	})
	if err != nil {
		return nil, err
	}

	// 自动迁移
	db.AutoMigrate(
		&model.ScanTool{},
		&model.ScanTarget{},
		&model.ScanTask{},
		&model.ScanResult{},
		&model.LLMAnalysis{},
	)

	return db, nil
}

func listTools(ws *workflow.WorkflowService) {
	tools, err := ws.GetTools("")
	if err != nil {
		log.Fatalf("获取工具列表失败: %v", err)
	}

	fmt.Println("\n========== 可用工具 ==========")
	fmt.Printf("%-15s %-10s %-15s %s\n", "工具名称", "类型", "分类", "描述")
	fmt.Println("-------------------------------------------------------------")
	for _, t := range tools {
		fmt.Printf("%-15s %-10s %-15s %s\n", t.Name, t.Type, t.Category, t.Description)
	}
}

func listTasks(ws *workflow.WorkflowService) {
	tasks, _, err := ws.ListTasks(0, -1, 1, 20)
	if err != nil {
		log.Fatalf("获取任务列表失败: %v", err)
	}

	fmt.Println("\n========== 扫描任务 ==========")
	fmt.Printf("%-5s %-30s %-15s %-10s %s\n", "ID", "目标", "工具", "状态", "结果数")
	fmt.Println("-------------------------------------------------------------")
	for _, t := range tasks {
		status := []string{"待执行", "执行中", "已完成", "失败"}[t.Status]
		fmt.Printf("%-5d %-30s %-15s %-10s %d\n", t.ID, t.ToolName, t.ToolName, status, t.ResultCount)
	}
}

func listResults(ws *workflow.WorkflowService) {
	var results []model.ScanResult
	if err := ws.GetDB().Model(&model.ScanResult{}).Find(&results).Error; err != nil {
		log.Fatalf("获取结果失败: %v", err)
	}

	fmt.Println("\n========== 扫描结果 ==========")
	fmt.Printf("%-5s %-20s %-15s %-10s %s\n", "ID", "漏洞名称", "工具", "严重程度", "描述")
	fmt.Println("-------------------------------------------------------------")
	for _, r := range results {
		desc := r.Description
		if len(desc) > 30 {
			desc = desc[:30] + "..."
		}
		fmt.Printf("%-5d %-20s %-15s %-10s %s\n", r.ID, r.Name, r.ToolName, r.Severity, desc)
	}
}

func showHelp() {
	fmt.Println(`
QingScan CLI - 扫描工作流工具

用法:
  -list              列出所有可用工具
  -init              初始化工具列表到数据库
  -t <target>       扫描目标URL
  -s <tool>         扫描工具名称
  -p <params>       工具参数 (JSON格式)
  -analyze          执行LLM分析
  -tasks            查看任务列表
  -results          查看扫描结果

示例:
  # 初始化工具
  ./new_cli -init

  # 列出工具
  ./new_cli -list

  # 执行扫描
  ./new_cli -t "http://example.com/test.php?id=1" -s sqlmap

  # 执行扫描并指定参数
  ./new_cli -t "http://example.com/test.php?id=1" -s sqlmap -p '{"level":5,"risk":2}'

  # 查看结果
  ./new_cli -results
`)
}

// ========== 完整工作流 ==========

func runFullWorkflow(ws *workflow.WorkflowService) {
	fmt.Println("\n========== 开始扫描工作流 ==========")

	// Step 1: 添加目标
	fmt.Println("\n[Step 1] 添加目标...")
	target := &model.ScanTarget{
		Name:   "扫描目标-" + os.Args[2],
		URL:    *flagTarget,
		Type:   "web",
		Status: 1,
		UserID: 1,
	}
	if err := ws.CreateTarget(target); err != nil {
		log.Fatalf("添加目标失败: %v", err)
	}
	fmt.Printf("  目标ID: %d, URL: %s\n", target.ID, target.URL)

	// Step 2: 获取工具
	fmt.Printf("\n[Step 2] 获取工具: %s\n", *flagTool)
	tool, err := ws.GetToolByName(*flagTool)
	if err != nil {
		log.Fatalf("获取工具失败: %v", err)
	}
	fmt.Printf("  工具: %s (%s) - %s\n", tool.DisplayName, tool.Name, tool.Description)

	// Step 3: 创建扫描任务
	fmt.Println("\n[Step 3] 创建扫描任务...")
	params := *flagParams
	if params == "" {
		params = tool.Params
	}
	task := &model.ScanTask{
		TargetID: target.ID,
		ToolID:   tool.ID,
		ToolName: tool.Name,
		Status:   0,
		Params:   params,
		UserID:   1,
	}
	if err := ws.CreateTask(task); err != nil {
		log.Fatalf("创建任务失败: %v", err)
	}
	fmt.Printf("  任务ID: %d\n", task.ID)

	// Step 4: 执行扫描
	fmt.Println("\n[Step 4] 执行扫描...")
	if err := ws.RunScan(task.ID); err != nil {
		log.Printf("扫描执行完成 (可能有警告): %v", err)
	}

	// 显示扫描结果
	var results []model.ScanResult
	ws.GetDB().Where("task_id = ?", task.ID).Find(&results)
	fmt.Printf("  发现 %d 个结果\n", len(results))
	for _, r := range results {
		fmt.Printf("    - %s [%s]: %s\n", r.Name, r.Severity, r.Description)
	}

	// Step 5: LLM分析
	fmt.Println("\n[Step 5] LLM分析...")
	analysis, err := ws.AnalyzeResults(target.ID, task.ID, tool.Name)
	if err != nil {
		log.Printf("LLM分析失败: %v", err)
	} else {
		fmt.Printf("  风险等级: %s\n", analysis.RiskLevel)
		fmt.Printf("  分析结果: %s\n", analysis.Analysis)
		fmt.Printf("  修复建议: %s\n", analysis.Recommendations)
	}

	fmt.Println("\n========== 工作流完成 ==========")
	fmt.Printf("目标: %s\n", target.URL)
	fmt.Printf("工具: %s\n", tool.Name)
	fmt.Printf("任务ID: %d\n", task.ID)

	if analysis != nil {
		fmt.Printf("分析ID: %d\n", analysis.ID)
	}
}

// 执行LLM分析
func runAnalysis(ws *workflow.WorkflowService) {
	// 获取最新的目标
	var target model.ScanTarget
	if err := ws.GetDB().Order("id DESC").First(&target).Error; err != nil {
		log.Fatalf("获取目标失败: %v", err)
	}

	fmt.Printf("\n========== LLM分析目标: %s ==========\n", target.URL)

	analysis, err := ws.AnalyzeResults(target.ID, 0, "")
	if err != nil {
		log.Fatalf("LLM分析失败: %v", err)
	}

	fmt.Printf("\n分析结果:\n")
	fmt.Printf("  风险等级: %s\n", analysis.RiskLevel)
	fmt.Printf("  漏洞数量: %d\n", analysis.VulnCount)
	fmt.Printf("  总结: %s\n", analysis.Summary)
	fmt.Printf("  分析: %s\n", analysis.Analysis)
	fmt.Printf("  建议: %s\n", analysis.Recommendations)
}
