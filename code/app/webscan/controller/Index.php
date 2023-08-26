<?php

namespace app\webscan\controller;

use app\controller\Common;
use app\model\ConfigModel;
use app\webscan\model\XrayModel;
use Org\Util\Date;
use think\facade\Db;
use think\facade\View;
use think\Request;

class Index extends Common
{
    public $statusArr = ["未启用", "已启用", "已删除"];
    public $tools = [
        'xray' => 'xray',
        'awvs' => 'awvs',
        'rad' => 'rad爬虫',
        'auto_add_host' => '解析主机记录',
        'unauthorize' => '未授权扫描',
        #'nmap'=>'nmap',
        'masscan' => 'masscan',
        'whatweb' => 'whatweb',
        'subdomain' => 'subdomain',
        'hydra' => 'hydra',
        'sqlmap' => 'sqlmap',
        'dirmapScan' => 'dirmapScan',
        'wafw00f' => 'wafw00f',
        'nuclei' => 'nuclei',
        'vulmap' => 'vulmap',
        'dismap' => 'dismap',
        'crawlergo' => 'crawlergo',
    ];

    public function index(Request $request)
    {
        if (function_exists('addWebscanTarget')) addWebscanTarget();
        $pageSize = 15;
        $statusCode = $request->param('statuscode');
        $cms = base64_decode($_GET['cms'] ?? '');
        $server = base64_decode($_GET['server'] ?? '');

        $where = ['is_delete' => 0];
        $where = $statusCode ? array_merge($where, ['info.statuscode' => $statusCode]) : $where;
        $where = $cms ? array_merge($where, ['info.cms' => $cms]) : $where;
        $where = $server ? array_merge($where, ['info.server' => $server]) : $where;

        $where1 = [];

        $list = Db::table('app')->LeftJoin('app_info info', 'app.id = info.app_id')->where($where)->paginate([
            'list_rows' => $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        $data['statuscodeArr'] = Db::table('app')->Join('app_info info', 'app.id = info.app_id')->where($where)->group('info.statuscode')->field('statuscode')->select()->toArray();
        $data['statuscodeArr'] = array_column($data['statuscodeArr'], 'statuscode');
        $data['cmsArr'] = Db::table('app')->Join('app_info info', 'app.id = info.app_id')->where($where)->group('info.cms')->field('cms')->select()->toArray();
        $data['cmsArr'] = array_column($data['cmsArr'], 'cms');
        $data['serverArr'] = Db::table('app')->Join('app_info info', 'app.id = info.app_id')->where($where)->group('info.server')->field('server')->select()->toArray();
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
            if ($v['status'] == 1) {
                $v['status'] = '启用';
            } else {
                $v['status'] = '暂停';
            }
            // 数据统计

            $v['name'] = parse_url($v['url'])['host'];
            $v['oneforall_num'] = Db::table('one_for_all')->where('app_id', $v['id'])->where($where1)->count('id');
            $v['dirmap_num'] = Db::table('app_dirmap')->where('app_id', $v['id'])->where($where1)->count('id');
            $v['sqlmap_num'] = Db::table('urls_sqlmap')->where('app_id', $v['id'])->where($where1)->count('id');
            $v['vulmap_num'] = Db::table('app_vulmap')->where('app_id', $v['id'])->where($where1)->count('id');
            //$data['dismap_num'] = Db::table('app_dismap')->where($where1)->count('id');
            $v['urls_num'] = Db::table('asm_urls')->where('app_id', $v['id'])->where($where1)->count('id');
            $v['xray_num'] = Db::table('xray')->where('app_id', $v['id'])->where($where1)->count('id');
            //$data['nuclei_num'] = Db::table('app_nuclei')->where($where1)->count('id');
            $v['crawlergo_num'] = Db::table('app_crawlergo')->where('app_id', $v['id'])->where($where1)->count('id');
            $v['awvs_num'] = Db::table('awvs_vuln')->where('app_id', $v['id'])->where($where1)->count('id');
            $v['namp_num'] = Db::table('asm_host_port')->where('app_id', $v['id'])->where($where1)->count('id');
            $v['host_num'] = Db::table('asm_host')->where('app_id', $v['id'])->where($where1)->count('id');
        }
        $configArr = ConfigModel::getNameArr();
        $data['statusArr'] = $this->statusArr;
        $data['tools_list'] = $this->tools;
        $data = array_merge($data, $configArr);
        return View::fetch('index', $data);
    }

    public function _add(Request $request)
    {
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $data['user_id'] = $this->userId;
        }
        $urlArr = $this->processUrls($request->param('url'));
        $tools = $request->param('tools');
        foreach ($urlArr as $url) {
            $this->addTarget($url, $tools);
        }

        return redirect(url('index/index'));
    }

