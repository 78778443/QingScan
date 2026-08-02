<?php

namespace app\api\controller;

use app\api\BaseController;
use think\facade\Db;

class Workorder extends BaseController
{
    /** 工单状态枚举 */
    const STATUS_LIST = ['pending_dispatch', 'dispatched', 'confirmed', 'fixed_unconfirmed', 'fixed_confirmed'];

    /** 工单类型枚举 */
    const TYPE_LIST = ['vulnerability', 'system', 'other'];

    /**
     * 工单列表
     * 筛选：status、type、keyword（title like）、page/limit
     * 行返回全字段 + vul_name（关联漏洞名）+ created_by_name（创建人用户名）
     */
    public function list()
    {
        $query = Db::table('asm_work_order');

        $status = $this->request->param('status');
        if (!empty($status)) {
            $query->where('status', '=', $status);
        }
        $type = $this->request->param('type');
        if (!empty($type)) {
            $query->where('type', '=', $type);
        }
        $keyword = $this->request->param('keyword');
        if (!empty($keyword)) {
            $query->where('title', 'like', "%{$keyword}%");
        }
        $query->order('id', 'desc');

        return $this->paginateJson($query, (int)$this->request->param('limit', 20), function ($items) {
            $vulIds = array_unique(array_filter(array_column($items, 'vul_id')));
            $vulMap = [];
            if ($vulIds) {
                $rows = Db::table('scan_vuln')
                    ->whereIn('id', $vulIds)
                    ->field('id,name')
                    ->select()
                    ->toArray();
                $vulMap = array_column($rows, 'name', 'id');
            }
            $userIds = array_unique(array_filter(array_column($items, 'created_by')));
            $userMap = [];
            if ($userIds) {
                $rows = Db::table('user')
                    ->whereIn('id', $userIds)
                    ->field('id,username')
                    ->select()
                    ->toArray();
                $userMap = array_column($rows, 'username', 'id');
            }
            foreach ($items as &$row) {
                $row['vul_name']        = $vulMap[$row['vul_id']] ?? '';
                $row['created_by_name'] = $userMap[$row['created_by']] ?? '';
            }
            return $items;
        });
    }

    /**
     * 新增工单
     * POST {title, type, content, vul_id?}
     * 带 vul_id 时：自动取 scan_vuln.name 作标题（未传 title 时）、url 补充进 content，vul_type='scan_vuln'
     */
    public function add()
    {
        $title   = $this->request->param('title');
        $type    = $this->request->param('type', 'vulnerability');
        $content = $this->request->param('content', '');
        $vul_id  = $this->request->param('vul_id');

        if (!in_array($type, self::TYPE_LIST, true)) {
            return $this->apiReturn(0, [], '工单类型不合法');
        }
        if (empty($title) && empty($vul_id)) {
            return $this->apiReturn(0, [], '标题不能为空');
        }

        $vul_type = '';
        if (!empty($vul_id)) {
            $vul = Db::table('scan_vuln')
                ->where('id', (int)$vul_id)
                ->where('is_delete', 0)
                ->find();
            if (!$vul) {
                return $this->apiReturn(0, [], '关联漏洞不存在');
            }
            $vul_type = 'scan_vuln';
            if (empty($title)) {
                $title = $vul['name'];
            }
            if (!empty($vul['url'])) {
                $content = trim(($content ?? '') . "\n" . $vul['url']);
            }
        }

        $id = Db::table('asm_work_order')->insertGetId([
            'title'      => $title,
            'type'       => $type,
            'content'    => $content ?? '',
            'status'     => 'pending_dispatch',
            'vul_id'     => !empty($vul_id) ? (int)$vul_id : null,
            'vul_type'   => $vul_type ?: null,
            'created_by' => 1,
        ]);
        return $this->apiReturn(1, ['id' => $id], '创建成功');
    }

    /**
     * 更新工单（非空字段才更新）
     * POST {id, title?, type?, content?, status?, assigned_to?, security_owner?, business_owner?, confirmer?}
     */
    public function update()
    {
        $id = (int)$this->request->param('id');
        if (empty($id)) {
            return $this->apiReturn(0, [], '参数错误');
        }
        $fields = ['title', 'type', 'content', 'status', 'assigned_to', 'security_owner', 'business_owner', 'confirmer'];
        $data   = [];
        foreach ($fields as $field) {
            $value = $this->request->param($field);
            if ($value !== null && $value !== '') {
                $data[$field] = $value;
            }
        }
        if (isset($data['status']) && !in_array($data['status'], self::STATUS_LIST, true)) {
            return $this->apiReturn(0, [], '状态不合法');
        }
        if (isset($data['type']) && !in_array($data['type'], self::TYPE_LIST, true)) {
            return $this->apiReturn(0, [], '工单类型不合法');
        }
        if (empty($data)) {
            return $this->apiReturn(0, [], '没有需要更新的字段');
        }
        Db::table('asm_work_order')->where('id', $id)->update($data);
        return $this->apiReturn(1, [], '更新成功');
    }

    /**
     * 工单状态流转
     * POST {id, status}，status 必须在校验枚举内
     */
    public function status()
    {
        $id     = (int)$this->request->param('id');
        $status = $this->request->param('status');
        if (empty($id)) {
            return $this->apiReturn(0, [], '参数错误');
        }
        if (!in_array($status, self::STATUS_LIST, true)) {
            return $this->apiReturn(0, [], '状态不合法');
        }
        $row = Db::table('asm_work_order')->where('id', $id)->find();
        if (!$row) {
            return $this->apiReturn(0, [], '工单不存在');
        }
        Db::table('asm_work_order')->where('id', $id)->update(['status' => $status]);
        return $this->apiReturn(1, [], '状态更新成功');
    }

    /**
     * 删除工单
     * POST {id}
     */
    public function del()
    {
        $id = (int)$this->request->param('id');
        if (empty($id)) {
            return $this->apiReturn(0, [], '参数错误');
        }
        Db::table('asm_work_order')->where('id', $id)->delete();
        return $this->apiReturn(1, [], '删除成功');
    }
}
