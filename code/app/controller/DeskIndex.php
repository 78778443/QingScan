<?php

namespace app\controller;

use app\BaseController;
use app\model\AppModel;
use app\model\ConfigModel;
use think\facade\Db;
use think\facade\View;
use think\Request;

class DeskIndex extends BaseController
{
    public $userId = 0;
    public $auth_group_id = 0;
    public $statusArr = ["未启用", "已启用", "已删除"];

    public function initialize()
    {
        $token = \Request()->param('token');
        //$token = '1ca4725c34758183af3fd1f723f07a31';
        $token = request()->header('token');
        $this->userId = Db::name('user')->where('token', $token)->value('id');
        $this->auth_group_id = Db::name('user')->where('token', $token)->value('auth_group_id');

        if (!$this->userId) {
            return $this->apiReturn(0, [], 'token错误');
        }
    }


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
        $urlsCount = Db::table('asm_urls')->where($where)->count();
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
        $hostCount = Db::table('asm_host')->count();
        //端口数量
        $portCount = Db::table('asm_host_port')->count();
        //服务数量
        $serviceCount = Db::table('asm_host_port')->group("service")->count();


        ####### 白盒统计
        //资产探测
        $codeCount = Db::table('code')->count();
        $semgrepCount = Db::table('semgrep')->count();
        $fortifyCount = Db::table('fortify')->count();
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
                "name" => "网站扫描",
                "value" => $appCount,
                "subInfo" => [
                    ["name" => "rad", "value" => $urlsCount],
                    ["name" => "xray", "value" => $xrayCount],
                    ["name" => "sqlmap", "value" => $sqlmapCount],
                    ["name" => "awvs", "value" => $awvsCount],
                    ["name" => "vulmap", "value" => $vulmapCount],
                    ["name" => "nuclei", "value" => $nucleiCount],
                    ["name" => "dirmap", "value" => $dirmapCount],
                    ["name" => "dirmap", "value" => $whatwebCount],
                    ["name" => "oneforall", "value" => $oneforallCount],
                ]
            ],
            [
                "name" => "资产探测",
                "value" => $hostCount,
                "subInfo" => [
                    ["name" => "port", "value" => $portCount],
                    ["name" => "组件", "value" => $serviceCount],
                ]
            ],
            [
                "name" => "白盒审计",
                "value" => $codeCount,
                "subInfo" => [
                    ["name" => "fortify", "value" => $fortifyCount],
                    ["name" => "semgrep", "value" => $semgrepCount],
                    ["name" => "webshell", "value" => $hemaCount],
                    ["name" => "php", "value" => $phpCount],
                    ["name" => "python", "value" => $pythonCount],
                    ["name" => "java", "value" => $javaCount],
                ]
            ]
            , [
                "name" => "漏洞站点",
                "value" => $pocsuite3Count,
                "subInfo" => [
                    ["name" => "漏洞情报", "value" => $vulnerableCount],
                    ["name" => "Poc脚本", "value" => $pocsCount],
                    ["name" => "可疑目标", "value" => $targetCount],
                ]
            ]
        ];
        return $this->apiReturn(200, $data);
    }


}