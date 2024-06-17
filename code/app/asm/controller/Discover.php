<?php

namespace app\asm\controller;

use app\asm\model\DomainModel;
use app\controller\Common;
use app\model\ConfigModel;
use think\facade\Db;
use think\facade\View;
use think\Request;


class Discover extends Common
{

    public function index(Request $request)
    {




        $pageSize = 50;
        $where = [];

        if ($request->param('keyword')) $where[] = ['ip|domain|host|title|product_category', 'like', "%" . $request->param('keyword') . "%"];

        $list = Db::table('tool_fofa')->where($where)->orderRand()->paginate([
            'list_rows' => $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        $data['keyword'] = $request->param('keyword', 'dedecms');

        $this->autoInsertData($data['list']);
        return View::fetch('index', $data);
    }

    private function autoInsertData($list)
    {
        foreach ($list as &$item) {
            $item['host'] = str_replace('https://', '', $item['host']);
            $item['url'] = $item['link'];
        }

        $domainCount = Db::table('asm_domain')->extra('IGNORE')->strict(false)->insertAll($list);
        $ipCount = Db::table('asm_ip')->extra('IGNORE')->strict(false)->insertAll($list);
        $portCount = Db::table('asm_ip_port')->extra('IGNORE')->strict(false)->insertAll($list);
        $urlCount = Db::table('asm_urls')->extra('IGNORE')->strict(false)->insertAll($list);


//        echo "域名：{$domainCount} 域名：{$ipCount} 域名：{$portCount} URL:{$urlCount}";
    }

    public function keyword_conf(Request $request)
    {
        if (function_exists('startGetDomain')) startGetDomain();

        $pageSize = 20;
        $where = [];
        if ($request->param('domain')) $where[] = ['domain', '=', $request->param('domain')];
        if (!empty($request->param('app_id'))) $where[] = ['app_id', '=', $request->param('app_id')];

        $list = Db::table('asm_discover')->where($where)->order("id", 'desc')->paginate([
            'list_rows' => $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        $data['page'] = $list->render();
        $data['fofa_user'] = ConfigModel::value('fofa_user');
        $data['fofa_token'] = ConfigModel::value('fofa_token');

        return View::fetch('keyword_conf', $data);
    }


    public function _add_keyword(Request $request)
    {

        $data = $request->param('', '', '');
        $data = array_map('html_entity_decode', $data);
        Db::table('asm_discover')->strict(false)->insert($data);

        return redirect($_SERVER['HTTP_REFERER']);
    }

    public function _del(Request $request)
    {
        $id = $request->param('id');

        Db::table('tool_fofa')->delete($id);

        return redirect($_SERVER['HTTP_REFERER']);
    }


    public function _del_keyword(Request $request)
    {
        $id = $request->param('id');

        Db::table('asm_discover')->delete($id);

        return redirect($_SERVER['HTTP_REFERER']);
    }


}
