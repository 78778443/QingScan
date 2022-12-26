<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;

class Index extends Common
{


    public function index()
    {
//        $where = ['is_delete' => 0];
        $where = [];
//        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
//            $where = array_merge($where, ['user_id' => $this->userId]);
//        }
        //黑盒项目数量
        $appCount = Db::table('app')->where($where)->count();
        //黑盒rad数量
        $urlsCount = Db::table('urls')->where($where)->count();
        //黑盒xray数量
        $xrayCount = Db::table('xray')->where($where)->count();
        //黑盒sqlmap数量
        $sqlmapCount = Db::table('urls_sqlmap')->where($where)->count();
        //黑盒awvs数量
        $awvsCount = Db::table('awvs_app')->where($where)->count();
        //黑盒vulmap数量
        $vulmapCount = Db::table('app_vulmap')->where($where)->count();
        //黑盒nuclei数量
        $nucleiCount = Db::table('app_nuclei')->where($where)->count();
        //黑盒dirmap数量
        $dirmapCount = Db::table('app_dirmap')->where($where)->count();
        //黑盒whatweb数量
        $whatwebCount = Db::table('app_whatweb')->where($where)->count();
        //黑盒one_for_all数量
        $oneforallCount = Db::table('one_for_all')->where($where)->count();


        ##########
        //资产探测
        $hostCount = Db::table('host')->count();
        //端口数量
        $portCount = Db::table('host_port')->count();
        //服务数量
        $serviceCount = Db::table('host_port')->group("service")->count();
        // 未授权漏洞
        $unauthorizedCount = Db::table('host_unauthorized')->count();


        ####### 白盒统计
        //资产探测
        $codeCount = Db::table('code')->count();
        $semgrepCount = Db::table('semgrep')->count();
        $fortifyCount = Db::table('fortify')->count();
        $mobsfscanCount = Db::table('mobsfscan')->count();
        $murphysecCount = Db::table('murphysec')->count();
        $phpCount = Db::table('code_composer')->count();
        $pythonCount = Db::table('code_python')->count();
        $javaCount = Db::table('code_java')->count();
        $hemaCount = Db::table('code_webshell')->count();


        #######漏洞信息库
        $pocsuite3Count = Db::table('pocsuite3')->count();
        $vulnerableCount = Db::table('vulnerable')->count();
        $pocsCount = Db::table('pocs_file')->count();
        $targetCount = Db::table('vul_target')->count();


        $data = [
            [
                "name" => "黑盒信息",
                "value" => $appCount,
                "subInfo" => [
                    ["name" => "rad", "value" => $urlsCount, "href" => url('urls/index')],
                    ["name" => "xray", "value" => $xrayCount, "href" => url('xray/index')],
                    ["name" => "sqlmap", "value" => $sqlmapCount, "href" => url('sqlmap/index')],
                    ["name" => "awvs", "value" => $awvsCount, "href" => url('bug/awvs')],
                    ["name" => "vulmap", "value" => $vulmapCount, "href" => url('vulmap/index')],
                    ["name" => "nuclei", "value" => $nucleiCount, "href" => url('app_nuclei/index')],
                    ["name" => "dirmap", "value" => $dirmapCount, "href" => url('dirmap/index')],
                    ["name" => "whatweb", "value" => $whatwebCount, "href" => url('whatweb/index')],
                    ["name" => "oneforall", "value" => $oneforallCount, "href" => url('one_for_all/index')],
                ]
            ],
            [
                "name" => "资产探测",
                "value" => $hostCount,
                "subInfo" => [
                    ["name" => "port", "value" => $portCount, "href" => url('host_port/index')],
                    ["name" => "中间件", "value" => $serviceCount, "href" => url('host_port/index')],
                    ["name" => "未授权漏洞", "value" => $unauthorizedCount, "href" => url('unauthorized/index')],
                ]
            ],
            [
                "name" => "白盒审计",
                "value" => $codeCount,
                "subInfo" => [
                    ["name" => "fortify", "value" => $fortifyCount, "href" => url('code/bug_list')],
                    ["name" => "semgrep", "value" => $semgrepCount, "href" => url('code/semgrep_list')],
                    ["name" => "mobsfscan", "value" => $mobsfscanCount, "href" => url('mobsfscan/index')],
                    ["name" => "murphysec", "value" => $murphysecCount, "href" => url('murphysec/index')],
                    ["name" => "webshell", "value" => $hemaCount, "href" => url('code_webshell/index')],
                    ["name" => "php", "value" => $phpCount, "href" => url('code_composer/index')],
                    ["name" => "python", "value" => $pythonCount, "href" => url('code_python/index')],
                    ["name" => "java", "value" => $javaCount, "href" => url('code_java/index')],
                ]
            ]
            , [
                "name" => "漏洞站点",
                "value" => $pocsuite3Count,
                "subInfo" => [
                    ["name" => "漏洞情报", "value" => $vulnerableCount, "href" => url('vulnerable/index')],
                    ["name" => "Poc脚本", "value" => $pocsCount, "href" => url('pocs_file/index')],
                    ["name" => "可疑目标", "value" => $targetCount, "href" => url('vul_target/index')],
                ]
            ]
        ];

        return View::fetch('index', ['data' => $data]);
    }

