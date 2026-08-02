<?php

namespace app\model;

use think\facade\Db;

class ToolsCheckModel extends BaseModel
{
    /** 已提供内置引擎的工具：外部工具未安装时由内置引擎兜底 */
    private const BUILTIN_TOOLS = [
        'port_scan', 'subdomain', 'dir_scan', 'finger', 'asset_finger',
        'sql_inject', 'weak_pass', 'gen_vuln', 'web_vuln', 'vul_verify',
        'crawler', 'spider', 'waf', 'code_audit',
    ];

    /**
     * 检查工具是否可用：外部工具已安装，或已提供内置引擎兜底
     * @param string $toolName 工具名称
     * @return bool 是否可用
     */
    public static function checkToolInstalled(string $toolName): bool
    {
        if (self::isToolAvailable($toolName)) {
            return true;
        }
        return in_array($toolName, self::BUILTIN_TOOLS, true);
    }

    /**
     * 外部工具是否已安装/可配置（不含内置引擎兜底）
     * 模型方法内据此判断：外部工具可用则优先使用，否则走内置引擎
     * @param string $toolName 工具名称
     * @return bool 外部工具是否可用
     */
    public static function isToolAvailable(string $toolName): bool
    {
        $methods = [
            'crawler' => 'checkCrawler',
            'web_vuln' => 'checkWebVuln',
            'gen_vuln' => 'checkGenVuln',
            'vul_verify' => 'checkVulVerify',
            'asset_finger' => 'checkAssetFinger',
            'finger' => 'checkFinger',
            'subdomain' => 'checkSubdomain',
            'weak_pass' => 'checkWeakPass',
            'sql_inject' => 'checkSqlInject',
            'dir_scan' => 'checkDirScan',
            'fortify' => 'checkFortify',
            'code_audit' => 'checkCodeAudit',
            'murphysec' => 'checkMurphysec',
            'codeql' => 'checkCodeql',
            'spider' => 'checkSpider',
            'waf' => 'checkWaf',
            'port_scan' => 'checkPortScan',
            'google' => 'checkGoogle'
        ];

        // 如果没有对应的检查方法，默认认为外部工具可用
        if (!isset($methods[$toolName])) {
            return true;
        }

        $method = $methods[$toolName];
        return self::$method();
    }

    /**
     * 获取工具安装引导信息
     * @param string $toolName 工具名称
     * @return string 安装引导信息
     */
    public static function getToolInstallGuide(string $toolName): string
    {
        $guides = [
            'crawler' => "浏览器爬虫（可选用 Rad 等外部爬虫引擎）。\n" .
                "安装文档请参考: https://github.com/zcgonvh/Rad\n" .
                "或查看项目文档: docs/tools/rad.md",

            'web_vuln' => "Web漏洞检测（可选用 XRAY 等外部检测工具）。\n" .
                "安装文档请参考: https://github.com/chaitin/xray\n" .
                "或查看项目文档: docs/tools/xray.md",

            'gen_vuln' => "通用漏洞扫描（可选用 Nuclei 等外部扫描器）。\n" .
                "安装文档请参考: https://github.com/projectdiscovery/nuclei\n" .
                "或使用命令安装: go install -v github.com/projectdiscovery/nuclei/v2/cmd/nuclei@latest\n" .
                "或查看项目文档: docs/tools/nuclei.md",

            'vul_verify' => "漏洞验证（可选用 Vulmap 等外部验证工具）。\n" .
                "安装文档请参考: https://github.com/zhzyker/vulmap\n" .
                "或查看项目文档: docs/tools/vulmap.md",

            'asset_finger' => "资产指纹识别（可选用 Dismap 等外部指纹工具）。\n" .
                "安装文档请参考: https://github.com/zhzyker/dismap\n" .
                "或查看项目文档: docs/tools/dismap.md",

            'finger' => "Web指纹识别（可选用 WhatWeb 等外部指纹工具）。\n" .
                "安装命令 (Ubuntu/Debian): sudo apt-get install whatweb\n" .
                "安装命令 (CentOS/RHEL): sudo yum install whatweb\n" .
                "或查看项目文档: docs/tools/whatweb.md",


            'subdomain' => "子域名枚举（可选用 OneForAll 等外部收集工具）。\n" .
                "安装文档请参考: https://github.com/shmilylty/OneForAll\n" .
                "或查看项目文档: docs/tools/oneforall.md",

            'weak_pass' => "弱口令爆破（可选用 Hydra 等外部爆破工具）。\n" .
                "安装命令 (Ubuntu/Debian): sudo apt-get install hydra\n" .
                "安装命令 (CentOS/RHEL): sudo yum install hydra\n" .
                "或查看项目文档: docs/tools/hydra.md",

            'sql_inject' => "SQL注入检测（可选用 SQLMap 等外部工具）。\n" .
                "安装文档请参考: https://github.com/sqlmapproject/sqlmap\n" .
                "或使用命令安装: git clone --depth 1 https://github.com/sqlmapproject/sqlmap.git\n" .
                "或查看项目文档: docs/tools/sqlmap.md",

            'dir_scan' => "目录扫描（可选用 Dirmap 等外部工具）。\n" .
                "或查看项目文档: docs/tools/dirmap.md",

            'fortify' => "Fortify是一款商业静态代码分析工具。\n" .
                "需要商业许可证才能使用。\n" .
                "或查看项目文档: docs/tools/fortify.md",

            'code_audit' => "代码审计（可选用 Semgrep 等外部静态分析工具）。\n" .
                "安装命令: pip install semgrep\n" .
                "或查看项目文档: docs/tools/semgrep.md",

            'murphysec' => "Murphysec是一款代码安全检测工具。\n" .
                "安装文档请参考: https://www.murphysec.com/docs/cli/install.html\n" .
                "或查看项目文档: docs/tools/murphysec.md",

            'codeql' => "CodeQL是一款语义代码分析引擎。\n" .
                "安装文档请参考: https://codeql.github.com/docs/codeql-cli/\n" .
                "或查看项目文档: docs/tools/codeql.md",

            'spider' => "爬虫抓取（可选用 Crawlergo 等外部爬虫）。\n" .
                "安装文档请参考: https://github.com/0Kee-Team/crawlergo\n" .
                "或查看项目文档: docs/tools/crawlergo.md",

            'waf' => "WAF识别（可选用 Wafw00f 等外部识别工具）。\n" .
                "安装命令: pip install wafw00f\n" .
                "或查看项目文档: docs/tools/wafw00f.md",

            'port_scan' => "端口扫描（可选用 Nmap 等外部扫描工具）。\n" .
                "安装命令 (Ubuntu/Debian): sudo apt-get install nmap\n" .
                "安装命令 (CentOS/RHEL): sudo yum install nmap\n" .
                "或查看项目文档: docs/tools/nmap.md",

            'google' => "Google相关功能依赖Google API。\n" .
                "请确保网络可以访问Google服务。\n" .
                "或查看项目文档: docs/tools/google.md"
        ];

        return $guides[$toolName] ?? "暂无该工具的安装说明，请查看相关文档。";
    }

