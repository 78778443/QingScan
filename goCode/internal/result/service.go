package result

import (
	"encoding/json"
	"fmt"
	"log"
	"strings"

	"gorm.io/gorm"

	"qingscan/internal/model"
)

// ResultType 结果类型
type ResultType string

const (
	ResultTypeVulnerability ResultType = "vulnerability"
	ResultTypePort         ResultType = "port"
	ResultTypeDomain       ResultType = "domain"
	ResultTypeURL          ResultType = "url"
)

// ResultParser 结果解析器接口
type ResultParser interface {
	// Parse 解析扫描结果
	Parse(toolName string, output string) ([]ParsedResult, error)
}

// ParsedResult 解析后的结果
type ParsedResult struct {
	Type       ResultType
	Name       string
	Target     string
	Severity   string
	Status     int
	Tool       string
	Poc        string
	TypeStr    string
	Port       string
	Protocol   string
	State      string
	Service    string
	Version    string
	Request    string
	Response   string
	Description string
}

// ResultSaver 结果存储接口
type ResultSaver interface {
	// SaveVulnerability 保存漏洞结果
	SaveVulnerability(taskID uint, vuln *ParsedResult) error
	// SavePort 保存端口结果
	SavePort(taskID uint, host string, port *ParsedResult) error
}

// ResultService 结果服务
type ResultService struct {
	db *gorm.DB
}

// NewResultService 创建结果服务
func NewResultService(db *gorm.DB) *ResultService {
	return &ResultService{
		db: db,
	}
}

// ParseAndSave 解析并保存扫描结果
func (s *ResultService) ParseAndSave(toolName, target, output string) (int, error) {
	parser := NewToolResultParser()
	results, err := parser.Parse(toolName, output)
	if err != nil {
		return 0, fmt.Errorf("parse result failed: %w", err)
	}

	savedCount := 0
	for _, result := range results {
		switch result.Type {
		case ResultTypeVulnerability:
			if err := s.SaveVulnerability(0, &result); err != nil {
				log.Printf("Save vulnerability failed: %v", err)
			} else {
				savedCount++
			}
		case ResultTypePort:
			if err := s.SavePort(0, target, &result); err != nil {
				log.Printf("Save port failed: %v", err)
			} else {
				savedCount++
			}
		}
	}

	return savedCount, nil
}

// SaveVulnerability 保存漏洞结果到数据库
func (s *ResultService) SaveVulnerability(taskID uint, result *ParsedResult) error {
	vuln := model.Vulnerability{
		Name:        result.Name,
		Target:      result.Target,
		Type:        result.TypeStr,
		Severity:    result.Severity,
		Status:      result.Status,
		Tool:        result.Tool,
		Poc:         result.Poc,
		Description: result.Description,
		Request:     result.Request,
		Response:    result.Response,
	}

	// 检查是否已存在
	var existing model.Vulnerability
	query := s.db.Where("name = ? AND target = ? AND tool = ?",
		result.Name, result.Target, result.Tool)
	if err := query.First(&existing).Error; err == nil {
		// 已存在，跳过
		log.Printf("Vulnerability already exists: %s @ %s", result.Name, result.Target)
		return nil
	}

	if err := s.db.Create(&vuln).Error; err != nil {
		return fmt.Errorf("create vulnerability failed: %w", err)
	}

	log.Printf("Saved vulnerability: %s @ %s", result.Name, result.Target)
	return nil
}

