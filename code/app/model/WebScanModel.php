<?php

namespace app\model;


use app\webscan\model\XrayModel;
use think\facade\Db;

class WebScanModel extends BaseModel
{
    public static function crawlerScan()
    {
        //使用内置爬虫引擎（基础爬虫，不做 JS 渲染）
        $where = ['tool' => 'scan_app_crawler', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();

        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $value = json_decode($task['ext_info'], true);
            PluginModel::addScanLog($value['id'], __METHOD__, 0);
            $url = $value['url'];
            $id = $value['id'];
            $user_id = $value['user_id'];

            $urlList = \app\scan\CrawlerScan::crawl($url);
            if (empty($urlList)) {
                addlog(["爬虫扫描失败,未提取到URL", $url]);
                PluginModel::addScanLog($value['id'], __METHOD__, 0, 2);
                continue;
            }

            $blackExt = ['.js', '.css', '.png', '.jpg', '.jpeg', '.gif', '.mp3', '.mp4', '.ico', '.bmp', '.wmv', '.avi', '.psd'];
            foreach ($urlList as $val) {
                $val['url'] = rtrim($val['url'], '/');
                $arr = parse_url($val['url']);
                if (isset($arr['path']) && in_array_strpos(strtolower($arr['path']), $blackExt) || in_array_strpos(strtolower($val['url']), $blackExt)) {
                    addlog(["爬虫扫描跳过无意义URL", $val['url']]);
                    continue;
                }

                $newData = [
                    'app_id' => $id,
                    'method' => $val['method'],
                    'url' => $val['url'],
                    'status' => 1,
                    'hash' => md5($val['url']),
                    'crawl_status' => 1,
                    'scan_status' => 0,
                    'header' => "",
                    'user_id' => $user_id
                ];
                Db::name('asm_urls')->extra('IGNORE')->insert($newData);
                addlog(["爬虫扫描数据写入成功", json_encode($newData)]);

            }
            PluginModel::addScanLog($value['id'], __METHOD__, 0, 1, 1, ['content' => $urlList]);
        }


    }


