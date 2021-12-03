<?php

namespace app\controller;

use app\model\AppModel;
use app\model\ConfigModel;
use app\model\HostModel;
use app\model\HostPortModel;
use app\model\HydraModel;
use app\model\OneForAllModel;
use app\model\UrlsModel;
use app\model\TaskModel;
use app\model\XrayModel;
use Pdp\Domain;
use Pdp\Rules;
use Pdp\TopLevelDomains;
use phpseclib3\Math\BigInteger\Engines\PHP;
use think\facade\Db;
use think\facade\View;

class App extends Common
{
    public $statusArr = ["未启用", "已启用", "已删除"];

    public function a()
    {
        echo '<pre>';
        //AppModel::whatweb();
        HydraModel::sshScan();

    }

    public function index()
    {
        $pageSize = 15;
        $page = getParam('page', 1);
        $statusCode = getParam('statuscode');
        $cms = base64_decode($_GET['cms'] ?? '');
        $server = base64_decode($_GET['server'] ?? '');

        $where = ['is_delete' => 0];
        $where = $statusCode ? array_merge($where, ['info.statuscode' => $statusCode]) : $where;
        $where = $cms ? array_merge($where, ['info.cms' => $cms]) : $where;
        $where = $server ? array_merge($where, ['info.server' => $server]) : $where;


        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where = array_merge($where, ['user_id' => $this->userId]);
        }

        $data['list'] = Db::table('app')->LeftJoin('app_info info', 'app.id = info.app_id')->where($where)->limit($pageSize)->page($page)->select()->toArray();
        $data['statuscodeArr'] = Db::table('app')->Join('app_info info', 'app.id = info.app_id')->where($where)->group('info.statuscode')->field('statuscode')->select()->toArray();
        $data['statuscodeArr'] = array_column($data['statuscodeArr'], 'statuscode');
        $data['cmsArr'] = Db::table('app')->Join('app_info info', 'app.id = info.app_id')->where($where)->group('info.cms')->field('cms')->select()->toArray();
        $data['cmsArr'] = array_column($data['cmsArr'], 'cms');
        $data['serverArr'] = Db::table('app')->Join('app_info info', 'app.id = info.app_id')->where($where)->order('id', 'desc')->group('info.server')->field('server')->select()->toArray();
        $data['serverArr'] = array_column($data['serverArr'], 'server');

