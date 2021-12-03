<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;

class Api extends BaseController
{
    public $user_id = 0;

    public function initialize()
    {
        $token = getParam('token');
        $this->user_id = Db::name('user')->where('token',$token)->value('id');
        if (!$this->user_id) {
            return $this->apiReturn(0, [], 'token错误');
        }
    }

    /**
     * @return int
     * @Route("Index/app")
     */
    public function app()
    {
        //1. 接收图片

        //
    }

    public function getTargetData(){
        $where[] = ['user_id','=',$this->user_id];
        $where[] = ['is_delete','=',0];
        $app = Db::name('app')->where($where)->where('status',1)->limit(50)->orderRand()->field('id,name,url,username,password,')->find();
        $code = Db::name('code')->where($where)->limit(50)->orderRand()->field('id,name,ssh_url,desc,hash,pulling_mode$,username,password,')->find();
        return $this->apiReturn(1, ['app'=>$app,'code'=>$code], 'ok');
    }


    public function isMyCode(){
        $code_id = getParam('code_id');
        $where[] = ['user_id','=',$this->user_id];
        $where[] = ['is_delete','=',0];
        $where[] = ['id','=',$code_id];
        if (!Db::name('code')->where($where)->count('id')) {
            return false;
        } else {
            return true;
        }
    }


    public function addComposerLibrary(){
        if (!$this->isMyCode()) {
            return $this->apiReturn(0, [], '项目信息不存在');
        }
        $code_id = getParam('code_id');
        $name = getParam('name');
        $version = getParam('version');
        $source = getParam('source');
        $dist = getParam('dist');
        $require = getParam('require');
        $require_dev = getParam('require_dev');
        $type = getParam('type');
        $autoload = getParam('autoload');
        $notification_url = getParam('notification_url');
        $license = getParam('license');
        $authors = getParam('authors');
        $description = getParam('description');
        $homepage = getParam('homepage');
        $keywords = getParam('keywords');
        $time = getParam('time');
        $data['code_id'] = $code_id;
        $data['name'] = $name;
        $data['version'] = $version;
        $data['source'] = json_encode($source);
        $data['dist'] = json_encode($dist);
        $data['require'] = json_encode($require);
        $data['require_dev'] = json_encode($require_dev);
        $data['type'] = $type;
        $data['autoload'] = json_encode($autoload);
        $data['notification_url'] = $notification_url;
        $data['license'] = json_encode($license);
        $data['authors'] = json_encode($authors);
        $data['description'] = $description;
        $data['homepage'] = $homepage;
        $data['keywords'] = json_encode($keywords);
        $data['time'] = $time;
        $data['create_time'] = date('Y-m-d H:i:s', time());
        $data['user_id'] = $this->user_id;
        if (Db::name('code_composer')->insert($data)) {
            $this->scanTime('code',$code_id,'composer_scan_time');
            return $this->apiReturn(1, [], '数据写入成功');
        } else {
            addlog('composer组件数据写入失败：'.json_encode($data));
            return $this->apiReturn(0, [], '数据写入失败');
        }
    }

    public function addPythonLibrary(){
        if (!$this->isMyCode()) {
            return $this->apiReturn(0, [], '项目信息不存在');
        }
        $code_id = getParam('code_id');
        $nameArr = getParam('name');
        $data = [];
        foreach ($nameArr as $v) {
            if (!empty($v)) {
                $data[] = [
                    'code_id' => $code_id,
                    'name' => $v,
                    'create_time' => date('Y-m-d H:i:s', time())
                ];
            }
        }
        if (!$data) {
            return $this->apiReturn(0, [], '请输入正确的数据');
        }
        $data['user_id'] = $this->user_id;
        if (Db::name('code_python')->insert($data)) {
            $this->scanTime('code',$code_id,'python_scan_time');
            return $this->apiReturn(1, [], '数据写入成功');
        } else {
            addlog('python组件数据写入失败：'.json_encode($data));
            return $this->apiReturn(0, [], '数据写入失败');
        }
    }

