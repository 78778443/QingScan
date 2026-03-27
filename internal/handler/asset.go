package handler

import (
	"net/http"
	"strconv"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"

	"qingscan/internal/model"
)

// AssetHandler handles asset-related requests
type AssetHandler struct {
	DB *gorm.DB
}

func NewAssetHandler(db *gorm.DB) *AssetHandler {
	return &AssetHandler{DB: db}
}

// ===== Host =====

// ListHosts 获取主机列表
func (h *AssetHandler) ListHosts(c *gin.Context) {
	page, _ := strconv.Atoi(c.DefaultQuery("page", "1"))
	pageSize, _ := strconv.Atoi(c.DefaultQuery("page_size", "10"))
	ip := c.Query("ip")
	status := c.Query("status")

	var hosts []model.Host
	var total int64

	query := h.DB.Model(&model.Host{})

	if ip != "" {
		query = query.Where("ip LIKE ?", "%"+ip+"%")
	}
	if status != "" {
		query = query.Where("status = ?", status)
	}

	query.Count(&total)

	offset := (page - 1) * pageSize
	if err := query.Offset(offset).Limit(pageSize).Order("id DESC").Find(&hosts).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to get hosts"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": gin.H{"list": hosts, "total": total, "page": page}})
}

// GetHost 获取主机详情
func (h *AssetHandler) GetHost(c *gin.Context) {
	id := c.Param("id")

	var host model.Host
	if err := h.DB.First(&host, id).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"code": 404, "msg": "Host not found"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": host})
}

// CreateHost 创建主机
func (h *AssetHandler) CreateHost(c *gin.Context) {
	var req model.Host
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "msg": "Invalid request"})
		return
	}

	if err := h.DB.Create(&req).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to create host"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": req})
}

// UpdateHost 更新主机
func (h *AssetHandler) UpdateHost(c *gin.Context) {
	id := c.Param("id")

	var req struct {
		Hostname string `json:"hostname"`
		OS       string `json:"os"`
		Tags     string `json:"tags"`
		Remark   string `json:"remark"`
	}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "msg": "Invalid request"})
		return
	}

	var host model.Host
	if err := h.DB.First(&host, id).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"code": 404, "msg": "Host not found"})
		return
	}

	updates := map[string]interface{}{}
	if req.Hostname != "" {
		updates["hostname"] = req.Hostname
	}
	if req.OS != "" {
		updates["os"] = req.OS
	}
	if req.Tags != "" {
		updates["tags"] = req.Tags
	}
	if req.Remark != "" {
		updates["remark"] = req.Remark
	}

	if err := h.DB.Model(&host).Updates(updates).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to update host"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success"})
}

// DeleteHost 删除主机
func (h *AssetHandler) DeleteHost(c *gin.Context) {
	id := c.Param("id")

	if err := h.DB.Delete(&model.Host{}, id).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to delete host"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success"})
}

// ===== Domain =====

// ListDomains 获取域名列表
func (h *AssetHandler) ListDomains(c *gin.Context) {
	page, _ := strconv.Atoi(c.DefaultQuery("page", "1"))
	pageSize, _ := strconv.Atoi(c.DefaultQuery("page_size", "10"))
	domain := c.Query("domain")
	domainType := c.Query("type")

	var domains []model.Domain
	var total int64

	query := h.DB.Model(&model.Domain{})

	if domain != "" {
		query = query.Where("domain LIKE ?", "%"+domain+"%")
	}
	if domainType != "" {
		query = query.Where("type = ?", domainType)
	}

	query.Count(&total)

	offset := (page - 1) * pageSize
	if err := query.Offset(offset).Limit(pageSize).Order("id DESC").Find(&domains).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to get domains"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": gin.H{"list": domains, "total": total, "page": page}})
}

// GetDomain 获取域名详情
func (h *AssetHandler) GetDomain(c *gin.Context) {
	id := c.Param("id")

	var domain model.Domain
	if err := h.DB.First(&domain, id).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"code": 404, "msg": "Domain not found"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": domain})
}