    /**
     * 安全记录日志的函数
     */
    private static function log($message)
    {
        // 在CLI环境中，我们只输出到标准错误
        if (PHP_SAPI === 'cli') {
            error_log(is_array($message) ? json_encode($message) : $message);
        } // 在Web环境中，可以使用系统的addlog函数
        else if (function_exists('addlog')) {
            addlog($message);
        }
    }

    /**
     * 检查爬虫工具（可选用外部浏览器爬虫）
     * @return bool
     */
    private static function checkCrawler(): bool
    {
        $radPath = trim(shell_exec('pwd') ). '/extend/tools/rad/';
        $chromeExists = file_exists("/usr/bin/google-chrome");

        if (!file_exists($radPath) || !$chromeExists) {
            self::log(["工具检查失败: RAD 未安装或依赖环境缺失", $radPath, "/usr/bin/google-chrome"]);
            return false;
        }

        return true;
    }

    /**
     * 检查Web漏洞检测工具（可选用外部检测工具）
     * @return bool
     */
    private static function checkWebVuln(): bool
    {
        $xrayPath = trim(shell_exec('pwd')) . '/extend/tools/xray/';

        if (!file_exists($xrayPath)) {
            self::log(["工具检查失败: XRAY 未安装", $xrayPath]);
            return false;
        }

        return true;
    }

    /**
     * 检查通用漏洞扫描工具（可选用外部扫描器）
     * @return bool
     */
    private static function checkGenVuln(): bool
    {
        $nucleiPath = "./extend/tools/nuclei/";

        if (!file_exists($nucleiPath)) {
            self::log(["工具检查失败: Nuclei 未安装", $nucleiPath]);
            return false;
        }

        return true;
    }

    /**
     * 检查漏洞验证工具（可选用外部验证工具）
     * @return bool
     */
    private static function checkVulVerify(): bool
    {
        $vulmapPath = "./extend/tools/vulmap/";

        if (!file_exists($vulmapPath)) {
            self::log(["工具检查失败: Vulmap 未安装", $vulmapPath]);
            return false;
        }

        return true;
    }

    /**
     * 检查资产指纹识别工具（可选用外部指纹工具）
     * @return bool
     */
    private static function checkAssetFinger(): bool
    {
        $dismapPath = "./extend/tools/dismap/";

        if (!file_exists($dismapPath)) {
            self::log(["工具检查失败: Dismap 未安装", $dismapPath]);
            return false;
        }

        return true;
    }

