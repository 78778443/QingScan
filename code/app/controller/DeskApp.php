<?php

namespace app\controller;

use app\BaseController;
use app\model\AppModel;
use app\model\ConfigModel;
use think\facade\Db;
use think\facade\View;
use think\Request;

class DeskApp extends BaseController
{
    public $userId = 0;
    public $auth_group_id = 0;
    public $statusArr = ["未启用", "已启用", "已删除"];

    public function initialize()
    {
        $token = \Request()->param('token');
        $this->userId = Db::name('user')->where('token', $token)->value('id');
        $this->auth_group_id = Db::name('user')->where('token', $token)->value('auth_group_id');

        if (!$this->userId) {
            return $this->apiReturn(0, [], 'token错误');
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
        $data['urls'] = Db::table('urls')->where($where)->order("app_id", 'desc')->limit(0, 15)->select()->toArray();
        $data['xray'] = Db::table('xray')->where($where)->order("app_id", 'desc')->limit(0, 15)->select()->toArray();
        $data['nuclei'] = Db::table('app_nuclei')->where($where)->order("app_id", 'desc')->limit(0, 15)->select()->toArray();
        $data['crawlergo'] = Db::table('app_crawlergo')->where($where)->order("app_id", 'desc')->limit(0, 15)->select()->toArray();
        $data['awvs'] = Db::table('awvs_vuln')->where($where)->order("app_id", 'desc')->limit(0, 15)->select()->toArray();
        //获取此域名对应主机的端口信息
        $urlInfo = parse_url($data['info']['url']);
        $ip = gethostbyname($urlInfo['host']);
        $data['host_port'] = Db::table('host_port')->where(['host' => $ip])->limit(0, 15)->select()->toArray();
        $data['host'] = Db::table('host')->where(['host' => $ip])->limit(0, 15)->select()->toArray();

        return $this->apiReturn(200, $data);
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
        $id = AppModel::addData($data);

        return $this->apiReturn(200, ['id'=>$id]);
    }

    public function index(Request $request)
    {
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
            $wafw00f = Db::name('app_wafw00f')->where('app_id', $v['id'])->find();
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

        return $this->apiReturn(200, $data);
    }


}