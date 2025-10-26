<?php

namespace app\code\model;

use think\facade\Db;
use think\Model;


class CodeQlModel extends Model
{

    public static function getDetailCount()
    {


        $countList = [
        ];

        return $countList;
    }

    public static function ai_message()
    {

        //读取上游数据
        $list = Db::table('codeql')->where(['ai_message' => ''])->select()->toArray();


        //处理数据
        $data = [];
        foreach ($list as $value) {
            $aiMessage = ChatGPT::tongyi($value['prompt']);
            $translatedJson = json_decode($aiMessage, true);
            $aiMessage = $translatedJson['output']['choices'][0]['message']['content'] ?? $aiMessage;

            //更新扫描时间
            $data = ['ai_message' => $aiMessage];
            Db::table('codeql')->where(['id' => $value['id']])->update($data);
        }
    }

    public static function start()
    {
        //安装工具
        self::autoDownTool();

        //读取上游数据
        $where = [];
        $list = Db::table('git_addr')->whereNotNull('code_path')->where($where)
            ->whereTime('codeql_scan_time', '<=', date('Y-m-d', time() - (7 * 86400)))
            ->select()->toArray();


        //处理数据
        $data = [];
        foreach ($list as $value) {

            $codePath = $value['code_path'];
            //执行检测脚本
            $resultsPath = self::execTool($codePath);

            //录入检测结果
            $tempList = self::writeData($resultsPath, $value);

            //开始执行
            $data = array_merge($data, $tempList);

            if (empty($data)) {
                print_r("codeql 扫描失败:{$value['code_path']}\n");
                continue;
            }
            Db::table('codeql')->replace()->strict(false)->insertAll($data);

            //更新扫描时间
            $data = ['codeql_scan_time' => date('Y-m-d H:i:s')];
//            Db::table('git_addr')->where(['id' => $value['id']])->update($data);
        }
    }

    public static function autoDownTool()
    {
        if (!file_exists('/opt/codeql/codeql')) {
            // 下载工具
            $url = "https://github.com/github/codeql-cli-binaries/releases/latest/download/codeql-linux64.zip";
            $cmd = "curl -sSL {$url} -o codeql.zip && unzip codeql.zip -d /opt && ln -s /opt/codeql/codeql /usr/local/bin/codeql";

            echo "开始安装CodeQL :{$cmd} \n";
            exec($cmd);
        }

        //下载规则
        if (!file_exists('extend/codeql/rules')) {
            $cmd = "cd extend/codeql/ && git clone --depth=1 https://github.com/github/codeql.git rules";
            echo "下载CodeQL规则集 :{$cmd} \n";
            exec($cmd);
        }
    }

    public static function WriteData($resultsPath)
    {
        if (!file_exists($resultsPath)) {
            echo "扫描结果文件不存在:{$resultsPath}\n";
            return [];
        }
        // 解析SARIF文件并存储到数据库
        $sarifContent = file_get_contents($resultsPath);
        $sarifData = json_decode($sarifContent, true);

        $data = [];
        foreach ($sarifData['runs'][0]['results'] as $result) {
            $data[] = [
                'ruleId' => $result['ruleId'],
                'message' => $result['message']['text'],
                'locations' => json_encode($result['locations']),
                'codeFlows' => json_encode($result['codeFlows'] ?? []),
                'prompt' => rtrim(self::getPrompt($result)),
                'ai_message' => ''
            ];

        }

        foreach ($sarifData['runs'][0]['tool']['driver']['rules'] as $rule) {

            $properties = $rule['properties'];
            $properties['tags'] = json_encode($properties['tags'], JSON_UNESCAPED_UNICODE);
            $properties['security_severity'] = $properties['security-severity'];
            unset($properties['security-severity']);
            $properties['problem_severity'] = $properties['problem.severity'];
            unset($properties['problem.severity']);
            $properties['sub_severity'] = $properties['sub-severity'] ?? '';
            unset($properties['sub-severity']);

            Db::table('codeql_rules')->extra('IGNORE')->strict(false)->insert($properties);

        }

        return $data;

    }