    /**
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function tongji()
    {
        //漏洞类型分布
        $folderCount = Db::table('fortify')->field('Folder as name,count(Folder) as value')->group('Folder')->select()->toArray();

        //漏洞统计
        $shijianCount = [];
        $shijianCount[] = ['name' => '14天', 'value' => Db::table('fortify')->whereTime('create_time', '>=', date('Y-m-d H:i:s', time() - 14 * 86400))->count('id')];
        $shijianCount[] = ['name' => '7天', 'value' => Db::table('fortify')->whereTime('create_time', '>=', date('Y-m-d H:i:s', time() - 7 * 86400))->count('id')];
        $shijianCount[] = ['name' => '24小时', 'value' => Db::table('fortify')->whereTime('create_time', '>=', date('Y-m-d H:i:s', time() - 1 * 86400))->count('id')];

        //漏洞排行
        $bugPaihang = Db::table('fortify')->field('Category as name,count(Category) as value')->where("Folder != 'Low'")->group('Category')->select()->toArray();
        array_multisort(array_column($bugPaihang, 'value'), SORT_DESC, $bugPaihang);
        $bugPaihang = array_slice($bugPaihang, 0, 10);

        //端口发现
        $portCount = Db::table('host_port')->field('port as name,count(port) as value')->group('port')->select()->toArray();
        array_multisort(array_column($portCount, 'value'), SORT_DESC, $portCount);
        $portCount = array_slice($portCount, 0, 10);

        //主机统计
        $hostCount = Db::table('host_port')->field('host as name,count(host) as value')->group('host')->select()->toArray();
        array_multisort(array_column($hostCount, 'value'), SORT_DESC, $hostCount);
        $hostCount = array_slice($hostCount, 0, 10);

        //网站统计
        $serviceCount = Db::table('app_info')->where('server', '<>', 'unknown')->field('server as name,count(server) as value')->group('server')->select()->toArray();
        array_multisort(array_column($serviceCount, 'value'), SORT_DESC, $serviceCount);
        $serviceCount = array_slice($serviceCount, 0, 10);

        //赞助信息
        $zanzhu = Db::table('system_zanzhu')->order('time', 'desc')->limit(15)->select()->toArray();

        $data = [
            ['key' => 'folderCount', 'data' => $folderCount, 'title' => "危害等级"],
            ['key' => 'shijianCount', 'data' => $shijianCount, 'title' => "新增统计"],
            ['key' => 'bugPaihang', 'data' => $bugPaihang, 'title' => "漏洞分类"],
            ['key' => 'portCount', 'data' => $portCount, 'title' => "端口统计"],
            ['key' => 'hostCount', 'data' => $hostCount, 'title' => "主机统计"],
            ['key' => 'serviceCount', 'data' => $serviceCount, 'title' => "服务统计"],
        ];

        // 不带任何参数 自动定位当前操作的模板文件
        return View::fetch('index', ['list' => $data, 'zanzhu' => $zanzhu]);
//        $this->show('index/index', ['list' => $data]);
    }

    public function upgradeTips(){
        $result = curl_get(config('app.plugin_store.domain_name').'index/upgradeTips');
        $result = json_decode($result,true);
        if (!$result['code']) {
            return $this->apiReturn(0,[],'');
        }
        $path = \think\facade\App::getRootPath() . '../docker/data/update.lock';
        // 获取当前版本号
        $version = file_get_contents($path);
        $news_version = $result['data']['news_version'];
        if ($version < $news_version) {
            return $this->apiReturn(1,[],"当前系统最新版本为：{$news_version},是否升级");
        } else {
            return $this->apiReturn(0,[],"update.lock文件不存在");
        }
    }
}