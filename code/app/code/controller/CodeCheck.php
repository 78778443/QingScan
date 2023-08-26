<?php


namespace app\code\controller;


use app\controller\Common;
use think\facade\Db;
use think\facade\View;

class CodeCheck extends Common
{
    public function bug_detail()
    {
        $id = getParam('id');
        if (!$id) {
            return redirect('index','参数错误');
        }
        $where[] = ['id','=',$id];

        $info = Db::table('awvs_vuln')->where($where)->find();
        if (!$info) {
            return redirect('index','0');
        }
        $upper_id = Db::name('awvs_vuln')->where('id','<',$id)->order('id','desc')->value('id');
        $info['upper_id'] = $upper_id?:$id;
        $lower_id = Db::name('awvs_vuln')->where('id','>',$id)->order('id','asc')->value('id');
        $info['lower_id'] = $lower_id?:$id;

        $data['info'] = $info;
        //var_dump($info);exit;
        return View::fetch('details', $data);
    }
}