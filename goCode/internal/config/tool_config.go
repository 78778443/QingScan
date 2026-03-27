package config

import (
	"fmt"
	"os"
	"path/filepath"
	"strings"

	"gopkg.in/yaml.v3"
)

// ToolConfig 工具配置
type ToolConfig struct {
	Name         string          `yaml:"name" json:"name"`
	Type         string          `yaml:"type" json:"type"` // blackbox | whitebox | asset
	Version      string          `yaml:"version" json:"version"`
	Path         string          `yaml:"path" json:"path"`

	// 运行模式：native(本地运行) | container(容器运行)
	Runtime string `yaml:"runtime" json:"runtime"`

	// 容器配置（container 模式）
	Container *ContainerConfig `yaml:"container" json:"container"`

	// 能力定义
	Capabilities []string `yaml:"capabilities" json:"capabilities"`

	// 扫描参数模板
	ScanParams map[string]ParamConfig `yaml:"scan_params" json:"scan_params"`

	// 输出解析器
	ResultParser ResultParser `yaml:"result_parser" json:"result_parser"`

	// 安装命令
	Install InstallConfig `yaml:"install" json:"install"`

	// 状态
	Status int `yaml:"-" json:"status"`
}

// ContainerConfig 容器配置
type ContainerConfig struct {
	Image     string            `yaml:"image" json:"image"`
	ImageFile string            `yaml:"image_file" json:"image_file"`
	Tag      string            `yaml:"tag" json:"tag"`
	Resources ResourceConfig    `yaml:"resources" json:"resources"`
	Volumes   []VolumeMount    `yaml:"volumes" json:"volumes"`
	Env      map[string]string `yaml:"env" json:"env"`
}

// ResourceConfig 资源限制
type ResourceConfig struct {
	Memory string `yaml:"memory" json:"memory"`
	CPU    string `yaml:"cpu" json:"cpu"`
}

// VolumeMount 卷挂载
type VolumeMount struct {
	Source string `yaml:"source" json:"source"`
	Target string `yaml:"target" json:"target"`
}

// ParamConfig 参数配置
type ParamConfig struct {
	Type    string   `yaml:"type" json:"type"` // select | number | string | boolean
	Options []string `yaml:"options" json:"options"`
	Default interface{} `yaml:"default" json:"default"`
}

// ResultParser 结果解析器配置
type ResultParser struct {
	Type     string            `yaml:"type" json:"type"` // json | xml | text
	Pattern  map[string]string `yaml:"pattern" json:"pattern"`
	Mapping  map[string]string `yaml:"mapping" json:"mapping"`
}

// InstallConfig 安装配置
type InstallConfig struct {
	Import   string `yaml:"import" json:"import"`
	CheckCmd string `yaml:"check_cmd" json:"check_cmd"`
	Cmd      string `yaml:"cmd" json:"cmd"`
}

// ToolLoader 工具加载器
type ToolLoader struct {
	configDir string
	toolsDir  string
	imagesDir string
}

// NewToolLoader 创建工具加载器
func NewToolLoader(configDir, toolsDir, imagesDir string) *ToolLoader {
	return &ToolLoader{
		configDir: configDir,
		toolsDir:  toolsDir,
		imagesDir: imagesDir,
	}
}

// LoadTools 加载所有工具配置
func (l *ToolLoader) LoadTools() ([]*ToolConfig, error) {
	var tools []*ToolConfig

	// 确保目录存在
	if err := os.MkdirAll(l.configDir, 0755); err != nil {
		return nil, fmt.Errorf("create config dir failed: %w", err)
	}

	// 扫描配置文件
	entries, err := os.ReadDir(l.configDir)
	if err != nil {
		return nil, fmt.Errorf("read config dir failed: %w", err)
	}

	for _, entry := range entries {
		if entry.IsDir() {
			continue
		}

		name := entry.Name()
		if !strings.HasSuffix(name, ".yaml") && !strings.HasSuffix(name, ".yml") {
			continue
		}

		tool, err := l.loadTool(filepath.Join(l.configDir, name))
		if err != nil {
			fmt.Printf("Load tool %s failed: %v\n", name, err)
			continue
		}

		// 设置默认运行模式
		if tool.Runtime == "" {
			tool.Runtime = "native"
		}

		tools = append(tools, tool)
	}

	return tools, nil
}

// loadTool 加载单个工具配置
func (l *ToolLoader) loadTool(path string) (*ToolConfig, error) {
	data, err := os.ReadFile(path)
	if err != nil {
		return nil, fmt.Errorf("read file failed: %w", err)
	}

	var config ToolConfig
	if err := yaml.Unmarshal(data, &config); err != nil {
		return nil, fmt.Errorf("parse yaml failed: %w", err)
	}

	// 如果是容器模式，检查镜像文件路径
	if config.Runtime == "container" && config.Container != nil {
		if config.Container.ImageFile != "" {
			// 相对路径转换为绝对路径
			if !filepath.IsAbs(config.Container.ImageFile) {
				config.Container.ImageFile = filepath.Join(l.imagesDir, config.Container.ImageFile)
			}
		}
	}

	// 检查工具是否存在
	if config.Runtime == "native" && config.Path != "" {
		if _, err := os.Stat(config.Path); err != nil {
			config.Status = 0 // 未安装
		} else {
			config.Status = 1 // 已安装
		}
	}

	return &config, nil
}

// GetToolImagePath 获取工具镜像路径
func (l *ToolLoader) GetToolImagePath(toolName string) string {
	// 查找同名的 tar 文件
	entries, err := os.ReadDir(l.imagesDir)
	if err != nil {
		return ""
	}

	for _, entry := range entries {
		name := entry.Name()
		// 匹配规则：toolname*.tar 或 toolname-*.tar
		if strings.HasPrefix(strings.ToLower(name), strings.ToLower(toolName)) &&
			strings.HasSuffix(name, ".tar") {
			return filepath.Join(l.imagesDir, name)
		}
	}

	return ""
}