// CreateDomain 创建域名
func (h *AssetHandler) CreateDomain(c *gin.Context) {
	var req model.Domain
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "msg": "Invalid request"})
		return
	}

	if err := h.DB.Create(&req).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to create domain"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": req})
}

// DeleteDomain 删除域名
func (h *AssetHandler) DeleteDomain(c *gin.Context) {
	id := c.Param("id")

	if err := h.DB.Delete(&model.Domain{}, id).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to delete domain"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success"})
}

// ===== Port =====

// ListPorts 获取端口列表
func (h *AssetHandler) ListPorts(c *gin.Context) {
	page, _ := strconv.Atoi(c.DefaultQuery("page", "1"))
	pageSize, _ := strconv.Atoi(c.DefaultQuery("page_size", "10"))
	host := c.Query("host")
	service := c.Query("service")

	var ports []model.Port
	var total int64

	query := h.DB.Model(&model.Port{})

	if host != "" {
		query = query.Where("host = ?", host)
	}
	if service != "" {
		query = query.Where("service LIKE ?", "%"+service+"%")
	}

	query.Count(&total)

	offset := (page - 1) * pageSize
	if err := query.Offset(offset).Limit(pageSize).Order("id DESC").Find(&ports).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to get ports"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": gin.H{"list": ports, "total": total, "page": page}})
}

// CreatePort 创建端口
func (h *AssetHandler) CreatePort(c *gin.Context) {
	var req model.Port
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "msg": "Invalid request"})
		return
	}

	if err := h.DB.Create(&req).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to create port"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": req})
}

// DeletePort 删除端口
func (h *AssetHandler) DeletePort(c *gin.Context) {
	id := c.Param("id")

	if err := h.DB.Delete(&model.Port{}, id).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to delete port"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success"})
}

// ===== URL =====

// ListURLs 获取URL列表
func (h *AssetHandler) ListURLs(c *gin.Context) {
	page, _ := strconv.Atoi(c.DefaultQuery("page", "1"))
	pageSize, _ := strconv.Atoi(c.DefaultQuery("page_size", "10"))
	host := c.Query("host")
	domain := c.Query("domain")

	var urls []model.URL
	var total int64

	query := h.DB.Model(&model.URL{})

	if host != "" {
		query = query.Where("host = ?", host)
	}
	if domain != "" {
		query = query.Where("domain = ?", domain)
	}

	query.Count(&total)

	offset := (page - 1) * pageSize
	if err := query.Offset(offset).Limit(pageSize).Order("id DESC").Find(&urls).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to get URLs"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": gin.H{"list": urls, "total": total, "page": page}})
}

// CreateURL 创建URL
func (h *AssetHandler) CreateURL(c *gin.Context) {
	var req model.URL
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "msg": "Invalid request"})
		return
	}

	if err := h.DB.Create(&req).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to create URL"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": req})
}

// DeleteURL 删除URL
func (h *AssetHandler) DeleteURL(c *gin.Context) {
	id := c.Param("id")

	if err := h.DB.Delete(&model.URL{}, id).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to delete URL"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success"})
}

// ===== Vulnerability =====

// ListVulnerabilities 获取漏洞列表
func (h *AssetHandler) ListVulnerabilities(c *gin.Context) {
	page, _ := strconv.Atoi(c.DefaultQuery("page", "1"))
	pageSize, _ := strconv.Atoi(c.DefaultQuery("page_size", "10"))
	vulnType := c.Query("type")
	severity := c.Query("severity")
	status := c.Query("status")

	var vulns []model.Vulnerability
	var total int64

	query := h.DB.Model(&model.Vulnerability{})

	if vulnType != "" {
		query = query.Where("type = ?", vulnType)
	}
	if severity != "" {
		query = query.Where("severity = ?", severity)
	}
	if status != "" {
		query = query.Where("status = ?", status)
	}

	query.Count(&total)

	offset := (page - 1) * pageSize
	if err := query.Offset(offset).Limit(pageSize).Order("id DESC").Find(&vulns).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to get vulnerabilities"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": gin.H{"list": vulns, "total": total, "page": page}})
}

