package handler

import (
	"net/http"
	"strconv"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"

	"qingscan/internal/model"
)

// AppHandler handles app-related requests
type AppHandler struct {
	DB *gorm.DB
}

func NewAppHandler(db *gorm.DB) *AppHandler {
	return &AppHandler{DB: db}
}

// ListApps 获取应用列表
func (h *AppHandler) ListApps(c *gin.Context) {
	page, _ := strconv.Atoi(c.DefaultQuery("page", "1"))
	pageSize, _ := strconv.Atoi(c.DefaultQuery("page_size", "10"))
	name := c.Query("name")
	domain := c.Query("domain")
	status := c.Query("status")

	var apps []model.App
	var total int64

	query := h.DB.Model(&model.App{})

	if name != "" {
		query = query.Where("name LIKE ?", "%"+name+"%")
	}
	if domain != "" {
		query = query.Where("domain LIKE ?", "%"+domain+"%")
	}
	if status != "" {
		query = query.Where("status = ?", status)
	}

	query.Count(&total)

	offset := (page - 1) * pageSize
	if err := query.Offset(offset).Limit(pageSize).Order("id DESC").Find(&apps).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to get apps"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": gin.H{"list": apps, "total": total, "page": page}})
}

// GetApp 获取应用详情
func (h *AppHandler) GetApp(c *gin.Context) {
	id := c.Param("id")

	var app model.App
	if err := h.DB.First(&app, id).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"code": 404, "msg": "App not found"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": app})
}

// CreateApp 创建应用
func (h *AppHandler) CreateApp(c *gin.Context) {
	var req struct {
		Name     string `json:"name" binding:"required"`
		URL      string `json:"url" binding:"required"`
		Domain   string `json:"domain"`
		IP       string `json:"ip"`
		Port     int    `json:"port"`
		Scheme   string `json:"scheme"`
		Remark   string `json:"remark"`
	}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "msg": "Invalid request"})
		return
	}

	app := model.App{
		Name:   req.Name,
		URL:    req.URL,
		Domain: req.Domain,
		IP:     req.IP,
		Port:   req.Port,
		Scheme: req.Scheme,
		Status: 1,
		Remark: req.Remark,
	}

	if err := h.DB.Create(&app).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to create app"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": app})
}

// UpdateApp 更新应用
func (h *AppHandler) UpdateApp(c *gin.Context) {
	id := c.Param("id")

	var req struct {
		Name     string `json:"name"`
		URL      string `json:"url"`
		Domain   string `json:"domain"`
		IP       string `json:"ip"`
		Port     int    `json:"port"`
		Status   int    `json:"status"`
		Remark   string `json:"remark"`
	}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "msg": "Invalid request"})
		return
	}

	var app model.App
	if err := h.DB.First(&app, id).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"code": 404, "msg": "App not found"})
		return
	}

	updates := map[string]interface{}{}
	if req.Name != "" {
		updates["name"] = req.Name
	}
	if req.URL != "" {
		updates["url"] = req.URL
	}
	if req.Domain != "" {
		updates["domain"] = req.Domain
	}
	if req.IP != "" {
		updates["ip"] = req.IP
	}
	if req.Port > 0 {
		updates["port"] = req.Port
	}
	if req.Status > 0 {
		updates["status"] = req.Status
	}
	if req.Remark != "" {
		updates["remark"] = req.Remark
	}

	if err := h.DB.Model(&app).Updates(updates).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to update app"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success"})
}

// DeleteApp 删除应用
func (h *AppHandler) DeleteApp(c *gin.Context) {
	id := c.Param("id")

	if err := h.DB.Delete(&model.App{}, id).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to delete app"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success"})
}

