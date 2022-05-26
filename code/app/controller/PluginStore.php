<?php

namespace app\controller;

use think\facade\Db;
use think\facade\View;
use think\Request;

class PluginStore extends Common
{
    public $plugin_store_domain;

    public function initialize()
    {
        $this->plugin_store_domain = config('app.plugin_store.domain_name');

        parent::initialize();
    }

    public function index(){
        ini_set('max_execution_time', 0);
        $result = curl_get($this->plugin_store_domain.'plugin_store/list');
        $list = json_decode($result,true);
        if (!isset($list['data'])) {
            $this->error('获取插件信息失败：'.$list['msg']);
        }
        $list = $list['data'];
        if ($list) {
            foreach ($list as &$v) {
                $v['is_install'] = 0;
                $v['status'] = '未安装';
                $where['name'] = $v['name'];
                $info = Db::name('plugin_store')->where($where)->find();
                if ($info) {
                    $v['is_install'] = 1;
                    if ($info['status']) {
                        $v['status'] = '开启';
                    } else {
                        $v['status'] = '禁用';
                    }
                    $v['plugin_id'] = $info['id'];
                }
            }
            $data['list'] = $list;
        } else {
            $data['list'] = [];
        }
        return View::fetch('index',$data);
    }

    public function install(Request $request){
        $code = $request->param('code');
        $result = curl_get($this->plugin_store_domain.'plugin_store/code_info?code='.$code);
        $result = json_decode($result,true);
        if (!$result['code']) {
            return $this->apiReturn(0,[],'插件安装失败，错误原因：'.$result['msg']);
        }
        $id = $request->param('id',0,'intval');
        $result = curl_get($this->plugin_store_domain.'plugin_store/info?id='.$id);
        $info = json_decode($result,true);
        if (!$info['code']) {
            return $this->apiReturn(0,[],'插件安装失败，错误原因：'.$result['msg']);
        }
        $info = $info['data'];
        if (Db::name('plugin_store')->where('name',$info['name'])->count('id')) {
            return $this->apiReturn(0,[],'插件安装失败，错误原因：插件已安装');
        }
        // 兑换
        $result = curl_get($this->plugin_store_domain.'plugin_store/use_code?code='.$code.'&id='.$id);
        $result = json_decode($result,true);
        if (!$result['code']) {
            return $this->apiReturn(0,[],'插件安装失败，错误原因：'.$result['msg']);
        }
        // 下载链接
        $download_url = $this->plugin_store_domain.$result['data']['download_url'];
        $save_dir = \think\facade\App::getRuntimePath().'plugins/'; // 服务资源目录
        $filename = substr($download_url, strrpos($download_url, '/') + 1);
        if (downloadFile($download_url, $save_dir, $filename) === true) {
            $file_path = $save_dir.$filename;

            // 删除原有目录
            $temp_plugin_path = $save_dir.$info['name'].'/';
            if(is_dir($temp_plugin_path)) {
                deldir($temp_plugin_path);
            }

            // 解压目录
            $zip = new \ZipArchive();
            if ($zip->open($file_path) === TRUE) {//中文文件名要使用ANSI编码的文件格式
                $zip->extractTo($save_dir);//提取全部文件
                $zip->close();

                $app = \think\facade\App::getAppPath();

                Db::startTrans();
                try {
                    // 执行sh文件
                    $sh = $temp_plugin_path.'sqlOrsh/exec.sh';
                    if (file_exists($sh)) {
                        $content = file_get_contents($sh);
                        if (!empty($content)) {
                            $cmdArr = explode(';',$content);
                            unset($cmdArr[count($cmdArr)-1]);
                            if (!empty($cmdArr[count($cmdArr)-1])) {
                                foreach ($cmdArr as $cmd) {
                                    systemLog(trim($cmd),false);
                                }
                            }
                        }
                    }

                    foreach (scandir($temp_plugin_path.'sqlOrsh') as $value){
                        if($value != '.' && $value != '..'){
                            $preg = "/(.*?)\.sql/";
                            if (preg_match($preg,$value)) {
                                $content = file_get_contents($temp_plugin_path.'sqlOrsh'.'/'.$value);
                                $sqlArr = explode(';',$content);
                                foreach ($sqlArr as $sql) {
                                    $sql = trim($sql);  // 去除两边空白造成的报错
                                    if ($sql) {
                                        Db::execute($sql.';');
                                    }
                                }
                            }
                        }
                    }

                    // 移动相应的文件
                    if (!file_exists($app.'plugins/')) {
                        mkdir($app.'plugins/', 0777, true);
                    }
                    $app_tools = $app.'plugins/'.$info['name'];
                    copydir($temp_plugin_path.'sqlOrsh',$app_tools);
                    copydir($temp_plugin_path.'controller',$app.'controller');
                    copydir($temp_plugin_path.'model',$app.'model');
                    copydir($temp_plugin_path.'view',$app.'../view');
                    if (!file_exists($app.'../../tools/plugins/')) {
                        mkdir($app.'../../tools/plugins/', 0777, true);
                    }
                    copydir($temp_plugin_path.'tools/',$app.'../../tools/plugins/');

                    // 删除压缩包目录文件
                    deldir($temp_plugin_path);
                    deldir($app_tools);
                    @unlink($file_path);

                    $data = [
                        'status'=>1,
                        'create_time'=>date('Y-m-d H:i:s',time()),
                        'name'=>$info['name'],
                        'title'=>$info['title'],
                        'version'=>$info['version'],
                        'description'=>$info['description'],
                        'code'=>$code,
                    ];
                    Db::name('plugin_store')->insert($data);

                    Db::commit();
                    return $this->apiReturn(1,[],'插件安装成功');
                } catch (\Exception $e) {
                    // 回退兑换码
                    $result = curl_get($this->plugin_store_domain.'plugin_store/no_use_code?code='.$code);
                    $result = json_decode($result,true);
                    Db::rollback();
                    if (!$result['code']) {
                        return $this->apiReturn(0,[],'插件安装失败，错误原因：'.$result['msg']);
                    } else {
                        return $this->apiReturn(0,[],'插件安装失败，错误原因'.$e->getMessage());
                    }
                }
            } else {
                // 回退兑换码
                $result = curl_get($this->plugin_store_domain.'plugin_store/no_use_code?code='.$code);
                $result = json_decode($result,true);
                if (!$result['code']) {
                    return $this->apiReturn(0,[],'插件安装失败，错误原因：'.$result['msg']);
                } else {
                    return $this->apiReturn(0,[],'插件安装失败，解压失败');
                }
            }
        } else {
            // 回退兑换码
            $result = curl_get($this->plugin_store_domain.'plugin_store/no_use_code?code='.$code);
            $result = json_decode($result,true);
            if (!$result['code']) {
                return $this->apiReturn(0,[],'插件安装失败，错误原因：'.$result['msg']);
            } else {
                return $this->apiReturn(0,[],'插件安装失败，请稍候重试');
            }
        }
    }

    // 卸载
    public function uninstall(Request $request){
        // 测试代码
        $id = $request->param('id',0,'intval');
        $info = Db::name('plugin_store')->where('id',$id)->find();
        if (!$info) {
            $this->error('插件未安装');
        }
        // 删除相关信息
        $pathArr = getUninstallPath($info['name']);
        foreach ($pathArr as $value) {
            if(!is_dir($value)) {
                @unlink($value);
            } else {
                deldir($value);
            }
        }
        Db::name('plugin_store')->where('id',$id)->delete();
        $this->success('插件卸载成功',url('index'));
    }
}