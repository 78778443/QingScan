<?php

namespace app\controller;

use app\model\UserModel;
use think\App;
use think\facade\Db;
use think\facade\Config;
use think\facade\View;

class Auth extends Common
{
    /**
     * 管理员列表
     *
     * @return mixed
     */
    public function user_list()
    {
        $list = Db::name('auth_rule')->field('auth_rule_id,href')->select();
        foreach ($list as $k => $v) {

        }
        $ids = implode(',', config('app.ADMINISTRATOR'));
        $map = "a.id not in($ids) and is_delete = 0";
        $list = UserModel::getListPage($map);
        $data['list'] = $list['data'];
        $data['page'] = $list['current_page'];
        return View::fetch('auth/user_list',$data);
    }

    // 添加管理员
    public function userAdd()
    {
        if (request()->isPost()) {
            $data['username'] = getParam('username');
            $data['nickname'] = getParam('nickname');
            $data['auth_group_id'] = getParam('auth_group_id');
            $data['status'] = getParam('status');
            $data['sex'] = getParam('sex');
            $data['phone'] = getParam('phone');
            $data['dd_token'] = getParam('dd_token');
            $data['email'] = getParam('email');
            $password = getParam('password');
            $check_user = Db::name('user')->where('username',$data['username'])->find();
            if (!$password || !$data['username'] || !$data['nickname'] || !$data['auth_group_id']) {
                $this->error('参数不能为空');
            }
            if ($check_user) {
                $this->error('用户已存在，请重新输入用户名');
            }
            $data['created_at'] = time();
            $data['update_time'] = time();
            $data['status'] = 1;

            $data['password'] = ucenter_md5($password . $data['username'], config('app.UC_AUTH_KEY'));
            //添加
            if (Db::name('user')->insert($data)) {
                $this->success('用户添加成功','auth/user_list');
            } else {
                $this->error('用户添加失败');
            }
        } else {
            $map = [
                ['is_delete', '=', 0],
                ['status', '=', 1],
            ];
            $auth_group = Db::name('auth_group')->where($map)->select();
            $data['authGroup'] = $auth_group;
            return View::fetch('auth/user_add',$data);
        }
    }

    public function userEdit()
    {
        $id = getParam('id');
        if (request()->isPost()) {
            $data['username'] = getParam('username');
            $data['nickname'] = getParam('nickname');
            $data['auth_group_id'] = getParam('auth_group_id');
            $data['status'] = getParam('status');
            $data['sex'] = getParam('sex');
            $data['phone'] = getParam('phone');
            $data['dd_token'] = getParam('dd_token');
            $data['email'] = getParam('email');
            $password = getParam('password');
            if (!$id || !$data['username'] || !$data['nickname'] || !$data['auth_group_id']) {
                $this->error('参数不能为空');
            }
            if (in_array($id, config('ADMINISTRATOR')))
                $this->error('该用户不允许操作');
            $map[] = ['id', '<>', $id];
            $map[] = ['username', '=', $data['username']];
            $check_user = Db::name('user')->where($map)->find();
            if ($check_user) {
                $this->error('用户已存在，请重新输入用户名!');
            }

            if ($password) {
                $data['password'] = ucenter_md5($password . $data['username'], config('app.UC_AUTH_KEY'));
            } else {
                unset($data['password']);
            }
            $data['update_time'] = time();
            if (Db::name('user')->where('id',$id)->update($data)) {
                $this->success('用户信息修改成功','auth/user_list');
            } else {
                $this->error('用户信息修改失败');
            }
        } else {
            $map = [
                ['is_delete', '=', 0],
                //['status', '=', 1,],
            ];
            $auth_group = Db::name('auth_group')->where('status',1)->where($map)->select()->toArray();
            $map[] = ['id','=',$id];
            $data['info'] = Db::name('user')->where($map)->find();
            $data['authGroup'] = $auth_group;
            return View::fetch('auth/user_edit',$data);
        }
    }

    public function user_info()
    {
        $id = $this->userId;
        if (request()->isPost()) {
            $data['nickname'] = getParam('nickname');
            $data['sex'] = getParam('sex');
            $data['phone'] = getParam('phone');
            $data['dd_token'] = getParam('dd_token');
            $data['email'] = getParam('email');
            $data['token'] = getParam('token','');
            $data['url'] = getParam('url','');
            $password = getParam('password');

            /*if ($password) {
                $data['password'] = ucenter_md5($password . 'test1', config('app.UC_AUTH_KEY'));
            } else {
                unset($data['password']);
            }*/
            $data['update_time'] = time();
            if (Db::name('user')->where('id',$id)->update($data)) {
                $this->success('个人资料修改成功');
            } else {
                $this->error('个人资料修改失败');
            }
        } else {
            $map[] = ['id','=',$id];
            $data['info'] = Db::name('user')->where($map)->find();
            return View::fetch('auth/user_info',$data);
        }
    }

