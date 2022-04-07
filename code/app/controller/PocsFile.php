<?php


namespace app\controller;


use think\facade\Db;
use think\facade\View;
use think\Request;

class PocsFile extends Common
{
    public function index(Request $request){
        $pageSize = 20;
        $where = [];
        $search = $request->param('search','');
        if (!empty($search)) {
            $where[] = ['name|cve_num','like',"%{$search}%"];
        }
        $list = Db::table('pocs_file')->where($where)->order("id", 'desc')->paginate([
            'list_rows'=> $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return View::fetch('index', $data);
    }


    public function add(Request $request)
    {
        if (request()->isPost()) {
            $data['cve_num'] = $request->param('cve_num');
            $data['status'] = $request->param('status');
            $data['tools'] = $request->param('tools');
            $data['name'] = $request->param('name');
            $data['content'] = $request->param('content');
            if (empty($data['content'])   || !$data['name']) {
                $this->error('数据不能为空');
            }
            if (Db::name('pocs_file')->where('name',$data['name'])->count('id')) {
                $this->error('POC名字已存在');
            }

            //添加
            if (Db::name('pocs_file')->insert($data)) {
                $this->success('添加成功','index');
            } else {
                $this->error('添加失败');
            }
        } else {;
            return View::fetch('add');
        }
    }

    public function edit(Request $request)
    {
        $id = $request->param('id');
        $map[] = ['id','=',$id];
        $info = Db::name('pocs_file')->where($map)->find();
        if (!$info) {
            $this->error('数据不存在');
        }
        if (request()->isPost()) {
            $data['status'] = $request->param('status');
            $data['content'] = $request->param('content');
            if (Db::name('pocs_file')->where($map)->update($data)) {
                $this->success('编辑成功','index');
            } else {
                $this->error('编辑失败');
            }
        } else {
            $data['info'] = $info;
            return View::fetch('edit',$data);
        }
    }

    public function details(Request $request){
        $id = $request->param('id');
        $info = Db::name('pocs_file')->where('id',$id)->find();
        if (!$info) {
            $this->error('数据不存在');
        }

        $upper_id = Db::name('pocs_file')->where('id', '<', $id)->order('id', 'desc')->value('id');
        $info['upper_id'] = $upper_id ?: $id;
        $lower_id = Db::name('pocs_file')->where('id', '>', $id)->order('id', 'asc')->value('id');
        $info['lower_id'] = $lower_id ?: $id;
        $data['info'] = $info;
        return View::fetch('details',$data);
    }

    public function del(Request $request)
    {
        $id = $request->param('id');
        if (!$id) {
            $this->error('参数不能为空');
        }
        $this->addUserLog('POC脚本',"删除POC脚本数据[{$id}]");
        if (Db::name('pocs_file')->where('id',$id)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    // 批量删除
    public function batch_del(Request $request){
        $ids = $request->param('ids');
        if (!$ids) {
            return $this->apiReturn(0,[],'请先选择要删除的数据');
        }
        $map[] = ['id','in',$ids];
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $map[] = ['user_id', '=', $this->userId];
        }
        if (Db::name('pocs_file')->where($map)->delete()) {
            return $this->apiReturn(1,[],'批量删除成功');
        } else {
            return $this->apiReturn(0,[],'批量删除失败');
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
            $data['name'] = $v['A'];
            $data['cve_num'] = $v['B'];
            $data['content'] = $v['C'];
            $data['tool'] = $v['D'];
            $data['status'] = $v['E'];
            $data['create_time'] = date('Y-m-d H:i:s',time());

            if (Db::name('pocs_file')->where('name',$data['name'])->count('id')) {
                $this->error('POC名字已存在');
            }
            $temp_data[] = $data;
        }
        if (Db::name('pocs_file')->insertAll($temp_data)) {
            $this->success('POC脚本批量导入成功');
        } else {
            $this->error('POC脚本批量导入失败');
        }
    }

    public function downloaAppTemplate()
    {
        $file_dir = \think\facade\App::getRootPath() . 'public/static/';
        $file_name = 'POC脚本批量导入模版.xls';
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