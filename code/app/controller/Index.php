<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;

class Index extends Common
{

    /**
     * @return int
     * @Route("Index/app")
     */
    public function app()
    {
        return 123;
    }


    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }


    /**
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
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


        $data = [
            ['key' => 'folderCount', 'data' => $folderCount, 'title' => "危害等级"],
            ['key' => 'shijianCount', 'data' => $shijianCount, 'title' => "新增统计"],
            ['key' => 'bugPaihang', 'data' => $bugPaihang, 'title' => "漏洞分类"],
            ['key' => 'portCount', 'data' => $portCount, 'title' => "端口统计"],
            ['key' => 'hostCount', 'data' => $hostCount, 'title' => "主机统计"],
            ['key' => 'serviceCount', 'data' => $serviceCount, 'title' => "服务统计"],
        ];

        // 不带任何参数 自动定位当前操作的模板文件
        return View::fetch('index', ['list' => $data]);
//        $this->show('index/index', ['list' => $data]);
    }

}