package scanner

import (
	"fmt"
	"log"
	"path/filepath"
	"strings"
	"time"

	"qingscan/internal/config"
	"qingscan/internal/docker"
	"qingscan/internal/model"
)

// ContainerRunner 容器运行器
type ContainerRunner struct {
	dockerClient *docker.DockerClient
	toolsPath    string
}

// NewContainerRunner 创建容器运行器
func NewContainerRunner(toolsPath string) *ContainerRunner {
	return &ContainerRunner{
		dockerClient: docker.NewDockerClient(""),
		toolsPath:    toolsPath,
	}
}

// LoadToolImages 加载工具镜像
func (r *ContainerRunner) LoadToolImages(toolConfigs []*config.ToolConfig) map[string]error {
	results := make(map[string]error)

	for _, tc := range toolConfigs {
		if tc.Runtime != "container" || tc.Container == nil {
			continue
		}

		imageName := tc.Container.Image
		if tc.Container.Tag != "" {
			imageName = fmt.Sprintf("%s:%s", tc.Container.Image, tc.Container.Tag)
		}

		// 检查镜像是否存在
		exists, err := r.dockerClient.IsImageExists(imageName)
		if err != nil {
			results[tc.Name] = err
			continue
		}

		if exists {
			log.Printf("Image %s already loaded", imageName)
			continue
		}

		// 加载镜像
		if tc.Container.ImageFile != "" {
			loaded, err := r.dockerClient.LoadImage(tc.Container.ImageFile)
			if err != nil {
				results[tc.Name] = fmt.Errorf("load image failed: %w", err)
				continue
			}
			log.Printf("Loaded image: %s", loaded)
		}
	}

	return results
}

// RunContainerScan 运行容器化扫描
func (r *ContainerRunner) RunContainerScan(toolConfig *config.ToolConfig, target string, options map[string]interface{}) (*ScanResult, error) {
	if toolConfig.Runtime != "container" {
		return nil, fmt.Errorf("not a container tool")
	}

	start := time.Now()
	result := &ScanResult{
		Scanner:   toolConfig.Name,
		Target:    target,
		Timestamp: start,
	}

	// 构建命令参数
	args := r.buildArgs(toolConfig, target, options)

	// 获取工作目录
	workDir := "/workspace"
	for _, vol := range toolConfig.Container.Volumes {
		if vol.Target == workDir {
			break
		}
	}

	// 运行容器
	output, err := r.dockerClient.RunContainer(
		toolConfig.Container,
		args,
		workDir,
		30*time.Minute,
	)

	result.Duration = time.Since(start).Milliseconds()

	if err != nil {
		result.Success = false
		result.Error = err.Error()
	} else {
		result.Success = true
		result.Results = output
	}

	return result, nil
}

// buildArgs 构建扫描参数
func (r *ContainerRunner) buildArgs(toolConfig *config.ToolConfig, target string, options map[string]interface{}) []string {
	var args []string

	switch toolConfig.Name {
	case "nuclei":
		args = r.buildNucleiArgs(target, options)
	case "xray":
		args = r.buildXrayArgs(target, options)
	case "sqlmap":
		args = r.buildSQLMapArgs(target, options)
	case "semgrep":
		args = r.buildSemgrepArgs(target, options)
	case "nmap":
		args = r.buildNmapArgs(target, options)
	default:
		// 默认直接把 target 作为参数
		args = []string{target}
	}

	return args
}

// buildNucleiArgs 构建 Nuclei 参数
func (r *ContainerRunner) buildNucleiArgs(target string, options map[string]interface{}) []string {
	args := []string{}

	// 严重程度
	if severity, ok := options["severity"].(string); ok && severity != "" {
		args = append(args, "-severity", severity)
	} else {
		args = append(args, "-severity", "critical,high,medium,low,info")
	}

	// 模板目录
	args = append(args, "-templates", "/nuclei-templates")

	// JSON 输出
	args = append(args, "-json")

	// 并发
	if concurrency, ok := options["concurrency"].(int); ok && concurrency > 0 {
		args = append(args, "-c", fmt.Sprintf("%d", concurrency))
	}

	// 目标
	args = append(args, "-u", target)

	return args
}

// buildXrayArgs 构建 Xray 参数
func (r *ContainerRunner) buildXrayArgs(target string, options map[string]interface{}) []string {
	args := []string{"webscan"}

	if mode, ok := options["mode"].(string); ok && mode != "" {
		args = append(args, "--spider-max", mode)
	} else {
		args = append(args, "--spider-max", "10")
	}

	// 目标
	args = append(args, "--url", target)

	// JSON 输出
	args = append(args, "--json-output", "/output/result.json")

	return args
}

