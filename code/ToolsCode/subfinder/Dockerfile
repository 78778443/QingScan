FROM golang:1.24-alpine

# 安装必要的工具
RUN apk add --no-cache git python3 py3-pip curl

# 安装 subfinder
RUN go install -v github.com/projectdiscovery/subfinder/v2/cmd/subfinder@latest

# 安装 Python requests 库
RUN pip3 install --no-cache-dir requests --break-system-packages

# 设置工作目录
WORKDIR /app

# 复制入口脚本
COPY entrypoint.py /app/entrypoint.py
RUN chmod +x /app/entrypoint.py

# 将 subfinder 添加到 PATH
ENV PATH="/go/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/usr/lib/wsl/lib:/mnt/c/Python313/Scripts/:/mnt/c/Python313/:/mnt/d/Program Files (x86)/VMware/VMware Workstation/bin/:/mnt/c/WINDOWS/system32:/mnt/c/WINDOWS:/mnt/c/WINDOWS/System32/Wbem:/mnt/c/WINDOWS/System32/WindowsPowerShell/v1.0/:/mnt/c/WINDOWS/System32/OpenSSH/:/mnt/c/Program Files/dotnet/:/mnt/c/ProgramData/chocolatey/bin:/mnt/d/public/apache-maven-3.9.10/bin:/mnt/d/Users/soupq/.jdks/ms-17.0.15/bin:/mnt/d/Users/soupq/AppData/Local/nvm:/mnt/d/Program Files/nvm4w/nodejs:/mnt/c/Program Files/Git/cmd:/mnt/d/Program Files (x86)/NetSarang/Xshell 8/:/mnt/c/Program Files/PowerShell/7/:/mnt/c/Users/soupq/AppData/Local/Microsoft/WindowsApps:/mnt/c/Users/soupq/AppData/Local/Programs/Microsoft VS Code/bin:/mnt/c/Users/soupq/AppData/Roaming/npm:/mnt/d/Users/soupq/AppData/Local/nvm:/mnt/d/Program Files/nvm4w/nodejs:/mnt/c/Users/soupq/AppData/Local/Programs/Ollama:/usr/local/go/bin"

# 设置入口点
ENTRYPOINT ["python3", "/app/entrypoint.py"]
