<?php


namespace app\controller;


use think\facade\Db;

class ToExamine extends Common
{
    public function fortify()
    {
        $id = getParam('id');
        $check_status = intval(getParam('check_status'));
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

    public function process_safe()
    {
        $id = getParam('id');
        $check_status = intval(getParam('check_status'));
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

    public function kunlun()
    {
        $id = getParam('id');
        $check_status = intval(getParam('check_status'));
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

        if (Db::connect('kunlun')->table("index_scanresulttask")->where('id', $id)->update(['check_status' => $check_status])) {
            return $this->apiReturn(1, [], '操作成功');
        } else {
            return $this->apiReturn(0, [], '操作失败');
        }
    }

    public function semgrep()
    {
        $id = getParam('id');
        $check_status = intval(getParam('check_status'));
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

    public function xray()
    {
        $id = getParam('id');
        $check_status = intval(getParam('check_status'));
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

    public function awvs()
    {
        $id = getParam('id');
        $check_status = intval(getParam('check_status'));
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

    public function node()
    {
        $id = getParam('id');
        $check_status = intval(getParam('check_status'));
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

    public function auth_rule_auth()
    {
        $auth_rule_id = getParam('id');
        $check_status = intval(getParam('check_status'));
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

    public function auth_rule_status()
    {
        $auth_rule_id = getParam('id');
        $check_status = intval(getParam('check_status'));
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
}