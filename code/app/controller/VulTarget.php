<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;
use think\Request;

class VulTarget extends Common
{

    public function index()
    {
        $where = [];
        $pageSize = 25;
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $where[] = ['v.user_id', '=', $this->userId];
        }
        $list = Db::table('vul_target')->alias('v')
            ->leftJoin('pocs_file p','v.id = p.vul_id')
            ->where($where)
            ->order('v.id', 'desc')
            ->field('v.*,p.name,p.content')
            ->paginate($pageSize);
        $data = [];
        $data['list'] = $list->toArray()['data'];
        $data['page'] = $list->render();

        return View::fetch('index', $data);
    }


    public function add(Request $request){
        if ($request->isPost()) {
            $data['user_id'] = $this->userId;
            $data['addr'] = $request->param('addr');
            $data['ip'] = $request->param('ip');
            $data['port'] = $request->param('port');
            $data['query'] = $request->param('query');
            $data['is_vul'] = $request->param('is_vul','intval');
            $data['vul_id'] = $request->param('vul_id','intval');
            $data['create_time'] = date('Y-m-d H:i:s',time());
            if (Db::name('vul_target')->insert($data)) {
                return $this->success('数据添加成功');
            } else {
                return $this->error('数据添加失败');
            }
        } else {
            return View::fetch('add');
        }
    }



    public function del(Request $request)
    {
        $id = $request->param('id');
        if (Db::name('vul_target')->where('id',$id)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    // 批量导入
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
            $data['vul_id'] = $v['A'];
            $data['addr'] = $v['B'];
            $data['ip'] = $v['C'];
            $data['port'] = $v['D'];
            $data['query'] = $v['E'];
            $data['is_vul'] = $v['F'];
            $data['user_id'] = $this->userId;
            $data['create_time'] = date('Y-m-d H:i:s',time());

            $temp_data[] = $data;
        }
        if (Db::name('vul_target')->insertAll($temp_data)) {
            $this->success('收集目标批量导入成功');
        } else {
            $this->error('收集目标批量导入失败');
        }
    }

    public function downloaAppTemplate()
    {
        $file_dir = \think\facade\App::getRootPath() . 'public/static/';
        $file_name = '收集目标批量导入模版.xls';
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