<?php

namespace app\controller;

use app\BaseController;
use app\model\AppModel;
use app\model\HostModel;
use app\model\HostPortModel;
use app\model\UrlsModel;
use phpseclib3\File\ASN1\Maps\DSAPrivateKey;
use think\facade\Db;
use think\facade\View;


class HostPort extends Common
{
    public function index()
    {
        $where[] = ['is_delete','=',0];
        $search = getParam('search','');    // 项目名称
        if (!empty($search)) {
            $where[] = ['port|host','like',"%{$search}%"];
        }
        $host = getParam('host'); // 主机名称
        $port = getParam('port');   // 端口名称
        $service = getParam('service');   // 组件类型
        $check_status = getParam('check_status');   // 审核状态
        if (!empty($host)) {
            $where[] = ['host','=',$host];
        }
        if (!empty($port)) {
            $where[] = ['check_id','=',$port];
        }
        if (!empty($service)) {
            $where[] = ['service','=',$service];
        }
        if ($check_status !== null && in_array($check_status,[0,1,2])) {
            $where[] = ['check_status','=',$check_status];
        }

        $list = Db::table(HostPortModel::$tableName)->where($where)->paginate(10);

        $data = [];
        $data['list'] = $list->toArray()['data'];
        $data['classify'] = HostPortModel::getClassify($where);
        $data['appArr'] = AppModel::getAppName();
        // 获取分页显示
        $data['page'] = $list->render();


        $projectArr = Db::table('code')->select()->toArray();
        $projectArr = array_column($projectArr, null, 'id');
        $data['projectArr'] = $projectArr;
        $data['host'] = Db::table(HostPortModel::$tableName)->where($where)->group('host')->column('host');
        $data['port'] = Db::table(HostPortModel::$tableName)->where($where)->group('port')->column('port');
        $data['service'] = Db::table(HostPortModel::$tableName)->where($where)->group('service')->column('service');
        $data['check_status_list'] = ['未审计','有效漏洞','无效漏洞'];

        return View::fetch('index', $data);
    }

    public function details(){
        $id = getParam('id');
        if (!$id) {
            $this->error('参数错误');
        }
        $where['id'] = $id;
        $info = Db::table('host_port')->where($where)->find();
        if (!$info) {
            $this->error('数据不存在');
        }
        $upper_id = Db::name('host_port')->where('id','<',$id)->order('id','desc')->value('id');
        $info['upper_id'] = $upper_id?:$id;
        $lower_id = Db::name('host_port')->where('id','>',$id)->order('id','asc')->value('id');
        $info['lower_id'] = $lower_id?:$id;

        $data['info'] = $info;
        return View::fetch('details', $data);
    }

    public function add()
    {
        $this->show('host/add');
    }

    public function _add()
    {
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $_POST['user_id'] = $this->userId;
        }
        UrlsModel::addData($_POST);

        $this->Location("index.php?s=host/index");
    }

    public function add_api_url()
    {
        $this->show('host/add_api_url');
    }

    public function _add_api_url()
    {
        if ($this->auth_group_id != 5 && !in_array($this->userId,config('app.ADMINISTRATOR'))) {
            $_POST['user_id'] = $this->userId;
        }
        UrlsModel::addData($_POST);
    }

    public function getHtmlContent()
    {

        $list = HostPortModel::getHttpList();

        var_dump($list);
        foreach ($list as $value) {

            $url = "{$value['service']}://{$value['host']}:{$value['port']}/";

            $value['headers'] = get_headers($url);
            $value['html'] = file_get_contents($url);
            HostPortModel::updateByWhere(['id' => $value['id']], $value);
        }
    }


    public function checkRedis()
    {
        $redisPath = __BASEPATH__ . "/tools/redis";
        $redisList = Db::table('host_port')->where(['port' => 6379])->select()->toArray();

        foreach ($redisList as $value) {
            $cmd = "cd {$redisPath}  && python3 unauthorized.py -u {$value['host']} -p  {$value['port']}";
            $result = [];
            exec($cmd, $result);

            if ($result[0] ?? '' == '存在Redis未授权访问') {
                $data = ['category' => 'Redis unauthorized', 'host' => $value['host'], 'name' => "{$value['host']}  Redis未授权漏洞"];
                Db::table('bug')->insert($data);
                echo "主机 {$value['host']}:{$value['port']} Redis服务未开启认证" . PHP_EOL;
            }
        }
    }

    public function del()
    {


        $id = getParam('id');
        if (Db::name('host_port')->where('id',$id)->update(['is_delete'=>1])) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }
}
