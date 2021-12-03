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
            Log::write("RAD 运行依赖环境不存在，请安装chrome环境~");
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
                    unlink($pathArr['tool_result']);
                }
                if (file_exists($pathArr['cmd_result'])) {
                    addlog(["清理老文件", $pathArr['tool_result']]);
                    unlink($pathArr['cmd_result']);
                }

                // 设置代理
                $filename = '/data/tools/rad/rad_config.yaml';
                $arr = @yaml_parse_file($filename);
                if ($arr) {
                    $proxyArr = Db::name('proxy')->where('status',1)->limit(3)->orderRand()->select();
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
                        yaml_emit_file($filename,$arr);
                    }
                }


                $cmd = "{$path} ./rad_linux_amd64 -t  \"{$url}\"  -json  {$pathArr['tool_result']}";

//                echo $cmd;die;
                addlog(["开始执行抓取URL地址命令", $cmd]);

                $result = [];
                exec($cmd, $result);

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
                        'user_id'=>$user_id
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
            $list = Db::table('app')->whereTime('xray_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))->where('is_delete',0)->limit(1)->orderRand()->select()->toArray();
            foreach ($list as $val) {
                $url = $val['url'];
                $id = $val['id'];
                $user_id = $val['user_id'];
                addlog(["XRAY开始执行扫描任务", $id, $url]);
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

                    $cmd = "{$path} ./xray_linux_amd64 webscan --url \"{$url}\"  --json-output  {$pathArr['tool_result']}";
                    addlog(["开始执行漏洞扫描命令", base64_encode($cmd)]);

                    $result = [];
                    exec($cmd, $result);
                    $result = implode("\n", $result);
                    addlog(["漏洞扫描结束", $id, $url, $cmd, base64_encode($result)]);

                    $result = file_put_contents($pathArr['cmd_result'], $result);
                    if ($result == false) {
                        addlog(["写入执行结果失败", base64_encode($pathArr['cmd_result'])]);
                    }
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
                            'user_id'=>$user_id,
                            'poc' => $value['detail']['payload']
                        ];
                        echo "添加漏洞结果:" . json_encode($newData) . PHP_EOL;
                        XrayModel::addXray($newData);
                    }

                    Db::table('app')->where(['id' => $id])->save(['xray_scan_time' => date('Y-m-d H:i:s')]);
                } else {
                    Log::write("文件不存在:{$pathArr['tool_result']}  ,扫描URL失败: {$url}");
                    Db::table('app')->where(['id' => $id])->save(['xray_scan_time' => date('2048-m-d H:i:s')]);
                }
            }
            sleep(10);
        }
    }

    public static function xrayAgentResult(){
        ini_set('max_execution_time', 0);
        $agent = "/data/tools/xray/";
        while (true) {
            $list = Db::table('app')->where('xray_agent_prot','<>',0)->where('is_delete',0)->limit(2)->orderRand()->select()->toArray();
            foreach ($list as $k=>$v) {
                $filename = $agent.$v['id'].'.json';
                if (!file_exists($filename)) {
                    addlog(["文件不存在:{$filename}"]);
                    continue;
                }
                $data = json_decode(file_get_contents($agent.$v['id']), true);
                foreach ($data as $value) {
                    $newData = [
                        'detail' => json_encode($value['detail']),
                        'plugin' => json_encode($value['plugin']),
                        'target' => json_encode($value['target']),
                        'app_id' => $v['app_id'],
                        'user_id'=>$v['user_id'],
                        'xray_id'=>$v['id'],
                    ];
                    if ($v['user_id']) {
                        $where[] = ['user_id','=',$v['user_id']];
                    }
                    $where[] = ['app_id','=',$v['app_id']];
                    $where[] = ['xray_id','=',$v['id']];
                    if (Db::name('xray_agent_result')->where($where)->count('id')) {
                        Db::name('xray_agent_result')->where($where)->update($newData);
                    } else {
                        $newData['create_time'] = substr($value['create_time'], 0, 10);
                        Db::name('xray_agent_result')->insert($newData);
                    }
                }
            }
            sleep(120);
        }
    }
}
