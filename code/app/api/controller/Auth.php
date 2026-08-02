<?php

namespace app\api\controller;

use app\api\BaseController;
use app\model\UserModel;
use think\facade\Db;

class Auth extends BaseController
{
    /**
     * 登录（复用 UserModel::login，会写 cookie）
     * POST {username, password, remember_password}
     */
    public function login()
    {
        $username = $this->request->param('username');
        $password = $this->request->param('password');
        $remember_password = $this->request->param('remember_password', 0);

        if (empty($username)) {
            return $this->apiReturn(0, [], '请输入用户名');
        }
        if (empty($password)) {
            return $this->apiReturn(0, [], '请输入密码');
        }
        $result = UserModel::login($username, $password, $remember_password);
        if ($result['code'] === 0) {
            return $this->apiReturn(1, ['url' => $result['url'] ?: '/']);
        }
        return $this->apiReturn(0, [], $result['msg']);
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        UserModel::logout();
        return $this->apiReturn(1, []);
    }

    /**
     * 当前用户信息
     * 优先查 user 表 id=1，查不到使用与 Common.php 一致的硬编码
     */
    public function info()
    {
        $user = Db::name('user')->where('id', 1)->field('id,username,nickname')->find();
        if ($user) {
            return $this->apiReturn(1, $user);
        }
        return $this->apiReturn(1, ['id' => 1, 'username' => 'admin', 'nickname' => 'Administrator']);
    }
}
