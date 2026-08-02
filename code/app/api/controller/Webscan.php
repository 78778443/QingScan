<?php

namespace app\api\controller;

use app\api\BaseController;
use think\facade\Db;

class Webscan extends BaseController
{
    /**
     * 目标（app）列表
     * 筛选：keyword（name/url like）、statuscode、cms、server（app_info）、status（app.status）
     * 每行返回 app 全字段 + app_info 的 statuscode/cms/server + is_waf + 各工具计数
     */
    public function app_list()
    {
        $query = Db::table('app')
            ->leftJoin('app_info info', 'app.id = info.app_id')
            ->group('app.id');

        $keyword = $this->request->param('keyword');
        if (!empty($keyword)) {
            $query->where('app.name|app.url', 'like', "%{$keyword}%");
        }
        $statuscode = $this->request->param('statuscode');
        if ($statuscode !== null && $statuscode !== '') {
            $query->where('info.statuscode', '=', $statuscode);
        }
        $cms = $this->request->param('cms');
        if (!empty($cms)) {
            $query->where('info.cms', '=', $cms);
        }
        $server = $this->request->param('server');
        if (!empty($server)) {
            $query->where('info.server', '=', $server);
        }
        $status = $this->request->param('status');
        if ($status !== null && $status !== '') {
            $query->where('app.status', '=', $status);
        }
        $query->order('app.id', 'desc');

        return $this->paginateJson($query, 20, function ($items) {
            $appIds = array_values(array_unique(array_filter(array_column($items, 'id'))));
            $counts = [];
            if ($appIds) {
                // 各扫描结果计数：每个表只查一次，group by app_id
                $tables = [
                    'web_vuln' => ['scan_vuln', ['source' => 'web_vuln']],
                    'sql_inject' => ['urls_sqlmap', []],
                    'vul_verify' => ['scan_vuln', ['source' => 'vul_verify']],
                    'gen_vuln' => ['scan_vuln', ['source' => 'gen_vuln']],
                    'dir_scan' => ['app_dirmap', []],
                    'finger' => ['app_whatweb', []],
                    'subdomain' => ['scan_subdomain', []],
                    'urls' => ['asm_urls', []],
                    'host' => ['asm_host', []],
                ];
                foreach ($tables as $key => [$table, $extra]) {
                    $query = Db::table($table)->whereIn('app_id', $appIds);
                    foreach ($extra as $k => $v) {
                        $query->where($k, $v);
                    }
                    $rows = $query->field('app_id,count(*) as num')->group('app_id')->select()->toArray();
                    $counts[$key] = array_column($rows, 'num', 'app_id');
                }
            }
            // app_info（一个 app 可能多行，取第一条）
            $infoMap = [];
            if ($appIds) {
                $infoRows = Db::table('app_info')->whereIn('app_id', $appIds)
                    ->field('app_id,statuscode,cms,server')->select()->toArray();
                foreach ($infoRows as $r) {
                    if (!isset($infoMap[$r['app_id']])) {
                        $infoMap[$r['app_id']] = $r;
                    }
                }
            }
            // waf 检测
            $wafMap = [];
            if ($appIds) {
                $wafRows = Db::table('app_wafw00f')->whereIn('app_id', $appIds)
                    ->field('app_id,detected')->select()->toArray();
                foreach ($wafRows as $r) {
                    if (!empty($r['detected'])) {
                        $wafMap[$r['app_id']] = true;
                    }
                }
            }

            foreach ($items as &$row) {
                $id = $row['id'];
                $row['statuscode'] = $infoMap[$id]['statuscode'] ?? '';
                $row['cms'] = $infoMap[$id]['cms'] ?? '';
                $row['server'] = $infoMap[$id]['server'] ?? '';
                $row['is_waf'] = !empty($wafMap[$id]) ? '是' : '否';
                $row['web_vuln_num'] = $counts['web_vuln'][$id] ?? 0;
                $row['sql_inject_num'] = $counts['sql_inject'][$id] ?? 0;
                $row['vul_verify_num'] = $counts['vul_verify'][$id] ?? 0;
                $row['gen_vuln_num'] = $counts['gen_vuln'][$id] ?? 0;
                $row['dir_scan_num'] = $counts['dir_scan'][$id] ?? 0;
                $row['finger_num'] = $counts['finger'][$id] ?? 0;
                $row['subdomain_num'] = $counts['subdomain'][$id] ?? 0;
                $row['urls_num'] = $counts['urls'][$id] ?? 0;
                $row['host_num'] = $counts['host'][$id] ?? 0;
            }
            return $items;
        });
    }