    public function user_password()
    {
        $id = $this->userId;
        if (request()->isPost()) {
            $password = getParam('password');
            $news_password = getParam('news_password');
            $news_password1 = getParam('news_password1');
            if ($news_password != $news_password1) {
                $this->error('新密码和确认密码不一致');
            }
            $user = Db::name('user')->where('id',$id)->find();
            $password = ucenter_md5($password . $user['username'], config('app.UC_AUTH_KEY'));
            if ($password != $user['password']) {
                $this->error('原密码错误');
            }
            $data['password'] = ucenter_md5($news_password . $user['username'], config('app.UC_AUTH_KEY'));
            $data['update_time'] = time();
            if (Db::name('user')->where('id',$id)->update($data)) {
                UserModel::logout();
                $this->success('密码修改成功,请重新登录',url('login/index'));
            } else {
                $this->error('密码修改失败');
            }
        } else {
            return View::fetch('auth/user_password');
        }
    }

    //删除管理员
    public function userDel()
    {
        $id = getParam('id');
        if (in_array($id, config('ADMINISTRATOR')))
            $this->error('该用户不允许删除');
        if (Db::name('user')->where('id',$id)->update(['is_delete'=>-1,'update_time'=>time()])) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    public function getToken(){
        return $this->apiReturn(1, ['token'=>getToken($this->userId)], 'ok');
    }

    /********************************用户组*******************************/
    /**
     * 用户组列表
     * @return mixed
     */
    public function auth_group_list()
    {
        $list = Db::name('auth_group')->where('is_delete', '=', 0)->order('auth_group_id','desc')->paginate(25)->toArray();
        $data['list'] = $list['data'];
        foreach ($data['list'] as &$val) {
            if ($val['status'] == 1) {
                $val['show_status'] = '正常';
            } else {
                $val['show_status'] = '禁用';
            }
        }
        $data['page'] = $list['current_page'];
        return View::fetch('auth/auth_group_list',$data);
    }

    //添加用户分组
    public function authGroupAdd()
    {
        if (request()->isPost()) {
            $data['title'] = getParam('title');
            $data['status'] = getParam('status');
            $data['created_at'] = time();
            $data['update_time'] = time();
            if (Db::name('auth_group')->insert($data)) {
                $this->success('用户组添加成功','auth/auth_group_list');
            } else {
                $this->error('用户组添加失败');
            }
        } else {
            return View::fetch('auth_group_add');
        }
    }

    //修改用户分组
    public function authGroupEdit()
    {
        if (request()->isPost()) {
            $auth_group_id = getParam('auth_group_id');
            $data['title'] = getParam('title');
            $data['status'] = getParam('status');
            $data['update_time'] = time();
            if (Db::name('auth_group')->where('auth_group_id', $auth_group_id)->update($data)) {
                $this->success('用户组修改成功','auth/auth_group_list');
            } else {
                $this->error('用户组修改失败',url('auth/authGroupEdit',['auth_group_id'=>$auth_group_id]));
            }
        } else {
            $auth_group_id = getParam('auth_group_id');
            $data['info'] = Db::name('auth_group')->where('auth_group_id',$auth_group_id)->find();
            return View::fetch('auth_group_edit',$data);
        }
    }

    //删除用户分组
    public function authGroupDel()
    {
        $auth_group_id = getParam('auth_group_id');
        if ($auth_group_id == 1) {
            $this->error('该分组不能删除','auth/auth_group_list');
        }
        if (Db::name('auth_group')->where('auth_group_id',$auth_group_id)->update(['is_delete' => 1, 'update_time' => time()])) {
            $this->success('删除成功','auth/auth_group_list');
        } else {
            $this->error('删除失败','auth/auth_group_list');
        }
    }

    //分组配置规则
    public function authGroupAccess()
    {
        $nav = new \Leftnav();
        $map['is_delete'] = 0;
        //$map['menu_status'] = 1;
        $auth_rule = Db::name('auth_rule')->where($map)->field('auth_rule_id,pid,title,href')->order('sort asc')->select()->toArray();
        $auth_group_id = getParam('auth_group_id');
        $rules = Db::name('auth_group')->where('auth_group_id', $auth_group_id)->value('rules');
        foreach ($auth_rule as &$v) {
            if (!empty($v['href'])) {
                $v['title'] = "{$v['title']}({$v['href']})";
            }
        }
        $arr = $nav->auth($auth_rule, $pid = 0, $rules);
        $arr[] = array(
            "auth_rule_id" => 0,
            "pid" => 0,
            "title" => "全部",
            "open" => true,
        );
        $data['auth_group_id'] = $auth_group_id;
        $data['data'] = $arr;
        return View::fetch('auth_group_access',$data);
    }

    // 用户组状态操作
    public function authGroupSetaccess()
    {
        $data['rules'] = getParam('rules');
        if (empty($data['rules'])) {
            return $this->apiReturn(0,[],'请选择权限');
        }
        $auth_group_id = getParam('auth_group_id');
        if (Db::name('auth_group')->where('auth_group_id', $auth_group_id)->update($data)) {
            return $this->apiReturn(1,[],'保存成功');
        } else {
            return $this->apiReturn(0,[],'保存错误');
        }
    }


    /********************************菜单权限*******************************/
    // 菜单管理
    public function auth_rule()
    {
        $nav = new \Leftnav();
        $map['is_delete'] = 0;
        $authRule = Db::name('auth_rule')->where($map)->field('auth_rule_id,href,title,is_open_auth,pid,sort,menu_status,level,icon_url')->order
        ('sort','asc')->select()->toArray();
        $data['list'] = $nav->menu($authRule);
        return View::fetch('auth/auth_rule_list',$data);
    }

    public function ruleAdd()
    {
        if (request()->isPost()) {
            $data['href'] = getParam('href');
            $data['title'] = getParam('title');
            $data['is_open_auth'] = getParam('is_open_auth');
            $data['menu_status'] = getParam('menu_status');
            $data['sort'] = getParam('sort');
            $data['pid'] = getParam('pid');
            $data['created_at'] = time();
            $map['href'] = $data['href'];
            $map['is_delete'] = 0;
            if ($data['pid']) {
                $map['level'] = ['>', 1];
            } else {
                $map['level'] = 1;
            }
            if ($map['href']) {
                if (Db::name('auth_rule')->where($map)->lock(true)->find())
                    $this->error('控制器/方法已存在');
            }
            if ($data['pid']) {
                $parent = Db::name('auth_rule')->where('auth_rule_id',$data['pid'])->find();
                $data['level'] = $parent['level'] + 1;
            }
            if (Db::name('auth_rule')->insert($data)) {
                $this->success('菜单添加成功','auth/auth_rule');
            }   else{
                $this->error('菜单添加失败');
            }
        } else {
            $nav = new \Leftnav();
            $map['is_delete'] = 0;
            $authRule = Db::name('authRule')->where($map)->field('auth_rule_id,title,pid,href')->order('sort asc')->select();
            $arr = $nav->menu($authRule);
            $auth_rule_id = getParam('auth_rule_id');
            if ($auth_rule_id) {
                $where['auth_rule_id'] = $auth_rule_id;
                $where['is_delete'] = 0;
                $res = Db::name('auth_rule')->where('auth_rule_id',$auth_rule_id)->find();
                if (!$res)
                    $this->error('数据不存在');
                $data['strAuthRule'] = $res;
            }
            foreach ($arr as &$v) {
                if ($v['href']) {
                    $v['ltitle'] = "{$v['ltitle']}（{$v['href']}）";
                }
            }
            $data['auth_rule'] = $arr;
            return View::fetch('auth/auth_rule_add',$data);
        }
    }


    public function ruleEdit()
    {
        if (request()->isPost()) {
            $auth_rule_id = getParam('auth_rule_id');
            $data['href'] = getParam('href');
            $data['title'] = getParam('title');
            $data['is_open_auth'] = getParam('is_open_auth');
            $data['menu_status'] = getParam('menu_status');
            $data['sort'] = getParam('sort');
            $data['pid'] = getParam('pid');
            $data['update_time'] = time();
            $map = [
                ['href', '=', $data['href']],
                ['is_delete', '=', 0],
                ['auth_rule_id', '<>', $auth_rule_id],
            ];
            if ($data['pid']) {
                $map[] = ['level', '>', 1];
            } else {
                $map[] = ['level', '=', 1];
            }
            if ($data['href']) {
                if (Db::name('auth_rule')->where($map)->lock(true)->find())
                    $this->error('控制器/方法已存在');
            }
            if ($data['pid']) {
                $parent = Db::name('auth_rule')->where('auth_rule_id', $data['pid'])->find();
                $data['level'] = $parent['level'] + 1;
            }
            if (Db::name('auth_rule')->where('auth_rule_id',$auth_rule_id)->update($data)) {
                $this->success('修改成功','auth/auth_rule');
            } else {
                $this->error('修改失败');
            }
        } else {
            $nav = new \Leftnav();
            $map['is_delete'] = 0;
            $authRule = Db::name('authRule')->where($map)->field('auth_rule_id,title,pid,href')->order('sort','asc')->select();
            $where['auth_rule_id'] = input('auth_rule_id');
            $where['is_delete'] = 0;
            $info = Db::name('auth_rule')->where($where)->find();
            if (!$info)
                $this->error('数据不存在');
            $list = $nav->menu($authRule);
            foreach ($list as &$v) {
                if ($v['href']) {
                    $v['ltitle'] = "{$v['ltitle']}（{$v['href']}）";
                }
            }
            $data['list'] = $list;
            $data['info'] = $info;
            return View::fetch('auth_rule_edit',$data);
        }
    }

    // 删除菜单
    public function ruleDel()
    {
        if (in_array(getParam('auth_rule_id'), config('app.not_del'))) {
            $this->error('该菜单权限不能删除');
        }
        if (Db::name('auth_rule')->where('auth_rule_id',getParam('auth_rule_id'))->update(['is_delete' => 1, 'delete_time' => time()])) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }
}