    private function processUrls($inputString)
    {
        $lines = explode("\n", $inputString);
        $urls = [];

        foreach ($lines as $line) {
            $url = trim($line);
            if (empty($url)) continue;
            if (!preg_match('#^https?://#i', $url)) {
                $url = "http://{$url}/";
            }

            if (filter_var($url, FILTER_VALIDATE_URL) === false) continue;
            $urls[] = $url;
        }

        return $urls;
    }


    private function addTarget($url, $tools)
    {
        $host = parse_url($url)['host'];
        $data = ['url' => $url, 'name' => $host];
        $project_id = Db::name('app')->insertGetId($data);

        // 写入到关键词监控表中
        if (empty($host)) return true;
        $data = ['user_id' => $this->userId, 'app_id' => $project_id, 'title' => $host];
        Db::name('github_keyword_monitor')->insert($data);

        //写入到要执行的工具表中
        if (empty($tools)) return true;
        $project_tools_data = [];
        foreach ($tools as $k => $v) {
            $project_tools_data[] = ['type' => 1, 'project_id' => $project_id, 'tools_name' => $v];
        }
        Db::name('project_tools')->where('project_id', $project_id)->where('type', 1)->delete();
        Db::name('project_tools')->insertAll($project_tools_data);
    }


    public function details(Request $request)
    {
        $app_id = $request->param('id', '0', 'intval');
        $where[] = ['app_id', '=', $app_id];
        $map[] = ['id', '=', $app_id];
        $where1 = [];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            //$where[] = ['user_id','=',$this->userId];
            $map[] = ['user_id', '=', $this->userId];
            $where1[] = ['user_id', '=', $this->userId];
        }
        $data['info'] = Db::name('app')->where($map)->find();
        if (!$data['info']) {
            return $this->error('数据不存在');
        }
        $data['whatweb'] = Db::table('app_whatweb')->where($where)->where($where1)->order("id", 'desc')->limit(0, 15)->select()->toArray();
        $data['oneforall'] = Db::table('one_for_all')->where($where)->order("id", 'desc')->limit(0, 15)->select()->toArray();
        $data['hydra'] = Db::table('host_hydra_scan_details')->where($where)->order("id", 'desc')->limit(0, 15)->select()->toArray();
        $data['dirmap'] = Db::table('app_dirmap')->where($where)->where($where1)->order("id", 'desc')->limit(0, 15)->select()->toArray();
        $data['sqlmap'] = Db::table('urls_sqlmap')->where($where)->order("id", 'desc')->limit(0, 15)->select()->toArray();
        $data['app_info'] = Db::table('app_info')->where($where)->order("app_id", 'desc')->limit(0, 15)->select()->toArray();
        $data['app_vulmap'] = Db::table('app_vulmap')->where($where)->order("app_id", 'desc')->limit(0, 15)->select()->toArray();
        $data['app_dismap'] = Db::table('app_dismap')->where($where)->order("app_id", 'desc')->limit(0, 15)->select()->toArray();
        $data['urls'] = Db::table('asm_urls')->where($where)->order("app_id", 'desc')->limit(0, 15)->select()->toArray();
        $data['xray'] = Db::table('xray')->where($where)->order("app_id", 'desc')->limit(0, 15)->select()->toArray();
        $data['nuclei'] = Db::table('app_nuclei')->where($where)->order("app_id", 'desc')->limit(0, 15)->select()->toArray();
        $data['crawlergo'] = Db::table('app_crawlergo')->where($where)->order("app_id", 'desc')->limit(0, 15)->select()->toArray();
        $data['awvs'] = Db::table('awvs_vuln')->where($where)->order("app_id", 'desc')->limit(0, 15)->select()->toArray();
        $data['pluginScanLog'] = Db::table('plugin_scan_log')->alias('a')
            ->leftJoin('plugin b', 'b.id=a.plugin_id')
            ->where($where)
            ->where('is_custom', 2)
            ->field('a.*,b.name,b.result_file')
            ->order("a.id", 'desc')
            ->limit(0, 15)
            ->select()
            ->toArray();
        //获取此域名对应主机的端口信息
        //$urlInfo = parse_url($data['info']['url']);
        //$ip = gethostbyname($urlInfo['host']);
        $data['host_port'] = Db::table('asm_host_port')->where(['app_id' => $app_id])->limit(0, 15)->select()->toArray();
        $data['host'] = Db::table('asm_host')->where(['app_id' => $app_id])->limit(0, 15)->select()->toArray();
        $data['host_id'] = isset($data['host'][0]['id']) ? $data['host'][0]['id'] : 9999;

