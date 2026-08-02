<?php

namespace app\api\controller;

use app\api\BaseController;
use think\facade\Db;

class Result extends BaseController
{
    /**
     * 插件扫描日志列表（plugin_scan_log 联 plugin）
     * 筛选：search（content/插件名 like）、plugin_id、app_id；行内加 app_name
     */
    public function plugin_list()
    {
        $query = Db::table('plugin_scan_log')->alias('a')
            ->field('a.*');

        $search = $this->request->param('search');
        if (!empty($search)) {
            $query->where('a.content|a.plugin_name', 'like', "%{$search}%");
        }
        $plugin_id = $this->request->param('plugin_id');
        if (!empty($plugin_id)) {
            $query->where('a.plugin_id', '=', $plugin_id);
        }
        $app_id = $this->request->param('app_id');
        if (!empty($app_id)) {
            $query->where('a.app_id', '=', $app_id);
        }
        $query->order('a.id', 'desc');

        return $this->paginateJson($query, 20, function ($items) {
            $appIds = array_values(array_unique(array_filter(array_column($items, 'app_id'))));
            $nameMap = [];
            if ($appIds) {
                $rows = Db::table('app')->whereIn('id', $appIds)->field('id,name')->select()->toArray();
                $nameMap = array_column($rows, 'name', 'id');
            }
            foreach ($items as &$row) {
                $row['app_name'] = $nameMap[$row['app_id']] ?? '';
            }
            return $items;
        });
    }

    /**
     * 漏洞信息库列表（vulnerable 表）
     * 筛选：search（name/cve_num/cnvd_num like）、vul_level、product_field、check_status、product_type、product_cate
     */
    public function vulnerable_list()
    {
        $query = Db::table('vulnerable');

        $search = $this->request->param('search', '');
        if (!empty($search)) {
            $query->where('name|cve_num|cnvd_num', 'like', "%{$search}%");
        }
        $vul_level = $this->request->param('vul_level');
        if (!empty($vul_level)) {
            $query->where('vul_level', '=', $vul_level);
        }
        $product_field = $this->request->param('product_field');
        if (!empty($product_field)) {
            $query->where('product_field', '=', $product_field);
        }
        $product_type = $this->request->param('product_type');
        if (!empty($product_type)) {
            $query->where('product_type', '=', $product_type);
        }
        $product_cate = $this->request->param('product_cate');
        if (!empty($product_cate)) {
            $query->where('product_cate', '=', $product_cate);
        }
        $check_status = $this->request->param('check_status');
        // 部分库版本 vulnerable 表无 check_status 列，先探测再过滤
        static $hasCheckStatus = null;
        if ($hasCheckStatus === null) {
            $hasCheckStatus = !empty(Db::query("SHOW COLUMNS FROM vulnerable LIKE 'check_status'"));
        }
        if ($check_status !== null && $check_status !== '' && $hasCheckStatus && in_array((int)$check_status, [0, 1, 2])) {
            $query->where('check_status', '=', (int)$check_status);
        }
        $query->order('id', 'desc');

        return $this->paginateJson($query);
    }
}
