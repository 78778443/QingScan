package docker

import (
	"bufio"
	"context"
	"encoding/base64"
	"encoding/json"
	"fmt"
	"io"
	"os"
	"os/exec"
	"strings"
	"time"

	"qingscan/internal/config"
)

// DockerClient Docker 客户端封装
type DockerClient struct {
	socket string
}

// NewDockerClient 创建 Docker 客户端
func NewDockerClient(socket string) *DockerClient {
	if socket == "" {
		socket = "/var/run/docker.sock"
	}
	return &DockerClient{
		socket: socket,
	}
}

// 执行 docker 命令
func (d *DockerClient) runCmd(args ...string) (string, error) {
	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Minute)
	defer cancel()

	cmd := exec.CommandContext(ctx, "docker", args...)
	output, err := cmd.CombinedOutput()
	if err != nil {
		return string(output), fmt.Errorf("docker %s failed: %w, output: %s", strings.Join(args, " "), err, string(output))
	}
	return string(output), nil
}

// IsImageExists 检查镜像是否存在
func (d *DockerClient) IsImageExists(imageName string) (bool, error) {
	output, err := d.runCmd("images", "-q", imageName)
	if err != nil {
		return false, err
	}
	return strings.TrimSpace(output) != "", nil
}

// LoadImage 从 tar 文件加载镜像
func (d *DockerClient) LoadImage(tarFile string) (string, error) {
	// 检查文件是否存在
	if _, err := os.Stat(tarFile); err != nil {
		return "", fmt.Errorf("tar file not found: %w", err)
	}

	fmt.Printf("Loading image from %s...\n", tarFile)

	// 使用 docker load
	cmd := exec.Command("docker", "load", "-i", tarFile)
	cmd.Stdout = os.Stdout
	cmd.Stderr = os.Stderr

	if err := cmd.Run(); err != nil {
		return "", fmt.Errorf("docker load failed: %w", err)
	}

	// 获取镜像名称
	// 从 tar 文件名提取默认名称
	parts := strings.Split(strings.TrimSuffix(tarFile, ".tar"), "/")
	baseName := parts[len(parts)-1]

	// 查找刚加载的镜像
	output, err := d.runCmd("images", "--format", "{{.Repository}}:{{.Tag}}")
	if err != nil {
		return "", err
	}

	lines := strings.Split(strings.TrimSpace(output), "\n")
	for i := len(lines) - 1; i >= 0; i-- {
		line := strings.TrimSpace(lines[i])
		if line != "" && strings.Contains(strings.ToLower(line), strings.ToLower(baseName)) {
			return line, nil
		}
	}

	return "", fmt.Errorf("image not found after load")
}

// TagImage 给镜像打标签
func (d *DockerClient) TagImage(source, target string) error {
	_, err := d.runCmd("tag", source, target)
	return err
}

// RunContainer 运行容器执行扫描
func (d *DockerClient) RunContainer(cfg *config.ContainerConfig, cmd []string, workDir string, timeout time.Duration) (string, error) {
	imageName := cfg.Image
	if cfg.Tag != "" {
		imageName = fmt.Sprintf("%s:%s", cfg.Image, cfg.Tag)
	}

	// 检查镜像是否存在
	exists, err := d.IsImageExists(imageName)
	if err != nil {
		return "", err
	}
	if !exists {
		// 尝试加载镜像
		if cfg.ImageFile != "" {
			loadedImage, err := d.LoadImage(cfg.ImageFile)
			if err != nil {
				return "", fmt.Errorf("load image failed: %w", err)
			}
			imageName = loadedImage
		} else {
			return "", fmt.Errorf("image %s not found and no image_file specified", imageName)
		}
	}

	// 构建 docker run 命令
	args := []string{"run", "--rm", "--network", "host"}

	// 资源限制
	if cfg.Resources.Memory != "" {
		args = append(args, "-m", cfg.Resources.Memory)
	}
	if cfg.Resources.CPU != "" {
		args = append(args, "--cpus", cfg.Resources.CPU)
	}

	// 卷挂载
	for _, vol := range cfg.Volumes {
		args = append(args, "-v", fmt.Sprintf("%s:%s", vol.Source, vol.Target))
	}

	// 环境变量
	for k, v := range cfg.Env {
		args = append(args, "-e", fmt.Sprintf("%s=%s", k, v))
	}

	// 工作目录
	if workDir != "" {
		args = append(args, "-w", workDir)
	}

	// 添加镜像和命令
	args = append(args, imageName)
	args = append(args, cmd...)

	fmt.Printf("Running docker command: docker %s\n", strings.Join(args, " "))

	// 执行命令
	ctx, cancel := context.WithTimeout(context.Background(), timeout)
	defer cancel()

	dockerCmd := exec.CommandContext(ctx, "docker", args...)
	stdout, err := dockerCmd.StdoutPipe()
	if err != nil {
		return "", err
	}
	stderr, err := dockerCmd.StderrPipe()
	if err != nil {
		return "", err
	}

	if err := dockerCmd.Start(); err != nil {
		return "", fmt.Errorf("start docker failed: %w", err)
	}

	// 读取输出
	stdoutChan := make(chan string, 1)
	go func() {
		scanner := bufio.NewScanner(stdout)
		var lines []string
		for scanner.Scan() {
			lines = append(lines, scanner.Text())
		}
		stdoutChan <- strings.Join(lines, "\n")
	}()

	stderrChan := make(chan string, 1)
	go func() {
		scanner := bufio.NewScanner(stderr)
		var lines []string
		for scanner.Scan() {
			lines = append(lines, scanner.Text())
		}
		stderrChan <- strings.Join(lines, "\n")
	}()

	err = dockerCmd.Wait()
	stdoutStr := <-stdoutChan
	stderrStr := <-stderrChan

	if err != nil {
		// 如果有 stderr 输出，优先使用
		if stderrStr != "" {
			return stdoutStr, fmt.Errorf("container execution failed: %s, stdout: %s", stderrStr, stdoutStr)
		}
		return stdoutStr, fmt.Errorf("container execution failed: %w, stdout: %s", err, stdoutStr)
	}

	return stdoutStr, nil
}

