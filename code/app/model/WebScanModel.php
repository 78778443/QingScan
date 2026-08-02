<?php

namespace app\model;


use app\webscan\model\XrayModel;
use think\facade\Db;

class WebScanModel extends BaseModel
{
    public static function rad()
    {
        $path = "cd " . trim(`pwd`) . "/extend/tools/rad/ && ";
        //判断rad运行环境是否安装
        $radPath = trim(`pwd`) . '/extend/tools/rad/';
        if (!file_exists($radPath)) die("工具RAD不存在：{$radPath}");
        if (!file_exists("/usr/bin/google-chrome")) die("RAD 运行依赖环境不存在，请安装chrome环境~");

        $where = ['tool' => 'scan_app_rad', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();

        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $value = json_decode($task['ext_info'], true);
            PluginModel::addScanLog($value['id'], __METHOD__, 0);
            $url = $value['url'];
            $id = $value['id'];
            $user_id = $value['user_id'];
            $pathArr = getSavePath($url, "rad", $id);
            //初始化清理目录
            if (file_exists($pathArr['tool_result'])) {
                addlog(["清理老文件", $pathArr['tool_result']]);
                @unlink($pathArr['tool_result']);
            }


            $cmd = "{$path} ./rad_linux_amd64 -t  \"{$url}\" -json {$pathArr['tool_result']}";
            echo "开始执行抓取URL地址命令:" . $cmd . PHP_EOL;

            $result = [];
            execLog($cmd, $result);

            if (!file_exists($pathArr['tool_result'])) {
                addlog(["rad扫描失败,结果文件不存在", $pathArr['tool_result']]);
                PluginModel::addScanLog($value['id'], __METHOD__, 0, 2);
                continue;
            }

            $urlList = json_decode(file_get_contents($pathArr['tool_result']), true);
            foreach ($urlList as $val) {
                $val['URL'] = rtrim($val['URL'], '/');
                $arr = parse_url($val['URL']);
                $blackExt = ['.js', '.css', '.png', '.jpg', '.jpeg', '.gif', '.mp3', '.mp4', '.ico', '.bmp', '.wmv', '.avi', '.psd'];
                if (isset($arr['path']) && in_array_strpos(strtolower($arr['path']), $blackExt) || in_array_strpos(strtolower($val['URL']), $blackExt)) {
                    addlog(["rad扫描跳过无意义URL", $val['URL']]);
                    continue;
                }

                $newData = [
                    'app_id' => $id,
                    'method' => $val['Method'],
                    'url' => $val['URL'],
                    'status' => 1,
                    'hash' => md5($val['URL']),
                    'crawl_status' => 1,
                    'scan_status' => 0,
                    'header' => isset($val['Header']) ? json_encode($val['Header']) : "",
                    'user_id' => $user_id
                ];
                Db::name('asm_urls')->extra('IGNORE')->insert($newData);
                addlog(["rad扫描数据写入成功", json_encode($newData)]);

            }
            PluginModel::addScanLog($value['id'], __METHOD__, 0, 1, 1, ['content' => $urlList]);
        }


    }


    public static function xray()
    {
        //使用内置漏洞检测引擎替代外部 xray 工具
        $where = ['tool' => 'scan_app_xray', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $val = json_decode($task['ext_info'], true);

            PluginModel::addScanLog($val['id'], __METHOD__, 0);

            //hazard_level: 0=Low 1=Medium 2=High 3=Critical
            $levelMap = ['low' => 0, 'medium' => 1, 'high' => 2, 'critical' => 3];
            $result = \app\scan\VulnScan::scan($val['url']);
            if (empty($result)) {
                PluginModel::addScanLog($val['id'], __METHOD__, 0, 1);
                addlog(["xray扫描未发现漏洞:{$val['url']}，数据结构：" . json_encode($result)]);
                continue;
            }

            foreach ($result as $item) {
                $targetJson = json_encode(['url' => $item['url']], JSON_UNESCAPED_UNICODE);
                $detailJson = json_encode([
                    'addr' => $item['url'],
                    'payload' => $item['payload'],
                    'description' => $item['description'],
                ], JSON_UNESCAPED_UNICODE);
                $pluginJson = json_encode([$item['name']], JSON_UNESCAPED_UNICODE);

                //去重：同 app_id + plugin + target 不重复插入
                if (Db::name('xray')->where(['app_id' => $val['id'], 'plugin' => $pluginJson, 'target' => $targetJson])->count()) {
                    continue;
                }
                $newData = [
                    'app_id' => $val['id'],
                    'create_time' => (string)time(),
                    'detail' => $detailJson,
                    'plugin' => $pluginJson,
                    'target' => $targetJson,
                    'check_status' => 0,
                    'hazard_level' => $levelMap[$item['severity']] ?? 0,
                    'url_source' => $val['url'],
                    'is_delete' => 0,
                    'user_id' => $val['user_id'],
                ];
                Db::name('xray')->insert($newData);
                echo "xray添加漏洞结果:" . json_encode($newData, 256) . PHP_EOL;
            }
            addlog(["xray扫描数据写入成功:" . json_encode($result)]);
            PluginModel::addScanLog($val['id'], __METHOD__, 0, 1);
        }

    }

