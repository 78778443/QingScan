<?php

namespace app\model;


use think\facade\Db;
use think\facade\Log;

class WebScanModel extends BaseModel
{


    public static function rad()
    {
        $path = "cd /data/tools/rad/ && ";

        //判断rad运行环境是否安装
        if (file_exists("/usr/bin/google-chrome") == false) {
            addlog("RAD 运行依赖环境不存在，请安装chrome环境~");
            return false;
        }
        while (true) {
            $list = Db::table('app')->whereTime('crawler_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))->limit(1)->orderRand()->select()->toArray();
            foreach ($list as $value) {
                $url = $value['url'];
                $id = $value['id'];
                $user_id = $value['user_id'];
                $pathArr = getSavePath($url, "rad", $id);
                //初始化清理目录
                if (file_exists($pathArr['tool_result'])) {
                    addlog(["清理老文件", $pathArr['tool_result']]);
                    @unlink($pathArr['tool_result']);
                }
                if (file_exists($pathArr['cmd_result'])) {
                    addlog(["清理老文件", $pathArr['tool_result']]);
                    @unlink($pathArr['cmd_result']);
                }

                $filename = '/data/tools/rad/rad_config.yml';
                if (!$value['is_intranet']) {   // 不是内网
                    // 设置代理
                    $arr = @yaml_parse_file($filename);
                    if ($arr) {
                        $proxyArr = Db::name('proxy')->where('status', 1)->limit(3)->orderRand()->select();
                        $proxy = '';
                        foreach ($proxyArr as $v) {
                            $result = testAgent($v['host'], $v['port']);
                            if ($result == 200) {
                                $proxy = 'http://' . $v['host'] . ":{$v['port']}";
                                break;
                            }
                        }
                        if (!empty($proxy)) {
                            $arr['proxy'] = $proxy;
                            yaml_emit_file($filename, $arr);
                        }
                    }
                } else {
                    unlink($filename);
                }


                $cmd = "{$path} ./rad_linux_amd64 -t  \"{$url}\"  -json  {$pathArr['tool_result']}";
                addlog(["开始执行抓取URL地址命令", $cmd]);

                $result = [];
                execLog($cmd, $result);

                $result = implode("\n", $result);
                $urlList = json_decode(file_get_contents($pathArr['tool_result']), true);

                foreach ($urlList as $value) {
                    $newData = [
                        'app_id' => $id,
                        'method' => $value['Method'],
                        'url' => $value['URL'],
                        'status' => 1,
                        'hash' => md5($value['URL']),
                        'crawl_status' => 1,
                        'scan_status' => 0,
//                        'header' => json_encode($value['Header']),
                        'header' => "{}",
                        'user_id' => $user_id
                    ];
                    UrlsModel::addData($newData);
                }

                Db::table('app')->where(['id' => $id])->save(['crawler_time' => date('Y-m-d H:i:s')]);
            }
            sleep(10);
        }

    }


    public static function xray()
    {
        while (true) {
            $list = Db::table('app')->whereTime('xray_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))->where('is_delete', 0)->limit(1)->orderRand()->select()->toArray();
            foreach ($list as $val) {
                $url = $val['url'];
                $id = $val['id'];
                $user_id = $val['user_id'];
                //addlog(["XRAY开始执行扫描任务", $id, $url]);
                $path = "cd /data/tools/xray/ && ";
                $pathArr = getSavePath($url, "xray", $id);

                //初始化清理目录
                if (file_exists($pathArr['tool_result'])) {
                    unlink($pathArr['tool_result']);
                }
                if (file_exists($pathArr['cmd_result'])) {
                    unlink($pathArr['cmd_result']);
                }

                if (file_exists($pathArr['tool_result']) == false) {
                    // 设置代理
                    $filename = '/data/tools/xray/config.yaml';
                    if (!$val['is_intranet']) {  // 不是内网
                        $arr = @yaml_parse_file($filename);
                        if ($arr) {
                            $arr['http']['proxy_rule'][0]['match'] = '*';
                            $proxyArr = Db::name('proxy')->where('status', 1)->limit(10)->orderRand()->select();
                            foreach ($proxyArr as $v) {
                                $result = testAgent($v['host'], $v['port']);
                                if ($result == 200) {
                                    $proxy[] = 'http://' . $v['host'] . ":{$v['port']}";
                                }
                            }
                            $arr['http']['proxy_rule'][0]['servers'] = [];
                            $weight = random_split(10,count($proxy));
                            foreach ($proxy as $k=>$v) {
                                $arr['http']['proxy_rule'][0]['servers'][] = [
                                    'addr' => $v,
                                    'weight' => $weight[$k],
                                ];
                            }
                            yaml_emit_file($filename, $arr);
                        }
                    } else {
                        unlink($filename);
                    }

                    $cmd = "{$path} ./xray_linux_amd64 webscan --url \"{$url}\"  --json-output  {$pathArr['tool_result']}";

                    $result = [];
                    execLog($cmd, $result);
                    $result = implode("\n", $result);
                    addlog(["漏洞扫描结束", $id, $url, $cmd, base64_encode($result)]);

                    $result = file_put_contents($pathArr['cmd_result'], $result);
                    if ($result == false) {
                        addlog(["写入执行结果失败", base64_encode($pathArr['cmd_result'])]);
                    }
                } else {
                    addlog("xray文件已存在:{$pathArr['tool_result']}");
                }


                if (file_exists($pathArr['tool_result']) == true) {
                    $data = json_decode(file_get_contents($pathArr['tool_result']), true);
                    foreach ($data as $value) {
                        $newData = [
                            'create_time' => substr($value['create_time'], 0, 10),
                            'detail' => json_encode($value['detail']),
                            'plugin' => json_encode($value['plugin']),
                            'target' => json_encode($value['target']),
                            'url' => $value['detail']['addr'],
                            'url_id' => $val['id'],
                            'app_id' => $val['id'],
                            'user_id' => $user_id,
                            'poc' => $value['detail']['payload']
                        ];
                        echo "添加漏洞结果:" . json_encode($newData) . PHP_EOL;
                        XrayModel::addXray($newData);
                    }

                    Db::table('app')->where(['id' => $id])->save(['xray_scan_time' => date('Y-m-d H:i:s')]);
                } else {
                    addlog("文件不存在:{$pathArr['tool_result']}  ,扫描URL失败: {$url}");
                    Db::table('app')->where(['id' => $id])->save(['xray_scan_time' => date('2048-m-d H:i:s')]);
                }
            }
            sleep(10);
        }
    }

    public static function startXrayAgent()
    {
        while (true) {
            $list = Db::name('app_xray_agent_port')->where('start_up', 0)->limit(10)->orderRand()->select()->toArray();
            foreach ($list as $v) {
                // 执行命令查看任务是否已经执行
                $cmd = "ps -ef | grep 'xray_" . $v['app_id'] . "_" . $v['xray_agent_prot'] . "' | grep -v ' grep'";
                $result = [];
                execLog($cmd, $result);
                // 如果返回值长度是0说明任务没有执行
                if (count($result) == 0) {
                    Db::name('app_xray_agent_port')->where('id', $v['id'])->update(['start_up' => 1]);

                    $agent = "/data/tools/xray/";
                    $cmd = "cd {$agent} && nohup ./xray_linux_amd64 webscan --listen 0.0.0.0:{$v['xray_agent_prot']} --json-output {$v['app_id']}.json   xray_{$v['app_id']}_{$v['xray_agent_prot']} >> /dev/null 2>&1";
                    // 执行命令
                    systemLog($cmd);
                    addlog(["xray代理模式启动", json_encode($cmd)]);
                }
            }
            sleep(3);
        }
    }

    public static function xrayAgentResult()
    {
        ini_set('max_execution_time', 0);
        $agent = "/data/tools/xray/";
        while (true) {
            $list = Db::table('app_xray_agent_port')->where('start_up', 1)->where('is_get_result', 0)->limit(10)->orderRand()->select()->toArray();
            foreach ($list as $v) {
                $filename = $agent . $v['app_id'] . '.json';
                if (!file_exists($filename)) {
                    addlog(["文件不存在:{$filename}"]);
                    continue;
                }
                $user_id = Db::name('app')->where('id', $v['app_id'])->value('user_id');
                $data = trim(file_get_contents($filename));
                $data = ($data[strlen($data)-1] == ']') ? $data : "{$data}]";
                $data = json_decode($data, true);
                foreach ($data as $value) {
                    $newData = [
                        'create_time' => substr($value['create_time'], 0, 10),
                        'detail' => json_encode($value['detail']),
                        'plugin' => json_encode($value['plugin']),
                        'target' => json_encode($value['target']),
                        'url' => $value['detail']['addr'],
                        'app_id' => $v['app_id'],
                        'user_id' => $user_id,
                        'poc' => $value['detail']['payload']
                    ];
                    echo "添加漏洞结果:" . json_encode($newData) . PHP_EOL;
                    XrayModel::addXray($newData);
                }
                Db::table('app_xray_agent_port')->where('id', $v['id'])->update(['is_get_result' => 1]);
            }
            sleep(120);
        }
    }

    public static function nucleiScan(){
        ini_set('max_execution_time', 0);
        $agent = "/data/tools/nuclei/";
        while (true) {
            $list = Db::name('app')->whereTime('nuclei_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))->where('is_delete', 0)->limit(1)->orderRand()->select()->toArray();
            foreach ($list as $v) {
                self::scanTime('app', $v['id'], 'nuclei_scan_time');

                $filename = '/tmp/nuclei.json';
                @unlink($filename);
                $cmd = "cd $agent && nuclei -u {$v['url']} -json -o nuclei.json";
                systemLog($cmd);
                if (!file_exists($filename)) {
                    addlog(["nucel扫描失败，url:{$v['url']}"]);
                    continue;
                }
                //打开一个文件
                $file = fopen($filename, "r");
                //检测指正是否到达文件的未端
                $data = [];
                while (!feof($file)) {
                    $result = fgets($file);
                    if (empty($result)) {
                        continue;
                    }
                    $arr = json_decode($result, true);
                    $data[] = [
                        'app_id'=>$v['id'],
                        'user_id'=>$v['user_id'],
                        'template'=>$arr['template'],
                        'template_url'=>$arr['template-url'],
                        'template_id'=>$arr['template-id'],
                        'name'=>$arr['info']['name'],
                        'author'=>json_encode($arr['info']['author']),
                        'tags'=>json_encode($arr['info']['tags']),
                        'description'=>isset($arr['info']['description'])?$arr['info']['description']:'',
                        'reference'=>$arr['info']['reference'],
                        'severity'=>$arr['info']['severity'],
                        'type'=>$arr['type'],
                        'host'=>$arr['host'],
                        'matched_at'=>$arr['matched-at'],
                        'extracted_results'=>isset($arr['extracted-results'])?json_encode($arr['extracted-results']):'',
                        'ip'=>$arr['ip'],
                        'curl_command'=>isset($arr['curl-command'])?json_encode($arr['curl-command']):'',
                        'status'=>isset($arr['matcher-status'])?$arr['matcher-status']?1:0:0,
                        'create_time'=>date('Y-m-d H:i:s',strtotime($arr['timestamp']))
                    ];
                }
                fclose($file);
                if (!$data) {
                    addlog(["nucel扫描数据不存在，url:{$v['url']}"]);
                    continue;
                }
                if (!Db::name('app_nuclei')->insertAll($data)) {
                    addlog(["app_nuclei数据写入失败:".json_encode($data)]);
                }
            }
            sleep(120);
        }
    }

    public static function vulmapPocTest()
    {
        ini_set('max_execution_time', 0);
        $agent = "/data/tools/vulmap/";
        while (true) {
            $list = Db::name('app')->whereTime('vulmap_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))->where('is_delete', 0)->limit(1)->orderRand()->select()->toArray();
            foreach ($list as $v) {
                self::scanTime('app', $v['id'], 'vulmap_scan_time');

                $filename = '/tmp/vulmap.json';
                @unlink($filename);
                $cmd = "cd $agent && python3 vulmap.py -u {$v['url']} --output-json {$filename}";
                systemLog($cmd);
                if (!file_exists($filename)) {
                    addlog(["vulmap扫描失败，url:{$v['url']}"]);
                    continue;
                }
                $arr = json_decode(file_get_contents($filename), true);
                if (!$arr) {
                    addlog(["{$v['url']}文件内容不存在:{$filename}"]);
                    continue;
                }
                foreach ($arr as $val) {
                    $data = [
                        'app_id' => $v['id'],
                        'user_id' => $v['user_id'],
                        'author' => $val['detail']['author'],
                        'description' => $val['detail']['description'],
                        'host' => $val['detail']['host'],
                        'port' => $val['detail']['port'],
                        'param' => json_encode($val['detail']['param']),
                        'request' => $val['detail']['request'],
                        'payload' => $val['detail']['payload'],
                        'response' => $val['detail']['response'],
                        'url' => $val['detail']['url'],
                        'plugin' => $val['plugin'],
                        'target' => json_encode($val['target']),
                        'vuln_class' => $val['vuln_class'],
                        'create_time' => substr($val['create_time'], 0, 10),
                    ];
                    if (!Db::name('app_vulmap')->insert($data)) {
                        addlog(["app_vulmap数据写入失败:{$data}"]);
                    };
                }
            }

            sleep(120);
        }
    }


    public static function crawlergoScan(){
        ini_set('max_execution_time', 0);
        $tools = "/data/tools/crawlergo/";
        while (true) {
            $list = Db::name('app')->whereTime('crawlergo_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))->where('is_delete',0)->limit(1)->orderRand()->select()->toArray();
            foreach ($list as $val) {
                self::scanTime('app',$val['id'],'crawlergo_scan_time');

                $filename = $tools.'crawlergo.json';
                @unlink($filename);

                $cmd = "cd $tools && ./cmd/crawlergo/crawlergo_cmd -c /usr/bin/google-chrome -o none --output-json $filename -f 'strict' -t 10 {$val['url']}";
                systemLog($cmd);
                if (!file_exists($filename)) {
                    addlog(["crawlergo扫描失败，url:{$val['url']}"]);
                    continue;
                }
                $result = json_decode(file_get_contents($filename),true);
                $data = [];
                foreach ($result['all_req_list'] as $v) {
                    $data[] = [
                        'app_id'=>$val['id'],
                        'user_id'=>$val['user_id'],
                        'url'=>$v['url'],
                        'method'=>$v['method'],
                        'accept'=>isset($v['headers']['Accept'])?$v['headers']['Accept']:'',
                        'cache_control'=>isset($v['headers']['Cache-Control'])?$v['headers']['Cache-Control']:'',
                        'cookie'=>isset($v['headers']['Cookie'])?$v['headers']['Cookie']:'',
                        'referer'=>isset($v['headers']['Referer'])?$v['headers']['Referer']:'',
                        'spider_name'=>isset($v['headers']['Spider-Name'])?$v['headers']['Spider-Name']:'',
                        'user_agent'=>isset($v['headers']['User-Agent'])?$v['headers']['User-Agent']:'',
                        'data'=>$v['data'],
                        'source'=>$v['source'],
                        'create_time'=>date('Y-n-d H:i:s',time())
                    ];
                }
                if ($data) {
                    Db::name('app_crawlergo')->insertAll($data);
                }
                sleep(120);
            }
            sleep(120);
        }
    }

    public static function dismapScan(){
        ini_set('max_execution_time', 0);
        $tools = "/data/tools/dismap/";
        while (true) {
            $list = Db::name('app')->whereTime('dismap_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))->where('is_delete',0)->limit(10)->orderRand()->select()->toArray();
            foreach ($list as $v) {
                self::scanTime('app',$v['id'],'dismap_scan_time');

                $filename = $tools.'dismap.txt';
                @unlink($filename);
                $cmd = "cd $tools && ./dismap -url {$v['url']} -output dismap.txt";
                systemLog($cmd);
                if (!file_exists($filename)) {
                    addlog(["dismap扫描失败，url:{$v['url']}"]);
                    continue;
                }
                //打开一个文件
                $file = fopen($filename, "r");
                //检测指正是否到达文件的未端
                $data = [];
                while (!feof($file)) {
                    $result = fgets($file);
                    if (empty($result)) {
                        continue;
                    }
                    if (preg_match('/^\[/', trim($result))) {
                        $regex = "/(?:\[)(.*?)(?:\])/i";
                        preg_match_all($regex, trim($result), $acontent);
                        $data[] = [
                            'app_id' => $v['id'],
                            'user_id' => $v['user_id'],
                            'create_time' => date('Y-m-d H:i:s',time()),
                            'result'=>json_encode($acontent[1])
                        ];
                    }
                }
                //关闭被打开的文件
                fclose($file);
                if (!$data) {
                    addlog(["dismap扫描数据不存在，url:{$v['url']}"]);
                    continue;
                }
                if (!Db::name('app_dismap')->insertAll($data)) {
                    addlog(["app_dismap数据写入失败:".json_encode($data)]);
                };
            }

            sleep(1200);
        }
    }
}