    /**
     * 检查Web指纹识别工具（可选用外部指纹工具）
     * @return bool
     */
    private static function checkFinger(): bool
    {
        // Whatweb 是系统命令，检查是否存在
        $result = shell_exec("which whatweb 2>/dev/null");
        if (empty($result)) {
            self::log(["工具检查失败: Whatweb 未安装"]);
            return false;
        }

        return true;
    }


    /**
     * 检查子域名枚举工具（可选用外部收集工具）
     * @return bool
     */
    private static function checkSubdomain(): bool
    {
        $oneforallPath = '/data/tools/oneforall/';

        if (!file_exists($oneforallPath)) {
            self::log(["工具检查失败: OneForAll 未安装", $oneforallPath]);
            return false;
        }

        return true;
    }

    /**
     * 检查弱口令爆破工具（可选用外部爆破工具）
     * @return bool
     */
    private static function checkWeakPass(): bool
    {
        $hydraPath = '/data/tools/hydra/';

        if (!file_exists($hydraPath)) {
            self::log(["工具检查失败: Hydra 未安装", $hydraPath]);
            return false;
        }

        return true;
    }

    /**
     * 检查SQL注入检测工具（可选用外部注入检测工具）
     * @return bool
     */
    private static function checkSqlInject(): bool
    {
        // Sqlmap 是系统命令，检查是否存在
        $result = shell_exec("which sqlmap 2>/dev/null");
        if (empty($result)) {
            self::log(["工具检查失败: Sqlmap 未安装"]);
            return false;
        }

        return true;
    }

    /**
     * 检查端口扫描工具（可选用外部扫描工具）
     * @return bool
     */
    private static function checkPortScan(): bool
    {
        // Nmap 是系统命令，检查是否存在
        $result = shell_exec("which nmap 2>/dev/null");
        if (empty($result)) {
            self::log(["工具检查失败: Nmap 未安装"]);
            return false;
        }

        return true;
    }

    /**
     * 检查目录扫描工具（可选用外部目录扫描工具）
     * @return bool
     */
    private static function checkDirScan(): bool
    {
        $dirmapPath = "./extend/tools/dirmap/";

        if (!file_exists($dirmapPath)) {
            self::log(["工具检查失败: Dirmap 未安装", $dirmapPath]);
            return false;
        }

        return true;
    }

    /**
     * 检查Fortify工具
     * @return bool
     */
    private static function checkFortify(): bool
    {
        // Fortify是商业工具，检查目录是否存在
        $codePath = trim(shell_exec('pwd')) . "/data/codeCheck";
        $fortifyRetDir = trim(shell_exec('pwd')) . "/data/fortify_result";

        if (!file_exists($codePath) || !file_exists($fortifyRetDir)) {
            self::log(["工具检查失败: Fortify 环境未配置", $codePath, $fortifyRetDir]);
            return false;
        }

        return true;
    }

    /**
     * 检查代码审计工具（可选用外部静态分析工具）
     * @return bool
     */
    private static function checkCodeAudit(): bool
    {
        // Semgrep 是系统命令，检查是否存在
        $result = shell_exec("which semgrep 2>/dev/null");
        if (empty($result)) {
            self::log(["工具检查失败: Semgrep 未安装"]);
            return false;
        }

        return true;
    }

    /**
     * 检查Murphysec工具
     * @return bool
     */
    private static function checkMurphysec(): bool
    {
        // Murphysec 是系统命令，检查是否存在
        $result = shell_exec("which murphysec 2>/dev/null");
        if (empty($result)) {
            self::log(["工具检查失败: Murphysec 未安装"]);
            return false;
        }

        return true;
    }

    /**
     * 检查Codeql工具
     * @return bool
     */
    private static function checkCodeql(): bool
    {
        // CodeQL 是系统命令，检查是否存在
        $result = shell_exec("which codeql 2>/dev/null");
        if (empty($result)) {
            self::log(["工具检查失败: CodeQL 未安装"]);
            return false;
        }

        return true;
    }

    /**
     * 检查爬虫抓取工具（可选用外部爬虫）
     * @return bool
     */
    private static function checkSpider(): bool
    {
        $crawlergoPath = "./extend/tools/crawlergo/";

        if (!file_exists($crawlergoPath)) {
            self::log(["工具检查失败: Crawlergo 未安装", $crawlergoPath]);
            return false;
        }

        return true;
    }

    /**
     * 检查WAF识别工具（可选用外部识别工具）
     * @return bool
     */
    private static function checkWaf(): bool
    {
        // Wafw00f 是系统命令，检查是否存在
        $result = shell_exec("which wafw00f 2>/dev/null");
        if (empty($result)) {
            self::log(["工具检查失败: Wafw00f 未安装"]);
            return false;
        }

        return true;
    }

    /**
     * 检查Google工具（这里指Google相关的功能）
     * @return bool
     */
    private static function checkGoogle(): bool
    {
        // Google功能主要是API调用，暂时返回true
        return true;
    }
}