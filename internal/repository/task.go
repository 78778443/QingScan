package repository

import (
	"time"

	"gorm.io/gorm"

	"qingscan/internal/model"
)

// TaskRepository 任务仓储
type TaskRepository struct {
	db *gorm.DB
}

// NewTaskRepository 创建任务仓储
func NewTaskRepository(db *gorm.DB) *TaskRepository {
	return &TaskRepository{db: db}
}

// Create 创建任务
func (r *TaskRepository) Create(task *model.Task) error {
	return r.db.Create(task).Error
}

// GetByID 根据ID获取任务
func (r *TaskRepository) GetByID(id uint) (*model.Task, error) {
	var task model.Task
	err := r.db.First(&task, id).Error
	if err != nil {
		return nil, err
	}
	return &task, nil
}

// GetPendingTasks 获取待执行的任务
func (r *TaskRepository) GetPendingTasks(limit int) ([]model.Task, error) {
	var tasks []model.Task
	err := r.db.Where("status = ?", 0).Limit(limit).Order("id ASC").Find(&tasks).Error
	return tasks, err
}

// UpdateStatus 更新任务状态
func (r *TaskRepository) UpdateStatus(id uint, status int) error {
	return r.db.Model(&model.Task{}).Where("id = ?", id).Update("status", status).Error
}

// StartTask 开始任务
func (r *TaskRepository) StartTask(id uint, startTime *time.Time) error {
	updates := map[string]interface{}{
		"status": 1,
	}
	if startTime != nil {
		updates["start_time"] = startTime
	}
	return r.db.Model(&model.Task{}).Where("id = ?", id).Updates(updates).Error
}

// CompleteTask 完成任务
func (r *TaskRepository) CompleteTask(id uint) error {
	now := time.Now()
	return r.db.Model(&model.Task{}).Where("id = ?", id).Updates(map[string]interface{}{
		"status":     2,
		"progress":   100,
		"end_time":   &now,
	}).Error
}

// FailTask 任务失败
func (r *TaskRepository) FailTask(id uint, errMsg string) error {
	now := time.Now()
	return r.db.Model(&model.Task{}).Where("id = ?", id).Updates(map[string]interface{}{
		"status":    3,
		"error_msg": errMsg,
		"end_time":  &now,
	}).Error
}

// UpdateProgress 更新进度
func (r *TaskRepository) UpdateProgress(id uint, progress int, resultCount int) error {
	return r.db.Model(&model.Task{}).Where("id = ?", id).Updates(map[string]interface{}{
		"progress":     progress,
		"result_count": resultCount,
	}).Error
}

// Delete 删除任务
func (r *TaskRepository) Delete(id uint) error {
	return r.db.Delete(&model.Task{}, id).Error
}

// List 任务列表
func (r *TaskRepository) List(page, pageSize int, status string, taskType string) ([]model.Task, int64, error) {
	var tasks []model.Task
	var total int64

	query := r.db.Model(&model.Task{})

	if status != "" {
		query = query.Where("status = ?", status)
	}
	if taskType != "" {
		query = query.Where("type = ?", taskType)
	}

	query.Count(&total)

	offset := (page - 1) * pageSize
	err := query.Offset(offset).Limit(pageSize).Order("id DESC").Find(&tasks).Error

	return tasks, total, err
}

// HostRepository 主机资产仓储
type HostRepository struct {
	db *gorm.DB
}

func NewHostRepository(db *gorm.DB) *HostRepository {
	return &HostRepository{db: db}
}

func (r *HostRepository) Create(host *model.Host) error {
	return r.db.Create(host).Error
}

func (r *HostRepository) BatchCreate(hosts []model.Host) error {
	return r.db.Create(&hosts).Error
}

func (r *HostRepository) GetByID(id uint) (*model.Host, error) {
	var host model.Host
	err := r.db.First(&host, id).Error
	return &host, err
}

func (r *HostRepository) GetByIP(ip string) (*model.Host, error) {
	var host model.Host
	err := r.db.Where("ip = ?", ip).First(&host).Error
	return &host, err
}

func (r *HostRepository) List(page, pageSize int, ip string) ([]model.Host, int64, error) {
	var hosts []model.Host
	var total int64

	query := r.db.Model(&model.Host{})
	if ip != "" {
		query = query.Where("ip LIKE ?", "%"+ip+"%")
	}

	query.Count(&total)

	offset := (page - 1) * pageSize
	err := query.Offset(offset).Limit(pageSize).Order("id DESC").Find(&hosts).Error

	return hosts, total, err
}

func (r *HostRepository) Update(id uint, updates map[string]interface{}) error {
	return r.db.Model(&model.Host{}).Where("id = ?", id).Updates(updates).Error
}

func (r *HostRepository) Delete(id uint) error {
	return r.db.Delete(&model.Host{}, id).Error
}

// VulnerabilityRepository 漏洞仓储
type VulnerabilityRepository struct {
	db *gorm.DB
}

func NewVulnerabilityRepository(db *gorm.DB) *VulnerabilityRepository {
	return &VulnerabilityRepository{db: db}
}

func (r *VulnerabilityRepository) Create(vuln *model.Vulnerability) error {
	return r.db.Create(vuln).Error
}

func (r *VulnerabilityRepository) BatchCreate(vulns []model.Vulnerability) error {
	return r.db.Create(&vulns).Error
}

func (r *VulnerabilityRepository) GetByID(id uint) (*model.Vulnerability, error) {
	var vuln model.Vulnerability
	err := r.db.First(&vuln, id).Error
	return &vuln, err
}

func (r *VulnerabilityRepository) List(page, pageSize int, vulnType, severity string) ([]model.Vulnerability, int64, error) {
	var vulns []model.Vulnerability
	var total int64

	query := r.db.Model(&model.Vulnerability{})
	if vulnType != "" {
		query = query.Where("type = ?", vulnType)
	}
	if severity != "" {
		query = query.Where("severity = ?", severity)
	}

	query.Count(&total)

	offset := (page - 1) * pageSize
	err := query.Offset(offset).Limit(pageSize).Order("id DESC").Find(&vulns).Error

	return vulns, total, err
}

func (r *VulnerabilityRepository) UpdateStatus(id uint, status int) error {
	return r.db.Model(&model.Vulnerability{}).Where("id = ?", id).Update("status", status).Error
}

func (r *VulnerabilityRepository) Delete(id uint) error {
	return r.db.Delete(&model.Vulnerability{}, id).Error
}

func (r *VulnerabilityRepository) GetStats() (map[string]int64, error) {
	stats := make(map[string]int64)

	var total int64
	r.db.Model(&model.Vulnerability{}).Count(&total)
	stats["total"] = total

	severities := []string{"critical", "high", "medium", "low", "info"}
	for _, s := range severities {
		var count int64
		r.db.Model(&model.Vulnerability{}).Where("severity = ?", s).Count(&count)
		stats[s] = count
	}

	return stats, nil
}