    public static function execTool($codePath, $language = 'python')
    {
        //语言类型
        if (!file_exists($codePath)) return false;
        if (!in_array($language, ['python', 'java', 'javascript', 'go'])) $language = 'python';

        //获取codepath最后的名字
        $dirName = basename($codePath) . date('YmdH');
        $repoDbPath = "extend/codeql/repo-db/{$dirName}";
        $qlPackPath = "extend/codeql/rules/{$language}/ql/src/Security/";
        $resultsPath = "extend/codeql/results/{$dirName}.sarif";

        //创建目录
        if (!file_exists(dirname($repoDbPath))) mkdir(dirname($repoDbPath), 0777, true);
        if (!file_exists(dirname($resultsPath))) mkdir(dirname($resultsPath), 0777, true);

        if (!file_exists($repoDbPath)) {
            // 创建CodeQL数据库
            $cmd = "codeql database create $repoDbPath --language={$language} --source-root $codePath";
            echo $cmd . PHP_EOL;
            exec($cmd);
        }


        $cmd = "codeql database finalize {$repoDbPath}";
        echo $cmd . PHP_EOL;
        exec($cmd);

        // 分析代码
        $cmd = "codeql database analyze $repoDbPath $qlPackPath --format=sarifv2.1.0 --output=$resultsPath";
        echo $cmd . PHP_EOL;
        exec($cmd);

        return $resultsPath;

    }

    public static function getSourceCodeSnippet($filePath, $startLine, $endLine)
    {
        if (strpos($filePath, "file:") !== false) {
            $filePath = str_replace("file:", "", $filePath);
        } else {
            $filePath = "/data/code/kcweb/{$filePath}";
        }

        $sourceCodeSnippet = '';
        $fileContent = file($filePath);
        for ($i = $startLine - 1; $i < $endLine; $i++) {
            if (isset($fileContent[$i])) {
                $sourceCodeSnippet .= $fileContent[$i];
            }
        }
        return $sourceCodeSnippet;
    }

    public static function getPrompt($result): string
    {

        // 提取漏洞信息
//        if (!isset($result['locations'][0]['physicalLocation']['artifactLocation']['uri'])) return '';
        $filePath = $result['locations'][0]['physicalLocation']['artifactLocation']['uri'];
        $lineNumber = $result['locations'][0]['physicalLocation']['region']['startLine'];
        $vulnerabilityDescription = $result['message']['text'];

        // 初始化代码片段
        $sourceCodeSnippets = '';

        // 遍历 codeFlows 以提取所有相关代码片段
        if (isset($result['codeFlows'][0]['threadFlows'][0]['locations'])) {
            foreach ($result['codeFlows'][0]['threadFlows'][0]['locations'] as $location) {
                if (isset($location['location']['physicalLocation']['region'])) {

                    $filePath = $location['location']['physicalLocation']['artifactLocation']['uri'];
                    $startLine = $location['location']['physicalLocation']['region']['startLine'];
                    $endLine = $location['location']['physicalLocation']['region']['endLine'] ?? $startLine;
                    $snippet = self::getSourceCodeSnippet($filePath, $startLine, $endLine);
                    $sourceCodeSnippets .= "代码 {$filePath} 片段（行 $startLine 到 $endLine ）：\n$snippet\n\n";
                }
            }
        } else {
            // 如果没有 codeFlows 信息，使用漏洞位置的行号
            $startLine = $lineNumber;
            $endLine = $lineNumber;
            $sourceCodeSnippets = self::getSourceCodeSnippet($filePath, $startLine, $endLine);
        }

        // 构建审计提示
        $auditPrompt = "审计提示：\n";
        $auditPrompt .= "-------------------------------\n";
        $auditPrompt .= "漏洞类型: {$result['ruleId']}\n";
        $auditPrompt .= "文件路径: $filePath\n";
        $auditPrompt .= "行号: $lineNumber\n\n";
        $auditPrompt .= "漏洞描述:\n$vulnerabilityDescription\n\n";
        $auditPrompt .= "漏洞代码片段:\n$sourceCodeSnippets\n";
        $auditPrompt .= "-------------------------------\n";

        return $auditPrompt;
    }
}