        $data['monitor_notice'] = Db::table('github_keyword_monitor_notice')->where($where)->order("app_id", 'desc')->limit(0, 15)->select()->toArray();

        return View::fetch('details', $data);
    }

    // 重新扫描
    public function qingkong(Request $request)
    {
        $id = $request->param('id', '', 'intval');
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
        $where[] = ['id', '=', $id];

        $data['info'] = Db::name('app')->where($where)->find();
        if (!$data['info']) {
            $this->error('黑盒数据不存在');
        }
        Db::table('app')->where(['id' => $id])->save($array);
        Db::table('app_info')->where(['app_id' => $id])->delete();
        Db::table('app_crawlergo')->where(['app_id' => $id])->delete();
        Db::table('app_dirmap')->where(['app_id' => $id])->delete();
        Db::table('app_dismap')->where(['app_id' => $id])->delete();
        Db::table('app_nuclei')->where(['app_id' => $id])->delete();
        Db::table('app_vulmap')->where(['app_id' => $id])->delete();
        Db::table('app_wafw00f')->where(['app_id' => $id])->delete();
        Db::table('app_whatweb')->where(['app_id' => $id])->delete();
        Db::table('app_whatweb_poc')->where(['app_id' => $id])->delete();
        Db::table('app_xray_agent_port')->where(['app_id' => $id])->delete();
        Db::table('awvs_app')->where(['app_id' => $id])->delete();
        Db::table('awvs_vuln')->where(['app_id' => $id])->delete();
        Db::table('asm_host')->where(['app_id' => $id])->delete();
        Db::table('host_hydra_scan_details')->where(['app_id' => $id])->delete();
        Db::table('asm_host_port')->where(['app_id' => $id])->delete();
        Db::table('one_for_all')->where(['app_id' => $id])->delete();
        Db::table('plugin_scan_log')->where(['app_id' => $id])->delete();
        Db::table('asm_urls')->where(['app_id' => $id])->delete();
        Db::table('urls_sqlmap')->where(['app_id' => $id])->delete();
        Db::table('xray')->where(['app_id' => $id])->delete();
        Db::table('plugin_scan_log')->where(['app_id' => $id])->delete();

        return redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }

    // 启用-暂停扫描
    public function suspend_scan(Request $request)
    {
        $ids = $request->param('ids');
        $status = $request->param('status');
        if (!$ids) {
            return $this->apiReturn(0, [], '请选择要重新扫描的数据');
        }
        $where[] = ['id', 'in', $ids];

        $data['info'] = Db::name('app')->where($where)->find();
        if (!$data['info']) {
            return $this->apiReturn(0, [], '黑盒数据不存在');
        }
        if ($status == 1) { // 启用
            Db::name('app')->where($where)->where('status', 2)->update(['status' => 1]);
            $this->success('扫描已启用');
        } elseif ($status == 2) { // 暂停
            Db::name('app')->where($where)->where('status', 1)->update(['status' => 2]);
            $this->success('扫描已暂停');
        }
    }

    // 单个工具重新扫描
    public function rescan(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $where[] = ['id', '=', $id];

        $info = Db::name('app')->where($where)->find();
        if (!$info) {
            $this->error('黑盒数据不存在');
        }
        $tools_name = $request->param('tools_name', '');

        switch ($tools_name) {
            case 'rad':
                $data = [
                    'crawler_time' => '2000-01-01 00:00:00'
                ];
                Db::table('asm_urls')->where(['app_id' => $id])->delete();
                Db::table('urls_sqlmap')->where(['app_id' => $id])->delete();
                break;
            case 'crawlergoScan':
                $data = [
                    'crawlergo_scan_time' => '2000-01-01 00:00:00',
                ];
                Db::table('app_crawlergo')->where(['app_id' => $id])->delete();
                break;
            case 'awvsScan':
                $data = [
                    'awvs_scan_time' => '2000-01-01 00:00:00',
                ];
                Db::table('awvs_app')->where(['app_id' => $id])->delete();
                Db::table('awvs_vuln')->where(['app_id' => $id])->delete();
                break;
            case 'nucleiScan':
                $data = [
                    'nuclei_scan_time' => '2000-01-01 00:00:00',
                ];
                Db::table('app_nuclei')->where(['app_id' => $id])->delete();
                break;
            case 'xray':
                $data = [
                    'xray_scan_time' => '2000-01-01 00:00:00',
                ];
                Db::table('xray')->where(['app_id' => $id])->delete();
                break;
            case 'getBaseInfo':
                $data = [
                    'screenshot_time' => '2000-01-01 00:00:00',
                ];
                Db::table('app_info')->where(['app_id' => $id])->delete();
                break;
            case 'whatweb':
                $data = [
                    'whatweb_scan_time' => '2000-01-01 00:00:00',
                ];
                Db::table('app_whatweb')->where(['app_id' => $id])->delete();
                Db::table('app_whatweb_poc')->where(['app_id' => $id])->delete();
                break;
            case 'sqlmapScan':
                Db::table('asm_urls')->where(['app_id' => $id])->update(['sqlmap_scan_time' => '2000-01-01 00:00:00']);
                Db::table('urls_sqlmap')->where(['app_id' => $id])->delete();
                break;
            case 'subdomainScan':
                $data = [
                    'subdomain_scan_time' => '2000-01-01 00:00:00',
                ];
                Db::table('one_for_all')->where(['app_id' => $id])->delete();
                break;
            case 'sshScan':
                Db::table('asm_host')->where(['app_id' => $id])->update(['hydra_scan_time' => '2000-01-01 00:00:00']);
                Db::table('host_hydra_scan_details')->where(['app_id' => $id])->delete();
                break;
            case 'dirmapScan':
                $data = [
                    'dirmap_scan_time' => '2000-01-01 00:00:00',
                ];
                Db::table('app_dirmap')->where(['app_id' => $id])->delete();
                break;
            case 'NmapPortScan':
                Db::table('asm_host_port')->where(['app_id' => $id])->update(['service' => null]);
                break;
            case 'vulmapPocTest':
                $data = [
                    'vulmap_scan_time' => '2000-01-01 00:00:00',
                ];
                Db::table('app_vulmap')->where(['app_id' => $id])->delete();
                break;
            case 'autoAddHost':
                Db::table('asm_host')->where(['app_id' => $id])->delete();
                Db::table('asm_host_port')->where(['app_id' => $id])->delete();
                Db::table('host_hydra_scan_details')->where(['app_id' => $id])->delete();
                break;
            case 'dismapScan':
                $data = [
                    'dismap_scan_time' => '2000-01-01 00:00:00',
                ];
                Db::table('app_dismap')->where(['app_id' => $id])->delete();
                break;
            case 'plugin':
                Db::table('plugin_scan_log')->where(['app_id' => $id])->delete();
                break;
            default:
                $this->error('参数错误');
                break;
        }
        Db::table('plugin_scan_log')->where(['app_id' => $id, 'scan_type' => 0, 'plugin_name' => $tools_name])->delete();
        if (!empty($data)) {
            $this->addUserLog('目标管理', "重新扫描数据[{$id}],工具[{$tools_name}]操作成功");
            Db::table('app')->where(['id' => $id])->update($data);
        }
        return redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }

    // 批量重新扫描
    public function again_scan(Request $request)
    {
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
        $ids = $request->param('ids');
        if (!$ids) {
            return $this->apiReturn(0, [], '请选择要重新扫描的数据');
        }
        $where[] = ['id', 'in', $ids];
        $map[] = ['app_id', 'in', $ids];

        $data['info'] = Db::name('app')->where($where)->find();
        if (!$data['info']) {
            return $this->apiReturn(0, [], '黑盒数据不存在');
        }
        Db::table('app')->where($where)->save($array);
        Db::table('app_info')->where($map)->delete();
        Db::table('app_crawlergo')->where($map)->delete();
        Db::table('app_dirmap')->where($map)->delete();
        Db::table('app_dismap')->where($map)->delete();
        Db::table('app_nuclei')->where($map)->delete();
        Db::table('app_vulmap')->where($map)->delete();
        Db::table('app_wafw00f')->where($map)->delete();
        Db::table('app_whatweb')->where($map)->delete();
        Db::table('app_whatweb_poc')->where($map)->delete();
        Db::table('app_xray_agent_port')->where($map)->delete();
        Db::table('awvs_app')->where($map)->delete();
        Db::table('awvs_vuln')->where($map)->delete();
        Db::table('asm_host')->where($map)->delete();
        Db::table('host_hydra_scan_details')->where($map)->delete();
        Db::table('asm_host_port')->where($map)->delete();
        Db::table('one_for_all')->where($map)->delete();
        Db::table('plugin_scan_log')->where($map)->delete();
        Db::table('asm_urls')->where($map)->delete();
        Db::table('urls_sqlmap')->where($map)->delete();
        Db::table('xray')->where($map)->delete();
        Db::table('plugin_scan_log')->where($map)->delete();
        $this->addUserLog('目标管理', "重新扫描[{$ids}]操作成功");
        return $this->apiReturn(1, [], '操作成功');
    }

    // 删除
    public function del(Request $request)
    {
        $id = $request->param('id', '0', 'intval');
        $map[] = ['id', '=', $id];


        $data['info'] = Db::name('app')->where(['id' => $id])->find();
        if (!empty($data)) {
            $urlInfo = parse_url($data['info']['url']);
            $ip = gethostbyname($urlInfo['host'] ?? '127.0.0.1');
            Db::table('app_info')->where(['app_id' => $id])->delete();
            Db::table('asm_host')->where(['host' => $ip])->delete();
            Db::table('asm_host_port')->where(['host' => $ip])->delete();
        }
        Db::table('app_crawlergo')->where(['app_id' => $id])->delete();
        Db::table('app_dirmap')->where(['app_id' => $id])->delete();
        Db::table('app_nuclei')->where(['app_id' => $id])->delete();
        Db::table('app_vulmap')->where(['app_id' => $id])->delete();
        Db::table('app_wafw00f')->where(['app_id' => $id])->delete();
        Db::table('app_whatweb')->where(['app_id' => $id])->delete();
        Db::table('app_whatweb_poc')->where(['app_id' => $id])->delete();
        Db::table('app_xray_agent_port')->where(['app_id' => $id])->delete();
        Db::table('awvs_app')->where(['app_id' => $id])->delete();
        Db::table('awvs_vuln')->where(['app_id' => $id])->delete();
        Db::table('host_hydra_scan_details')->where(['app_id' => $id])->delete();
        Db::table('one_for_all')->where(['app_id' => $id])->delete();
        Db::table('plugin_scan_log')->where(['app_id' => $id])->delete();
        Db::table('asm_urls')->where(['app_id' => $id])->delete();
        Db::table('urls_sqlmap')->where(['app_id' => $id])->delete();
        Db::table('xray')->where(['app_id' => $id])->delete();
        Db::table('plugin_scan_log')->where(['app_id' => $id])->delete();
        Db::table('github_keyword_monitor')->where(['app_id' => $id])->delete();
        Db::table('github_keyword_monitor_notice')->where(['app_id' => $id])->delete();

        if (Db::name('app')->where($map)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    // 批量删除
    public function batch_del(Request $request)
    {
        $ids = $request->param('ids');
        if (!$ids) {
            return $this->apiReturn(0, [], '请先选择要删除的数据');
        }
        $map[] = ['app_id', 'in', $ids];

        $data['info'] = Db::name('app')->where('id', 'in', $ids)->find();
        if (!empty($data)) {
            $urlInfo = parse_url($data['info']['url']);
            $ip = gethostbyname($urlInfo['host'] ?? '127.0.0.1');
            Db::table('app_info')->where($map)->delete();
            Db::table('asm_host')->where(['host' => $ip])->delete();
            Db::table('asm_host_port')->where(['host' => $ip])->delete();
        }
        Db::table('app_crawlergo')->where($map)->delete();
        Db::table('app_dirmap')->where($map)->delete();
        Db::table('app_nuclei')->where($map)->delete();
        Db::table('app_vulmap')->where($map)->delete();
        Db::table('app_wafw00f')->where($map)->delete();
        Db::table('app_whatweb')->where($map)->delete();
        Db::table('app_whatweb_poc')->where($map)->delete();
        Db::table('app_xray_agent_port')->where($map)->delete();
        Db::table('awvs_app')->where($map)->delete();
        Db::table('awvs_vuln')->where($map)->delete();
        Db::table('host_hydra_scan_details')->where($map)->delete();
        Db::table('one_for_all')->where($map)->delete();
        Db::table('plugin_scan_log')->where($map)->delete();
        Db::table('asm_urls')->where($map)->delete();
        Db::table('urls_sqlmap')->where($map)->delete();
        Db::table('xray')->where($map)->delete();
        Db::table('plugin_scan_log')->where($map)->delete();
        Db::table('github_keyword_monitor')->where($map)->delete();
        Db::table('github_keyword_monitor_notice')->where($map)->delete();

        if (Db::name('app')->where('id', 'in', $ids)->delete()) {
            $this->addUserLog('目标管理', "批量删除数据[{$ids}]成功");
            return $this->apiReturn(1, [], '批量删除成功');
        } else {
            $this->addUserLog('目标管理', "批量删除数据[{$ids}]失败");
            return $this->apiReturn(0, [], '批量删除失败');
        }
    }

    // 启动代理
    public function start_agent(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $where[] = ['id', '=', $id];
        $where[] = ['is_delete', '=', 0];

        $info = Db::name('app')->where($where)->field('id,user_id,xray_agent_port')->find();
        if (!$info) {
            return $this->apiReturn(0, [], '数据不存在');
        }
        $agent = "./extend/tools/xray/";
        // 判断是否已开启代理
        if ($info['xray_agent_port']) { // 关闭代理 导入结果
            $port = $info['xray_agent_port'];
            // 执行命令查看任务是否已经执行
            $cmd = "ps -ef | grep 'xray_" . $info['id'] . "_" . $port . "' | grep -v ' grep'";
            $result = [];
            execLog($cmd, $result);
            if (count($result) > 0) {  // 已存在进程
                $cmd = "kill -9 $(ps -ef |  grep 'xray_" . $info['id'] . "_" . $port . "'  | grep -v grep | awk '{print \$2}')";
                systemLog($cmd);
                addlog("已强制终止xray代理模式:app_id = {$info['id']}");
            }
            $data = [
                'xray_agent_port' => 0,
                'agent_start_up' => 0,
                'agent_time' => date('Y-m-d H:i:s', time()),
            ];
            Db::name('app')->where('id', $id)->update($data);

            // 导入数据
            $filename = $agent . $info['id'] . '.json';
            if (!file_exists($filename)) {
                return $this->apiReturn(1, [], "代理已关闭，数据导入失败");
            }
            $data = trim(file_get_contents($filename));
            $data = ($data[strlen($data) - 1] == ']') ? $data : "{$data}]";
            $data = json_decode($data, true);
            @unlink($filename);
            if (!$data) {
                return $this->apiReturn(1, [], "代理已关闭，数据导入失败");
            }
            foreach ($data as $value) {
                $newData = [
                    'create_time' => substr($value['create_time'], 0, 10),
                    'detail' => json_encode($value['detail']),
                    'plugin' => json_encode($value['plugin']),
                    'target' => json_encode($value['target']),
                    'url' => $value['detail']['addr'],
                    'app_id' => $info['id'],
                    'user_id' => $info['user_id'],
                    'poc' => $value['detail']['payload']
                ];
                XrayModel::addXray($newData);
            }
            return $this->apiReturn(1, [], "代理已关闭，数据导入成功");
        } else { // 开启代理
            //$port = rangeCrearePort();
            $port = 14256;
            if (!Db::name('app')->where('xray_agent_port', $port)->count('id')) {
                $data = [
                    'xray_agent_port' => $port,
                    'agent_time' => date('Y-m-d H:i:s', time()),
                ];
                Db::name('app')->where('id', $id)->update($data);

                return $this->apiReturn(1, [], "xray代理模式已开启，端口号：{$port}");
            } else {
                return $this->apiReturn(0, [], "xray代理模式开启失败，请重试");
            }
        }
    }

    // 黑盒项目批量导入
    public function batch_import(Request $request)
    {
        $file = $_FILES["file"]["tmp_name"];
        $result = $this->importExecl($file);
        if ($result['code'] == 0) {
            $this->error($result['msg']);
        }
        $list = $result['data'];
        unset($list[0]);
        $temp_data = [];
        foreach ($list as $k => $v) {
            $data['name'] = $v['A'];
            $data['url'] = $v['B'];
            /*$data['username'] = $v['C'];
            $data['password'] = $v['D'];
            $is_xray = intval($v['E']);
            $is_awvs = intval($v['F']);
            $is_whatweb = intval($v['G']);
            $is_one_for_all = intval($v['H']);
            $is_dirmap = intval($v['I']);*/
            $data['is_intranet'] = intval($v['C']);

            // 判断url是否已存在
            if (Db::name('app')->where('url', $data['url'])->count('id')) {
                $this->error("{$data['url']}地址已存在！");
            }

            /*$datetime = date('Y-m-d H:i:s', time() + 86400 * 365);
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
            }*/
            $data['user_id'] = $this->userId;
            $temp_data[] = $data;
        }
        if (Db::name('app')->insertAll($temp_data)) {
            $this->success('黑盒项目批量导入成功');
        } else {
            $this->error('黑盒项目批量导入失败');
        }
    }

    // 下载批量导入模版
    public function downloaAppTemplate()
    {
        $file_dir = \think\facade\App::getRootPath() . 'public/static/';
        $file_name = '黑盒项目批量导入模版.xls';
        //以只读和二进制模式打开文件
        $file = fopen($file_dir . $file_name, "rb");

        //告诉浏览器这是一个文件流格式的文件
        Header("Content-type: application/octet-stream");
        //请求范围的度量单位
        Header("Accept-Ranges: bytes");
        //Content-Length是指定包含于请求或响应中数据的字节长度
        Header("Accept-Length: " . filesize($file_dir . $file_name));
        //用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
        Header("Content-Disposition: attachment; filename=" . $file_name);
        ob_end_clean();
        //读取文件内容并直接输出到浏览器
        echo fread($file, filesize($file_dir . $file_name));
        fclose($file);
        exit ();
    }
}