<?php

namespace app\model;

use think\facade\Db;

class ToolsCheckModel extends BaseModel
{
    /**
     * 检查工具是否已安装
     * @param string $toolName 工具名称
     * @return bool 是否已安装
     */
    public static function checkToolInstalled(string $toolName): bool
    {
        $methods = [
            'rad' => 'checkRad',
            'xray' => 'checkXray',
            'nuclei' => 'checkNuclei',
            'vulmap' => 'checkVulmap',
            'dismap' => 'checkDismap',
            'whatweb' => 'checkWhatweb',
            'awvs' => 'checkAwvs',
            'fofa' => 'checkFofa',
            'oneforall' => 'checkOneForAll',
            'hydra' => 'checkHydra',
            'sqlmap' => 'checkSqlmap',
            'dirmap' => 'checkDirmap',
            'fortify' => 'checkFortify',
            'semgrep' => 'checkSemgrep',
            'murphysec' => 'checkMurphysec',
            'codeql' => 'checkCodeql',
            'crawlergo' => 'checkCrawlergo',
            'wafw00f' => 'checkWafw00f',
            'nmap' => 'checkNmap',
            'google' => 'checkGoogle'
        ];

        // 如果没有对应的检查方法，默认返回true
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
            'rad' => "RAD是一款浏览器爬虫，用于爬取Web应用程序。\n" .
                "安装文档请参考: https://github.com/zcgonvh/Rad\n" .
                "或查看项目文档: docs/tools/rad.md",

            'xray' => "XRAY是一款功能强大的安全评估工具。\n" .
                "安装文档请参考: https://github.com/chaitin/xray\n" .
                "或查看项目文档: docs/tools/xray.md",

            'nuclei' => "Nuclei是一款基于模板的漏洞扫描器。\n" .
                "安装文档请参考: https://github.com/projectdiscovery/nuclei\n" .
                "或使用命令安装: go install -v github.com/projectdiscovery/nuclei/v2/cmd/nuclei@latest\n" .
                "或查看项目文档: docs/tools/nuclei.md",

            'vulmap' => "Vulmap是一款Web漏洞扫描和验证工具。\n" .
                "安装文档请参考: https://github.com/zhzyker/vulmap\n" .
                "或查看项目文档: docs/tools/vulmap.md",

            'dismap' => "Dismap是一款Web指纹识别工具。\n" .
                "安装文档请参考: https://github.com/zhzyker/dismap\n" .
                "或查看项目文档: docs/tools/dismap.md",

            'whatweb' => "WhatWeb是一款Web应用程序指纹识别工具。\n" .
                "安装命令 (Ubuntu/Debian): sudo apt-get install whatweb\n" .
                "安装命令 (CentOS/RHEL): sudo yum install whatweb\n" .
                "或查看项目文档: docs/tools/whatweb.md",

            'awvs' => "AWVS是一款商业Web应用漏洞扫描器。\n" .
                "需要配置AWVS服务器地址和API密钥。\n" .
                "请在系统配置中设置awvs_url和awvs_token参数。\n" .
                "或查看项目文档: docs/tools/awvs.md",

            'fofa' => "FOFA是一款网络空间资产搜索引擎。\n" .
                "需要配置FOFA账号邮箱和API密钥。\n" .
                "请在系统配置中设置fofa_email和fofa_key参数。\n" .
                "或查看项目文档: docs/tools/fofa.md",

            'oneforall' => "OneForAll是一款功能强大的子域收集工具。\n" .
                "安装文档请参考: https://github.com/shmilylty/OneForAll\n" .
                "或查看项目文档: docs/tools/oneforall.md",

            'hydra' => "Hydra是一款网络登录破解工具。\n" .
                "安装命令 (Ubuntu/Debian): sudo apt-get install hydra\n" .
                "安装命令 (CentOS/RHEL): sudo yum install hydra\n" .
                "或查看项目文档: docs/tools/hydra.md",

            'sqlmap' => "SQLMap是一款自动化SQL注入工具。\n" .
                "安装文档请参考: https://github.com/sqlmapproject/sqlmap\n" .
                "或使用命令安装: git clone --depth 1 https://github.com/sqlmapproject/sqlmap.git\n" .
                "或查看项目文档: docs/tools/sqlmap.md",

            'dirmap' => "Dirmap是一款目录扫描工具。\n" .
                "或查看项目文档: docs/tools/dirmap.md",

            'fortify' => "Fortify是一款商业静态代码分析工具。\n" .
                "需要商业许可证才能使用。\n" .
                "或查看项目文档: docs/tools/fortify.md",

            'semgrep' => "Semgrep是一款快速静态分析工具。\n" .
                "安装命令: pip install semgrep\n" .
                "或查看项目文档: docs/tools/semgrep.md",

            'murphysec' => "Murphysec是一款代码安全检测工具。\n" .
                "安装文档请参考: https://www.murphysec.com/docs/cli/install.html\n" .
                "或查看项目文档: docs/tools/murphysec.md",

            'codeql' => "CodeQL是一款语义代码分析引擎。\n" .
                "安装文档请参考: https://codeql.github.com/docs/codeql-cli/\n" .
                "或查看项目文档: docs/tools/codeql.md",

            'crawlergo' => "Crawlergo是一款浏览器爬虫。\n" .
                "安装文档请参考: https://github.com/0Kee-Team/crawlergo\n" .
                "或查看项目文档: docs/tools/crawlergo.md",

            'wafw00f' => "Wafw00f是一款WAF指纹识别工具。\n" .
                "安装命令: pip install wafw00f\n" .
                "或查看项目文档: docs/tools/wafw00f.md",

            'nmap' => "Nmap是一款网络发现和安全审计工具。\n" .
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
     * 检查RAD工具
     * @return bool
     */
    private static function checkRad(): bool
    {
        $radPath = trim(`pwd`) . '/extend/tools/rad/';
        $chromeExists = file_exists("/usr/bin/google-chrome");

        if (!file_exists($radPath) || !$chromeExists) {
            self::log(["工具检查失败: RAD 未安装或依赖环境缺失", $radPath, "/usr/bin/google-chrome"]);
            return false;
        }

        return true;
    }

    /**
     * 检查XRAY工具
     * @return bool
     */
    private static function checkXray(): bool
    {
        $xrayPath = trim(`pwd`) . '/extend/tools/xray/';

        if (!file_exists($xrayPath)) {
            self::log(["工具检查失败: XRAY 未安装", $xrayPath]);
            return false;
        }

        return true;
    }

    /**
     * 检查Nuclei工具
     * @return bool
     */
    private static function checkNuclei(): bool
    {
        $nucleiPath = "./extend/tools/nuclei/";

        if (!file_exists($nucleiPath)) {
            self::log(["工具检查失败: Nuclei 未安装", $nucleiPath]);
            return false;
        }

        return true;
    }

    /**
     * 检查Vulmap工具
     * @return bool
     */
    private static function checkVulmap(): bool
    {
        $vulmapPath = "./extend/tools/vulmap/";

        if (!file_exists($vulmapPath)) {
            self::log(["工具检查失败: Vulmap 未安装", $vulmapPath]);
            return false;
        }

        return true;
    }

    /**
     * 检查Dismap工具
     * @return bool
     */
    private static function checkDismap(): bool
    {
        $dismapPath = "./extend/tools/dismap/";

        if (!file_exists($dismapPath)) {
            self::log(["工具检查失败: Dismap 未安装", $dismapPath]);
            return false;
        }

        return true;
    }

    /**
     * 检查Whatweb工具
     * @return bool
     */
    private static function checkWhatweb(): bool
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
     * 检查Awvs工具
     * @return bool
     */
    private static function checkAwvs(): bool
    {
        // AWVS是远程API，检查配置是否存在
        try {
            $awvsUrl = Db::table('config')->where(['name' => 'awvs_url'])->value('value');
            $awvsToken = Db::table('config')->where(['name' => 'awvs_token'])->value('value');

            if (empty($awvsUrl) || empty($awvsToken)) {
                self::log(["工具检查失败: AWVS 配置缺失"]);
                return false;
            }
        } catch (\Exception $e) {
            self::log(["工具检查失败: AWVS 配置查询异常", $e->getMessage()]);
            return false;
        }

        return true;
    }

    /**
     * 检查Fofa工具
     * @return bool
     */
    private static function checkFofa(): bool
    {
        // FOFA是远程API，检查配置是否存在
        try {
            $fofaEmail = Db::table('config')->where(['name' => 'fofa_email'])->value('value');
            $fofaKey = Db::table('config')->where(['name' => 'fofa_key'])->value('value');

            if (empty($fofaEmail) || empty($fofaKey)) {
                self::log(["工具检查失败: FOFA 配置缺失"]);
                return false;
            }
        } catch (\Exception $e) {
            self::log(["工具检查失败: FOFA 配置查询异常", $e->getMessage()]);
            return false;
        }

        return true;
    }

    /**
     * 检查OneForAll工具
     * @return bool
     */
    private static function checkOneForAll(): bool
    {
        $oneforallPath = '/data/tools/oneforall/';

        if (!file_exists($oneforallPath)) {
            self::log(["工具检查失败: OneForAll 未安装", $oneforallPath]);
            return false;
        }

        return true;
    }

    /**
     * 检查Hydra工具
     * @return bool
     */
    private static function checkHydra(): bool
    {
        $hydraPath = '/data/tools/hydra/';

        if (!file_exists($hydraPath)) {
            self::log(["工具检查失败: Hydra 未安装", $hydraPath]);
            return false;
        }

        return true;
    }

    /**
     * 检查Sqlmap工具
     * @return bool
     */
    private static function checkSqlmap(): bool
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
     * 检查Nmap工具
     * @return bool
     */
    private static function checkNmap(): bool
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
     * 检查Dirmap工具
     * @return bool
     */
    private static function checkDirmap(): bool
    {
        // 如果配置文件中没有设置路径，则检查默认路径
        $defaultPath = "./extend/tools/dirmap/";
        if (!file_exists($defaultPath)) {
            self::log(["工具检查失败: Dirmap 未安装或路径配置错误", $defaultPath]);
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
        $codePath = trim(`pwd`) . "/data/codeCheck";
        $fortifyRetDir = trim(`pwd`) . "/data/fortify_result";

        if (!file_exists($codePath) || !file_exists($fortifyRetDir)) {
            self::log(["工具检查失败: Fortify 环境未配置", $codePath, $fortifyRetDir]);
            return false;
        }

        return true;
    }

    /**
     * 检查Semgrep工具
     * @return bool
     */
    private static function checkSemgrep(): bool
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
     * 检查Crawlergo工具
     * @return bool
     */
    private static function checkCrawlergo(): bool
    {
        $crawlergoPath = "./extend/tools/crawlergo/";

        if (!file_exists($crawlergoPath)) {
            self::log(["工具检查失败: Crawlergo 未安装", $crawlergoPath]);
            return false;
        }

        return true;
    }

    /**
     * 检查Wafw00f工具
     * @return bool
     */
    private static function checkWafw00f(): bool
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