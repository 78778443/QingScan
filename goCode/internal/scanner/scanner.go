package scanner

import (
	"encoding/json"
	"fmt"
	"log"
	"os"
	"os/exec"
	"strings"
	"sync"
	"time"

	"qingscan/internal/model"
)

// Scanner 扫描器接口
type Scanner interface {
	Name() string
	Version() string
	Check() error
	Run(target string, options map[string]interface{}) (*ScanResult, error)
}

// ScanResult 扫描结果
type ScanResult struct {
	Scanner   string      `json:"scanner"`
	Target    string      `json:"target"`
	Success   bool        `json:"success"`
	Results   interface{} `json:"results"`
	Error     string      `json:"error,omitempty"`
	Duration  int64       `json:"duration"`
	Timestamp time.Time   `json:"timestamp"`
}

// BaseScanner 扫描器基类
type BaseScanner struct {
	Name_    string
	Version_ string
	Path     string
}

func (b *BaseScanner) Name() string {
	return b.Name_
}

func (b *BaseScanner) Version() string {
	return b.Version_
}

func (b *BaseScanner) Check() error {
	if b.Path == "" {
		return fmt.Errorf("%s path not configured", b.Name_)
	}
	if _, err := os.Stat(b.Path); os.IsNotExist(err) {
		return fmt.Errorf("%s not found at %s", b.Name_, b.Path)
	}
	return nil
}

// NmapScanner Nmap扫描器
type NmapScanner struct {
	BaseScanner
}

func NewNmapScanner(path string) *NmapScanner {
	return &NmapScanner{
		BaseScanner: BaseScanner{
			Name_:    "nmap",
			Version_: "unknown",
			Path:     path,
		},
	}
}

func (s *NmapScanner) Check() error {
	if err := s.BaseScanner.Check(); err != nil {
		return err
	}
	cmd := exec.Command(s.Path, "-V")
	output, err := cmd.Output()
	if err != nil {
		return err
	}
	// 解析版本
	lines := strings.Split(string(output), "\n")
	if len(lines) > 0 {
		s.Version_ = strings.TrimSpace(lines[0])
	}
	return nil
}