    /**
     * 添加目标
     * POST {urls: '多行文本', tools: ['工具名'...]}
     */
    public function app_add()
    {
        $urls = (string)$this->request->param('urls', '');
        $tools = $this->request->param('tools', []);

        if (empty($urls)) {
            return $this->apiReturn(0, [], '请输入目标URL');
        }
        $urlArr = $this->processUrls($urls);
        if (empty($urlArr)) {
            return $this->apiReturn(0, [], 'URL格式不正确');
        }
        foreach ($urlArr as $url) {
            $this->addTarget($url, $tools);
        }
        return $this->apiReturn(1, [], '添加成功');
    }

    /**
     * 删除目标（级联删除各结果表，复用 webscan/Index::del 逻辑）
     * POST {id}
     */
    public function app_del()
    {
        $id = (int)$this->request->param('id', 0);
        if (!$id) {
            return $this->apiReturn(0, [], '参数错误');
        }
        $info = Db::name('app')->where('id', $id)->find();
        if ($info) {
            $urlInfo = parse_url($info['url'] ?? 'http://127.0.0.1');
            $ip = gethostbyname($urlInfo['host'] ?? '127.0.0.1');
            Db::table('app_info')->where(['app_id' => $id])->delete();
            Db::table('asm_host')->where(['host' => $ip])->delete();
            Db::table('asm_host_port')->where(['host' => $ip])->delete();
        }
        $this->deleteAppResults(['app_id' => $id], ['id' => $id]);

        if (Db::name('app')->where('id', $id)->delete()) {
            return $this->apiReturn(1, [], '删除成功');
        }
        return $this->apiReturn(0, [], '删除失败');
    }