// RunContainerWithOutput 带完整输出的容器运行
func (d *DockerClient) RunContainerWithOutput(cfg *config.ContainerConfig, cmd []string, workDir string, timeout time.Duration) (io.Reader, io.Reader, error) {
	imageName := cfg.Image
	if cfg.Tag != "" {
		imageName = fmt.Sprintf("%s:%s", cfg.Image, cfg.Tag)
	}

	// 检查镜像是否存在
	exists, err := d.IsImageExists(imageName)
	if err != nil {
		return nil, nil, err
	}
	if !exists {
		if cfg.ImageFile != "" {
			loadedImage, err := d.LoadImage(cfg.ImageFile)
			if err != nil {
				return nil, nil, fmt.Errorf("load image failed: %w", err)
			}
			imageName = loadedImage
		} else {
			return nil, nil, fmt.Errorf("image %s not found", imageName)
		}
	}

	// 构建命令
	args := []string{"run", "--rm", "--network", "host"}

	if cfg.Resources.Memory != "" {
		args = append(args, "-m", cfg.Resources.Memory)
	}
	if cfg.Resources.CPU != "" {
		args = append(args, "--cpus", cfg.Resources.CPU)
	}

	for _, vol := range cfg.Volumes {
		args = append(args, "-v", fmt.Sprintf("%s:%s", vol.Source, vol.Target))
	}

	for k, v := range cfg.Env {
		args = append(args, "-e", fmt.Sprintf("%s=%s", k, v))
	}

	if workDir != "" {
		args = append(args, "-w", workDir)
	}

	args = append(args, imageName)
	args = append(args, cmd...)

	dockerCmd := exec.Command("docker", args...)
	stdout, err := dockerCmd.StdoutPipe()
	if err != nil {
		return nil, nil, err
	}
	stderr, err := dockerCmd.StderrPipe()
	if err != nil {
		return nil, nil, err
	}

	if err := dockerCmd.Start(); err != nil {
		return nil, nil, err
	}

	return stdout, stderr, nil
}

// PullImage 拉取镜像
func (d *DockerClient) PullImage(imageName string) error {
	_, err := d.runCmd("pull", imageName)
	return err
}

// RemoveImage 删除镜像
func (d *DockerClient) RemoveImage(imageName string) error {
	_, err := d.runCmd("rmi", imageName)
	return err
}

// ListImages 列出镜像
func (d *DockerClient) ListImages() ([]string, error) {
	output, err := d.runCmd("images", "--format", "{{.Repository}}:{{.Tag}}")
	if err != nil {
		return nil, err
	}
	return strings.Split(strings.TrimSpace(output), "\n"), nil
}

// GetImageInfo 获取镜像信息
type ImageInfo struct {
	ID       string
	RepoTags []string
	Size     string
	Created  string
}

func (d *DockerClient) GetImageInfo(imageName string) (*ImageInfo, error) {
	output, err := d.runCmd("inspect", imageName)
	if err != nil {
		return nil, err
	}

	var inspect []struct {
		ID       string   `json:"Id"`
		RepoTags []string `json:"RepoTags"`
		Size     string   `json:"Size"`
		Created  string   `json:"Created"`
	}

	if err := json.Unmarshal([]byte(output), &inspect); err != nil {
		return nil, err
	}

	if len(inspect) == 0 {
		return nil, fmt.Errorf("image not found")
	}

	return &ImageInfo{
		ID:       inspect[0].ID,
		RepoTags: inspect[0].RepoTags,
		Size:     inspect[0].Size,
		Created:  inspect[0].Created,
	}, nil
}

// CheckDockerAvailable 检查 Docker 是否可用
func (d *DockerClient) CheckDockerAvailable() error {
	_, err := d.runCmd("version")
	return err
}

// Base64EncodeAuth 编码 Docker 认证信息
func Base64EncodeAuth(username, password string) string {
	auth := map[string]string{
		"username": username,
		"password": password,
	}
	authBytes, _ := json.Marshal(auth)
	return base64.StdEncoding.EncodeToString(authBytes)
}