func (s *NmapScanner) Run(target string, options map[string]interface{}) (*ScanResult, error) {
	start := time.Now()
	result := &ScanResult{
		Scanner:   s.Name(),
		Target:    target,
		Timestamp: start,
	}

	// 构建命令参数
	args := []string{}

	// 端口扫描
	if ports, ok := options["ports"].(string); ok && ports != "" {
		args = append(args, "-p", ports)
	} else {
		args = append(args, "-p", "1-10000") // 默认扫描常用端口
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

	// 快速扫描
	if fast, ok := options["fast"].(bool); ok && fast {
		args = append(args, "-F")
	}

	// 目标
	args = append(args, target)

	cmd := exec.Command(s.Path, args...)
	log.Printf("Running nmap: %s %s", s.Path, strings.Join(args, " "))

	output, err := cmd.CombinedOutput()
	result.Duration = time.Since(start).Milliseconds()

	if err != nil {
		result.Success = false
		result.Error = err.Error()
	} else {
		result.Success = true
		result.Results = string(output)
	}

	return result, nil
}

// NucleiScanner Nuclei扫描器
type NucleiScanner struct {
	BaseScanner
}

func NewNucleiScanner(path string) *NucleiScanner {
	return &NucleiScanner{
		BaseScanner: BaseScanner{
			Name_:    "nuclei",
			Version_: "unknown",
			Path:     path,
		},
	}
}

func (s *NucleiScanner) Check() error {
	if err := s.BaseScanner.Check(); err != nil {
		return err
	}
	cmd := exec.Command(s.Path, "-version")
	output, err := cmd.Output()
	if err != nil {
		return err
	}
	lines := strings.Split(string(output), "\n")
	if len(lines) > 0 {
		s.Version_ = strings.TrimSpace(lines[0])
	}
	return nil
}

func (s *NucleiScanner) Run(target string, options map[string]interface{}) (*ScanResult, error) {
	start := time.Now()
	result := &ScanResult{
		Scanner:   s.Name(),
		Target:    target,
		Timestamp: start,
	}

	args := []string{}

	// 严重程度过滤
	if severity, ok := options["severity"].(string); ok && severity != "" {
		args = append(args, "-severity", severity)
	} else {
		args = append(args, "-severity", "critical,high,medium,low,info")
	}

	// 模板目录
	if templates, ok := options["templates"].(string); ok && templates != "" {
		args = append(args, "-templates", templates)
	}

	// 输出格式
	outputFile := "json"
	if of, ok := options["output_format"].(string); ok && of != "" {
		outputFile = of
	}

	// JSON输出
	if outputFile == "json" {
		args = append(args, "-json")
	}

	// 并发数
	if concurrency, ok := options["concurrency"].(int); ok && concurrency > 0 {
		args = append(args, "-c", fmt.Sprintf("%d", concurrency))
	}

	// 目标
	args = append(args, "-u", target)

	cmd := exec.Command(s.Path, args...)
	log.Printf("Running nuclei: %s %s", s.Path, strings.Join(args, " "))

	output, err := cmd.CombinedOutput()
	result.Duration = time.Since(start).Milliseconds()

	if err != nil {
		// Nuclei 可能返回非0但仍有结果
		result.Error = err.Error()
	}

	result.Success = true
	result.Results = string(output)

	return result, nil
}

// XrayScanner Xray扫描器
type XrayScanner struct {
	BaseScanner
}

func NewXrayScanner(path string) *XrayScanner {
	return &XrayScanner{
		BaseScanner: BaseScanner{
			Name_:    "xray",
			Version_: "unknown",
			Path:     path,
		},
	}
}

func (s *XrayScanner) Check() error {
	if err := s.BaseScanner.Check(); err != nil {
		return err
	}
	cmd := exec.Command(s.Path, "--version")
	output, err := cmd.Output()
	if err != nil {
		return err
	}
	s.Version_ = strings.TrimSpace(string(output))
	return nil
}

func (s *XrayScanner) Run(target string, options map[string]interface{}) (*ScanResult, error) {
	start := time.Now()
	result := &ScanResult{
		Scanner:   s.Name(),
		Target:    target,
		Timestamp: start,
	}

	// Xray 使用 ws 进行被动扫描或使用命令行进行主动扫描
	args := []string{}

	if mode, ok := options["mode"].(string); ok && mode == "webscan" {
		args = append(args, "webscan")
		args = append(args, "--url", target)
	}

	// 输出
	if outputFile, ok := options["output"].(string); ok && outputFile != "" {
		args = append(args, "--json-output", outputFile)
	}

	cmd := exec.Command(s.Path, args...)
	log.Printf("Running xray: %s %s", s.Path, strings.Join(args, " "))

	output, err := cmd.CombinedOutput()
	result.Duration = time.Since(start).Milliseconds()

	if err != nil {
		result.Success = false
		result.Error = err.Error()
	} else {
		result.Success = true
		result.Results = string(output)
	}

	return result, nil
}

// DirMapScanner 目录扫描器
type DirMapScanner struct {
	BaseScanner
}

func NewDirMapScanner(path string) *DirMapScanner {
	return &DirMapScanner{
		BaseScanner: BaseScanner{
			Name_:    "dirmap",
			Version_: "unknown",
			Path:     path,
		},
	}
}

func (s *DirMapScanner) Check() error {
	return s.BaseScanner.Check()
}

func (s *DirMapScanner) Run(target string, options map[string]interface{}) (*ScanResult, error) {
	start := time.Now()
	result := &ScanResult{
		Scanner:   s.Name(),
		Target:    target,
		Timestamp: start,
	}

	args := []string{}

	// 目标
	args = append(args, "-u", target)

	// 线程数
	if threads, ok := options["threads"].(int); ok && threads > 0 {
		args = append(args, "-t", fmt.Sprintf("%d", threads))
	}

	cmd := exec.Command(s.Path, args...)
	log.Printf("Running dirmap: %s %s", s.Path, strings.Join(args, " "))

	output, err := cmd.CombinedOutput()
	result.Duration = time.Since(start).Milliseconds()

	if err != nil {
		result.Success = false
		result.Error = err.Error()
	} else {
		result.Success = true
		result.Results = string(output)
	}

	return result, nil
}

// ScanManager 扫描管理器
type ScanManager struct {
	Scanners map[string]Scanner
	mu       sync.RWMutex
}

var manager *ScanManager

func GetScanManager() *ScanManager {
	if manager == nil {
		manager = &ScanManager{
			Scanners: make(map[string]Scanner),
		}
	}
	return manager
}

func (m *ScanManager) Register(scanner Scanner) {
	m.mu.Lock()
	defer m.mu.Unlock()
	m.Scanners[scanner.Name()] = scanner
	log.Printf("Registered scanner: %s", scanner.Name())
}

func (m *ScanManager) Get(name string) Scanner {
	m.mu.RLock()
	defer m.mu.RUnlock()
	return m.Scanners[name]
}

func (m *ScanManager) List() []string {
	m.mu.RLock()
	defer m.mu.RUnlock()
	names := []string{}
	for name := range m.Scanners {
		names = append(names, name)
	}
	return names
}

// CheckAllScanners 检查所有已注册扫描器
func (m *ScanManager) CheckAllScanners() map[string]error {
	results := make(map[string]error)
	m.mu.RLock()
	defer m.mu.RUnlock()

	for name, scanner := range m.Scanners {
		if err := scanner.Check(); err != nil {
			results[name] = err
		} else {
			results[name] = nil
		}
	}
	return results
}

// RunScan 运行扫描
func (m *ScanManager) RunScan(scannerName, target string, options map[string]interface{}) (*ScanResult, error) {
	scanner := m.Get(scannerName)
	if scanner == nil {
		return nil, fmt.Errorf("scanner not found: %s", scannerName)
	}

	if err := scanner.Check(); err != nil {
		return nil, fmt.Errorf("scanner check failed: %w", err)
	}

	return scanner.Run(target, options)
}

// ScannerResultHandler 扫描结果处理器
type ScannerResultHandler func(result *ScanResult) error

// AsyncRunScan 异步运行扫描
func (m *ScanManager) AsyncRunScan(scannerName, target string, options map[string]interface{}, handler ScannerResultHandler) {
	go func() {
		result, err := m.RunScan(scannerName, target, options)
		if err != nil {
			log.Printf("Scan error: %v", err)
			return
		}
		if handler != nil {
			if err := handler(result); err != nil {
				log.Printf("Result handler error: %v", err)
			}
		}
	}()
}

// ParseNmapXMLResult 解析Nmap XML结果
func ParseNmapXMLResult(xmlData string) ([]model.Port, error) {
	// 简化实现，实际应该使用XML解析库
	ports := []model.Port{}
	return ports, nil
}

// ParseNucleiJSONResult 解析Nuclei JSON结果
func ParseNucleiJSONResult(jsonData string) ([]model.Vulnerability, error) {
	var results []model.Vulnerability

	decoder := json.NewDecoder(strings.NewReader(jsonData))
	for {
		var result struct {
			Info struct {
				Name        string `json:"name"`
				Severity    string `json:"severity"`
				Description string `json:"description"`
			} `json:"info"`
			MatchedAt string `json:"matched-at"`
		}
		if err := decoder.Decode(&result); err != nil {
			break
		}

		if result.Info.Name != "" {
			vuln := model.Vulnerability{
				Name:        result.Info.Name,
				Target:      result.MatchedAt,
				Type:        "nuclei",
				Severity:    result.Info.Severity,
				Description: result.Info.Description,
				Status:      0,
				Tool:        "nuclei",
			}
			results = append(results, vuln)
		}
	}

	return results, nil
}

// SaveScanResultToDB 保存扫描结果到数据库
func SaveScanResultToDB(result *ScanResult, db interface{}) error {
	// 实现数据库保存逻辑
	log.Printf("Saving scan result: scanner=%s, target=%s, success=%v",
		result.Scanner, result.Target, result.Success)
	return nil
}
