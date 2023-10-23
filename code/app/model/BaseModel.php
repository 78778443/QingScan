<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/8/15
 * Time: 上午10:54
 */


namespace app\model;


use think\facade\Db;

class BaseModel
{
    public static function curlExec($url, $methods = 'GET', $data = null, $isHearder = null, $timeout = 10)
    {
        ToolModel::addLog("开始执行CURL访问,相关参数为" . var_export([$url, $methods, $data], true));
        //初始化curl
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //设置URL地址
        curl_setopt($ch, CURLOPT_URL, $url);

        //设置header信息
        if (!empty($isHearder)) {
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $isHearder);
        }
        //如果是post，则把data的数据传递过去
        if (($methods == 'POST') && $data) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        //如果是删除方法，则是以delete请求
        if ($methods == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        //设置超时时间，毫秒
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout * 100);
        //        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //执行CURL时间
        $result = curl_exec($ch);


        //如果有异常，记录到日志当中
        $curl_errno = curl_errno($ch);
        if ($curl_errno > 0) {
            $curlError = '执行URL出错: ' . curl_error($ch) . ';调用参数分别为:' . json_encode([$url, $methods, $data]);
            ToolModel::addLog($curlError);
        }

        //关闭URL，返回数据
        curl_close($ch);
        return $result;
    }


    public static function scanTime($table, $id, $filed)
    {
        $data = [$filed => date('Y-m-d H:i:s', time())];
        return Db::name($table)->where('id', $id)->update($data);
    }

    // 获取app待扫描列表
    public static function getAppStayScanList($filed, $num = 1)
    {
        $endTime = date('Y-m-d', time() - 86400 * 15);
//        $where[] = [$filed, '<=', $endTime];
        $where[] = ['is_delete', '=', 0];
        $where[] = ['status', '=', 1];
        return Db::name('app')->where($where)->field('id,name,url,username,password,user_id,is_intranet')->limit($num)->orderRand()->select()->toArray();
    }

    // 获取app待扫描列表
    public static function getCodeStayScanList($filed, $where = [], $num = 10)
    {
        $where[] = ['is_delete', '=', 0];
        $where[] = ['status', '=', 1];
//        $endTime = date('Y-m-d H:i:s', time() - 86400 * 7);
        return Db::name('code')->where($where)->limit($num)->orderRand()->select()->toArray();
    }

    // 检查工具权限
    public static function checkToolAuth($type, $project_id, $tools_name)
    {
        $where[] = ['type', '=', $type];
        $where[] = ['project_id', '=', $project_id];
        $where[] = ['tools_name', '=', $tools_name];
        return Db::name('project_tools')->where($where)->count('id');
    }
}