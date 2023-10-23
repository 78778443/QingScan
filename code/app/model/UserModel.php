<?php
namespace app\model;

use think\facade\Db;

class UserModel extends BaseModel
{
    protected $pk = 'id';

    public static function Login($username, $password,$remember_password)
    {
        $where['username'] = $username;
//        $where['password'] = ucenter_md5($password . $username, config('app.UC_AUTH_KEY'));
        $strUser = Db::name('user')
            ->where($where)
            ->field('id,username,nickname,status,created_at,auth_group_id,last_login_ip,last_login_time,url')
            ->find();
        if (!$strUser)
            return ['code' => 1, 'msg' => '账号或密码错误'];
        if ($strUser['status'] != 1) {
            return ['code' => 1, 'msg' => '该用户已被禁用'];
        }
        if ($remember_password) {
            $expire = 60 * 60 * 24 * 7;
        } else {
            $expire = 60 * 60 * 2;
        }
        cookie('scan_user', think_encrypt(http_build_query(array(
            'id' => $strUser['id'],
            'auth_group_id' => $strUser['auth_group_id'],
            'username' => $strUser['username'],
            'nickname' => $strUser['nickname'],
        ))), [
            'expire' => $expire,
            'path' => '/'
        ]);
        return ['code' => 0, 'id' => $strUser['id'],'url'=>$strUser['url']];
    }

    /**
     * 退出登录
     * @return array
     */
    public static function logout()
    {
        cookie('scan_user', null);
        return ['code' => 1, 'msg' => '退出登录成功'];
    }

    public static function new_password($admin_id, $password)
    {
        $username = Db::name('user')->where('id', $admin_id)->value('username');
        if (!$username) {
            return false;
        }
        return Db::name('user')->where('id', $admin_id)->update(['password'=>ucenter_md5($password .
            $username, config('UC_AUTH_KEY')),'update_time'=>time()]);
    }

    public static function getListPage($map = '', $pagesize = 20)
    {
        return Db::name('user')->alias('a')
            ->where($map)
            ->order('id','desc')
            ->paginate($pagesize)
            ->each(function ($item, $key) {
                if ($item['status'] == 1) {
                    $item['show_status'] = '正常';
                } else {
                    $item['show_status'] = '禁用';
                }
                $item['created_at'] = date("Y-m-d H:i:s", $item['created_at']);
                $item['auth_group_name'] = Db::name('auth_group')->where('auth_group_id',$item['auth_group_id'])->value('title');
                return $item;
            });
    }

    public static function getUserInfo($map)
    {
        return self::where($map)->field('id,username,status,nickname,auth_group_id,created_at,last_login_ip,last_login_time')->find();
    }
}