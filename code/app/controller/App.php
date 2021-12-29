<?php

namespace app\controller;

use app\model\AppModel;
use app\model\ConfigModel;
use app\model\HydraModel;
use app\model\UrlsModel;
use app\model\TaskModel;
use app\model\XrayModel;
use think\facade\Db;
use think\facade\View;
use think\Request;

class App extends Common
{
    public $statusArr = ["未启用", "已启用", "已删除"];

    public function a()
    {
        echo '<pre>';
        //AppModel::whatweb();
        HydraModel::sshScan();
    }

    public function index(Request $request){
        $pageSize = 15;
        $page = $request->param('page', 1);
        $statusCode = $request->param('statuscode');
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
        foreach ($data['list'] as &$v) {
            $v['is_waf'] = '否';
            $wafw00f = Db::name('app_wafw00f')->where('app_id',$v['id'])->find();
            if ($wafw00f && $wafw00f['detected']) {
                $v['is_waf'] = '是';
            }
            if ($v['is_intranet']) {
                $v['is_intranet'] = '是';
            } else {
                $v['is_intranet'] = '否';
            }
        }
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

    public function _add(Request $request)
    {
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $data['user_id'] = $this->userId;
        }
        $data['name'] = $request->param('name');
        $data['url'] = $request->param('url');
        $data['username'] = $request->param('username');
        $data['password'] = $request->param('password');
        $data['is_xray'] = $request->param('is_xray');
        $data['is_awvs'] = $request->param('is_awvs');
        $data['is_whatweb'] = $request->param('is_whatweb');
        $data['is_one_for_all'] = $request->param('is_one_for_all');
        $data['is_hydra'] = $request->param('is_hydra');
        $data['is_dirmap'] = $request->param('is_dirmap');
        $data['is_intranet'] = $request->param('is_intranet');
        AppModel::addData($data);

        return redirect(url('app/index'));
    }

    //开始抓取
    public function _start_crawler(Request $request)
    {
        $id = $request->param('id');
        $appInfo = AppModel::getInfo($id);
        $user_id = $appInfo['user_id'];
        $url = $appInfo['url'];
        TaskModel::startTask($id, $url, $user_id);
        $this->Location("/index.php?s=app/index");
    }

