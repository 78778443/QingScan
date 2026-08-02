<?php

namespace app\api\controller;

use app\api\BaseController;
use think\facade\Db;

class Task extends BaseController
{
    /**
     * 扫描任务列表（task_scan 表）
     * 筛选：tool（精确）、status（0/1）
     * 行内 target_table/target 由 tool + ext_info 派生（表无此列，复用 taskscan 视图逻辑），
     * ext_info、create_time、update_time 原样返回
     */
    public function list()
    {
        $query = Db::table('task_scan');

        $tool = $this->request->param('tool');
        if (!empty($tool)) {
            $query->where('tool', '=', $tool);
        }
        $status = $this->request->param('status');
        if ($status !== null && $status !== '') {
            $query->where('status', '=', (int)$status);
        }
        $query->order('id', 'desc');

        return $this->paginateJson($query, 20, function ($items) {
            foreach ($items as &$row) {
                $extInfo = json_decode($row['ext_info'] ?? '', true);
                $targetTable = '';
                if (strpos((string)$row['tool'], 'asm_') === 0) {
                    if (strpos((string)$row['tool'], 'domain_') !== false) {
                        $targetTable = 'asm_domain';
                    } elseif (strpos((string)$row['tool'], 'ip_') !== false) {
                        $targetTable = 'asm_ip';
                    } elseif (strpos((string)$row['tool'], 'urls') !== false) {
                        $targetTable = 'asm_urls';
                    } elseif ($row['tool'] == 'asm_discover') {
                        $targetTable = 'asm_discover';
                    }
                } elseif (strpos((string)$row['tool'], 'scan_app_') === 0) {
                    $targetTable = 'app';
                } elseif (strpos((string)$row['tool'], 'code_') === 0) {
                    $targetTable = 'code';
                }
                $row['target_table'] = $targetTable;
                $row['target'] = isset($extInfo['id']) ? $extInfo['id'] : '';
            }
            return $items;
        });
    }
}
