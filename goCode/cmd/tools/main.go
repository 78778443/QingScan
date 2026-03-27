package main

import (
	"flag"
	"fmt"
	"os"
	"os/exec"
	"strings"
)

// ToolConfig 工具配置
type ToolConfig struct {
	Name     string
	Install  string
	Check    string
	Type     string // binary, python, go
	URL      string
}

// 工具列表
var tools = []ToolConfig{
	{
		Name:    "nmap",
		Install: "apt-get install -y nmap",
		Check:   "nmap --version",
		Type:    "system",
	},
	{
		Name:    "sqlmap",
		Install: "git clone --depth 1 https://github.com/sqlmapproject/sqlmap.git {TOOLS_PATH}/sqlmap",
		Check:   "python3 {TOOLS_PATH}/sqlmap/sqlmap.py --version",
		Type:    "python",
	},
	{
		Name:    "nuclei",
		Install: "go install github.com/projectdiscovery/nuclei/v3@latest",
		Check:   "nuclei --version",
		Type:    "go",
	},
	{
		Name:    "xray",
		Install: "download from https://github.com/chaitin/xray/releases",
		Check:   "{TOOLS_PATH}/xray --version",
		Type:    "binary",
	},
	{
		Name:    "dirmap",
		Install: "git clone --depth 1 https://github.com/H4ckForJob/dirmap.git {TOOLS_PATH}/dirmap",
		Check:   "python3 {TOOLS_PATH}/dirmap/dirmap.py --version",
		Type:    "python",
	},
	{
		Name:    "crawlergo",
		Install: "go install github.com/9bie/sec/crawlergo@latest",
		Check:   "crawlergo --version",
		Type:    "go",
	},
	{
		Name:    "whatweb",
		Install: "apt-get install -y whatweb",
		Check:   "whatweb --version",
		Type:    "system",
	},
	{
		Name:    "rad",
		Install: "go install github.com/chaitin/rad@latest",
		Check:   "rad --version",
		Type:    "go",
	},
	{
		Name:    "hydra",
		Install: "apt-get install -y hydra",
		Check:   "hydra --version",
		Type:    "system",
	},
	{
		Name:    "semgrep",
		Install: "pip3 install semgrep",
		Check:   "semgrep --version",
		Type:    "python",
	},
	{
		Name:    "vulmap",
		Install: "git clone --depth 1 https://github.com/zhzyker/vulmap.git {TOOLS_PATH}/vulmap",
		Check:   "python3 {TOOLS_PATH}/vulmap/vulmap.py -h",
		Type:    "python",
	},
	{
		Name:    "dismap",
		Install: "git clone --depth 1 https://github.com/zhzyker/dismap.git {TOOLS_PATH}/dismap",
		Check:   "python3 {TOOLS_PATH}/dismap/dismap.py -h",
		Type:    "python",
	},
}

func main() {
	toolsPath := flag.String("p", "/opt/qingscan/tools", "工具安装目录")
	list := flag.Bool("list", false, "列出所有工具")
	install := flag.String("install", "", "安装指定工具")
	installAll := flag.Bool("all", false, "安装所有工具")
	check := flag.String("check", "", "检查指定工具")
	flag.Parse()

	// 创建工具目录
	os.MkdirAll(*toolsPath, 0755)

	// 替换路径变量
	for i := range tools {
		tools[i].Install = strings.ReplaceAll(tools[i].Install, "{TOOLS_PATH}", *toolsPath)
		tools[i].Check = strings.ReplaceAll(tools[i].Check, "{TOOLS_PATH}", *toolsPath)
	}

	if *list {
		listTools(*toolsPath)
		return
	}

	if *install != "" {
		installTool(*install, *toolsPath)
		return
	}

	if *installAll {
		installAllTools(*toolsPath)
		return
	}

	if *check != "" {
		checkTool(*check, *toolsPath)
		return
	}

	fmt.Println("用法:")
	fmt.Println("  -list              列出所有工具")
	fmt.Println("  -install <name>    安装指定工具")
	fmt.Println("  -all               安装所有工具")
	fmt.Println("  -check <name>      检查工具是否可用")
	fmt.Printf("  -p                 工具目录 (默认: %s)\n", *toolsPath)
}

func listTools(toolsPath string) {
	fmt.Println("可用的工具:")
	fmt.Println("-------------")
	for _, t := range tools {
		fmt.Printf("  %-15s [%s]\n", t.Name, t.Type)
	}
}

func installTool(name string, toolsPath string) {
	for _, t := range tools {
		if t.Name == name {
			fmt.Printf("安装 %s...\n", name)

			// 检查是否已安装
			checkCmd := strings.ReplaceAll(t.Check, "{TOOLS_PATH}", toolsPath)
			parts := strings.Fields(checkCmd)
			cmd := exec.Command(parts[0], parts[1:]...)
			if err := cmd.Run(); err == nil {
				fmt.Printf("%s 已安装\n", name)
				return
			}

			// 执行安装
			installCmd := strings.ReplaceAll(t.Install, "{TOOLS_PATH}", toolsPath)
			fmt.Printf("执行: %s\n", installCmd)

			// 分割安装命令
			var installParts []string
			if strings.Contains(installCmd, " && ") {
				installParts = strings.Split(installCmd, " && ")
			} else {
				installParts = strings.Fields(installCmd)
			}

			for _, cmdStr := range installParts {
				parts := strings.Fields(cmdStr)
				if len(parts) == 0 {
					continue
				}
				cmd := exec.Command(parts[0], parts[1:]...)
				cmd.Dir = toolsPath
				cmd.Stdout = os.Stdout
				cmd.Stderr = os.Stderr
				if err := cmd.Run(); err != nil {
					fmt.Printf("安装失败: %v\n", err)
					return
				}
			}

			fmt.Printf("%s 安装完成\n", name)
			return
		}
	}
	fmt.Printf("未找到工具: %s\n", name)
}

func installAllTools(toolsPath string) {
	for _, t := range tools {
		installTool(t.Name, toolsPath)
	}
}

func checkTool(name string, toolsPath string) {
	for _, t := range tools {
		if t.Name == name {
			checkCmd := strings.ReplaceAll(t.Check, "{TOOLS_PATH}", toolsPath)
			parts := strings.Fields(checkCmd)
			if len(parts) == 0 {
				fmt.Printf("未找到工具: %s\n", name)
				return
			}

			var cmd *exec.Cmd
			if len(parts) > 1 {
				cmd = exec.Command(parts[0], parts[1:]...)
			} else {
				cmd = exec.Command(parts[0])
			}

			output, err := cmd.CombinedOutput()
			if err != nil {
				fmt.Printf("%s: 不可用 - %v\n", name, err)
				os.Exit(1)
			}

			fmt.Printf("%s: 可用\n", name)
			fmt.Printf("版本: %s\n", strings.TrimSpace(string(output)))
			return
		}
	}
	fmt.Printf("未找到工具: %s\n", name)
}
