CREATE DATABASE IF NOT EXISTS qingscan;
USE qingscan;

CREATE TABLE IF NOT EXISTS scan_target (
    id INT AUTO_INCREMENT PRIMARY KEY,
    target VARCHAR(500) NOT NULL,
    hash VARCHAR(64),
    last_scan_time DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS scan_tool (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tool_name VARCHAR(50) NOT NULL UNIQUE,
    tool_label VARCHAR(100),
    description VARCHAR(500),
    command TEXT,
    start_command TEXT,
    script_code TEXT,
    output_parse TEXT,
    is_enabled TINYINT DEFAULT 1,
    remark VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS scan_task (
    id INT AUTO_INCREMENT PRIMARY KEY,
    target_id INT,
    tool_id INT,
    task_status VARCHAR(20) DEFAULT 'pending',
    progress INT DEFAULT 0,
    result_count INT DEFAULT 0,
    message VARCHAR(500),
    start_time DATETIME,
    end_time DATETIME,
    output TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS scan_result (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    data MEDIUMTEXT,
    data_type VARCHAR(50) DEFAULT 'raw',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS llm_analysis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT,
    risk_level VARCHAR(20),
    critical_count INT DEFAULT 0,
    high_count INT DEFAULT 0,
    medium_count INT DEFAULT 0,
    low_count INT DEFAULT 0,
    analysis_summary TEXT,
    fix_suggestion TEXT,
    llm_model VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 插入所有工具配置（使用 Docker Hub 官方镜像）
INSERT INTO scan_tool (tool_name, tool_label, description, start_command, is_enabled) VALUES
('sqlmap', 'SQLMap', 'SQL注入检测工具', 'docker run --rm --network qingscan-net daxia/qingscan-sqlmap:latest --task-id={task_id} --callback={callback_url} --target={target}', 1),
('fscan', 'Fscan', '综合扫描工具', 'docker run --rm --network qingscan-net daxia/qingscan-fscan:latest --task-id={task_id} --callback={callback_url} --target={target}', 1),
('nmap', 'Nmap', '端口扫描工具', 'docker run --rm --network qingscan-net daxia/qingscan-nmap:latest --task-id={task_id} --callback={callback_url} --target={target}', 1),
('nuclei', 'Nuclei', '漏洞扫描模板引擎', 'docker run --rm --network qingscan-net daxia/qingscan-nuclei:latest --task-id={task_id} --callback={callback_url} --target={target}', 1),
('gobuster', 'Gobuster', '目录爆破工具', 'docker run --rm --network qingscan-net daxia/qingscan-gobuster:latest --task-id={task_id} --callback={callback_url} --target={target}', 1),
('xray', 'Xray', '被动安全扫描', 'docker run --rm --network qingscan-net daxia/qingscan-xray:latest --task-id={task_id} --callback={callback_url} --target={target}', 1),
('afrog', 'Afrog', '漏洞扫描工具', 'docker run --rm --network qingscan-net daxia/qingscan-afrog:latest --task-id={task_id} --callback={callback_url} --target={target}', 1),
('subfinder', 'Subfinder', '子域名发现工具', 'docker run --rm --network qingscan-net daxia/qingscan-subfinder:latest --task-id={task_id} --callback={callback_url} --target={target}', 1),
('httpx', 'Httpx', 'HTTP探测工具', 'docker run --rm --network qingscan-net daxia/qingscan-httpx:latest --task-id={task_id} --callback={callback_url} --target={target}', 1),
('dismap', 'Dismap', '资产识别工具', 'docker run --rm --network qingscan-net daxia/qingscan-dismap:latest --task-id={task_id} --callback={callback_url} --target={target}', 1),
('kunpeng', 'Kunpeng', '漏洞扫描工具', 'docker run --rm --network qingscan-net daxia/qingscan-kunpeng:latest --task-id={task_id} --callback={callback_url} --target={target}', 1)
ON DUPLICATE KEY UPDATE start_command=VALUES(start_command);
