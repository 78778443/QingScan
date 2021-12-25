<?php


namespace app\controller;


use think\facade\Db;
use think\facade\View;
use think\Request;

class PocsFile extends Common
{
    public function index(){
        $pageSize = 20;
        $where = [];
        $list = Db::table('pocs_file')->where($where)->order("id", 'desc')->paginate($pageSize);
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
            $data['content'] = input('content');
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
        if (Db::name('pocs_file')->where('id',$id)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }
}