    /**
     * 批量删除目标
     * POST {ids: [1,2]}
     */
    public function app_batch_del()
    {
        $ids = $this->request->param('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }
        if (empty($ids)) {
            return $this->apiReturn(0, [], '请先选择要删除的数据');
        }
        $map = [['app_id', 'in', $ids]];

        $info = Db::name('app')->where('id', 'in', $ids)->find();
        if ($info) {
            $urlInfo = parse_url($info['url'] ?? 'http://127.0.0.1');
            $ip = gethostbyname($urlInfo['host'] ?? '127.0.0.1');
            Db::table('app_info')->where($map)->delete();
            Db::table('asm_host')->where(['host' => $ip])->delete();
            Db::table('asm_host_port')->where(['host' => $ip])->delete();
        }
        $this->deleteAppResults($map, [['id', 'in', $ids]]);

        if (Db::name('app')->where('id', 'in', $ids)->delete()) {
            return $this->apiReturn(1, [], '批量删除成功');
        }
        return $this->apiReturn(0, [], '批量删除失败');
    }

    /**
     * 启用/暂停扫描（现有代码：status 1=启用 2=暂停）
     * POST {id}
     */
    public function app_suspend()
    {
        $id = (int)$this->request->param('id', 0);
        if (!$id) {
            return $this->apiReturn(0, [], '参数错误');
        }
        $info = Db::name('app')->where('id', $id)->find();
        if (!$info) {
            return $this->apiReturn(0, [], '黑盒数据不存在');
        }
        $status = ($info['status'] == 1) ? 2 : 1;
        Db::name('app')->where('id', $id)->update(['status' => $status]);
        return $this->apiReturn(1, ['id' => $id, 'status' => $status], $status == 1 ? '扫描已启用' : '扫描已暂停');
    }

    /**
     * 单个工具重新扫描（复用 webscan/Index::rescan 逻辑）
     * POST {id, tools_name}
     */
    public function app_rescan()
    {
        $id = (int)$this->request->param('id', 0);
        if (!$id) {
            return $this->apiReturn(0, [], '参数错误');
        }
        $info = Db::name('app')->where('id', $id)->find();
        if (!$info) {
            return $this->apiReturn(0, [], '黑盒数据不存在');
        }
        $tools_name = (string)$this->request->param('tools_name', '');
        $data = [];

        switch ($tools_name) {
            case 'scan_app_web_vuln':
                $data = ['xray_scan_time' => '2000-01-01 00:00:00'];
                Db::table('scan_vuln')->where(['app_id' => $id, 'source' => 'web_vuln'])->delete();
                break;
            case 'scan_app_gen_vuln':
                $data = ['nuclei_scan_time' => '2000-01-01 00:00:00'];
                Db::table('scan_vuln')->where(['app_id' => $id, 'source' => 'gen_vuln'])->delete();
                break;
            case 'scan_app_vul_verify':
                $data = ['vulmap_scan_time' => '2000-01-01 00:00:00'];
                Db::table('scan_vuln')->where(['app_id' => $id, 'source' => 'vul_verify'])->delete();
                break;
            case 'scan_app_dir_scan':
                $data = ['dirmap_scan_time' => '2000-01-01 00:00:00'];
                Db::table('app_dirmap')->where(['app_id' => $id])->delete();
                break;
            case 'scan_app_finger':
                $data = ['whatweb_scan_time' => '2000-01-01 00:00:00'];
                Db::table('app_whatweb')->where(['app_id' => $id])->delete();
                Db::table('app_whatweb_poc')->where(['app_id' => $id])->delete();
                break;
            case 'scan_app_asset_finger':
                $data = ['dismap_scan_time' => '2000-01-01 00:00:00'];
                Db::table('app_dismap')->where(['app_id' => $id])->delete();
                break;
            case 'scan_app_crawler':
                $data = ['crawler_time' => '2000-01-01 00:00:00'];
                Db::table('asm_urls')->where(['app_id' => $id])->delete();
                Db::table('urls_sqlmap')->where(['app_id' => $id])->delete();
                break;
            case 'scan_app_spider':
                $data = ['crawlergo_scan_time' => '2000-01-01 00:00:00'];
                Db::table('app_crawlergo')->where(['app_id' => $id])->delete();
                break;
            case 'scan_app_web_info_extra':
                $data = ['screenshot_time' => '2000-01-01 00:00:00'];
                Db::table('app_info')->where(['app_id' => $id])->delete();
                break;
            case 'scan_url_sql_inject':
                Db::table('asm_urls')->where(['app_id' => $id])->update(['sqlmap_scan_time' => '2000-01-01 00:00:00']);
                Db::table('urls_sqlmap')->where(['app_id' => $id])->delete();
                break;
            case 'asm_domain_subdomain':
                $data = ['subdomain_scan_time' => '2000-01-01 00:00:00'];
                Db::table('scan_subdomain')->where(['app_id' => $id])->delete();
                break;
            case 'scan_ip_weak_pass':
                Db::table('asm_host')->where(['app_id' => $id])->update(['hydra_scan_time' => '2000-01-01 00:00:00']);
                Db::table('host_hydra_scan_details')->where(['app_id' => $id])->delete();
                break;
            case 'asm_ip_port_scan':
                Db::table('asm_host_port')->where(['app_id' => $id])->update(['service' => null]);
                break;
            case 'autoAddHost':
                Db::table('asm_host')->where(['app_id' => $id])->delete();
                Db::table('asm_host_port')->where(['app_id' => $id])->delete();
                Db::table('host_hydra_scan_details')->where(['app_id' => $id])->delete();
                break;
            case 'plugin':
                Db::table('plugin_scan_log')->where(['app_id' => $id])->delete();
                break;
            default:
                return $this->apiReturn(0, [], '参数错误');
        }
        Db::table('plugin_scan_log')->where(['app_id' => $id, 'scan_type' => 0, 'plugin_name' => $tools_name])->delete();
        if (!empty($data)) {
            Db::table('app')->where(['id' => $id])->update($data);
        }
        return $this->apiReturn(1, [], '重新扫描任务已下发');
    }

    /**
     * 清空目标结果（复用 webscan/Index::qingkong 逻辑）
     * POST {id}
     */
    public function app_qingkong()
    {
        $id = (int)$this->request->param('id', 0);
        if (!$id) {
            return $this->apiReturn(0, [], '参数错误');
        }
        $info = Db::name('app')->where('id', $id)->find();
        if (!$info) {
            return $this->apiReturn(0, [], '黑盒数据不存在');
        }
        $array = [
            'crawler_time' => '2000-01-01 00:00:00',
            'subdomain_time' => '2000-01-01 00:00:00',
            'whatweb_scan_time' => '2000-01-01 00:00:00',
            'subdomain_scan_time' => '2000-01-01 00:00:00',
            'screenshot_time' => '2000-01-01 00:00:00',
            'xray_scan_time' => '2000-01-01 00:00:00',
            'dirmap_scan_time' => '2000-01-01 00:00:00',
            'wafw00f_scan_time' => '2000-01-01 00:00:00',
            'nuclei_scan_time' => '2000-01-01 00:00:00',
            'dismap_scan_time' => '2000-01-01 00:00:00',
            'crawlergo_scan_time' => '2000-01-01 00:00:00',
            'vulmap_scan_time' => '2000-01-01 00:00:00',
        ];
        Db::table('app')->where(['id' => $id])->update($array);
        $this->deleteAppResults(['app_id' => $id], []);
        return $this->apiReturn(1, [], '清空成功');
    }

    /**
     * Web 漏洞列表（scan_vuln 统一漏洞表，source=web_vuln，原 xray）
     * 筛选：search（url/name like）、app_id、level/severity（low/medium/high/critical）、check_status
     */
    public function web_vuln_list()
    {
        $query = Db::table('scan_vuln')->where('source', '=', 'web_vuln');

        $search = $this->request->param('search');
        if (!empty($search)) {
            $query->where('url|name', 'like', "%{$search}%");
        }
        $app_id = $this->request->param('app_id');
        if (!empty($app_id)) {
            $query->where('app_id', '=', $app_id);
        }
        $level = $this->request->param('level');
        if ($level === null || $level === '') {
            // 兼容前端直接传 severity
            $level = $this->request->param('severity');
        }
        if ($level !== null && $level !== '' && $level != -1) {
            $severity = $this->parseSeverity($level);
            if ($severity !== null) {
                $query->where('severity', '=', $severity);
            }
        }
        $check_status = $this->request->param('check_status');
        if ($check_status !== null && $check_status !== '' && in_array((int)$check_status, [0, 1, 2])) {
            $query->where('check_status', '=', (int)$check_status);
        }
        $query->order('id', 'desc');

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
     * 通用漏洞列表（scan_vuln 统一漏洞表，source=gen_vuln，原 nuclei）
     * 筛选：search（url/name like）、app_id、level/severity（low/medium/high/critical）、check_status
     */
    public function gen_vuln_list()
    {
        $query = Db::table('scan_vuln')->where('source', '=', 'gen_vuln');

        $search = $this->request->param('search');
        if (!empty($search)) {
            $query->where('url|name', 'like', "%{$search}%");
        }
        $app_id = $this->request->param('app_id');
        if (!empty($app_id)) {
            $query->where('app_id', '=', $app_id);
        }
        $level = $this->request->param('level');
        if ($level === null || $level === '') {
            // 兼容前端直接传 severity
            $level = $this->request->param('severity');
        }
        if ($level !== null && $level !== '' && $level != -1) {
            $severity = $this->parseSeverity($level);
            if ($severity !== null) {
                $query->where('severity', '=', $severity);
            }
        }
        $check_status = $this->request->param('check_status');
        if ($check_status !== null && $check_status !== '' && in_array((int)$check_status, [0, 1, 2])) {
            $query->where('check_status', '=', (int)$check_status);
        }
        $query->order('id', 'desc');

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
     * SQL 注入结果列表（urls_sqlmap 表，原 sqlmap）
     * 筛选：search（type/title/payload like）、app_id；行内加 app_name、url
     */
    public function sql_inject_list()
    {
        $query = Db::table('urls_sqlmap');

        $search = $this->request->param('search');
        if (!empty($search)) {
            $query->where('type|title|payload', 'like', "%{$search}%");
        }
        $app_id = $this->request->param('app_id');
        if (!empty($app_id)) {
            $query->where('app_id', '=', $app_id);
        }
        $query->order('id', 'desc');

        return $this->paginateJson($query, 20, function ($items) {
            $appIds = array_values(array_unique(array_filter(array_column($items, 'app_id'))));
            $nameMap = [];
            if ($appIds) {
                $rows = Db::table('app')->whereIn('id', $appIds)->field('id,name')->select()->toArray();
                $nameMap = array_column($rows, 'name', 'id');
            }
            // url 补充：优先 urls 表，其次 asm_urls 表（复用 Sqlmap 控制器逻辑，批量查询避免 N+1）
            $urlsIds = array_values(array_unique(array_filter(array_column($items, 'urls_id'))));
            $urlMap = [];
            if ($urlsIds) {
                try {
                    $rows = Db::table('urls')->whereIn('id', $urlsIds)->field('id,url')->select()->toArray();
                    $urlMap = array_column($rows, 'url', 'id');
                } catch (\Throwable $e) {
                    $urlMap = [];
                }
                $missIds = array_diff($urlsIds, array_keys($urlMap));
                if ($missIds) {
                    try {
                        $rows = Db::table('asm_urls')->whereIn('id', $missIds)->field('id,url')->select()->toArray();
                        foreach ($rows as $r) {
                            $urlMap[$r['id']] = $r['url'];
                        }
                    } catch (\Throwable $e) {
                    }
                }
            }
            foreach ($items as &$row) {
                $row['app_name'] = $nameMap[$row['app_id']] ?? '';
                $row['url'] = $urlMap[$row['urls_id']] ?? '';
            }
            return $items;
        });
    }

    /**
     * 漏洞验证列表（scan_vuln 统一漏洞表，source=vul_verify，原 vulmap）
     * 筛选：search（url/name like）、app_id、level/severity（low/medium/high/critical）、check_status
     */
    public function vul_verify_list()
    {
        $query = Db::table('scan_vuln')->where('source', '=', 'vul_verify');

        $search = $this->request->param('search');
        if (!empty($search)) {
            $query->where('url|name', 'like', "%{$search}%");
        }
        $app_id = $this->request->param('app_id');
        if (!empty($app_id)) {
            $query->where('app_id', '=', $app_id);
        }
        $level = $this->request->param('level');
        if ($level === null || $level === '') {
            // 兼容前端直接传 severity
            $level = $this->request->param('severity');
        }
        if ($level !== null && $level !== '' && $level != -1) {
            $severity = $this->parseSeverity($level);
            if ($severity !== null) {
                $query->where('severity', '=', $severity);
            }
        }
        $check_status = $this->request->param('check_status');
        if ($check_status !== null && $check_status !== '' && in_array((int)$check_status, [0, 1, 2])) {
            $query->where('check_status', '=', (int)$check_status);
        }
        $query->order('id', 'desc');

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
     * 将前端 level/severity 参数统一映射为 scan_vuln.severity 取值
     * 兼容：low/medium/high/critical、Low/Medium/High/Critical、0-3 数字
     */
    private function parseSeverity($level)
    {
        $numMap = ['0' => 'low', '1' => 'medium', '2' => 'high', '3' => 'critical'];
        $key = strtolower((string)$level);
        if (isset($numMap[$key])) {
            return $numMap[$key];
        }
        if (in_array($key, ['low', 'medium', 'high', 'critical'])) {
            return $key;
        }
        return null;
    }

    /**
     * 目录扫描结果列表（app_dirmap 表，原 dirmap）
     * 筛选：search（url/type like）、app_id；行内加 app_name
     */
    public function dir_scan_list()
    {
        $query = Db::table('app_dirmap');

        $search = $this->request->param('search');
        if (!empty($search)) {
            $query->where('url|type', 'like', "%{$search}%");
        }
        $app_id = $this->request->param('app_id');
        if (!empty($app_id)) {
            $query->where('app_id', '=', $app_id);
        }
        $query->order('id', 'desc');

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
     * 指纹识别结果列表（app_whatweb 表，原 whatweb）
     * 筛选：search（target/plugins like）、app_id；行内加 app_name
     */
    public function finger_list()
    {
        $query = Db::table('app_whatweb');

        $search = $this->request->param('search');
        if (!empty($search)) {
            $query->where('target|plugins', 'like', "%{$search}%");
        }
        $app_id = $this->request->param('app_id');
        if (!empty($app_id)) {
            $query->where('app_id', '=', $app_id);
        }
        $query->order('id', 'desc');

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

    // ---------- 旧接口兼容别名（转发到新功能化命名方法，社区/老前端可继续调用） ----------

    public function xray_list()
    {
        return $this->web_vuln_list();
    }

    public function nuclei_list()
    {
        return $this->gen_vuln_list();
    }

    public function vulmap_list()
    {
        return $this->vul_verify_list();
    }

    public function sqlmap_list()
    {
        return $this->sql_inject_list();
    }

    public function dirmap_list()
    {
        return $this->dir_scan_list();
    }

    public function whatweb_list()
    {
        return $this->finger_list();
    }

    // ---------- 私有辅助方法 ----------

    /**
     * 解析多行 URL 文本（复用 webscan/Index::processUrls）
     */
    private function processUrls($inputString)
    {
        $lines = explode("\n", $inputString);
        $urls = [];
        foreach ($lines as $line) {
            $url = trim($line);
            if (empty($url)) {
                continue;
            }
            if (!preg_match('#^https?://#i', $url)) {
                $url = "http://{$url}/";
            }
            if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                continue;
            }
            $urls[] = $url;
        }
        return $urls;
    }

    /**
     * 添加目标并写入工具队列（复用 webscan/Index::addTarget）
     */
    private function addTarget($url, $tools)
    {
        $host = parse_url($url)['host'] ?? '';
        $data = ['url' => $url, 'name' => $host];
        $project_id = Db::name('app')->insertGetId($data);

        // 写入到关键词监控表中
        if (empty($host)) {
            return true;
        }
        $data = ['user_id' => 1, 'app_id' => $project_id, 'title' => $host];
        Db::name('github_keyword_monitor')->insert($data);

        // 写入到要执行的工具表中
        if (empty($tools)) {
            return true;
        }
        $project_tools_data = [];
        foreach ($tools as $k => $v) {
            $project_tools_data[] = ['type' => 1, 'project_id' => $project_id, 'tools_name' => $v];
        }
        Db::name('project_tools')->where('project_id', $project_id)->where('type', 1)->delete();
        Db::name('project_tools')->insertAll($project_tools_data);
        return true;
    }

    /**
     * 级联删除目标的所有结果表（复用 webscan/Index 删除逻辑）
     * @param array $where 按 app_id 条件删除（app_id 等于或 in）
     * @param array $appWhere app 表自身条件（可能为空）
     */
    private function deleteAppResults(array $where, array $appWhere)
    {
        Db::table('app_info')->where($where)->delete();
        Db::table('app_crawlergo')->where($where)->delete();
        Db::table('app_dirmap')->where($where)->delete();
        Db::table('app_dismap')->where($where)->delete();
        Db::table('scan_vuln')->where($where)->where('source', 'gen_vuln')->delete();
        Db::table('scan_vuln')->where($where)->where('source', 'vul_verify')->delete();
        Db::table('app_wafw00f')->where($where)->delete();
        Db::table('app_whatweb')->where($where)->delete();
        Db::table('app_whatweb_poc')->where($where)->delete();
        Db::table('app_xray_agent_port')->where($where)->delete();
        Db::table('host_hydra_scan_details')->where($where)->delete();
        Db::table('scan_subdomain')->where($where)->delete();
        Db::table('plugin_scan_log')->where($where)->delete();
        Db::table('asm_urls')->where($where)->delete();
        Db::table('urls_sqlmap')->where($where)->delete();
        Db::table('scan_vuln')->where($where)->where('source', 'web_vuln')->delete();
        if ($appWhere) {
            Db::table('github_keyword_monitor')->where($appWhere)->delete();
            Db::table('github_keyword_monitor_notice')->where($appWhere)->delete();
        }
    }
}
