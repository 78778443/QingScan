<?php


namespace app\controller;


use think\facade\Db;

class Config extends Common
{
    public function index()
    {
        $where[] = ['is_delete', '=', 0];
        $search = getParam('search');
        if (!empty($search)) {
            $where[] = ['key|name|value', 'like', "%{$search}%"];
        }
        $list = Db::name('system_config')->where($where)->order('id', 'desc')->paginate(25)->toArray();
        $data['list'] = $list['data'];
        $data['page'] = $list['current_page'];
        return view('config/index', $data);
    }

    public function add()
    {
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

    public function edit()
    {
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
            if (Db::name('system_config')->where('id', $id)->update($data)) {
                return redirect(url('config/index'));
            } else {
                $this->error('修改失败');
            }
        } else {
            $where[] = ['id', '=', $id];
            $where[] = ['is_delete', '=', 0];
            $data['info'] = Db::name('system_config')->where($where)->find();
            if (!$data['info']) {
                $this->error('数据不存在');
            }
            return view('config/edit', $data);
        }
    }

    public function del()
    {
        $id = getParam('id');
        if (!$id) {
            $this->error('参数错误');
        }
        if (Db::name('system_config')->where('id', $id)->update(['is_delete' => -1])) {
            $this->success('删除成功', 'config/index');
        } else {
            $this->error('删除失败');
        }
    }

    // 系统更新
    public function system_update()
    {
        $path = \think\facade\App::getRootPath() . '../';
        try {
            $cmd = "cd {$path} && git config --global user.email 'you@example.com' && git config --global user.name 'Your Name' && git pull origin main";
            $result = systemLog($cmd,false);
            $result = implode("\n", $result);
            //$msg = '系统更新成功：'.$result;

            // 更新sql语句
            $sqlPath = $path . 'docker/data';
            $fileNameList = getDirFileName($sqlPath);
            unset($fileNameList[count($fileNameList) - 1]);
            unset($fileNameList[count($fileNameList) - 1]);
            if (!empty($fileNameList)) {
                $lock = $sqlPath.'/update.lock';
                // 获取当前版本号
                $version = file_get_contents($lock);
                foreach ($fileNameList as $v) {
                    $filename = substr($v,strripos($v,'/')+1,strlen($v));
                    $newVersion = substr($filename,0,strripos($filename,'.'));
                    if ($version < $newVersion) {
                        $content = file_get_contents($sqlPath.'/'.$filename);
                        $sqlArr = explode(';',$content);
                        foreach ($sqlArr as $sql) {
                            if ($sql) {
                                Db::execute($sql.';');
                            }
                        }
                        $version = $newVersion;
                        file_put_contents($lock,$newVersion);
                    }
                }
            }
            $msg = '系统更新成功，当前版本号：'.str_replace(".sql","",$version);
        } catch (\Exception $e) {
            $msg = '系统更新失败：'.$e->getMessage();
        }
        $data['info'] = $msg;
        return view('config/update', $data);
    }
}