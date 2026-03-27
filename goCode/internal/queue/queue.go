package queue

import (
	"context"
	"encoding/json"
	"fmt"
	"log"
	"time"

	"github.com/redis/go-redis/v9"
	"github.com/hibiken/asynq"

	"qingscan/internal/config"
)

var (
	RedisClient *redis.Client
	AsynqServer *asynq.Server
	AsynqClient *asynq.Client
	AsynqMux   *asynq.ServeMux
)

// TaskPayload 任务负载
type TaskPayload struct {
	TaskID   uint   `json:"task_id"`
	Type     string `json:"type"`     // host, web, code
	Target   string `json:"target"`   // 扫描目标
	Tools    string `json:"tools"`    // 使用的工具
	UserID   uint   `json:"user_id"`
}

// TaskResult 任务结果
type TaskResult struct {
	TaskID      uint   `json:"task_id"`
	Status      int    `json:"status"`      // 0:pending, 1:running, 2:completed, 3:failed
	Progress    int    `json:"progress"`   // 0-100
	ResultCount int    `json:"result_count"`
	ErrorMsg    string `json:"error_msg"`
}

// InitRedis 初始化Redis连接
func InitRedis(cfg *config.RedisConfig) error {
	RedisClient = redis.NewClient(&redis.Options{
		Addr:     cfg.Addr(),
		Password: cfg.Password,
		DB:       cfg.DB,
	})

	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()

	_, err := RedisClient.Ping(ctx).Result()
	if err != nil {
		return fmt.Errorf("failed to connect to redis: %w", err)
	}

	log.Println("Redis connected successfully")
	return nil
}

// InitAsynq 初始化Asynq服务器
func InitAsynq(cfg *config.RedisConfig) {
	AsynqServer = asynq.NewServer(
		asynq.RedisClientOpt{
			Addr:     cfg.Addr(),
			Password: cfg.Password,
			DB:       cfg.DB,
		},
		asynq.Config{
			Concurrency: 10,
		},
	)

	// 创建客户端用于入队
	AsynqClient = asynq.NewClient(
		asynq.RedisClientOpt{
			Addr:     cfg.Addr(),
			Password: cfg.Password,
			DB:       cfg.DB,
		},
	)

	AsynqMux = asynq.NewServeMux()
	log.Println("Asynq server initialized")
}

// EnqueueTask 入队任务
func EnqueueTask(payload *TaskPayload) error {
	data, err := json.Marshal(payload)
	if err != nil {
		return fmt.Errorf("failed to marshal payload: %w", err)
	}

	task := asynq.NewTask(
		"scan", // 任务类型
		data,
		asynq.Queue("default"),
		asynq.MaxRetry(3),
		asynq.Timeout(30*time.Minute),
	)

	// 使用 Client 入队
	_, err = AsynqClient.Enqueue(task)
	if err != nil {
		return fmt.Errorf("failed to enqueue task: %w", err)
	}

	log.Printf("Task enqueued: TaskID=%d, Type=%s, Target=%s", payload.TaskID, payload.Type, payload.Target)
	return nil
}

// GetTaskStatus 获取任务状态
func GetTaskStatus(taskID uint) (*TaskResult, error) {
	key := fmt.Sprintf("task:result:%d", taskID)

	ctx := context.Background()
	data, err := RedisClient.Get(ctx, key).Result()
	if err == redis.Nil {
		return nil, nil
	}
	if err != nil {
		return nil, err
	}

	var result TaskResult
	if err := json.Unmarshal([]byte(data), &result); err != nil {
		return nil, err
	}

	return &result, nil
}

// SetTaskStatus 设置任务状态
func SetTaskStatus(result *TaskResult) error {
	key := fmt.Sprintf("task:result:%d", result.TaskID)

	data, err := json.Marshal(result)
	if err != nil {
		return err
	}

	ctx := context.Background()
	return RedisClient.Set(ctx, key, data, 24*time.Hour).Err()
}

// UpdateTaskProgress 更新任务进度
func UpdateTaskProgress(taskID uint, progress int, resultCount int) error {
	result := &TaskResult{
		TaskID:      taskID,
		Status:      1, // running
		Progress:    progress,
		ResultCount: resultCount,
	}
	return SetTaskStatus(result)
}

// CompleteTask 完成任务
func CompleteTask(taskID uint, resultCount int) error {
	result := &TaskResult{
		TaskID:      taskID,
		Status:      2, // completed
		Progress:    100,
		ResultCount: resultCount,
	}
	return SetTaskStatus(result)
}

// FailTask 任务失败
func FailTask(taskID uint, errMsg string) error {
	result := &TaskResult{
		TaskID:      taskID,
		Status:      3, // failed
		Progress:    0,
		ResultCount: 0,
		ErrorMsg:    errMsg,
	}
	return SetTaskStatus(result)
}

// PublishScanEvent 发布扫描事件 (用于WebSocket)
func PublishScanEvent(taskID uint, event string, data interface{}) error {
	key := fmt.Sprintf("scan:event:%d", taskID)

	msg := map[string]interface{}{
		"event": event,
		"data":  data,
	}

	msgData, err := json.Marshal(msg)
	if err != nil {
		return err
	}

	ctx := context.Background()
	return RedisClient.Publish(ctx, key, msgData).Err()
}

// SubscribeScanEvent 订阅扫描事件
func SubscribeScanEvent(taskID uint) *redis.PubSub {
	key := fmt.Sprintf("scan:event:%d", taskID)
	ctx := context.Background()
	return RedisClient.Subscribe(ctx, key)
}

// GetRedisClient 获取Redis客户端
func GetRedisClient() *redis.Client {
	return RedisClient
}

// Close 关闭连接
func Close() {
	if RedisClient != nil {
		RedisClient.Close()
	}
	if AsynqServer != nil {
		AsynqServer.Shutdown()
	}
}