        $data['pageSize'] = $pageSize;
        $data['count'] = Db::table('app')->Join('app_info info', 'app.id = info.app_id')->where($where)->count();
        $configArr = ConfigModel::getNameArr();
        $data['statusArr'] = $this->statusArr;
        $data['GET'] = $_GET;
        // 获取分页显示
        $data['page'] = Db::name('app')->where($where)->LeftJoin('app_info info', 'app.id = info.app_id')->paginate($pageSize)->render();
        $data = array_merge($data, $configArr);
        return View::fetch('index', $data);
    }

    public function add()
    {
        $this->show('app/add');
    }

    public function _add()
    {
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $_POST['user_id'] = $this->userId;
        }
        AppModel::addData($_POST);

        $this->success('添加成功', 'app/index');
    }

    //开始抓取
    public function _start_crawler()
    {
        $id = getParam('id');
        $appInfo = AppModel::getInfo($id);
        $user_id = $appInfo['user_id'];
        $url = $appInfo['url'];
        TaskModel::startTask($id, $url, $user_id);
        $this->Location("/index.php?s=app/index");
    }

    //开始扫描
    public function _start_scan()
    {
        $id = getParam('id');
        $appInfo = AppModel::getInfo($id);
        $user_id = $appInfo['user_id'];
        $url = $appInfo['url'];
        TaskModel::startTask($id, $url, $user_id);
        $this->Location("/index.php?s=app/index");
    }

    public function _start_scan_app()
    {

        addlog(['开始进行扫描', $_POST]);

        //接收参数
        $appId = getParam('app_id');

        //查询URL地址
        $urlList = UrlsModel::getCrawlerList($appId);

        //扫描URL地址
        foreach ($urlList as $urlInfo) {
            //插入到xray队列
            XrayModel::sendTask($urlInfo['id'], $urlInfo['url']);
        }

        $this->Location("/index.php?s=app/index");
    }

    public function del()
    {
        $id = getParam('id');
        $map[] = ['id', '=', $id];

        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $map[] = ['user_id', '=', $this->userId];
        }

        if (Db::name('app')->where($map)->update(['is_delete' => 1, 'status' => -1])) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    public function load_host()
    {
        //查询host
        $hostList = Db::table('host_port')->field('id,service,host,port')->whereIn('service', ['http'])->select()->toArray();

        foreach ($hostList as $key => $value) {
            $url = "{$value['service']}://{$value['host']}:{$value['port']}/";

            //添加数据到app
            if (Db::table("app")->where(['url' => $url])->find() != null) {
                print_r("数据已存在{$url}" . PHP_EOL);
                continue;
            }

            $headerInfo = curl_get_header($url);
            if (empty($headerInfo)) {
                print_r("地址请求为空{$url}" . PHP_EOL);
                unset($hostList[$key]);
                continue;
            }

            //处理数据
            $data = [
                'status' => 1,
                'name' => $value['host'],
                'url' => $url,
                'rad' => 1,
                'xray' => 1,
                'contact' => "汤青松",
                'phone' => "17600225914",
                'department' => "信息安全",
                'code_path' => "",
                'code_scan_last' => date('Y-m-d H:i:s', strtotime('2018-06-01')),
            ];
            Db::table("app")->insert($data);
        }


    }

    public function pachong()
    {
        $radPath = __BASEPATH__ . "/tools/rad";
        while (true) {
            $scanTime = date('Y-m-d', time() - 7 * 86400);
            $appList = Db::table('app')->whereTime('crawler_time', '<', $scanTime)->limit(20)->select()->toArray();
            $retPath = "/tmp/result.json";

            foreach ($appList as $value) {
                $cmd = "cd {$radPath} && ./rad -t {$value['url']}  -json {$retPath}";
                exec($cmd);
                $urlList = json_decode(file_get_contents($retPath), true);
                var_dump($cmd, array_column($urlList, 'URL'));
                unlink($retPath);
                //            var_dump($cmd,array_column($urlList,'URL'));

                foreach ($urlList as $item) {
                    if (strlen($item['URL']) >= 1024) {
                        continue;
                    }
                    $data = [
                        'method' => $item['Method'],
                        'app_id' => $value['id'],
                        'url' => $item['URL'],
                        'hash' => md5($item['URL']),
                    ];


                    if (Db::table('urls')->where(['hash' => md5($data['url'])])->find() == null) {
                        DB::table('urls')->insert($data);
                    }
                }


                DB::table('app')->where(['id' => $value['id']])->update(['crawler_time' => date('Y-m-d H:i:s')]);
            }

            sleep(30);
        }
    }

    public function xray()
    {
        $radPath = __BASEPATH__ . "/tools/xray";
        while (true) {
            $scanTime = date('Y-m-d', time() - 7 * 86400);
            $appList = Db::table('urls')->whereTime('scan_time', '<', $scanTime)->limit(20)->select()->toArray();
            $retPath = "/tmp/xray_result.json";

            foreach ($appList as $urlInfo) {
                file_exists($retPath) && unlink($retPath);
                $cmd = "cd {$radPath} && ./xray webscan --url  '{$urlInfo['url']}'   --json-output {$retPath}";
                echo $cmd . PHP_EOL;
                exec($cmd);

                if (file_exists($retPath) == false) {
                    echo "没有输出, {$urlInfo['url']} " . PHP_EOL;
                    continue;
                }

                $bugList = json_decode(file_get_contents($retPath), true);

                foreach ($bugList as &$item) {
                    foreach ($item as &$val) {
                        $val = is_string($val) ? $val : json_encode($val, JSON_UNESCAPED_UNICODE);
                    }
                }
                var_dump($cmd, $bugList);
                die;


                //                foreach ($appList as $item) {
                //                    if (strlen($item['URL']) >= 1024) {
                //                        continue;
                //                    }
                //                    $data = [
                //                        'method' => $item['Method'],
                //                        'app_id' => $value['id'],
                //                        'url' => $item['URL'],
                //                        'hash' => md5($item['URL']),
                //                    ];
                //
                //
                //                    if (Db::table('urls')->where(['hash' => md5($data['url'])])->find() == null) {
                //                        DB::table('urls')->insert($data);
                //                    }
                //                }
                //
                //
                //                DB::table('app')->where(['id' => $value['id']])->update(['crawler_time' => date('Y-m-d H:i:s')]);
            }

            sleep(30);
        }
    }

    public function autoDeleteApp()
    {
        $lastId = intval(file_get_contents('./tmp/appLastId.txt'));
        $httpList = true;
        while ($httpList) {


            //获取网站服务
            $field = 'id,url';
            $httpList = Db::table('app')->where('id', '>', $lastId)->order('id', 'asc')->limit(10)->field($field)->select()->toArray();

            $urlCheck = __BASEPATH__ . "/tools/url-survival-check";
            $urlStr = '';
            foreach ($httpList as $value) {
                $lastId = $value['id'];
                file_put_contents('./tmp/appLastId.txt', $lastId);
                $url = $value['url'];
                $urlStr .= $url . PHP_EOL;
            }
            $tempUrlFile = dirname(dirname(__DIR__)) . "/tmp/app_delete_urls.txt";
            file_put_contents($tempUrlFile, $urlStr);

            $survivalUrl = [];
            $cmd = "cd {$urlCheck} && python3 url_survival_check_min.py {$tempUrlFile}";
            exec($cmd, $survivalUrl);

            foreach ($survivalUrl as $url) {
                if (strstr($url, 'Not')) {
                    echo "URL 已不可访问:{$url}" . PHP_EOL;
                    $urlArr = explode(" ", $url);

                    Db::table('app')->where(['url' => $urlArr[0]])->delete();
                }
            }
        }
    }

    public function autoAddApp()
    {
        $lastId = intval(file_get_contents('./tmp/lastId.txt'));
        $httpList = true;
        while ($httpList) {
            //获取网站服务
            $field = 'id,host,port,service';
            $httpList = Db::table('host_port')->where('id', '>', $lastId)->whereIn('service', ['http', 'https'])->order('id', 'asc')->limit(50)->field($field)->select()->toArray();
            $tempUrlFile = dirname(dirname(__DIR__)) . "/tmp/app_urls.txt";

            $urlCheck = __BASEPATH__ . "/tools/url-survival-check";
            $urlStr = '';
            foreach ($httpList as $value) {
                $portStr = ":{$value['port']}";
                $lastId = $value['id'];
                file_put_contents('./tmp/lastId.txt', $lastId);

                $url = "{$value['service']}://{$value['host']}{$portStr}";
                $url .= "/";
                $urlStr .= $url . PHP_EOL;
            }

            file_put_contents($tempUrlFile, $urlStr);
            $cmd = "cd {$urlCheck} && python3 url_survival_check_min.py {$tempUrlFile}";
            exec($cmd, $survivalUrl);

            foreach ($survivalUrl as $url) {
                if (strstr($url, 'Not') == false) {
                    $urlInfo = parse_url($url);
                    $data = ['url' => $url, 'name' => "{$urlInfo['host']}-{$urlInfo['port']}"];
                    $appInfo = Db::table('app')->where(['url' => $url])->find();
                    if (empty($appInfo)) {
                        Db::table('app')->insert($data);
                    } else {
                        echo "URL已存在:{$url}" . PHP_EOL;
                    }
                }
            }
        }

    }


    public function getInfo()
    {
        $lastIdFile = __BASEPATH__ . '/tmp/appInfoLastId.txt';
        $lastId = intval(file_get_contents($lastIdFile));
        $httpList = true;
        //定义path
        $urlCheck = __BASEPATH__ . "/tools/Ehole3";
        $tempUrlFile = __BASEPATH__ . "/tmp/app_info_urls.txt";
        $jsonInfoFile = __BASEPATH__ . "/tmp/app_info_urls.json";
        while ($httpList) {
            //获取网站服务
            $field = 'id,url';
            $httpList = Db::table('app')->where('id', '>', $lastId)->order('id', 'asc')->limit(5)->field($field)->select()->toArray();

            //拼接URL地址
            $urlStr = implode("\r\n", array_column($httpList, 'url'));
            file_put_contents($tempUrlFile, $urlStr);
            //记录当前最后的URL地址
            $lastId = $httpList[count($httpList) - 1]['id'];
            file_put_contents($lastIdFile, $lastId);


            $cmd = "cd {$urlCheck} && ./Ehole3.0-linux -l {$tempUrlFile} -json {$jsonInfoFile}";
            exec($cmd);
            $jsonStr = trim(file_get_contents($jsonInfoFile));
            $jsonArr = array_filter(explode("\n", $jsonStr));


            $urlToIdArr = array_column($httpList, 'id', 'url');

            foreach ($jsonArr as $value) {
                $appInfo = json_decode($value, true);
                if (empty($appInfo)) {
                    continue;
                }

                if (!isset($urlToIdArr[$appInfo['url']])) {
                    echo "没有匹配到URL地址:{$appInfo['url']}";
                    continue;
                }

                $appInfo['app_id'] = $urlToIdArr[$appInfo['url']];
                $appInfo['cms'] = json_encode($appInfo['cms'], JSON_UNESCAPED_UNICODE);


                if (Db::table('app_info')->where(['url' => $appInfo['url']])->find()) {
                    echo "URL:{$appInfo['url']}已经存在" . PHP_EOL;
                    continue;
                }
                Db::table('app_info')->insert($appInfo);
            }
        }
    }

    public function details()
    {
        $app_id = getParam('id');
        $where[] = ['app_id', '=', $app_id];
        $map[] = ['id', '=', $app_id];
        $where1 = [];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            //$where[] = ['user_id','=',$this->userId];
            $map[] = ['user_id', '=', $this->userId];
            $where1[] = ['user_id', '=', $this->userId];
        }
        $data['info'] = Db::name('app')->where($map)->find();
        $data['whatweb'] = Db::table('app_whatweb')->where($where)->where($where1)->order("id", 'desc')->limit(0, 15)->select()->toArray();
        $data['oneforall'] = Db::table('one_for_all')->where($where)->order("id", 'desc')->limit(0, 15)->select()->toArray();
        $data['hydra'] = Db::table('host_hydra_scan_details')->where($where)->order("id", 'desc')->limit(0, 15)->select()->toArray();
        $data['dirmap'] = Db::table('app_dirmap')->where($where)->where($where1)->order("id", 'desc')->limit(0, 15)->select()->toArray();
        $data['sqlmap'] = Db::table('urls_sqlmap')->where($where)->order("id", 'desc')->limit(0, 15)->select()->toArray();
        return View::fetch('details', $data);
    }

    public function start_agent()
    {
        $id = getParam('id');
        $where[] = ['id', '=', $id];
        $where[] = ['is_delete', '=', 0];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        $info = Db::name('app')->where($where)->field('id,xray_agent_prot')->find();
        if (!$info) {
            return $this->apiReturn(0, [], '数据不存在');
        }
        // 执行命令查看任务是否已经执行
        $cmd = "ps -ef | grep 'xray_" . $info['id'] . "_" . $info['xray_agent_prot'] . "' | grep -v ' grep'";
        $result = [];
        exec($cmd, $result);
        // 如果返回值长度是0说明任务没有执行
        if (count($result) == 0) {
            $port = rangeCrearePort();
            Db::name('app')->where('id', $id)->update(['xray_agent_prot' => $port]);
            $agent = "/data/tools/xray/";
            $cmd = "cd {$agent} && nohup ./xray_linux_amd64 webscan --listen 0.0.0.0:{$port} --json-output {$info['id']}.json   xray_{$info['id']}_{$port} >> /dev/null 2>&1";
            // 执行命令
            systemLog($cmd);
            return $this->apiReturn(1, [], "xray代理模式已启动,端口号：{$port}");
        } else {
            return $this->apiReturn(1, [], "xray代理模式已启动,端口号：{$info['xray_agent_prot']}");
        }
    }
}