// StartScan 开始扫描应用
func (h *AppHandler) StartScan(c *gin.Context) {
	id := c.Param("id")

	var app model.App
	if err := h.DB.First(&app, id).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"code": 404, "msg": "App not found"})
		return
	}

	var req struct {
		Tools string `json:"tools"` // 使用的工具，如 "nuclei,xray,nmap"
	}
	c.ShouldBindJSON(&req)

	// 创建扫描任务
	task := model.Task{
		Name:   "Scan: " + app.Name,
		Type:   "web",
		Target: app.URL,
		Tools:  req.Tools,
		Status: 0,
	}

	// 从 context 获取 user_id
	if userID, exists := c.Get("user_id"); exists {
		task.UserID = userID.(uint)
	}

	if err := h.DB.Create(&task).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to create task"})
		return
	}

	// TODO: 推送到扫描队列

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": task})
}

// ===== Plugin =====

// PluginHandler handles plugin-related requests
type PluginHandler struct {
	DB *gorm.DB
}

func NewPluginHandler(db *gorm.DB) *PluginHandler {
	return &PluginHandler{DB: db}
}

// ListPlugins 获取插件列表
func (h *PluginHandler) ListPlugins(c *gin.Context) {
	page, _ := strconv.Atoi(c.DefaultQuery("page", "1"))
	pageSize, _ := strconv.Atoi(c.DefaultQuery("page_size", "10"))
	pluginType := c.Query("type")
	status := c.Query("status")

	var plugins []model.Plugin
	var total int64

	query := h.DB.Model(&model.Plugin{})

	if pluginType != "" {
		query = query.Where("type = ?", pluginType)
	}
	if status != "" {
		query = query.Where("status = ?", status)
	}

	query.Count(&total)

	offset := (page - 1) * pageSize
	if err := query.Offset(offset).Limit(pageSize).Order("id DESC").Find(&plugins).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to get plugins"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": gin.H{"list": plugins, "total": total, "page": page}})
}

// GetPlugin 获取插件详情
func (h *PluginHandler) GetPlugin(c *gin.Context) {
	id := c.Param("id")

	var plugin model.Plugin
	if err := h.DB.First(&plugin, id).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"code": 404, "msg": "Plugin not found"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": plugin})
}

// CreatePlugin 创建插件
func (h *PluginHandler) CreatePlugin(c *gin.Context) {
	var req model.Plugin
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "msg": "Invalid request"})
		return
	}

	if err := h.DB.Create(&req).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to create plugin"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": req})
}

// UpdatePlugin 更新插件
func (h *PluginHandler) UpdatePlugin(c *gin.Context) {
	id := c.Param("id")

	var req struct {
		Name        string `json:"name"`
		Command     string `json:"command"`
		Description string `json:"description"`
		Config      string `json:"config"`
		Status      int    `json:"status"`
	}
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"code": 400, "msg": "Invalid request"})
		return
	}

	var plugin model.Plugin
	if err := h.DB.First(&plugin, id).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"code": 404, "msg": "Plugin not found"})
		return
	}

	updates := map[string]interface{}{}
	if req.Name != "" {
		updates["name"] = req.Name
	}
	if req.Command != "" {
		updates["command"] = req.Command
	}
	if req.Description != "" {
		updates["description"] = req.Description
	}
	if req.Config != "" {
		updates["config"] = req.Config
	}
	if req.Status > 0 {
		updates["status"] = req.Status
	}

	if err := h.DB.Model(&plugin).Updates(updates).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to update plugin"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success"})
}

// DeletePlugin 删除插件
func (h *PluginHandler) DeletePlugin(c *gin.Context) {
	id := c.Param("id")

	if err := h.DB.Delete(&model.Plugin{}, id).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to delete plugin"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success"})
}

// CheckTool 检查工具是否安装
func (h *PluginHandler) CheckTool(c *gin.Context) {
	name := c.Param("name")

	var tool model.ToolConfig
	if err := h.DB.Where("name = ?", name).First(&tool).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"code": 404, "msg": "Tool not found"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": gin.H{
		"name":    tool.Name,
		"path":    tool.Path,
		"version": tool.Version,
		"status":  tool.Status,
	}})
}

// ListTools 获取工具列表
func (h *PluginHandler) ListTools(c *gin.Context) {
	var tools []model.ToolConfig
	if err := h.DB.Order("id DESC").Find(&tools).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"code": 500, "msg": "Failed to get tools"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"code": 0, "msg": "success", "data": tools})
}