    public static function webVulnScan()
    {
        //使用内置漏洞检测引擎
        $where = ['tool' => 'scan_app_web_vuln', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $val = json_decode($task['ext_info'], true);

            PluginModel::addScanLog($val['id'], __METHOD__, 0);

            $result = \app\scan\VulnScan::scan($val['url']);
            if (empty($result)) {
                PluginModel::addScanLog($val['id'], __METHOD__, 0, 1);
                addlog(["Web漏洞扫描未发现漏洞:{$val['url']}，数据结构：" . json_encode($result)]);
                continue;
            }

            foreach ($result as $item) {
                //去重：同 app_id + url + name 不重复插入（scan_vuln）
                if (Db::name('scan_vuln')->where(['app_id' => $val['id'], 'url' => $item['url'], 'name' => $item['name']])->count()) {
                    continue;
                }
                $newData = [
                    'app_id' => $val['id'],
                    'user_id' => $val['user_id'],
                    'url' => $item['url'],
                    'name' => $item['name'],
                    'severity' => $item['severity'],
                    'payload' => $item['payload'],
                    'description' => $item['description'],
                    'source' => 'web_vuln',
                    'check_status' => 0,
                    'create_time' => date('Y-m-d H:i:s', time()),
                    'is_delete' => 0,
                ];
                Db::name('scan_vuln')->insert($newData);
                echo "web_vuln添加漏洞结果:" . json_encode($newData, 256) . PHP_EOL;
            }
            addlog(["Web漏洞扫描数据写入成功:" . json_encode($result)]);
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


    public static function genVulnScan()
    {
        //使用内置漏洞检测引擎
        $where = ['tool' => 'scan_app_gen_vuln', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $v = json_decode($task['ext_info'], true);

            PluginModel::addScanLog($v['id'], __METHOD__, 0);

            $result = \app\scan\VulnScan::scan($v['url']);
            if (empty($result)) {
                PluginModel::addScanLog($v['id'], __METHOD__, 0, 1);
                addlog(["通用漏洞扫描未发现漏洞:{$v['url']}，数据结构：" . json_encode($result)]);
                continue;
            }

            foreach ($result as $item) {
                //去重：同 app_id + url + name 不重复插入（scan_vuln）
                if (Db::name('scan_vuln')->where(['app_id' => $v['id'], 'url' => $item['url'], 'name' => $item['name']])->count()) {
                    continue;
                }
                $data = [
                    'app_id' => $v['id'],
                    'user_id' => $v['user_id'],
                    'url' => $item['url'],
                    'name' => $item['name'],
                    'severity' => $item['severity'],
                    'payload' => $item['payload'],
                    'description' => $item['description'],
                    'source' => 'gen_vuln',
                    'check_status' => 0,
                    'create_time' => date('Y-m-d H:i:s', time()),
                    'is_delete' => 0,
                ];
                Db::name('scan_vuln')->insert($data);
            }
            addlog(["通用漏洞扫描数据写入成功:" . json_encode($result)]);
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

    public static function vulVerifyScan()
    {
        //使用内置漏洞检测引擎
        $where = ['tool' => 'scan_app_vul_verify', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $v = json_decode($task['ext_info'], true);

            if (!self::checkToolAuth(1, $v['id'], 'vul_verify')) {
                continue;
            }

            PluginModel::addScanLog($v['id'], __METHOD__, 0);

            $result = \app\scan\VulnScan::scan($v['url']);
            if (empty($result)) {
                PluginModel::addScanLog($v['id'], __METHOD__, 0, 1);
                addlog(["漏洞验证扫描完成,没有发现漏洞，url:{$v['url']}"]);
                continue;
            }

            foreach ($result as $item) {
                //去重：同 app_id + url + name 不重复插入（scan_vuln）
                if (Db::name('scan_vuln')->where(['app_id' => $v['id'], 'url' => $item['url'], 'name' => $item['name']])->count()) {
                    continue;
                }
                $data = [
                    'app_id' => $v['id'],
                    'user_id' => $v['user_id'],
                    'url' => $item['url'],
                    'name' => $item['name'],
                    'severity' => $item['severity'],
                    'payload' => $item['payload'],
                    'description' => $item['description'],
                    'source' => 'vul_verify',
                    'check_status' => 0,
                    'create_time' => date('Y-m-d H:i:s', time()),
                    'is_delete' => 0,
                ];
                if (!Db::name('scan_vuln')->insert($data)) {
                    addlog(["scan_vuln数据写入失败:" . json_encode($data)]);
                    PluginModel::addScanLog($v['id'], __METHOD__, 0, 2, 1, ['content' => 'scan_vuln数据写入失败']);
                }
            }
            PluginModel::addScanLog($v['id'], __METHOD__, 0, 1);
        }

    }


    public static function spiderScan()
    {
        //使用内置爬虫引擎（基础爬虫，不做 JS 渲染；request 头字段缺失时置空）
        $list = self::getAppStayScanList('crawlergo_scan_time');
        foreach ($list as $val) {
            PluginModel::addScanLog($val['id'], __METHOD__, 0);

            $urlList = \app\scan\CrawlerScan::crawl($val['url']);
            if (empty($urlList)) {
                PluginModel::addScanLog($val['id'], __METHOD__, 0, 2);
                addlog(["爬虫扫描失败，url:{$val['url']}"]);
                continue;
            }
            $data = [];
            foreach ($urlList as $v) {
                $data[] = [
                    'app_id' => $val['id'],
                    'user_id' => $val['user_id'],
                    'url' => $v['url'],
                    'method' => $v['method'],
                    'accept' => '',
                    'cache_control' => '',
                    'cookie' => '',
                    'referer' => '',
                    'spider_name' => 'qingscan',
                    'user_agent' => '',
                    'data' => '',
                    'source' => '',
                    'create_time' => date('Y-n-d H:i:s', time())
                ];
            }
            if ($data) {
                Db::name('app_crawlergo')->insertAll($data);
            }
            PluginModel::addScanLog($val['id'], __METHOD__, 0, 1);
        }
    }

    public static function assetFingerScan()
    {
        $where = ['tool' => 'scan_app_asset_finger', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {
            Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            $v = json_decode($task['ext_info'], true);
            if (!self::checkToolAuth(1, $v['id'], 'asset_finger')) {
                continue;
            }

            PluginModel::addScanLog($v['id'], __METHOD__, 0);

            // 使用内置指纹识别引擎
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
