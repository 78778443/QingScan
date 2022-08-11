<?php


namespace app\controller;


use think\facade\Db;
use think\Request;

class Config extends Common
{
    public function index(Request $request)
    {
        $pageSize = 25;
        $where[] = ['is_delete', '=', 0];
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['key|name|value', 'like', "%{$search}%"];
        }
        $list = Db::name('system_config')->where($where)->order('id', 'desc')->paginate([
            'list_rows' => $pageSize,
            'query' => $request->param()
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        return view('config/index', $data);
    }

    public function add(Request $request)
    {
        if ($request->isPost()) {
            $data['key'] = $request->param('key');
            $data['name'] = $request->param('name');
            $data['value'] = $request->param('value');
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

    public function edit(Request $request)
    {
        $id = $request->param('id');
        if (!$id) {
            $this->error('参数错误');
        }
        if (request()->isPost()) {
            $data['key'] = $request->param('key');
            $data['name'] = $request->param('name');
            $data['value'] = $request->param('value');
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

    public function del(Request $request)
    {
        $id = $request->param('id');
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
                sort($fileNameList);
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
                            $sql = trim($sql);  // 去除两边空白造成的报错
                            if ($sql) {
                                Db::execute($sql.';');
                            }
                        }
                        $version = $newVersion;
                        file_put_contents($lock,$newVersion);
                    }
                }
            }
            $version = str_replace(".sql","",$version);
            $msg = '系统更新成功，当前版本号：'.$version;
            $this->addUserLog('系统更新',"系统更新成功,当前版本号[{$version}]");
        } catch (\Exception $e) {
            $msg = '系统更新失败：'.$e->getMessage();
            $this->addUserLog('系统更新',$msg);
        }
        $data['info'] = $msg;
        return view('config/update', $data);
    }

    public function clear_cache(){
        $cmd = "cd /root/qingscan/code/runtime/log/ && rm -rf ./*";
        systemLog($cmd,false);
        $cmd = "cd /root/qingscan/code/runtime/temp/ && rm -rf ./*";
        systemLog($cmd,false);
        $this->success('系统缓存,清除成功');
    }
}