// buildSQLMapArgs 构建 SQLMap 参数
func (r *ContainerRunner) buildSQLMapArgs(target string, options map[string]interface{}) []string {
	args := []string{"-u", target, "--batch", "--smart"}

	if level, ok := options["level"].(int); ok && level > 0 {
		args = append(args, "--level", fmt.Sprintf("%d", level))
	}

	if risk, ok := options["risk"].(int); ok && risk > 0 {
		args = append(args, "--risk", fmt.Sprintf("%d", risk))
	}

	if threads, ok := options["threads"].(int); ok && threads > 0 {
		args = append(args, "--threads", fmt.Sprintf("%d", threads))
	}

	// 输出目录
	args = append(args, "--output-dir", "/output")

	return args
}

// buildSemgrepArgs 构建 Semgrep 参数
func (r *ContainerRunner) buildSemgrepArgs(target string, options map[string]interface{}) []string {
	args := []string{}

	if mode, ok := options["mode"].(string); ok && mode != "" {
		args = append(args, "--mode", mode)
	} else {
		args = append(args, "--mode", "auto")
	}

	if rules, ok := options["rules"].(string); ok && rules != "" {
		args = append(args, "--rules", rules)
	}

	// 输出格式
	args = append(args, "--format", "json")

	// 输出文件
	args = append(args, "--output", "/output/result.json")

	// 目标
	args = append(args, target)

	return args
}

// buildNmapArgs 构建 Nmap 参数
func (r *ContainerRunner) buildNmapArgs(target string, options map[string]interface{}) []string {
	args := []string{}

	// 端口
	if ports, ok := options["ports"].(string); ok && ports != "" {
		args = append(args, "-p", ports)
	} else {
		args = append(args, "-p", "1-1000")
	}

	// 服务版本检测
	if _, ok := options["service_version"]; ok {
		args = append(args, "-sV")
	}

	// 操作系统检测
	if _, ok := options["os_detection"]; ok {
		args = append(args, "-O")
	}

	// 脚本扫描
	if _, ok := options["script"]; ok {
		args = append(args, "-sC")
	}

	// 输出格式
	if outputFile, ok := options["output"].(string); ok && outputFile != "" {
		args = append(args, "-oX", outputFile)
	}

	// 目标
	args = append(args, target)

	return args
}

// RunNativeScan 运行本地扫描
func (r *ContainerRunner) RunNativeScan(toolConfig *config.ToolConfig, target string, options map[string]interface{}) (*ScanResult, error) {
	if toolConfig.Runtime != "native" {
		return nil, fmt.Errorf("not a native tool")
	}

	// 使用现有的扫描器
	manager := GetScanManager()
	scanner := manager.Get(toolConfig.Name)
	if scanner == nil {
		return nil, fmt.Errorf("scanner %s not found", toolConfig.Name)
	}

	return scanner.Run(target, options)
}

// RunTool 运行工具（自动选择运行模式）
func (r *ContainerRunner) RunTool(toolConfig *config.ToolConfig, target string, options map[string]interface{}) (*ScanResult, error) {
	if toolConfig == nil {
		return nil, fmt.Errorf("tool config is nil")
	}

	log.Printf("Running tool %s in %s mode", toolConfig.Name, toolConfig.Runtime)

	if toolConfig.Runtime == "container" {
		return r.RunContainerScan(toolConfig, target, options)
	} else {
		return r.RunNativeScan(toolConfig, target, options)
	}
}

// GetToolsPath 获取工具路径
func (r *ContainerRunner) GetToolsPath() string {
	return r.toolsPath
}

// ResolveToolPath 解析工具路径（支持容器和本地）
func ResolveToolPath(toolName, toolsPath string, runtime string) string {
	if runtime == "container" {
		return "" // 容器模式下不需要本地路径
	}
	return filepath.Join(toolsPath, toolName)
}

// ParseResults 解析扫描结果
func ParseResults(toolName string, output string, parserType string) ([]model.Vulnerability, error) {
	var vulns []model.Vulnerability

	switch parserType {
	case "json":
		vulns = parseJSONResults(toolName, output)
	case "xml":
		vulns = parseXMLResults(toolName, output)
	case "text":
		vulns = parseTextResults(toolName, output)
	default:
		log.Printf("Unknown parser type: %s", parserType)
	}

	return vulns, nil
}

func parseJSONResults(toolName, output string) []model.Vulnerability {
	var vulns []model.Vulnerability
	// 简化实现，实际需要根据不同工具的 JSON 格式解析
	lines := strings.Split(output, "\n")
	for _, line := range lines {
		line = strings.TrimSpace(line)
		if strings.HasPrefix(line, "{") {
			// 解析 JSON
			vuln := model.Vulnerability{
				Tool: toolName,
				Type: "unknown",
			}
			vulns = append(vulns, vuln)
		}
	}
	return vulns
}

func parseXMLResults(toolName, output string) []model.Vulnerability {
	// XML 解析
	return nil
}

func parseTextResults(toolName, output string) []model.Vulnerability {
	// 文本解析
	return nil
}
