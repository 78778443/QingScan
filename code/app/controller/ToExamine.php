<?php


namespace app\controller;


use think\facade\Db;
use think\Request;

class ToExamine extends Common
{
    public function fortify(Request $request)
    {
        $id = $request->param('id');
        $check_status = intval($request->param('check_status'));
        if (!$id) {
            return $this->apiReturn(0, [], '缺少参数');
        }
        if (!in_array($check_status, [0, 1, 2])) {
            return $this->apiReturn(0, [], '请先选择审核状态');
        }
        $map = [];
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $map[] = ['user_id','=',$this->userId];
        }
        $info = Db::name('fortify')->where('id', $id)->where($map)->find();
        if (!$info) {
            return $this->apiReturn(0, [], '数据不存在');
        }

        if (Db::name('fortify')->where('id', $id)->update(['check_status' => $check_status])) {
            return $this->apiReturn(1, [], '操作成功');
        } else {
            return $this->apiReturn(0, [], '操作失败');
        }
    }

    public function process_safe(Request $request)
    {
        $id = $request->param('id');
        $check_status = intval($request->param('check_status'));
        if (!$id) {
            return $this->apiReturn(0, [], '缺少参数');
        }
        if (!in_array($check_status, [0, 1, 2])) {
            return $this->apiReturn(0, [], '请先选择审核状态');
        }
        $info = Db::name('process_safe')->where('id', $id)->find();
        if (!$info) {
            return $this->apiReturn(0, [], '数据不存在');
        }

        if (Db::name('process_safe')->where('id', $id)->update(['status' => $check_status])) {
            return $this->apiReturn(1, [], '操作成功');
        } else {
            return $this->apiReturn(0, [], '操作失败');
        }
    }

    public function kunlun(Request $request)
    {
        $id = $request->param('id');
        $check_status = intval($request->param('check_status'));
        if (!$id) {
            return $this->apiReturn(0, [], '缺少参数');
        }
        if (!in_array($check_status, [0, 1, 2])) {
            return $this->apiReturn(0, [], '请先选择审核状态');
        }
        $map = [];
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $map[] = ['user_id','=',$this->userId];
        }
        $info = Db::connect('kunlun')->table("index_scanresulttask")->where('id', $id)->where($map)->order('id', 'desc')->find();
        if (!$info) {
            return $this->apiReturn(0, [], '数据不存在');
        }
        if (Db::connect('kunlun')->table("index_scanresulttask")->where('id', $id)->update(['check_status'=>$check_status,'update_time'=>date('Y-m-d H:i:s',time())])) {
            return $this->apiReturn(1, [], '操作成功');
        } else {
            return $this->apiReturn(0, [], '操作失败');
        }
    }

    public function semgrep(Request $request)
    {
        $id = $request->param('id');
        $check_status = intval($request->param('check_status'));
        if (!$id) {
            return $this->apiReturn(0, [], '缺少参数');
        }
        if (!in_array($check_status, [0, 1, 2])) {
            return $this->apiReturn(0, [], '请先选择审核状态');
        }
        $map = [];
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $map[] = ['user_id','=',$this->userId];
        }
        $info = Db::name('semgrep')->where('id', $id)->where($map)->find();
        if (!$info) {
            return $this->apiReturn(0, [], '数据不存在');
        }

        if (Db::name('semgrep')->where('id', $id)->update(['check_status' => $check_status])) {
            return $this->apiReturn(1, [], '操作成功');
        } else {
            return $this->apiReturn(0, [], '操作失败');
        }
    }

    public function xray(Request $request)
    {
        $id = $request->param('id');
        $check_status = intval($request->param('check_status'));
        if (!$id) {
            return $this->apiReturn(0, [], '缺少参数');
        }
        if (!in_array($check_status, [0, 1, 2])) {
            return $this->apiReturn(0, [], '请先选择审核状态');
        }
        $map = [];
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $map[] = ['user_id','=',$this->userId];
        }
        $info = Db::name('xray')->where('id', $id)->where($map)->find();
        if (!$info) {
            return $this->apiReturn(0, [], '数据不存在');
        }

        if (Db::name('xray')->where('id', $id)->update(['check_status' => $check_status])) {
            return $this->apiReturn(1, [], '操作成功');
        } else {
            return $this->apiReturn(0, [], '操作失败');
        }
    }

    public function awvs(Request $request)
    {
        $id = $request->param('id');
        $check_status = intval($request->param('check_status'));
        if (!$id) {
            return $this->apiReturn(0, [], '缺少参数');
        }
        if (!in_array($check_status, [0, 1, 2])) {
            return $this->apiReturn(0, [], '请先选择审核状态');
        }
        $info = Db::name('awvs_vuln')->where('id', $id)->find();
        if (!$info) {
            return $this->apiReturn(0, [], '数据不存在');
        }

        if (Db::name('awvs_vuln')->where('id', $id)->update(['check_status' => $check_status])) {
            return $this->apiReturn(1, [], '操作成功');
        } else {
            return $this->apiReturn(0, [], '操作失败');
        }
    }

    public function node(Request $request)
    {
        $id = $request->param('id');
        $check_status = intval($request->param('check_status'));
        if (!$id) {
            return $this->apiReturn(0, [], '缺少参数');
        }
        if (!in_array($check_status, [0, 1])) {
            return $this->apiReturn(0, [], '请先选择审核状态');
        }
        $info = Db::name('node')->where('id', $id)->find();
        if (!$info) {
            return $this->apiReturn(0, [], '数据不存在');
        }

        if (Db::name('node')->where('id', $id)->update(['status' => $check_status])) {
            return $this->apiReturn(1, [], '操作成功');
        } else {
            return $this->apiReturn(0, [], '操作失败');
        }
    }

    public function auth_rule_auth(Request $request)
    {
        $auth_rule_id = $request->param('id');
        $check_status = intval($request->param('check_status'));
        if (!$auth_rule_id) {
            return $this->apiReturn(0, [], '缺少参数');
        }
        if (!in_array($check_status, [0, 1])) {
            return $this->apiReturn(0, [], '请先选择审核状态');
        }
        $info = Db::name('auth_rule')->where('auth_rule_id', $auth_rule_id)->find();
        if (!$info) {
            return $this->apiReturn(0, [], '数据不存在');
        }

        if (Db::name('auth_rule')->where('auth_rule_id', $auth_rule_id)->update(['is_open_auth' => $check_status])) {
            return $this->apiReturn(1, [], '操作成功');
        } else {
            return $this->apiReturn(0, [], '操作失败');
        }
    }

    public function auth_rule_status(Request $request)
    {
        $auth_rule_id = $request->param('id');
        $check_status = intval($request->param('check_status'));
        if (!$auth_rule_id) {
            return $this->apiReturn(0, [], '缺少参数');
        }
        if (!in_array($check_status, [0, 1])) {
            return $this->apiReturn(0, [], '请先选择审核状态');
        }
        $info = Db::name('auth_rule')->where('auth_rule_id', $auth_rule_id)->find();
        if (!$info) {
            return $this->apiReturn(0, [], '数据不存在');
        }

        if (Db::name('auth_rule')->where('auth_rule_id', $auth_rule_id)->update(['menu_status' => $check_status])) {
            return $this->apiReturn(1, [], '操作成功');
        } else {
            return $this->apiReturn(0, [], '操作失败');
        }
    }

    public function code_webshell(Request $request)
    {
        $id = $request->param('id');
        $check_status = intval($request->param('check_status'));
        if (!$id) {
            return $this->apiReturn(0, [], '缺少参数');
        }
        if (!in_array($check_status, [0, 1,2])) {
            return $this->apiReturn(0, [], '请先选择审核状态');
        }
        $info = Db::name('code_webshell')->where('auth_rule_id', $id)->find();
        if (!$info) {
            return $this->apiReturn(0, [], '数据不存在');
        }

        if (Db::name('code_webshell')->where('id', $id)->update(['menu_status' => $check_status])) {
            return $this->apiReturn(1, [], '操作成功');
        } else {
            return $this->apiReturn(0, [], '操作失败');
        }
    }

    public function plugin_result(Request $request)
    {
        $id = $request->param('id');
        $check_status = intval($request->param('check_status'));
        if (!$id) {
            return $this->apiReturn(0, [], '缺少参数');
        }
        if (!in_array($check_status, [0, 1,2])) {
            return $this->apiReturn(0, [], '请先选择审核状态');
        }
        $info = Db::name('plugin_scan_log')->where('id', $id)->find();
        if (!$info) {
            return $this->apiReturn(0, [], '数据不存在');
        }

        if (Db::name('plugin_scan_log')->where('id', $id)->update(['check_status' => $check_status])) {
            return $this->apiReturn(1, [], '操作成功');
        } else {
            return $this->apiReturn(0, [], '操作失败');
        }
    }

    public function mobsfscan(Request $request)
    {
        $id = $request->param('id');
        $check_status = intval($request->param('check_status'));
        if (!$id) {
            return $this->apiReturn(0, [], '缺少参数');
        }
        if (!in_array($check_status, [0, 1,2])) {
            return $this->apiReturn(0, [], '请先选择审核状态');
        }
        $info = Db::name('mobsfscan')->where('id', $id)->find();
        if (!$info) {
            return $this->apiReturn(0, [], '数据不存在');
        }

        if (Db::name('mobsfscan')->where('id', $id)->update(['check_status' => $check_status])) {
            return $this->apiReturn(1, [], '操作成功');
        } else {
            return $this->apiReturn(0, [], '操作失败');
        }
    }
}