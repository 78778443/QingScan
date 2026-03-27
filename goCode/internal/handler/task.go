package handler

import (
	"log"
	"net/http"
	"strconv"
	"time"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"

	"qingscan/internal/model"
	"qingscan/internal/queue"
)

type TaskHandler struct {
	DB *gorm.DB
}

func NewTaskHandler(db *gorm.DB) *TaskHandler {
	return &TaskHandler{DB: db}
}

// CreateTask 创建扫描任务
func (h *TaskHandler) CreateTask(c *gin.Context) {
	userID, _ := c.Get("user_id")

	var req struct {
		Name   string   `json:"name" binding:"required"`
		Type   string   `json:"type" binding:"required"` // host, web, code
		Target string   `json:"target" binding:"required"`
		Tools  string   `json:"tools"` // 逗号分隔的工具列表
	}

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{
			"code": 400,
			"msg":  "Invalid request parameters",
		})
		return
	}

	task := model.Task{
		Name:    req.Name,
		Type:    req.Type,
		Target:  req.Target,
		Tools:   req.Tools,
		Status:  0,
		Progress: 0,
		UserID:  userID.(uint),
	}

	if err := h.DB.Create(&task).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{
			"code": 500,
			"msg":  "Failed to create task",
		})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"code": 0,
		"msg":  "success",
		"data": task,
	})
}

// ListTasks 获取任务列表
func (h *TaskHandler) ListTasks(c *gin.Context) {
	userID, _ := c.Get("user_id")
	role, _ := c.Get("role")

	page, _ := strconv.Atoi(c.DefaultQuery("page", "1"))
	pageSize, _ := strconv.Atoi(c.DefaultQuery("page_size", "10"))
	status := c.Query("status")
	taskType := c.Query("type")

	var tasks []model.Task
	var total int64

	query := h.DB.Model(&model.Task{})

	// 非管理员只能查看自己的任务
	if role != "admin" {
		query = query.Where("user_id = ?", userID)
	}

	if status != "" {
		query = query.Where("status = ?", status)
	}
	if taskType != "" {
		query = query.Where("type = ?", taskType)
	}

	query.Count(&total)

	offset := (page - 1) * pageSize
	if err := query.Offset(offset).Limit(pageSize).Order("id DESC").Find(&tasks).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{
			"code": 500,
			"msg":  "Failed to get tasks",
		})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"code": 0,
		"msg":  "success",
		"data": gin.H{
			"list":  tasks,
			"total": total,
			"page":  page,
		},
	})
}

// GetTask 获取任务详情
func (h *TaskHandler) GetTask(c *gin.Context) {
	id := c.Param("id")
	userID, _ := c.Get("user_id")
	role, _ := c.Get("role")

	var task model.Task
	query := h.DB.Where("id = ?", id)

	// 非管理员只能查看自己的任务
	if role != "admin" {
		query = query.Where("user_id = ?", userID)
	}

	if err := query.First(&task).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{
			"code": 404,
			"msg":  "Task not found",
		})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"code": 0,
		"msg":  "success",
		"data": task,
	})
}

// StartTask 开始执行任务
func (h *TaskHandler) StartTask(c *gin.Context) {
	id := c.Param("id")
	userID, _ := c.Get("user_id")
	role, _ := c.Get("role")

	var task model.Task
	query := h.DB.Where("id = ?", id)

	if role != "admin" {
		query = query.Where("user_id = ?", userID)
	}

	if err := query.First(&task).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{
			"code": 404,
			"msg":  "Task not found",
		})
		return
	}

	// 只有待执行的任务可以开始
	if task.Status != 0 {
		c.JSON(http.StatusBadRequest, gin.H{
			"code": 400,
			"msg":  "Task cannot be started",
		})
		return
	}

	now := time.Now()
	task.Status = 1 // 执行中
	task.StartTime = &now

	if err := h.DB.Save(&task).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{
			"code": 500,
			"msg":  "Failed to start task",
		})
		return
	}

	// 将任务推送到 Redis 队列，由 worker 执行
	err := queue.EnqueueTask(&queue.TaskPayload{
		TaskID: task.ID,
		Type:    task.Type,
		Target: task.Target,
		Tools:  task.Tools,
		UserID: task.UserID,
	})
	if err != nil {
		log.Printf("Failed to enqueue task: %v", err)
		// 队列失败，但任务已标记为执行中
	}

	c.JSON(http.StatusOK, gin.H{
		"code": 0,
		"msg":  "success",
		"data": task,
	})
}

// StopTask 停止任务
func (h *TaskHandler) StopTask(c *gin.Context) {
	id := c.Param("id")
	userID, _ := c.Get("user_id")
	role, _ := c.Get("role")

	var task model.Task
	query := h.DB.Where("id = ?", id)

	if role != "admin" {
		query = query.Where("user_id = ?", userID)
	}

	if err := query.First(&task).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{
			"code": 404,
			"msg":  "Task not found",
		})
		return
	}

	// 只有执行中的任务可以停止
	if task.Status != 1 {
		c.JSON(http.StatusBadRequest, gin.H{
			"code": 400,
			"msg":  "Task is not running",
		})
		return
	}

	now := time.Now()
	task.Status = 3 // 失败/停止
	task.EndTime = &now

	if err := h.DB.Save(&task).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{
			"code": 500,
			"msg":  "Failed to stop task",
		})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"code": 0,
		"msg":  "success",
	})
}

// DeleteTask 删除任务
func (h *TaskHandler) DeleteTask(c *gin.Context) {
	id := c.Param("id")
	userID, _ := c.Get("user_id")
	role, _ := c.Get("role")

	var task model.Task
	query := h.DB.Where("id = ?", id)

	if role != "admin" {
		query = query.Where("user_id = ?", userID)
	}

	if err := query.First(&task).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{
			"code": 404,
			"msg":  "Task not found",
		})
		return
	}

	if err := h.DB.Delete(&task).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{
			"code": 500,
			"msg":  "Failed to delete task",
		})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"code": 0,
		"msg":  "success",
	})
}

// UpdateTaskProgress 更新任务进度
func (h *TaskHandler) UpdateTaskProgress(c *gin.Context) {
	id := c.Param("id")

	var req struct {
		Progress    int    `json:"progress"`
		Status      int    `json:"status"`
		ResultCount int    `json:"result_count"`
		ErrorMsg    string `json:"error_msg"`
	}

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{
			"code": 400,
			"msg":  "Invalid request parameters",
		})
		return
	}

	var task model.Task
	if err := h.DB.First(&task, id).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{
			"code": 404,
			"msg":  "Task not found",
		})
		return
	}

	updates := map[string]interface{}{
		"progress":     req.Progress,
		"result_count": req.ResultCount,
	}

	if req.Status > 0 {
		updates["status"] = req.Status
		if req.Status == 2 || req.Status == 3 {
			now := time.Now()
			updates["end_time"] = &now
		}
	}

	if req.ErrorMsg != "" {
		updates["error_msg"] = req.ErrorMsg
	}

	if err := h.DB.Model(&task).Updates(updates).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{
			"code": 500,
			"msg":  "Failed to update task",
		})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"code": 0,
		"msg":  "success",
	})
}
