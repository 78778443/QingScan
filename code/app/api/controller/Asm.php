<?php

namespace app\api\controller;

use app\api\BaseController;
use think\facade\Db;

class Asm extends BaseController
{
    /**
     * 主机列表
     * 筛选：keyword（host/domain like）、app_id
     */
    public function host_list()
    {
        $query = Db::table('asm_host');

        $keyword = $this->request->param('keyword');
        if (!empty($keyword)) {
            $query->where('host|domain', 'like', "%{$keyword}%");
        }
        $app_id = $this->request->param('app_id');
        if (!empty($app_id)) {
            $query->where('app_id', '=', $app_id);
        }
        $query->order('id', 'desc');

        return $this->paginateJson($query);
    }

    /**
     * 端口列表
     * 筛选：keyword（host like）、port、service
     */
    public function port_list()
    {
        $query = Db::table('asm_host_port');

        $keyword = $this->request->param('keyword');
        if (!empty($keyword)) {
            $query->where('host', 'like', "%{$keyword}%");
        }
        $port = $this->request->param('port');
        if ($port !== null && $port !== '') {
            $query->where('port', '=', $port);
        }
        $service = $this->request->param('service');
        if (!empty($service)) {
            $query->where('service', '=', $service);
        }
        $query->order('id', 'desc');

        return $this->paginateJson($query);
    }

    /**
     * 域名列表（asm_domain 表仅 id/domain 两列，无 app_id）
     * 筛选：keyword（domain like）
     */
    public function domain_list()
    {
        $query = Db::table('asm_domain');

        $keyword = $this->request->param('keyword');
        if (!empty($keyword)) {
            $query->where('domain', 'like', "%{$keyword}%");
        }
        $query->order('id', 'desc');

        return $this->paginateJson($query);
    }

    /**
     * 子域名列表（one_for_all 表）
     * 筛选：keyword（subdomain/ip like）、app_id
     */
    public function subdomain_list()
    {
        $query = Db::table('one_for_all');

        $keyword = $this->request->param('keyword');
        if (!empty($keyword)) {
            $query->where('subdomain|ip', 'like', "%{$keyword}%");
        }
        $app_id = $this->request->param('app_id');
        if (!empty($app_id)) {
            $query->where('app_id', '=', $app_id);
        }
        $query->order('id', 'desc');

        return $this->paginateJson($query);
    }

    /**
     * URL 列表（asm_urls 表）
     * 筛选：keyword（url like）、app_id；行内加 status_code（表字段为 status）
     */
    public function url_list()
    {
        $query = Db::table('asm_urls');

        $keyword = $this->request->param('keyword');
        if (!empty($keyword)) {
            $query->where('url', 'like', "%{$keyword}%");
        }
        $app_id = $this->request->param('app_id');
        if (!empty($app_id)) {
            $query->where('app_id', '=', $app_id);
        }
        $query->order('id', 'desc');

        return $this->paginateJson($query, 20, function ($items) {
            foreach ($items as &$row) {
                $row['status_code'] = $row['status'] ?? '';
            }
            return $items;
        });
    }
}
