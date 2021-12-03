<?php


namespace app\controller;


use think\facade\Db;
use think\facade\View;

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


    public function add()
    {
        if (request()->isPost()) {
            $data['cve_num'] = getParam('cve_num');
            $data['status'] = getParam('status');
            $data['is_yaml'] = getParam('is_yaml');
            $data['filename'] = getParam('filename');
            $content = input('content');
            if (!$content || !$data['cve_num'] || !$data['filename']) {
                $this->error('数据不能为空');
            }
            if (Db::name('pocs_file')->where('filename',$data['filename'])->count('id')) {
                $this->error('文件名已存在');
            }
            if ($data['is_yaml']) {
                if (!@yaml_parse($content)) {
                    $this->error('yaml文件格式错误');
                }
            }
            $filename = '/data/tools/pocs/custom/'.$data['filename'];
            $data['poc_file'] = $filename;
            if( ($res = fopen($filename,'w+')) === false){
                $this->error('文件创建失败');
            }
            if(!fwrite ($res,$content)){ //将信息写入文件
                $this->error('文件内容写入失败');
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

    public function edit()
    {
        $id = getParam('id');
        $map[] = ['id','=',$id];
        $info = Db::name('pocs_file')->where($map)->find();
        if (!$info) {
            $this->error('数据不存在');
        }
        if (request()->isPost()) {
            /*$content = getParam('content');
            $filename = $info['poc_file'];
            if( ($res = fopen($filename,'w+')) === false){
                $this->error('文件创建失败');
            }
            if(!fwrite ($res,$content)){ //将信息写入文件
                $this->error('文件内容写入失败');
            }*/
            //$data['is_yaml'] = getParam('is_yaml');
            $data['status'] = getParam('status');
            if (Db::name('pocs_file')->where($map)->update($data)) {
                $this->success('编辑成功','index');
            } else {
                $this->error('编辑失败');
            }
        } else {
            $filename = $info['poc_file'];
            if (strpos($filename,'/data/') === false) {
                $filename = '/data/'.$filename;
            }
            $info['content'] = @file_get_contents($filename);
            $data['info'] = $info;
            return View::fetch('edit',$data);
        }
    }

    public function details(){
        $id = getParam('id');
        $info = Db::name('pocs_file')->where('id',$id)->find();
        if (!$info) {
            $this->error('数据不存在');
        }

        $upper_id = Db::name('pocs_file')->where('id', '<', $id)->order('id', 'desc')->value('id');
        $info['upper_id'] = $upper_id ?: $id;
        $lower_id = Db::name('pocs_file')->where('id', '>', $id)->order('id', 'asc')->value('id');
        $info['lower_id'] = $lower_id ?: $id;
        $filename = $info['poc_file'];
        if (strpos($filename,'/data/') === false) {
            $filename = '/data/'.$filename;
        }
        $info['content'] = @file_get_contents($filename);
        $data['info'] = $info;
        return View::fetch('details',$data);
    }

    public function del()
    {
        $id = getParam('id');
        if (Db::name('pocs_file')->where('id',$id)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }
}