    //开始扫描
    public function _start_scan(Request $request)
    {
        $id = $request->param('id');
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

    public function del(Request $request)
    {
        $id = $request->param('id');
        $map[] = ['id', '=', $id];

        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $map[] = ['user_id', '=', $this->userId];
        }
        $data['info'] = Db::name('app')->where(['id'=>$id])->find();
        $urlInfo = parse_url($data['info']['url']);
        $ip = gethostbyname($urlInfo['host']);

        Db::table('app_crawlergo')->where(['app_id' => $id])->delete();
        Db::table('app_dirmap')->where(['app_id' => $id])->delete();
        Db::table('app_nuclei')->where(['app_id' => $id])->delete();
        Db::table('app_vulmap')->where(['app_id' => $id])->delete();
        Db::table('app_wafw00f')->where(['app_id' => $id])->delete();
        Db::table('app_whatweb')->where(['app_id' => $id])->delete();
        Db::table('app_whatweb_poc')->where(['app_id' => $id])->delete();
        Db::table('app_xray_agent_port')->where(['app_id' => $id])->delete();
        Db::table('awvs_app')->where(['app_id' => $id])->delete();
        Db::table('host')->where(['host' => $ip])->delete();
        Db::table('host_hydra_scan_details')->where(['app_id' => $id])->delete();
        Db::table('host_port')->where(['host' => $ip])->delete();
        Db::table('one_for_all')->where(['app_id' => $id])->delete();
        Db::table('plugin_result')->where(['app_id' => $id])->delete();
        Db::table('urls')->where(['app_id' => $id])->delete();
        Db::table('urls_sqlmap')->where(['app_id' => $id])->delete();
        Db::table('xray')->where(['app_id' => $id])->delete();

        if (Db::name('app')->where($map)->delete()) {
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
                'phone' => "17600001122",
                'department' => "信息安全",
                'code_path' => "",
                'code_scan_last' => date('Y-m-d H:i:s', strtotime('2018-06-01')),
            ];
            Db::table("app")->insert($data);
        }
    }


    public function details(Request $request)
    {
        $app_id = $request->param('id');
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
        $data['app_info'] = Db::table('app_info')->where($where)->order("app_id", 'desc')->limit(0, 15)->select()->toArray();
        $data['app_vulmap'] = Db::table('app_vulmap')->where($where)->order("app_id", 'desc')->limit(0, 15)->select()->toArray();
        $data['app_dismap'] = Db::table('app_dismap')->where($where)->order("app_id", 'desc')->limit(0, 15)->select()->toArray();
        //获取此域名对应主机的端口信息
        $urlInfo = parse_url($data['info']['url']);
        $ip = gethostbyname($urlInfo['host']);
        $data['host_port'] = Db::table('host_port')->where(['host' => $ip])->limit(0, 15)->select()->toArray();
        $data['host'] = Db::table('host')->where(['host' => $ip])->limit(0, 15)->select()->toArray();

        return View::fetch('details', $data);
    }

    public function qingkong(Request $request)
    {
        $id = $request->param('id');
        $array = array(
            'crawler_time' => '2000-01-01 00:00:00',
            'awvs_scan_time' => '2000-01-01 00:00:00',
            'subdomain_time' => '2000-01-01 00:00:00',
            'whatweb_scan_time' => '2000-01-01 00:00:00',
            'subdomain_scan_time' => '2000-01-01 00:00:00',
            'screenshot_time' => '2000-01-01 00:00:00',
            'xray_scan_time' => '2000-01-01 00:00:00',
            'dirmap_scan_time' => '2000-01-01 00:00:00',
            'wafw00f_scan_time' => '2000-01-01 00:00:00',
            'nuclei_scan_time' => '2000-01-01 00:00:00',
            'dismap_scan_time' => '2000-01-01 00:00:00',
            'crawlergo_scan_time' => '2000-01-01 00:00:00',
            'vulmap_scan_time' => '2000-01-01 00:00:00',
        );
        $data['info'] = Db::name('app')->where(['id'=>$id])->find();
        $urlInfo = parse_url($data['info']['url']);
        $ip = gethostbyname($urlInfo['host']);

        Db::table('app')->where(['id' => $id])->save($array);
        Db::table('app_crawlergo')->where(['app_id' => $id])->delete();
        Db::table('app_dirmap')->where(['app_id' => $id])->delete();
        Db::table('app_nuclei')->where(['app_id' => $id])->delete();
        Db::table('app_vulmap')->where(['app_id' => $id])->delete();
        Db::table('app_wafw00f')->where(['app_id' => $id])->delete();
        Db::table('app_whatweb')->where(['app_id' => $id])->delete();
        Db::table('app_whatweb_poc')->where(['app_id' => $id])->delete();
        Db::table('app_xray_agent_port')->where(['app_id' => $id])->delete();
        Db::table('awvs_app')->where(['app_id' => $id])->delete();
        Db::table('host')->where(['host' => $ip])->delete();
        Db::table('host_hydra_scan_details')->where(['app_id' => $id])->delete();
        Db::table('host_port')->where(['host' => $ip])->delete();
        Db::table('one_for_all')->where(['app_id' => $id])->delete();
        Db::table('plugin_result')->where(['app_id' => $id])->delete();
        Db::table('urls')->where(['app_id' => $id])->delete();
        Db::table('urls_sqlmap')->where(['app_id' => $id])->delete();
        Db::table('xray')->where(['app_id' => $id])->delete();

        return redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }


    public function start_agent(Request $request)
    {
        $id = $request->param('id');
        $where[] = ['id', '=', $id];
        $where[] = ['is_delete', '=', 0];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['user_id', '=', $this->userId];
        }
        $info = Db::name('app')->where($where)->field('id')->find();
        if (!$info) {
            return $this->apiReturn(0, [], '数据不存在');
        }
        $port = rangeCrearePort();
        $map[] = ['app_id','=',$id];
        $map[] = ['xray_agent_prot','=',$port];
        if (!Db::name('app_xray_agent_port')->where($map)->count('id')) {
            $data = [
                'app_id'=>$id,
                'xray_agent_prot'=>$port,
                'create_time'=>date('Y-m-d H:i:s',time()),
            ];
            Db::name('app_xray_agent_port')->insert($data);
        }
        return $this->apiReturn(1, [], "xray代理模式端口号：{$port}");
    }


    // 黑盒项目批量导入
    public function batch_import(Request $request){
        $file = $_FILES["file"]["tmp_name"];
        $list = $this->importExecl($file);
        unset($list[0]);
        $temp_data = [];
        foreach ($list as $k=>$v) {
            $data['name'] = $v['A'];
            $data['url'] = $v['B'];
            $data['username'] = $v['C'];
            $data['password'] = $v['D'];
            $is_xray = intval($v['E']);
            $is_awvs = intval($v['F']);
            $is_whatweb = intval($v['G']);
            $is_one_for_all = intval($v['H']);
            $is_dirmap = intval($v['I']);
            $data['is_intranet'] = intval($v['J']);

            $datetime = date('Y-m-d H:i:s', time() + 86400 * 365);
            if ($is_xray == 0) {
                $data['xray_scan_time'] = $datetime;
            }
            if ($is_awvs == 0) {
                $data['awvs_scan_time'] = $datetime;
            }
            if ($is_whatweb == 0) {
                $data['whatweb_scan_time'] = $datetime;
            }
            if ($is_one_for_all == 0) {
                $data['subdomain_scan_time'] = $datetime;
            }
            if ($is_dirmap == 0) {
                $data['dirmap_scan_time'] = $datetime;
            }
            $temp_data[] = $data;
        }
        if (Db::name('app')->insertAll($temp_data)) {
            $this->success('黑盒项目批量导入成功');
        }else{
            $this->error('黑盒项目批量导入失败');
        }
    }
}