    public function addJavaLibrary(){
        if (!$this->isMyCode()) {
            return $this->apiReturn(0, [], '项目信息不存在');
        }
        $code_id = getParam('code_id');
        $modelVersion = getParam('modelVersion');
        $groupId = getParam('groupId');
        $artifactId = getParam('artifactId');
        $version = getParam('version');
        $modules = getParam('modules');
        $packaging = getParam('packaging');
        $name = getParam('name');
        $comment = getParam('comment');
        $url = getParam('url');
        $properties = getParam('properties');
        $dependencies = getParam('dependencies');
        $build = getParam('build');
        $data = [
            'code_id' => $code_id,
            'modelVersion' => $modelVersion,
            'groupId' => $groupId,
            'artifactId' => $artifactId,
            'version' => $version,
            'modules' => json_encode($modules),
            'packaging' => $packaging,
            'name' => $name,
            'comment' => $comment ? json_encode($comment) : '',
            'url' => $url,
            'properties' => json_encode($properties),
            'dependencies' => json_encode($dependencies),
            'build' => json_encode($build),
            'create_time' => date('Y-m-d H:i:s', time()),
            'user_id' => $this->user_id,
        ];
        if (Db::name('code_java')->insert($data)) {
            $this->scanTime('code',$code_id,'java_scan_time');
            return $this->apiReturn(1, [], '数据写入成功');
        } else {
            addlog('java组件数据写入失败：'.json_encode($data));
            return $this->apiReturn(0, [], '数据写入失败');
        }
    }

    public function addFortify(){
        if (!$this->isMyCode()) {
            return $this->apiReturn(0, [], '项目信息不存在');
        }
        $project_id = getParam('code_id');
        $Category = getParam('category');
        $Folder = getParam('folder');
        $Kingdom = getParam('kingdom');
        $Abstract = getParam('abstract');
        $Friority = getParam('friority');
        $Primary = getParam('primary');
        $Source = getParam('source');
        $comment = getParam('comment');
        $Source_filename = getParam('source_filename');
        $Primary_filename = getParam('primary_filename');
        $hash = getParam('hash');
        $data['project_id'] = $project_id;
        $data['Category'] = $Category;
        $data['Folder'] = $Folder;
        $data['Kingdom'] = $Kingdom;
        $data['Abstract'] = $Abstract;
        $data['Friority'] = $Friority;
        $data['Primary'] = $Primary;
        $data['Source'] = $Source;
        $data['comment'] = $comment;
        $data['Source_filename'] = $Source_filename;
        $data['Primary_filename'] = $Primary_filename;
        $data['hash'] = $hash;
        $data['user_id'] = $this->user_id;
        $data['create_time'] = date('Y-m-d H:i:s', time());
        if (Db::name('fortify')->insert($data)) {
            $this->scanTime('code',$project_id,'scan_time');
            return $this->apiReturn(1, [], '数据写入成功');
        } else {
            addlog('fortify数据写入失败：'.json_encode($data));
            return $this->apiReturn(0, [], '数据写入失败');
        }
    }


    public function addSemgrep(){
        if (!$this->isMyCode()) {
            return $this->apiReturn(0, [], '项目信息不存在');
        }
        $check_id = getParam('check_id');
        $project_id = getParam('code_id');
        $end_col = getParam('end_col');
        $end_line = getParam('end_line');
        $end_offset = getParam('end_offset');
        $extra_is_ignored = getParam('extra_is_ignored');
        $extra_lines = getParam('extra_lines');
        $extra_message = getParam('extra_message');
        $extra_metadata = getParam('extra_metadata');
        $extra_metavars = getParam('extra_metavars');
        $extra_severity = getParam('extra_severity');
        $path = getParam('path');
        $start_col = getParam('start_col');
        $start_line = getParam('start_line');
        $start_offset = getParam('start_offset');
        $data['check_id'] = $check_id;
        $data['project_id'] = $project_id;
        $data['end_col'] = $end_col;
        $data['end_line'] = $end_line;
        $data['end_offset'] = $end_offset;
        $data['extra_is_ignored'] = $extra_is_ignored;
        $data['extra_lines'] = $extra_lines;
        $data['extra_message'] = $extra_message;
        $data['extra_metadata'] = $extra_metadata;
        $data['extra_metavars'] = $extra_metavars;
        $data['extra_severity'] = $extra_severity;
        $data['path'] = $path;
        $data['start_col'] = $start_col;
        $data['start_line'] = $start_line;
        $data['start_offset'] = $start_offset;
        $data['user_id'] = $this->user_id;
        $data['create_time'] = date('Y-m-d H:i:s', time());
        if (Db::name('fortify')->insert($data)) {
            $this->scanTime('code',$project_id,'semgrep_scan_time');
            return $this->apiReturn(1, [], '数据写入成功');
        } else {
            addlog('semgrep数据写入失败：'.json_encode($data));
            return $this->apiReturn(0, [], '数据写入失败');
        }
    }

    public function scanTime($table,$id,$filed)
    {
        $data = [$filed => date('Y-m-d H:i:s', time())];
        Db::name($table)->where('id', $id)->update($data);
    }
}