// GetVulnerability 获取漏洞详情
func (h *AssetHandler) GetVulnerability(c *gin.Context) {
	id := c.Param("id")

	var vuln model.Vulnerability
	if err := h.DB.First(&vuln, id).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"code": 404, "msg": "Vulnerability not found"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": vuln})
}

// CreateVulnerability 创建漏洞
func (h *AssetHandler) CreateVulnerability(c *gin.Context) {
	var req model.Vulnerability
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "msg": "Invalid request"})
		return
	}

	if err := h.DB.Create(&req).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to create vulnerability"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": req})
}

// UpdateVulnerability 更新漏洞状态
func (h *AssetHandler) UpdateVulnerability(c *gin.Context) {
	id := c.Param("id")

	var req struct {
		Status      int    `json:"status"`
		Remark      string `json:"remark"`
	}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "msg": "Invalid request"})
		return
	}

	var vuln model.Vulnerability
	if err := h.DB.First(&vuln, id).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"code": 404, "msg": "Vulnerability not found"})
		return
	}

	updates := map[string]interface{}{}
	if req.Status > 0 {
		updates["status"] = req.Status
	}
	if req.Remark != "" {
		updates["remark"] = req.Remark
	}

	if err := h.DB.Model(&vuln).Updates(updates).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to update vulnerability"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success"})
}

// DeleteVulnerability 删除漏洞
func (h *AssetHandler) DeleteVulnerability(c *gin.Context) {
	id := c.Param("id")

	if err := h.DB.Delete(&model.Vulnerability{}, id).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to delete vulnerability"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success"})
}

// GetVulnStats 获取漏洞统计
func (h *AssetHandler) GetVulnStats(c *gin.Context) {
	var stats struct {
		Total     int64 `json:"total"`
		Critical  int64 `json:"critical"`
		High      int64 `json:"high"`
		Medium    int64 `json:"medium"`
		Low       int64 `json:"low"`
		Info      int64 `json:"info"`
		Confirmed int64 `json:"confirmed"`
		Pending   int64 `json:"pending"`
		Fixed     int64 `json:"fixed"`
	}

	h.DB.Model(&model.Vulnerability{}).Count(&stats.Total)
	h.DB.Model(&model.Vulnerability{}).Where("severity = ?", "critical").Count(&stats.Critical)
	h.DB.Model(&model.Vulnerability{}).Where("severity = ?", "high").Count(&stats.High)
	h.DB.Model(&model.Vulnerability{}).Where("severity = ?", "medium").Count(&stats.Medium)
	h.DB.Model(&model.Vulnerability{}).Where("severity = ?", "low").Count(&stats.Low)
	h.DB.Model(&model.Vulnerability{}).Where("severity = ?", "info").Count(&stats.Info)
	h.DB.Model(&model.Vulnerability{}).Where("status = ?", 1).Count(&stats.Confirmed)
	h.DB.Model(&model.Vulnerability{}).Where("status = ?", 0).Count(&stats.Pending)
	h.DB.Model(&model.Vulnerability{}).Where("status = ?", 3).Count(&stats.Fixed)

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": stats})
}

// GetAssetStats 获取资产统计
func (h *AssetHandler) GetAssetStats(c *gin.Context) {
	var stats struct {
		HostCount    int64 `json:"host_count"`
		DomainCount  int64 `json:"domain_count"`
		PortCount    int64 `json:"port_count"`
		URLCount     int64 `json:"url_count"`
		 VulnCount   int64 `json:"vuln_count"`
	}

	h.DB.Model(&model.Host{}).Count(&stats.HostCount)
	h.DB.Model(&model.Domain{}).Count(&stats.DomainCount)
	h.DB.Model(&model.Port{}).Count(&stats.PortCount)
	h.DB.Model(&model.URL{}).Count(&stats.URLCount)
	h.DB.Model(&model.Vulnerability{}).Count(&stats.VulnCount)

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": stats})
}
