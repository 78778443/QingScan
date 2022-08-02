<?php

namespace app\controller;

use app\BaseController;
use app\model\UserLogModel;
use app\model\UserModel;
use think\facade\Cookie;
use think\facade\Db;
use think\facade\View;
use think\Request;

class Login extends BaseController
{
    public function index()
    {
        parse_str(think_decrypt(Cookie::get('scan_user')), $arr);
        if ($arr) {
            return redirect(url('index/index'));
        }
        //echo ucenter_md5('123456' . 'test_scan', config('app.UC_AUTH_KEY'));
        return View::fetch('user/login');
    }

    public function doLogin(Request $request)
    {
        $username = $request->param('username'); // 账号
        $password =  $request->param('password'); // 密码
        $remember_password =  $request->param('remember_password',0); // 记住密码

        if (empty($username)){
            $this->error( '请输入用户名');
        }
        if (empty($password)){
            $this->error('请输入密码');
        }
        $result = UserModel::login($username, $password,$remember_password);
        if ($result['code'] === 0) {
            UserLogModel::addLog($username,'用户登录',"登录成功，账号：{$username}");

            if ($result['url']) {
                return redirect($result['url']);
            } else {
                return redirect(url('index/index'));
            }
        } else {
            UserLogModel::addLog($username,'用户登录',"登录失败，账号：{$username}");

            $this->error( $result['msg']);
        }

    }

    public function logout()
    {
        UserModel::logout();
        return redirect(url('login/index'));
    }

    public function register(){
        exit();
        if ($this->request->isPost()) {
            $this->error('注册功能待完善,暂时关闭，如需添加用户请在管理后台添加~');
            $username = input('username'); // 账号
            $password =  input('password'); // 密码
            $nickname =  input('nickname'); // 昵称
            if (Db::name('user')->where('username',$username)->count('id')) {
                $this->error('用户名已存在');
            }
            if (Db::name('user')->where('nickname',$nickname)->count('id')) {
                $this->error('昵称已存在');
            }
            $data = [
                'username'=>$username,
                'password'=>ucenter_md5($password . $username, config('app.UC_AUTH_KEY')),
                'nickname'=>$nickname,
                'auth_group_id'=>7,
                'created_at'=>time(),
                'status'=>1
            ];
//            if (Db::name('user')->insert($data)) {
//                $this->success('注册成功，请登录',url('login/index'));
//            } else {
//                $this->error('注册失败');
//            }
        } else {
            return View::fetch('user/register');
        }
    }

    public function clear_cache(){
        $cmd = "cd /root/qingscan/code/runtime/ && rm -rf ./*";
        systemLog($cmd,false);
        $this->success('系统缓存,清除成功','index');
    }
}