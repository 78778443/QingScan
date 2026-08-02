<?php

namespace app\api\controller;

use app\api\BaseController;
use think\facade\Db;

class Code extends BaseController
{
    /**
     * 代码项目列表
     * 筛选：keyword（name like）、page/limit
     * 行返回：id/name/ssh_url/desc/semgrep_scan_time/create_time/user_id + audit_num（该项目审计结果数）
     */
    public function project_list()
    {
        $query = Db::table('code')
            ->where('is_delete', 0)
            ->field('id,name,ssh_url,desc,semgrep_scan_time,create_time,user_id');

        $keyword = $this->request->param('keyword');
        if (!empty($keyword)) {
            $query->where('name', 'like', "%{$keyword}%");
        }
        $query->order('id', 'desc');

        return $this->paginateJson($query, (int)$this->request->param('limit', 20), function ($items) {
            $codeIds  = array_column($items, 'id');
            $auditMap = [];
            if ($codeIds) {
                $rows = Db::table('scan_code_audit')
                    ->whereIn('code_id', $codeIds)
                    ->where('is_delete', 0)
                    ->field('code_id, COUNT(*) AS num')
                    ->group('code_id')
                    ->select()
                    ->toArray();
                $auditMap = array_column($rows, 'num', 'code_id');
            }
            foreach ($items as &$row) {
                $row['audit_num'] = isset($auditMap[$row['id']]) ? (int)$auditMap[$row['id']] : 0;
            }
            return $items;
        });
    }

    /**
     * 添加代码项目
     * POST {name, ssh_url, desc}
     */
    public function project_add()
    {
        $name = $this->request->param('name');
        if (empty($name)) {
            return $this->apiReturn(0, [], '项目名称不能为空');
        }
        $id = Db::table('code')->insertGetId([
            'name'        => $name,
            'ssh_url'     => $this->request->param('ssh_url', ''),
            'desc'        => $this->request->param('desc', ''),
            'user_id'     => 1,
            'is_delete'   => 0,
            'create_time' => date('Y-m-d H:i:s'),
        ]);
        return $this->apiReturn(1, ['id' => $id], '添加成功');
    }

    /**
     * 删除代码项目（code 软删 is_delete=1，scan_code_audit 级联真删）
     * POST {id}
     */
    public function project_del()
    {
        $id = (int)$this->request->param('id');
        if (empty($id)) {
            return $this->apiReturn(0, [], '参数错误');
        }
        Db::table('code')->where('id', $id)->update(['is_delete' => 1]);
        Db::table('scan_code_audit')->where('code_id', $id)->delete();
        return $this->apiReturn(1, [], '删除成功');
    }

    /**
     * 代码审计结果列表
     * 筛选：code_id、keyword（message/rule_id like）、severity、page/limit
     * 行返回：id/code_id/file/line/rule_id/message/severity/create_time + project_name
     */
    public function audit_list()
    {
        $query = Db::table('scan_code_audit')
            ->where('is_delete', 0)
            ->field('id,code_id,file,line,rule_id,message,severity,create_time');

        $code_id = $this->request->param('code_id');
        if (!empty($code_id)) {
            $query->where('code_id', '=', (int)$code_id);
        }
        $keyword = $this->request->param('keyword');
        if (!empty($keyword)) {
            $query->where('message|rule_id', 'like', "%{$keyword}%");
        }
        $severity = $this->request->param('severity');
        if (!empty($severity)) {
            $query->where('severity', '=', $severity);
        }
        $query->order('id', 'desc');

        return $this->paginateJson($query, (int)$this->request->param('limit', 20), function ($items) {
            $codeIds = array_unique(array_filter(array_column($items, 'code_id')));
            $nameMap = [];
            if ($codeIds) {
                $rows = Db::table('code')
                    ->whereIn('id', $codeIds)
                    ->field('id,name')
                    ->select()
                    ->toArray();
                $nameMap = array_column($rows, 'name', 'id');
            }
            foreach ($items as &$row) {
                $row['project_name'] = $nameMap[$row['code_id']] ?? '';
            }
            return $items;
        });
    }

    /**
     * 审计结果详情
     * GET {id}
     */
    public function audit_detail()
    {
        $id = (int)$this->request->param('id');
        if (empty($id)) {
            return $this->apiReturn(0, [], '参数错误');
        }
        $row = Db::table('scan_code_audit')->where('id', $id)->find();
        if (!$row) {
            return $this->apiReturn(0, [], '记录不存在');
        }
        return $this->apiReturn(1, $row);
    }
}
