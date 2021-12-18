<?php


namespace app\controller;


use think\facade\Db;

class Config extends Common
{
    public function index()
    {
        $where[] = ['is_delete','=',0];
        $search = getParam('search');
        if (!empty($search)) {
            $where[] = ['key|name|value','like',"%{$search}%"];
        }
        $list = Db::name('system_config')->where($where)->order('id','desc')->paginate(25)->toArray();
        $data['list'] = $list['data'];
        $data['page'] = $list['current_page'];
        return view('config/index',$data);
    }

    public function add(){
        if (request()->isPost()) {
            $data['key'] = getParam('key');
            $data['name'] = getParam('name');
            $data['value'] = getParam('value');
            if (!$data['key'] || !$data['name'] || !$data['value']) {
                $this->error('参数不能为空');
            }
            if (Db::name('system_config')->insert($data)) {
                return redirect(url('config/index'));
            } else {
                $this->error('添加失败');
            }
        } else {
            return view('config/add');
        }
    }

    public function edit(){
        $id = getParam('id');
        if (!$id) {
            $this->error('参数错误');
        }
        if (request()->isPost()) {
            $data['key'] = getParam('key');
            $data['name'] = getParam('name');
            $data['value'] = getParam('value');
            if (!$data['key'] || !$data['name'] || !$data['value']) {
                $this->error('参数不能为空');
            }
            if (Db::name('system_config')->where('id',$id)->update($data)) {
                return redirect(url('config/index'));
            } else {
                $this->error('修改失败');
            }
        } else {
            $where[] = ['id','=',$id];
            $where[] = ['is_delete','=',0];
            $data['info'] = Db::name('system_config')->where($where)->find();
            if (!$data['info']) {
                $this->error('数据不存在');
            }
            return view('config/edit',$data);
        }
    }

    public function del(){
        $id = getParam('id');
        if (!$id) {
            $this->error('参数错误');
        }
        if (Db::name('system_config')->where('id',$id)->update(['is_delete'=>-1])) {
            $this->success('删除成功','config/index');
        } else {
            $this->error('删除失败');
        }
    }
}