    private static function chuliXrayData($val)
    {
        $pathArr = getSavePath($val['url'], "xray", $val['id']);
        //如果结果文件不存在
        if (!file_exists($pathArr['tool_result'])) {
            addlog("xray扫描结果文件不存在:{$pathArr['tool_result']},扫描URL失败: {$val['url']}");
            Db::table('app')->where(['id' => $val['id']])->save(['xray_scan_time' => date('2048-m-d H:i:s')]);
            PluginModel::addScanLog($val['id'], __METHOD__, 0, 2);
            return false;
        }


        $data = json_decode(file_get_contents($pathArr['tool_result']), true);
        $addr = [];
        foreach ($data as $value) {
            $newData = [
                'app_id' => $val['id'],
                'create_time' => substr($value['create_time'], 0, 10),
                'detail' => json_encode($value['detail'], 256),
                'plugin' => json_encode($value['plugin'], 256),
                'target' => json_encode($value['target'], 256),
                'url' => $value['detail']['addr'],
                'url_id' => $val['id'],
                'user_id' => $val['user_id'],
                'poc' => $value['detail']['payload']
            ];
            $addr[] = $newData;
            echo "xray添加漏洞结果:" . json_encode($newData, 256) . PHP_EOL;
            XrayModel::addXray($newData);
        }
        addlog(["xray扫描数据写入成功:" . json_encode($addr, 256)]);
    }


    public static function nucleiScan()
    {
        //使用内置漏洞检测引擎替代外部 nuclei 工具
        $where = ['tool' => 'scan_app_nuclei', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $v = json_decode($task['ext_info'], true);

            PluginModel::addScanLog($v['id'], __METHOD__, 0);

            $result = \app\scan\VulnScan::scan($v['url']);
            if (empty($result)) {
                PluginModel::addScanLog($v['id'], __METHOD__, 0, 1);
                addlog(["nuclei扫描未发现漏洞:{$v['url']}，数据结构：" . json_encode($result)]);
                continue;
            }

            $host = parse_url($v['url'], PHP_URL_HOST) ?: $v['url'];
            foreach ($result as $item) {
                //去重：同 app_id + name + matched_at 不重复插入
                if (Db::name('app_nuclei')->where(['app_id' => $v['id'], 'name' => $item['name'], 'matched_at' => $item['url']])->count()) {
                    continue;
                }
                $data = [
                    'app_id' => $v['id'],
                    'user_id' => $v['user_id'],
                    'template' => $item['name'],
                    'template_url' => $item['url'],
                    'template_id' => $item['name'],
                    'name' => $item['name'],
                    'author' => 'qingscan',
                    'tags' => 'web',
                    'description' => $item['description'],
                    'reference' => '',
                    'severity' => $item['severity'],
                    'type' => 'http',
                    'host' => $host,
                    'matched_at' => $item['url'],
                    'extracted_results' => '',
                    'ip' => '',
                    'curl_command' => '',
                    'status' => 1,
                    'create_time' => date('Y-m-d H:i:s', time()),
                ];
                Db::name('app_nuclei')->insert($data);
            }
            addlog(["nuclei扫描数据写入成功:" . json_encode($result)]);
            PluginModel::addScanLog($v['id'], __METHOD__, 0, 1);
        }
    }


    public static function addNuclei($v, $arr)
    {
        $data = [
            'app_id' => $v['id'],
            'user_id' => $v['user_id'],
            'template' => $arr['template'],
            'template_url' => $arr['template-url'],
            'template_id' => $arr['template-id'],
            'name' => $arr['info']['name'],
            'author' => json_encode($arr['info']['author']),
            'tags' => json_encode($arr['info']['tags']),
            'description' => $arr['info']['description'] ?? '',
            'reference' => $arr['info']['reference'],
            'severity' => $arr['info']['severity'],
            'type' => $arr['type'],
            'host' => $arr['host'],
            'matched_at' => $arr['matched-at'],
            'extracted_results' => isset($arr['extracted-results']) ? json_encode($arr['extracted-results']) : '',
            'ip' => $arr['ip'] ?? '',
            'curl_command' => isset($arr['curl-command']) ? json_encode($arr['curl-command']) : '',
            'status' => isset($arr['matcher-status']) ? $arr['matcher-status'] ? 1 : 0 : 0,
            'create_time' => strtotime($arr['timestamp']) ? date('Y-m-d H:i:s', strtotime($arr['timestamp'])) : date('Y-m-d H:i:s', time())
        ];
        Db::name('app_nuclei')->insert($data);
    }