// SavePort 保存端口结果到数据库
func (s *ResultService) SavePort(taskID uint, host string, result *ParsedResult) error {
	// 查找或创建主机记录
	var h model.Host
	query := s.db.Where("ip = ?", host)
	if err := query.First(&h).Error; err != nil {
		h = model.Host{
			IP:     host,
			Status: 1,
		}
		if err := s.db.Create(&h).Error; err != nil {
			return fmt.Errorf("create host failed: %w", err)
		}
	}

	// 检查是否已存在
	var existing model.Port
	query = s.db.Where("host = ? AND port = ? AND protocol = ?",
		host, result.Port, result.Protocol)
	if err := query.First(&existing).Error; err == nil {
		// 已存在，跳过
		return nil
	}

	port := model.Port{
		HostID:   h.ID,
		Host:     host,
		Port:     result.Port,
		Protocol: result.Protocol,
		State:    result.State,
		Service:  result.Service,
		Version:  result.Version,
	}

	if err := s.db.Create(&port).Error; err != nil {
		return fmt.Errorf("create port failed: %w", err)
	}

	// 更新主机的端口数量
	s.db.Model(&h).Update("port_count", gorm.Expr("port_count + ?", 1))

	log.Printf("Saved port: %s:%s/%s", host, result.Port, result.Protocol)
	return nil
}

// ToolResultParser 工具结果解析器
type ToolResultParser struct{}

// NewToolResultParser 创建结果解析器
func NewToolResultParser() *ToolResultParser {
	return &ToolResultParser{}
}

// Parse 解析扫描结果
func (p *ToolResultParser) Parse(toolName string, output string) ([]ParsedResult, error) {
	switch strings.ToLower(toolName) {
	case "nuclei":
		return p.parseNuclei(output)
	case "xray":
		return p.parseXray(output)
	case "nmap":
		return p.parseNmap(output)
	case "sqlmap":
		return p.parseSQLMap(output)
	default:
		return p.parseGeneric(output, toolName)
	}
}

// parseNuclei 解析 Nuclei JSON 结果
func (p *ToolResultParser) parseNuclei(output string) ([]ParsedResult, error) {
	var results []ParsedResult

	lines := strings.Split(output, "\n")
	for _, line := range lines {
		line = strings.TrimSpace(line)
		if line == "" || (!strings.HasPrefix(line, "{") && !strings.HasPrefix(line, "[")) {
			continue
		}

		var vulnData struct {
			Info struct {
				Name        string `json:"name"`
				Severity    string `json:"severity"`
				Description string `json:"description"`
			} `json:"info"`
			MatchedAt string `json:"matched-at"`
			Extractor string `json:"extractor"`
			Template  string `json:"template"`
			Type      string `json:"type"`
		}

		if err := json.Unmarshal([]byte(line), &vulnData); err != nil {
			continue
		}

		if vulnData.Info.Name == "" {
			continue
		}

		results = append(results, ParsedResult{
			Type:        ResultTypeVulnerability,
			Name:        vulnData.Info.Name,
			Target:      vulnData.MatchedAt,
			Severity:    vulnData.Info.Severity,
			Status:      0,
			Tool:        "nuclei",
			Poc:         vulnData.Template,
			TypeStr:     vulnData.Type,
			Description: vulnData.Info.Description,
		})
	}

	return results, nil
}

// parseXray 解析 Xray JSON 结果
func (p *ToolResultParser) parseXray(output string) ([]ParsedResult, error) {
	var results []ParsedResult

	// 尝试解析 JSON
	var data interface{}
	if err := json.Unmarshal([]byte(output), &data); err != nil {
		return results, nil
	}

	// Xray 输出格式解析
	// 这里简化处理，实际需要根据 Xray 的实际输出格式
	return results, nil
}

// parseNmap 解析 Nmap 文本结果
func (p *ToolResultParser) parseNmap(output string) ([]ParsedResult, error) {
	var results []ParsedResult

	lines := strings.Split(output, "\n")
	for _, line := range lines {
		line = strings.TrimSpace(line)

		// 跳过空行和标题行
		if line == "" || strings.HasPrefix(line, "Starting") ||
			strings.HasPrefix(line, "Nmap scan") ||
			strings.HasPrefix(line, "Host is") ||
			strings.HasPrefix(line, "PORT") ||
			strings.HasPrefix(line, "Service info") ||
			strings.HasPrefix(line, "OS details") {
			continue
		}

		// 匹配端口行: 22/tcp   open  ssh
		var port, protocol, state, service string
		n, _ := fmt.Sscanf(line, "%s %s %s %s", &port, &protocol, &state, &service)
		if n >= 4 {
			// 解析端口号
			parts := strings.Split(port, "/")
			if len(parts) >= 2 {
				results = append(results, ParsedResult{
					Type:     ResultTypePort,
					Port:     parts[0],
					Protocol: parts[1],
					State:    state,
					Service:  service,
				})
			}
		}
	}

	return results, nil
}

