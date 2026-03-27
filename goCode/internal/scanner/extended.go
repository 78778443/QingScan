package scanner

import (
	"bufio"
	"encoding/json"
	"fmt"
	"log"
	"os"
	"os/exec"
	"path/filepath"
	"strings"
	"time"
)

// SQLMapScanner SQLMap扫描器
type SQLMapScanner struct {
	BaseScanner
}

func NewSQLMapScanner(path string) *SQLMapScanner {
	// 如果path是目录，尝试找sqlmap.py
	if info, err := os.Stat(path); err == nil && info.IsDir() {
		sqlmapPath := filepath.Join(path, "sqlmap.py")
		if _, err := os.Stat(sqlmapPath); err == nil {
			path = sqlmapPath
		}
	}
	return &SQLMapScanner{
		BaseScanner: BaseScanner{
			Name_:    "sqlmap",
			Version_: "unknown",
			Path:     path,
		},
	}
}

func (s *SQLMapScanner) Check() error {
	// 检查文件是否存在
	if _, err := os.Stat(s.Path); os.IsNotExist(err) {
		// 尝试找sqlmap.py
		dir := filepath.Dir(s.Path)
		sqlmapPath := filepath.Join(dir, "sqlmap.py")
		if _, err := os.Stat(sqlmapPath); err == nil {
			s.Path = sqlmapPath
		} else {
			return fmt.Errorf("sqlmap not found at %s", s.Path)
		}
	}

	// 使用python3运行
	cmd := exec.Command("python3", s.Path, "--version")
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

func (s *SQLMapScanner) Run(target string, options map[string]interface{}) (*ScanResult, error) {
	start := time.Now()
	result := &ScanResult{
		Scanner:   s.Name(),
		Target:    target,
		Timestamp: start,
	}

	args := []string{}

	// 目标
	args = append(args, "-u", target)

	// 级别
	if level, ok := options["level"].(int); ok && level > 0 {
		args = append(args, "--level", fmt.Sprintf("%d", level))
	} else {
		args = append(args, "--level", "2")
	}

	// 风险
	if risk, ok := options["risk"].(int); ok && risk > 0 {
		args = append(args, "--risk", fmt.Sprintf("%d", risk))
	} else {
		args = append(args, "--risk", "1")
	}

	// 线程数
	if threads, ok := options["threads"].(int); ok && threads > 0 {
		args = append(args, "--threads", fmt.Sprintf("%d", threads))
	}

	// 批量模式
	if batch, ok := options["batch"].(bool); ok && batch {
		args = append(args, "--batch")
	}

	// 智能扫描
	if smart, ok := options["smart"].(bool); ok && smart {
		args = append(args, "--smart")
	}

	// 获取数据库
	if _, ok := options["dbs"].(bool); ok {
		args = append(args, "--dbs")
	}

	// 获取表
	if _, ok := options["tables"].(bool); ok {
		args = append(args, "--tables")
	}

	// 获取列
	if _, ok := options["columns"].(bool); ok {
		args = append(args, "--columns")
	}

	// 获取数据
	if _, ok := options["dump"].(bool); ok {
		args = append(args, "--dump")
	}

	// 输出目录
	if outputDir, ok := options["output_dir"].(string); ok && outputDir != "" {
		args = append(args, "--output-dir", outputDir)
	}

	// 不询问
	args = append(args, "--batch", "--smart")

	log.Printf("Running sqlmap: python3 %s %s", s.Path, strings.Join(args, " "))

	// 使用python3运行sqlmap
	cmd := exec.Command("python3", append([]string{s.Path}, args...)...)
	output, err := cmd.CombinedOutput()
	result.Duration = time.Since(start).Milliseconds()

	if err != nil {
		// SQLMap 有时返回非0但仍有结果
		result.Error = err.Error()
	}

	result.Success = true
	result.Results = string(output)

	return result, nil
}

// CrawlergoScanner Crawlergo爬虫
type CrawlergoScanner struct {
	BaseScanner
}

func NewCrawlergoScanner(path string) *CrawlergoScanner {
	return &CrawlergoScanner{
		BaseScanner: BaseScanner{
			Name_:    "crawlergo",
			Version_: "unknown",
			Path:     path,
		},
	}
}

func (s *CrawlergoScanner) Check() error {
	if err := s.BaseScanner.Check(); err != nil {
		return err
	}
	cmd := exec.Command(s.Path, "-v")
	output, err := cmd.Output()
	if err != nil {
		return err
	}
	s.Version_ = strings.TrimSpace(string(output))
	return nil
}

func (s *CrawlergoScanner) Run(target string, options map[string]interface{}) (*ScanResult, error) {
	start := time.Now()
	result := &ScanResult{
		Scanner:   s.Name(),
		Target:    target,
		Timestamp: start,
	}

	args := []string{}

	// 目标
	args = append(args, target)

	// 线程数
	if threads, ok := options["threads"].(int); ok && threads > 0 {
		args = append(args, "-t", fmt.Sprintf("%d", threads))
	} else {
		args = append(args, "-t", "4")
	}

	// 超时
	if timeout, ok := options["timeout"].(int); ok && timeout > 0 {
		args = append(args, "-timeout", fmt.Sprintf("%d", timeout))
	}

	// 最大抓取数
	if maxCount, ok := options["max_count"].(int); ok && maxCount > 0 {
		args = append(args, "-max-count", fmt.Sprintf("%d", maxCount))
	}

	// 过滤规则
	if filter, ok := options["filter"].(string); ok && filter != "" {
		args = append(args, "-filter", filter)
	}

	// 输出模式
	if outputMode, ok := options["output_mode"].(string); ok && outputMode != "" {
		args = append(args, "-output-mode", outputMode)
	}

	cmd := exec.Command(s.Path, args...)
	log.Printf("Running crawlergo: %s %s", s.Path, strings.Join(args, " "))

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

// WhatWebScanner WhatWeb指纹识别
type WhatWebScanner struct {
	BaseScanner
}

func NewWhatWebScanner(path string) *WhatWebScanner {
	return &WhatWebScanner{
		BaseScanner: BaseScanner{
			Name_:    "whatweb",
			Version_: "unknown",
			Path:     path,
		},
	}
}

func (s *WhatWebScanner) Check() error {
	if err := s.BaseScanner.Check(); err != nil {
		return err
	}
	cmd := exec.Command(s.Path, "--version")
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

func (s *WhatWebScanner) Run(target string, options map[string]interface{}) (*ScanResult, error) {
	start := time.Now()
	result := &ScanResult{
		Scanner:   s.Name(),
		Target:    target,
		Timestamp: start,
	}

	args := []string{}

	// 目标
	args = append(args, target)

	//  агрессивность
	if aggression, ok := options["aggression"].(int); ok && aggression > 0 {
		args = append(args, "-a", fmt.Sprintf("%d", aggression))
	}

	// 并发
	if concurrency, ok := options["concurrency"].(int); ok && concurrency > 0 {
		args = append(args, "-- concurrency", fmt.Sprintf("%d", concurrency))
	}

	// 快速扫描
	if quick, ok := options["quick"].(bool); ok && quick {
		args = append(args, "--quick")
	}

	// 输出格式
	if format, ok := options["format"].(string); ok && format != "" {
		args = append(args, "--log-xml", format)
	}

	cmd := exec.Command(s.Path, args...)
	log.Printf("Running whatweb: %s %s", s.Path, strings.Join(args, " "))

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

// RadScanner Rad浏览器爬虫
type RadScanner struct {
	BaseScanner
}

func NewRadScanner(path string) *RadScanner {
	return &RadScanner{
		BaseScanner: BaseScanner{
			Name_:    "rad",
			Version_: "unknown",
			Path:     path,
		},
	}
}

func (s *RadScanner) Check() error {
	return s.BaseScanner.Check()
}

func (s *RadScanner) Run(target string, options map[string]interface{}) (*ScanResult, error) {
	start := time.Now()
	result := &ScanResult{
		Scanner:   s.Name(),
		Target:    target,
		Timestamp: start,
	}

	args := []string{}

	// 目标
	args = append(args, "-target", target)

	// 隐藏域名
	if hideDomain, ok := options["hide_domain"].(string); ok && hideDomain != "" {
		args = append(args, "-hide-domain", hideDomain)
	}

	// 隐藏文本
	if hideText, ok := options["hide_text"].(string); ok && hideText != "" {
		args = append(args, "-hide-text", hideText)
	}

	// 输出文件
	if outputFile, ok := options["output"].(string); ok && outputFile != "" {
		args = append(args, "-output", outputFile)
	}

	// JSON输出
	args = append(args, "-json")

	cmd := exec.Command(s.Path, args...)
	log.Printf("Running rad: %s %s", s.Path, strings.Join(args, " "))

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

// HydraScanner Hydra暴力破解
type HydraScanner struct {
	BaseScanner
}

func NewHydraScanner(path string) *HydraScanner {
	return &HydraScanner{
		BaseScanner: BaseScanner{
			Name_:    "hydra",
			Version_: "unknown",
			Path:     path,
		},
	}
}

func (s *HydraScanner) Check() error {
	if err := s.BaseScanner.Check(); err != nil {
		return err
	}
	cmd := exec.Command(s.Path, "-V")
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

func (s *HydraScanner) Run(target string, options map[string]interface{}) (*ScanResult, error) {
	start := time.Now()
	result := &ScanResult{
		Scanner:   s.Name(),
		Target:    target,
		Timestamp: start,
	}

	args := []string{}

	// 目标格式: service://target
	parts := strings.Split(target, "://")
	if len(parts) == 2 {
		args = append(args, "-s") // 指定端口
		if port, ok := options["port"].(string); ok {
			args = append(args, port)
		}
		args = append(args, parts[1])
		args = append(args, parts[0])
	} else {
		args = append(args, target)
	}

	// 用户名
	if user, ok := options["user"].(string); ok && user != "" {
		args = append(args, "-L", user)
	}

	// 用户名文件
	if userFile, ok := options["user_file"].(string); ok && userFile != "" {
		args = append(args, "-L", userFile)
	}

	// 密码
	if password, ok := options["password"].(string); ok && password != "" {
		args = append(args, "-P", password)
	}

	// 密码文件
	if passFile, ok := options["pass_file"].(string); ok && passFile != "" {
		args = append(args, "-P", passFile)
	}

	// 服务
	if service, ok := options["service"].(string); ok && service != "" {
		args = append(args, service)
	}

	// 并发
	if concurrency, ok := options["concurrency"].(int); ok && concurrency > 0 {
		args = append(args, "-t", fmt.Sprintf("%d", concurrency))
	}

	// 详细输出
	args = append(args, "-V")

	// 退出第一个结果
	args = append(args, "-f")

	cmd := exec.Command(s.Path, args...)
	log.Printf("Running hydra: %s %s", s.Path, strings.Join(args, " "))

	output, err := cmd.CombinedOutput()
	result.Duration = time.Since(start).Milliseconds()

	if err != nil {
		result.Error = err.Error()
	}

	result.Success = true
	result.Results = string(output)

	return result, nil
}

// SemgrepScanner Semgrep代码扫描
type SemgrepScanner struct {
	BaseScanner
}

func NewSemgrepScanner(path string) *SemgrepScanner {
	return &SemgrepScanner{
		BaseScanner: BaseScanner{
			Name_:    "semgrep",
			Version_: "unknown",
			Path:     path,
		},
	}
}

func (s *SemgrepScanner) Check() error {
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

func (s *SemgrepScanner) Run(target string, options map[string]interface{}) (*ScanResult, error) {
	start := time.Now()
	result := &ScanResult{
		Scanner:   s.Name(),
		Target:    target,
		Timestamp: start,
	}

	args := []string{}

	// 扫描模式
	if mode, ok := options["mode"].(string); ok && mode != "" {
		args = append(args, "--mode", mode)
	} else {
		args = append(args, "--mode", "auto")
	}

	// 规则
	if rules, ok := options["rules"].(string); ok && rules != "" {
		args = append(args, "--rules", rules)
	}

	// 输出格式
	if format, ok := options["format"].(string); ok && format != "" {
		args = append(args, "--format", format)
	} else {
		args = append(args, "--format", "json")
	}

	// 输出文件
	if outputFile, ok := options["output"].(string); ok && outputFile != "" {
		args = append(args, "--output", outputFile)
	}

	// 目标
	args = append(args, target)

	cmd := exec.Command(s.Path, args...)
	log.Printf("Running semgrep: %s %s", s.Path, strings.Join(args, " "))

	output, err := cmd.CombinedOutput()
	result.Duration = time.Since(start).Milliseconds()

	if err != nil {
		result.Error = err.Error()
	}

	result.Success = true
	result.Results = string(output)

	return result, nil
}

// VulMapScanner VulMap扫描器
type VulMapScanner struct {
	BaseScanner
}

func NewVulMapScanner(path string) *VulMapScanner {
	return &VulMapScanner{
		BaseScanner: BaseScanner{
			Name_:    "vulmap",
			Version_: "unknown",
			Path:     path,
		},
	}
}

func (s *VulMapScanner) Check() error {
	return s.BaseScanner.Check()
}

func (s *VulMapScanner) Run(target string, options map[string]interface{}) (*ScanResult, error) {
	start := time.Now()
	result := &ScanResult{
		Scanner:   s.Name(),
		Target:    target,
		Timestamp: start,
	}

	args := []string{}

	// 目标
	args = append(args, "-u", target)

	// 线程
	if threads, ok := options["threads"].(int); ok && threads > 0 {
		args = append(args, "-t", fmt.Sprintf("%d", threads))
	}

	// POC类型
	if pocs, ok := options["pocs"].(string); ok && pocs != "" {
		args = append(args, "-poc", pocs)
	}

	// 输出
	if outputFile, ok := options["output"].(string); ok && outputFile != "" {
		args = append(args, "-o", outputFile)
	}

	cmd := exec.Command(s.Path, args...)
	log.Printf("Running vulmap: %s %s", s.Path, strings.Join(args, " "))

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

// DisMapScanner DisMap扫描器
type DisMapScanner struct {
	BaseScanner
}

func NewDisMapScanner(path string) *DisMapScanner {
	return &DisMapScanner{
		BaseScanner: BaseScanner{
			Name_:    "dismap",
			Version_: "unknown",
			Path:     path,
		},
	}
}

func (s *DisMapScanner) Check() error {
	return s.BaseScanner.Check()
}

func (s *DisMapScanner) Run(target string, options map[string]interface{}) (*ScanResult, error) {
	start := time.Now()
	result := &ScanResult{
		Scanner:   s.Name(),
		Target:    target,
		Timestamp: start,
	}

	args := []string{}

	// 目标
	args = append(args, "-u", target)

	// 线程
	if threads, ok := options["threads"].(int); ok && threads > 0 {
		args = append(args, "-t", fmt.Sprintf("%d", threads))
	}

	cmd := exec.Command(s.Path, args...)
	log.Printf("Running dismap: %s %s", s.Path, strings.Join(args, " "))

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

// ParseSQLMapJSONResult 解析SQLMap JSON结果
func ParseSQLMapJSONResult(jsonData string) ([]map[string]interface{}, error) {
	var results []map[string]interface{}

	scanner := bufio.NewScanner(strings.NewReader(jsonData))
	for scanner.Scan() {
		line := scanner.Text()
		if strings.HasPrefix(line, "[") || strings.HasPrefix(line, "{") {
			var data map[string]interface{}
			if err := json.Unmarshal([]byte(line), &data); err == nil {
				results = append(results, data)
			}
		}
	}

	return results, nil
}

// ParseCrawlergoResult 解析Crawlergo结果
func ParseCrawlergoResult(output string) ([]string, error) {
	// 简单解析，提取URL
	urls := []string{}
	scanner := bufio.NewScanner(strings.NewReader(output))
	for scanner.Scan() {
		line := scanner.Text()
		if strings.HasPrefix(line, "http") {
			urls = append(urls, line)
		}
	}
	return urls, nil
}

// RegisterAllScanners 注册所有扫描器
func RegisterAllScanners(toolsPath string) {
	m := GetScanManager()

	// 注册已实现的扫描器
	m.Register(NewNmapScanner(toolsPath + "/nmap"))
	m.Register(NewNucleiScanner(toolsPath + "/nuclei"))
	m.Register(NewXrayScanner(toolsPath + "/xray"))
	m.Register(NewDirMapScanner(toolsPath + "/dirmap"))
	m.Register(NewSQLMapScanner(toolsPath + "/sqlmap"))
	m.Register(NewCrawlergoScanner(toolsPath + "/crawlergo"))
	m.Register(NewWhatWebScanner(toolsPath + "/whatweb"))
	m.Register(NewRadScanner(toolsPath + "/rad"))
	m.Register(NewHydraScanner(toolsPath + "/hydra"))
	m.Register(NewSemgrepScanner(toolsPath + "/semgrep"))
	m.Register(NewVulMapScanner(toolsPath + "/vulmap"))
	m.Register(NewDisMapScanner(toolsPath + "/dismap"))
}
