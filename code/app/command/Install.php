<?php
declare (strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;

class Install extends Command
{
    // 颜色定义
    const COLORS = [
        'red' => '0;31m',
        'green' => '0;32m',
        'yellow' => '1;33m',
        'blue' => '0;34m',
        'nc' => '0m' // No Color
    ];
    
    // 支持的工具列表
    protected $tools = [
        'nmap' => '网络发现和端口扫描工具',
        'whatweb' => 'Web应用程序指纹识别工具',
        'hydra' => '网络登录破解工具',
        'sqlmap' => '自动化SQL注入工具',
        'wafw00f' => 'WAF指纹识别工具',
        'semgrep' => '快速静态分析工具',
        'rad' => '浏览器爬虫',
        'xray' => '安全评估工具',
        'nuclei' => '基于模板的漏洞扫描器',
        'vulmap' => 'Web漏洞扫描和验证工具',
        'dismap' => 'Web指纹识别工具',
        'crawlergo' => '浏览器爬虫',
        'murphysec' => '代码安全检测工具',
        'dirmap' => '目录扫描工具'
    ];

    protected function configure()
    {
        $this->setName('install')
            ->addArgument('tool', Argument::OPTIONAL, '要安装的工具名称')
            ->setDescription('QingScan 工具安装命令');
    }

    protected function execute(Input $input, Output $output): void
    {
        $tool = $input->getArgument('tool');
        
        if (empty($tool)) {
            $this->showHelp($output);
            return;
        }
        
        switch ($tool) {
            case 'help':
            case '-h':
            case '--help':
                $this->showHelp($output);
                break;
                
            case 'list':
                $this->showToolList($output);
                break;
                
            case 'all':
                $this->installAllTools($output);
                break;
                
            default:
                if (array_key_exists($tool, $this->tools)) {
                    $this->installTool($output, $tool);
                } else {
                    $output->writeln($this->colorize("<error>未知工具: {$tool}</error>", 'red'));
                    $this->showToolList($output);
                }
                break;
        }
    }
    
    /**
     * 显示帮助信息
     */
    protected function showHelp(Output $output): void
    {
        $output->writeln($this->colorize("QingScan 工具安装脚本", 'blue'));
        $output->writeln("用法: php think install [选项]");
        $output->writeln("");
        $output->writeln("选项:");
        $output->writeln("  all           安装所有工具");
        $output->writeln("  list          显示可安装的工具列表");
        $output->writeln("  <工具名>      安装指定工具");
        $output->writeln("  help          显示此帮助信息");
        $output->writeln("");
        $output->writeln("示例:");
        $output->writeln("  php think install all        # 安装所有工具");
        $output->writeln("  php think install nmap       # 安装nmap工具");
        $output->writeln("  php think install list       # 显示工具列表");
    }
    
    /**
     * 显示工具列表
     */
    protected function showToolList(Output $output): void
    {
        $output->writeln($this->colorize("可安装的工具列表:", 'blue'));
        $index = 1;
        foreach ($this->tools as $tool => $description) {
            $output->writeln(sprintf("%d. %-12s - %s", $index++, $tool, $description));
        }
        $output->writeln("");
        $output->writeln("使用方法: php think install <工具名> 或 php think install all");
    }
    
    /**
     * 安装所有工具
     */
    protected function installAllTools(Output $output): void
    {
        $output->writeln($this->colorize("开始安装所有工具...", 'blue'));
        
        foreach (array_keys($this->tools) as $tool) {
            $this->installTool($output, $tool);
        }
        
        $output->writeln($this->colorize("所有工具安装完成!", 'green'));
    }
    
    /**
     * 安装指定工具
     */
    protected function installTool(Output $output, string $tool): void
    {
        $output->writeln($this->colorize("正在安装 {$tool}...", 'blue'));
        
        // 检查工具是否已安装
        if ($this->isToolInstalled($tool)) {
            $output->writeln($this->colorize("{$tool} 已经安装", 'yellow'));
            return;
        }
        
        // 根据工具类型执行安装
        $method = 'install' . ucfirst($tool);
        if (method_exists($this, $method)) {
            $this->$method($output);
        } else {
            // 默认安装方法
            $this->installDefault($output, $tool);
        }
    }
    
    /**
     * 检查工具是否已安装
     */
    protected function isToolInstalled(string $tool): bool
    {
        // 对于系统命令工具，检查命令是否存在
        $systemTools = ['nmap', 'whatweb', 'hydra', 'sqlmap', 'wafw00f', 'semgrep', 'murphysec'];
        if (in_array($tool, $systemTools)) {
            $result = shell_exec("which {$tool} 2>/dev/null");
            return !empty($result);
        }
        
        // 对于需要下载的工具，检查目录是否存在
        $toolPaths = [
            'rad' => './extend/tools/rad/rad_linux_amd64',
            'xray' => './extend/tools/xray/xray_linux_amd64',
            'nuclei' => './extend/tools/nuclei/nuclei',
            'vulmap' => './extend/tools/vulmap/vulmap.py',
            'dismap' => './extend/tools/dismap/dismap',
            'crawlergo' => './extend/tools/crawlergo/crawlergo',
            'dirmap' => './extend/tools/dirmap/dirmap.py'
        ];
        
        if (isset($toolPaths[$tool])) {
            return file_exists($toolPaths[$tool]);
        }
        
        return false;
    }
    
    /**
     * 默认安装方法
     */
    protected function installDefault(Output $output, string $tool): void
    {
        $output->writeln($this->colorize("{$tool} 安装方法未实现", 'yellow'));
    }
    
    /**
     * 添加颜色到文本
     */
    protected function colorize(string $text, string $color): string
    {
        if (isset(self::COLORS[$color])) {
            return "\033[" . self::COLORS[$color] . $text . "\033[" . self::COLORS['nc'];
        }
        return $text;
    }
    
    /**
     * 安装 Nmap
     */
    protected function installNmap(Output $output): void
    {
        $os = $this->detectOS();
        if ($os === 'unknown') {
            $output->writeln($this->colorize("不支持的操作系统", 'red'));
            return;
        }
        
        $command = '';
        switch ($os) {
            case 'debian':
                $command = 'sudo apt-get update && sudo apt-get install -y nmap';
                break;
            case 'redhat':
                $command = 'sudo yum install -y nmap';
                break;
            case 'fedora':
                $command = 'sudo dnf install -y nmap';
                break;
        }
        
        if (!empty($command)) {
            $output->writeln($this->colorize("执行命令: {$command}", 'blue'));
            system($command, $result);
            if ($result === 0) {
                $output->writeln($this->colorize("Nmap 安装成功", 'green'));
            } else {
                $output->writeln($this->colorize("Nmap 安装失败", 'red'));
            }
        }
    }
    
    /**
     * 安装 WhatWeb
     */
    protected function installWhatweb(Output $output): void
    {
        $os = $this->detectOS();
        if ($os === 'unknown') {
            $output->writeln($this->colorize("不支持的操作系统", 'red'));
            return;
        }
        
        $command = '';
        switch ($os) {
            case 'debian':
                $command = 'sudo apt-get update && sudo apt-get install -y whatweb';
                break;
            case 'redhat':
            case 'fedora':
                // RedHat/Fedora 需要从源码安装
                $this->installWhatwebFromSource($output);
                return;
        }
        
        if (!empty($command)) {
            $output->writeln($this->colorize("执行命令: {$command}", 'blue'));
            system($command, $result);
            if ($result === 0) {
                $output->writeln($this->colorize("WhatWeb 安装成功", 'green'));
            } else {
                $output->writeln($this->colorize("WhatWeb 安装失败", 'red'));
            }
        }
    }
    
    /**
     * 从源码安装 WhatWeb
     */
    protected function installWhatwebFromSource(Output $output): void
    {
        $os = $this->detectOS();
        $output->writeln($this->colorize("从源码安装 WhatWeb...", 'blue'));
        
        // 安装依赖
        $depCommand = '';
        switch ($os) {
            case 'redhat':
                $depCommand = 'sudo yum install -y ruby ruby-devel';
                break;
            case 'fedora':
                $depCommand = 'sudo dnf install -y ruby ruby-devel';
                break;
        }
        
        if (!empty($depCommand)) {
            system($depCommand);
        }
        
        // 克隆源码并安装
        system('git clone https://github.com/urbanadventurer/WhatWeb.git /tmp/WhatWeb');
        chdir('/tmp/WhatWeb');
        system('sudo gem install bundler');
        system('bundle install');
        system('sudo ln -sf /tmp/WhatWeb/whatweb /usr/local/bin/whatweb');
        
        $output->writeln($this->colorize("WhatWeb 源码安装完成", 'green'));
    }
    
    /**
     * 安装 Hydra
     */
    protected function installHydra(Output $output): void
    {
        $os = $this->detectOS();
        if ($os === 'unknown') {
            $output->writeln($this->colorize("不支持的操作系统", 'red'));
            return;
        }
        
        $command = '';
        switch ($os) {
            case 'debian':
                $command = 'sudo apt-get update && sudo apt-get install -y hydra';
                break;
            case 'redhat':
                $command = 'sudo yum install -y hydra';
                break;
            case 'fedora':
                $command = 'sudo dnf install -y hydra';
                break;
        }
        
        if (!empty($command)) {
            $output->writeln($this->colorize("执行命令: {$command}", 'blue'));
            system($command, $result);
            if ($result === 0) {
                $output->writeln($this->colorize("Hydra 安装成功", 'green'));
            } else {
                $output->writeln($this->colorize("Hydra 安装失败", 'red'));
            }
        }
    }
    
    /**
     * 安装 SQLMap
     */
    protected function installSqlmap(Output $output): void
    {
        $output->writeln($this->colorize("使用pip安装 SQLMap...", 'blue'));
        system('pip3 install sqlmap', $result);
        if ($result === 0) {
            $output->writeln($this->colorize("SQLMap 安装成功", 'green'));
        } else {
            $output->writeln($this->colorize("SQLMap 安装失败", 'red'));
        }
    }
    
    /**
     * 安装 Wafw00f
     */
    protected function installWafw00f(Output $output): void
    {
        $output->writeln($this->colorize("使用pip安装 Wafw00f...", 'blue'));
        system('pip3 install wafw00f', $result);
        if ($result === 0) {
            $output->writeln($this->colorize("Wafw00f 安装成功", 'green'));
        } else {
            $output->writeln($this->colorize("Wafw00f 安装失败", 'red'));
        }
    }
    
    /**
     * 安装 Semgrep
     */
    protected function installSemgrep(Output $output): void
    {
        $output->writeln($this->colorize("使用pip安装 Semgrep...", 'blue'));
        system('pip3 install semgrep', $result);
        if ($result === 0) {
            $output->writeln($this->colorize("Semgrep 安装成功", 'green'));
        } else {
            $output->writeln($this->colorize("Semgrep 安装失败", 'red'));
        }
    }
    
    /**
     * 安装 Murphysec
     */
    protected function installMurphysec(Output $output): void
    {
        $output->writeln($this->colorize("使用pip安装 Murphysec...", 'blue'));
        system('pip3 install murphysec', $result);
        if ($result === 0) {
            $output->writeln($this->colorize("Murphysec 安装成功", 'green'));
        } else {
            $output->writeln($this->colorize("Murphysec 安装失败", 'red'));
        }
    }
    
    /**
     * 安装 Rad
     */
    protected function installRad(Output $output): void
    {
        $output->writeln($this->colorize("安装 Rad 浏览器爬虫...", 'blue'));
        
        // 创建目标目录
        $radPath = './extend/tools/rad';
        if (!is_dir($radPath)) {
            mkdir($radPath, 0755, true);
        }
        
        // 下载RAD
        chdir($radPath);
        system('wget https://github.com/zcgonvh/Rad/releases/latest/download/rad_linux_amd64.zip');
        system('unzip rad_linux_amd64.zip');
        system('chmod +x rad_linux_amd64');
        
        // 检查是否安装成功
        if (file_exists('rad_linux_amd64')) {
            $output->writeln($this->colorize("Rad 安装成功", 'green'));
        } else {
            $output->writeln($this->colorize("Rad 安装失败", 'red'));
        }
    }
    
    /**
     * 安装 Xray
     */
    protected function installXray(Output $output): void
    {
        $output->writeln($this->colorize("安装 Xray 安全评估工具...", 'blue'));
        
        // 创建目标目录
        $xrayPath = './extend/tools/xray';
        if (!is_dir($xrayPath)) {
            mkdir($xrayPath, 0755, true);
        }
        
        // 下载XRAY
        chdir($xrayPath);
        system('wget https://github.com/chaitin/xray/releases/latest/download/xray_linux_amd64.zip');
        system('unzip xray_linux_amd64.zip');
        system('chmod +x xray_linux_amd64');
        
        // 检查是否安装成功
        if (file_exists('xray_linux_amd64')) {
            $output->writeln($this->colorize("Xray 安装成功", 'green'));
        } else {
            $output->writeln($this->colorize("Xray 安装失败", 'red'));
        }
    }
    
    /**
     * 安装 Nuclei
     */
    protected function installNuclei(Output $output): void
    {
        $output->writeln($this->colorize("安装 Nuclei 漏洞扫描器...", 'blue'));
        
        // 创建目标目录
        $nucleiPath = './extend/tools/nuclei';
        if (!is_dir($nucleiPath)) {
            mkdir($nucleiPath, 0755, true);
        }
        
        // 下载Nuclei
        chdir($nucleiPath);
        system('wget https://github.com/projectdiscovery/nuclei/releases/latest/download/nuclei-linux-amd64.tar.gz');
        system('tar -xzf nuclei-linux-amd64.tar.gz');
        system('chmod +x nuclei');
        
        // 安装模板
        system('./nuclei -update-templates');
        
        // 检查是否安装成功
        if (file_exists('nuclei')) {
            $output->writeln($this->colorize("Nuclei 安装成功", 'green'));
        } else {
            $output->writeln($this->colorize("Nuclei 安装失败", 'red'));
        }
    }
    
    /**
     * 安装 Vulmap
     */
    protected function installVulmap(Output $output): void
    {
        $output->writeln($this->colorize("安装 Vulmap 漏洞扫描工具...", 'blue'));
        
        // 创建目标目录
        $vulmapPath = './extend/tools/vulmap';
        if (!is_dir($vulmapPath)) {
            mkdir($vulmapPath, 0755, true);
        }
        
        // 克隆项目
        chdir('./extend/tools');
        system('git clone https://github.com/zhzyker/vulmap.git vulmap');
        chdir('vulmap');
        
        // 安装依赖
        system('pip3 install -r requirements.txt');
        
        // 检查是否安装成功
        if (file_exists('vulmap.py')) {
            $output->writeln($this->colorize("Vulmap 安装成功", 'green'));
        } else {
            $output->writeln($this->colorize("Vulmap 安装失败", 'red'));
        }
    }
    
    /**
     * 安装 Dismap
     */
    protected function installDismap(Output $output): void
    {
        $output->writeln($this->colorize("安装 Dismap 指纹识别工具...", 'blue'));
        
        // 创建目标目录
        $dismapPath = './extend/tools/dismap';
        if (!is_dir($dismapPath)) {
            mkdir($dismapPath, 0755, true);
        }
        
        // 下载Dismap
        chdir($dismapPath);
        system('wget https://github.com/zhzyker/dismap/releases/latest/download/dismap-linux-amd64.zip');
        system('unzip dismap-linux-amd64.zip');
        system('chmod +x dismap');
        
        // 检查是否安装成功
        if (file_exists('dismap')) {
            $output->writeln($this->colorize("Dismap 安装成功", 'green'));
        } else {
            $output->writeln($this->colorize("Dismap 安装失败", 'red'));
        }
    }
    
    /**
     * 安装 Crawlergo
     */
    protected function installCrawlergo(Output $output): void
    {
        $output->writeln($this->colorize("安装 Crawlergo 浏览器爬虫...", 'blue'));
        
        // 创建目标目录
        $crawlergoPath = './extend/tools/crawlergo';
        if (!is_dir($crawlergoPath)) {
            mkdir($crawlergoPath, 0755, true);
        }
        
        // 下载Crawlergo
        chdir($crawlergoPath);
        system('wget https://github.com/0Kee-Team/crawlergo/releases/latest/download/crawlergo_linux_amd64.zip');
        system('unzip crawlergo_linux_amd64.zip');
        system('chmod +x crawlergo');
        
        // 检查是否安装成功
        if (file_exists('crawlergo')) {
            $output->writeln($this->colorize("Crawlergo 安装成功", 'green'));
        } else {
            $output->writeln($this->colorize("Crawlergo 安装失败", 'red'));
        }
    }
    
    /**
     * 安装 Dirmap
     */
    protected function installDirmap(Output $output): void
    {
        $output->writeln($this->colorize("安装 Dirmap 目录扫描工具...", 'blue'));
        
        // 创建目标目录
        $dirmapPath = './extend/tools/dirmap';
        if (!is_dir($dirmapPath)) {
            mkdir($dirmapPath, 0755, true);
        }
        
        // 克隆项目
        chdir('./extend/tools');
        system('git clone https://github.com/H4ckForJob/dirmap.git dirmap');
        chdir('dirmap');
        
        // 安装依赖
        system('pip3 install -r requirements.txt');
        
        // 检查是否安装成功
        if (file_exists('dirmap.py')) {
            $output->writeln($this->colorize("Dirmap 安装成功", 'green'));
        } else {
            $output->writeln($this->colorize("Dirmap 安装失败", 'red'));
        }
    }
    
    /**
     * 检测操作系统类型
     */
    protected function detectOS(): string
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return 'windows';
        }
        
        // 检查包管理器
        if (shell_exec('which apt-get 2>/dev/null')) {
            return 'debian';
        } elseif (shell_exec('which yum 2>/dev/null')) {
            return 'redhat';
        } elseif (shell_exec('which dnf 2>/dev/null')) {
            return 'fedora';
        }
        
        return 'unknown';
    }
}