// parseSQLMap 解析 SQLMap 结果
func (p *ToolResultParser) parseSQLMap(output string) ([]ParsedResult, error) {
	var results []ParsedResult

	lines := strings.Split(output, "\n")
	for _, line := range lines {
		line = strings.TrimSpace(line)

		// 检测 SQL 注入点
		if strings.Contains(line, "is vulnerable") ||
			strings.Contains(line, "Parameter:") {
			results = append(results, ParsedResult{
				Type:     ResultTypeVulnerability,
				Name:     "SQL Injection",
				Severity: "high",
				Status:   0,
				Tool:     "sqlmap",
				TypeStr:  "sql",
				Description: line,
			})
		}
	}

	return results, nil
}

// parseGeneric 通用解析（简单的文本解析）
func (p *ToolResultParser) parseGeneric(output string, toolName string) ([]ParsedResult, error) {
	var results []ParsedResult

	lines := strings.Split(output, "\n")
	for _, line := range lines {
		line = strings.TrimSpace(line)
		if line == "" {
			continue
		}

		// 简单处理：每行作为一个结果
		results = append(results, ParsedResult{
			Type:       ResultTypeVulnerability,
			Name:       toolName + " result",
			Target:     line,
			Severity:   "info",
			Status:     0,
			Tool:       toolName,
			Description: line,
		})
	}

	return results, nil
}

// SaveScanResult 保存扫描结果（统一入口）
func (s *ResultService) SaveScanResult(taskID uint, toolName, target, output string) (int, error) {
	savedCount := 0

	// 根据工具类型解析结果
	parser := NewToolResultParser()
	results, err := parser.Parse(toolName, output)
	if err != nil {
		return 0, fmt.Errorf("parse result failed: %w", err)
	}

	// 根据结果类型分别保存
	for _, result := range results {
		switch result.Type {
		case ResultTypeVulnerability:
			result.Tool = toolName
			if err := s.SaveVulnerability(taskID, &result); err != nil {
				log.Printf("Save vulnerability failed: %v", err)
			} else {
				savedCount++
			}
		case ResultTypePort:
			if err := s.SavePort(taskID, target, &result); err != nil {
				log.Printf("Save port failed: %v", err)
			} else {
				savedCount++
			}
		}
	}

	return savedCount, nil
}

// GetResultsByTaskID 根据任务ID获取结果
func (s *ResultService) GetResultsByTaskID(taskID uint) ([]model.Vulnerability, error) {
	var results []model.Vulnerability
	if err := s.db.Where("id > 0").Find(&results).Error; err != nil {
		return nil, err
	}
	return results, nil
}

// GetVulnerabilities 获取漏洞列表
func (s *ResultService) GetVulnerabilities(limit, offset int) ([]model.Vulnerability, int64, error) {
	var total int64
	var results []model.Vulnerability

	if err := s.db.Model(&model.Vulnerability{}).Count(&total).Error; err != nil {
		return nil, 0, err
	}

	if err := s.db.Order("created_at DESC").Limit(limit).Offset(offset).Find(&results).Error; err != nil {
		return nil, 0, err
	}

	return results, total, nil
}

// UpdateVulnStatus 更新漏洞状态
func (s *ResultService) UpdateVulnStatus(id uint, status int) error {
	return s.db.Model(&model.Vulnerability{}).Where("id = ?", id).Update("status", status).Error
}

// GetVulnByID 根据ID获取漏洞详情
func (s *ResultService) GetVulnByID(id uint) (*model.Vulnerability, error) {
	var vuln model.Vulnerability
	if err := s.db.First(&vuln, id).Error; err != nil {
		return nil, err
	}
	return &vuln, nil
}
