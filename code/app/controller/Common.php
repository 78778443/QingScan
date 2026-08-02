<?php


namespace app\controller;


use app\BaseController;
use app\model\UserLogModel;
use think\exception\HttpResponseException;
use think\facade\Cookie;
use think\facade\Db;
use think\facade\Request;
use think\facade\Response;
use think\facade\View;

class Common extends BaseController
{
    protected $userId, $auth_group_id, $username, $menu, $userInfo;


    public function initialize()
    {
        parent::initialize();
        // 临时关闭权限验证 - 使用admin身份
        $this->userInfo = [
            'id' => 1,
            'username' => 'admin',
            'auth_group_id' => 1,
            'nickname' => 'Administrator'
        ];
        // 原权限验证代码已注释
        // $this->userInfo = $this->isLogin('scan_user');
        // if (!$this->userInfo) {
        //     header("Location: " . url('/login/index'));
        //     exit();
        // }
        View::assign('userInfo', $this->userInfo);
        $this->userId = $this->userInfo ? $this->userInfo['id'] : 0;
        $this->username = $this->userInfo ? $this->userInfo['username'] : '';
        $this->auth_group_id = $this->userInfo['auth_group_id'];
        View::assign('title', env('website'));
    }


    /**
     * 判断用户是否登录
     * @param name
     * @return int|array
     */
    public function is_login($name)
    {
        return session($name);
    }

    /**
     * 判断用户是否登录
     * @param $cookie_name
     * @return int|array
     */
    public function isLogin($cookie_name)
    {
        if (!$cookie_name) {
            return 0;
        }
        parse_str(think_decrypt(Cookie::get($cookie_name)), $arr);
        if (!$arr)
            return 0;
        return $arr;
    }

    public function getMyAppList()
    {
        $where[] = ['is_delete', '=', 0];

        //查询项目数据
        $projectArr = Db::table('app')->where($where)->field('id,name')->select()->toArray();
        $projectList = array_column($projectArr, 'name', 'id');
        return $projectList;
    }

    public function getMyCodeList()
    {
        $where[] = ['is_delete', '=', 0];

        //查询项目数据
        $projectArr = Db::table('code')->where($where)->field('id,name')->select()->toArray();
        return array_column($projectArr, 'name', 'id');
    }


    // 批量审核
    public function batch_audit_that($request, $table)
    {
        $ids = $request->param('ids');
        $check_status = $request->param('check_status');
        $this->addUserLog('audit', "批量审核数据[$ids]");
        if (!$ids) {
            return $this->apiReturn(0, [], '请先选择要审核的数据');
        }
        $map[] = ['id', 'in', $ids];

        if (Db::name($table)->where($map)->update(['check_status' => $check_status, 'update_time' => date('Y-m-d H:i:s', time())])) {
            return $this->apiReturn(1, [], '审核成功');
        } else {
            return $this->apiReturn(0, [], '审核失败');
        }
    }

    // 批量删除
    public function batch_del_that($request, $table)
    {
        $ids = $request->param('ids');
        $this->addUserLog($table, "批量删除数据[$ids]");
        if (!$ids) {
            return $this->apiReturn(0, [], '请先选择要删除的数据');
        }
        $map[] = ['id', 'in', $ids];

        if (Db::name($table)->where($map)->delete()) {
            return $this->apiReturn(1, [], '删除成功');
        } else {
            return $this->apiReturn(0, [], '删除失败');
        }
    }

    // 添加用户操作日志
    public function addUserLog($type, $content)
    {
        UserLogModel::addLog($this->userInfo['username'], $type, $content);
    }
}