    public static function vulmapPocTest()
    {
        //使用内置漏洞检测引擎替代外部 vulmap 工具
        $where = ['tool' => 'scan_app_vulmap', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $v = json_decode($task['ext_info'], true);

            if (!self::checkToolAuth(1, $v['id'], 'vulmap')) {
                continue;
            }

            PluginModel::addScanLog($v['id'], __METHOD__, 0);

            $result = \app\scan\VulnScan::scan($v['url']);
            if (empty($result)) {
                PluginModel::addScanLog($v['id'], __METHOD__, 0, 1);
                addlog(["vulmap扫描完成,没有发现漏洞，url:{$v['url']}"]);
                continue;
            }

            $urlInfo = parse_url($v['url']);
            $host = $urlInfo['host'] ?? $v['url'];
            $port = $urlInfo['port'] ?? (($urlInfo['scheme'] ?? '') === 'https' ? 443 : 80);

            foreach ($result as $item) {
                //去重：同 app_id + url + plugin 不重复插入
                if (Db::name('app_vulmap')->where(['app_id' => $v['id'], 'url' => $item['url'], 'plugin' => $item['name']])->count()) {
                    continue;
                }
                $data = [
                    'app_id' => $v['id'],
                    'user_id' => $v['user_id'],
                    'author' => 'qingscan',
                    'description' => $item['description'],
                    'host' => $host,
                    'port' => (string)$port,
                    'param' => '',
                    'request' => $item['payload'],
                    'payload' => $item['payload'],
                    'response' => '',
                    'url' => $item['url'],
                    'plugin' => $item['name'],
                    'target' => json_encode(['url' => $item['url']]),
                    'vuln_class' => $item['name'],
                    'create_time' => time(),
                ];
                if (!Db::name('app_vulmap')->insert($data)) {
                    addlog(["app_vulmap数据写入失败:" . json_encode($data)]);
                    PluginModel::addScanLog($v['id'], __METHOD__, 0, 2, 1, ['content' => 'app_vulmap数据写入失败']);
                }
            }
            PluginModel::addScanLog($v['id'], __METHOD__, 0, 1);
        }

    }


    public static function crawlergoScan()
    {
        $tools = "./extend/tools/crawlergo/";
        $list = self::getAppStayScanList('crawlergo_scan_time');
        foreach ($list as $val) {


            PluginModel::addScanLog($val['id'], __METHOD__, 0);


            $filename = $tools . 'crawlergo.json';
            @unlink($filename);

            $cmd = "cd $tools && ./cmd/crawlergo/crawlergo_cmd -c /usr/bin/google-chrome -o none --output-json $filename -f 'strict' -t 10 {$val['url']}";
            systemLog($cmd);
            if (!file_exists($filename)) {
                PluginModel::addScanLog($val['id'], __METHOD__, 0, 2);
                addlog(["crawlergo扫描失败，url:{$val['url']}"]);
                continue;
            }
            $result = json_decode(file_get_contents($filename), true);
            $data = [];
            foreach ($result['all_req_list'] as $v) {
                $data[] = [
                    'app_id' => $val['id'],
                    'user_id' => $val['user_id'],
                    'url' => $v['url'],
                    'method' => $v['method'],
                    'accept' => isset($v['headers']['Accept']) ? $v['headers']['Accept'] : '',
                    'cache_control' => isset($v['headers']['Cache-Control']) ? $v['headers']['Cache-Control'] : '',
                    'cookie' => isset($v['headers']['Cookie']) ? $v['headers']['Cookie'] : '',
                    'referer' => isset($v['headers']['Referer']) ? $v['headers']['Referer'] : '',
                    'spider_name' => isset($v['headers']['Spider-Name']) ? $v['headers']['Spider-Name'] : '',
                    'user_agent' => isset($v['headers']['User-Agent']) ? $v['headers']['User-Agent'] : '',
                    'data' => $v['data'],
                    'source' => $v['source'],
                    'create_time' => date('Y-n-d H:i:s', time())
                ];
            }
            if ($data) {
                Db::name('app_crawlergo')->insertAll($data);
            }
            PluginModel::addScanLog($val['id'], __METHOD__, 0, 1);
        }
    }

    public static function dismapScan()
    {
        $where = ['tool' => 'scan_app_dismap', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $v = json_decode($task['ext_info'], true);
            if (!self::checkToolAuth(1, $v['id'], 'dismap')) {
                continue;
            }

            PluginModel::addScanLog($v['id'], __METHOD__, 0);

            // 使用内置指纹识别引擎，替代外部 dismap 工具
            $result = \app\scan\FingerScan::scan($v['url']);
            $data = [
                'app_id' => $v['id'],
                'user_id' => $v['user_id'],
                'create_time' => date('Y-m-d H:i:s', time()),
                'result' => json_encode(array_merge($result['fingerprints'], array_filter([$result['server'] ?? '', $result['title'] ?? ''])), JSON_UNESCAPED_UNICODE)
            ];
            if (!Db::name('app_dismap')->insert($data)) {
                PluginModel::addScanLog($v['id'], __METHOD__, 0, 2);
                addlog(["app_dismap数据写入失败:" . json_encode($data)]);
            };
            PluginModel::addScanLog($v['id'], __METHOD__, 0, 1);
        }


    }
}
