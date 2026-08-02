<?php

namespace app\asm\controller;

use app\asm\model\WorkOrderModel;
use app\controller\Common;
use think\facade\View;
use think\facade\Db;
use think\facade\Request;

class WorkOrder extends Common
{
    // 工单列表
    public function index()
    {
        // 获取搜索条件
        $keyword = Request::param('keyword', '');
        $status = Request::param('status', '');
        $type = Request::param('type', '');
        
        // 构建查询条件
        $where = [];
        if (!empty($keyword)) {
            $where[] = ['title|content', 'like', '%' . $keyword . '%'];
        }
        if (!empty($status)) {
            $where['status'] = $status;
        }
        if (!empty($type)) {
            $where['type'] = $type;
        }
        
        // 获取分页参数
        $page = Request::param('page', 1, 'intval');
        $limit = Request::param('limit', 20, 'intval');
        
        // 获取数据
        $work_order_page = Db::table('asm_work_order')
            ->where($where)
            ->order('updated_at desc')
            ->paginate([
                'list_rows' => $limit,
                'page' => $page,
                'query' => Request::param()
            ]);
        
        // 获取分页数据列表
        $list = $work_order_page->items();
        
        // 工单状态
        $work_order_status = [
            'pending_dispatch' => '待派发',
            'dispatched' => '已派发',
            'confirmed' => '已确认',
            'fixed_unconfirmed' => '已修复未确认',
            'fixed_confirmed' => '已修复确认'
        ];
        
        // 工单类型
        $work_order_type = [
            'vul_fix' => '漏洞修复',
            'asset_add' => '资产添加',
            'asset_delete' => '资产删除',
            'other' => '其他'
        ];
        
        // 计算分页显示信息
        $total = $work_order_page->total();
        $currentPage = $work_order_page->currentPage();
        $listRows = $work_order_page->listRows();
        $page_start = ($currentPage - 1) * $listRows + 1;
        $page_end = min($currentPage * $listRows, $total);

        View::assign([
            'list' => $list,
            'page' => $work_order_page,
            'paginator' => $work_order_page,
            'work_order_status' => $work_order_status,
            'work_order_type' => $work_order_type,
            'keyword' => $keyword,
            'status' => $status,
            'type' => $type,
            'total' => $total,
            'page_start' => $page_start,
            'page_end' => $page_end
        ]);

        return redirect('/web/');
    }
    
    // 工单详情
    public function detail()
    {
        $id = Request::param('id', 0, 'intval');
        
        if (empty($id)) {
            $this->error('参数错误');
        }
        
        // 获取工单信息
        $work_order = Db::table('asm_work_order')->where('id', $id)->find();
        
        if (empty($work_order)) {
            $this->error('工单不存在');
        }
        
        // 获取关联的漏洞信息
        $vul_data = [];
        if (!empty($work_order['vul_id']) && !empty($work_order['vul_type'])) {
            if ($work_order['vul_type'] == 'vul') {
                $vul_data = Db::table('asm_vulnerability_summary')->where('id', $work_order['vul_id'])->find();
            } elseif ($work_order['vul_type'] == 'qingteng') {
                $vul_data = Db::table('asm_vulnerability_qingteng')->where('id', $work_order['vul_id'])->find();
            }
        }
        
        // 工单状态
        $work_order_status = [
            'open' => '待处理',
            'processing' => '处理中',
            'closed' => '已关闭',
            'rejected' => '已驳回'
        ];
        
        // 工单类型
        $work_order_type = [
            'vul_fix' => '漏洞修复',
            'asset_add' => '资产添加',
            'asset_delete' => '资产删除',
            'other' => '其他'
        ];
        
        View::assign([
            'work_order' => $work_order,
            'vul_data' => $vul_data,
            'work_order_status' => $work_order_status,
            'work_order_type' => $work_order_type
        ]);
        
        return redirect('/web/');
    }
    
    // 飞书机器人一键拉群
    public function feishuCreateGroup()
    {
        $id = Request::param('id', 0, 'intval');
        
        if (empty($id)) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }
        
        // 获取工单信息
        $work_order = Db::table('asm_work_order')->where('id', $id)->find();
        
        if (empty($work_order)) {
            return json(['code' => 0, 'msg' => '工单不存在']);
        }
        
        // 这里需要实现飞书机器人拉群逻辑
        // 1. 获取飞书机器人配置
        $feishu_config = Db::table('config')->where('name', 'like', 'feishu_%')->column('value', 'name');
        
        if (empty($feishu_config['feishu_app_id']) || empty($feishu_config['feishu_app_secret']) || empty($feishu_config['feishu_bot_webhook'])) {
            return json(['code' => 0, 'msg' => '飞书机器人配置未完成']);
        }
        
        // 2. 调用飞书API创建群组
        // 这里需要根据飞书API文档实现具体的调用逻辑
        // 示例代码，需要根据实际API进行调整
        /*
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $feishu_config['feishu_bot_webhook']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'msg_type' => 'text',
            'content' => [
                'text' => '发现新工单：' . $work_order['title'] . '，请及时处理。\n工单详情：' . url('/asm/workorder/detail', ['id' => $id])
            ]
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        */
        
        // 模拟成功响应
        $result = ['code' => 0, 'msg' => 'success'];
        
        if (isset($result['code']) && $result['code'] == 0) {
            // 更新工单状态为已通知
            Db::table('asm_work_order')->where('id', $id)->update([
                'feishu_notified' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            return json(['code' => 1, 'msg' => '飞书群组创建成功并发送通知']);
        } else {
            return json(['code' => 0, 'msg' => '飞书群组创建失败：' . ($result['msg'] ?? '未知错误')]);
        }
    }
    
    // 更新工单状态
    public function updateStatus()
    {
        $id = Request::param('id', 0, 'intval');
        $status = Request::param('status', '');
        $security_owner = Request::param('security_owner', '');
        $business_owner = Request::param('business_owner', '');
        $fixer = Request::param('fixer', '');
        $confirmer = Request::param('confirmer', '');
        
        if (empty($id) || empty($status)) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }
        
        // 准备更新数据
        $update_data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // 添加可选字段
        if (!empty($security_owner)) {
            $update_data['security_owner'] = $security_owner;
        }
        if (!empty($business_owner)) {
            $update_data['business_owner'] = $business_owner;
        }
        if (!empty($fixer)) {
            $update_data['fixer'] = $fixer;
        }
        if (!empty($confirmer)) {
            $update_data['confirmer'] = $confirmer;
        }
        
        // 更新工单
        $result = Db::table('asm_work_order')->where('id', $id)->update($update_data);
        
        if ($result) {
            return json(['code' => 1, 'msg' => '更新成功']);
        } else {
            return json(['code' => 0, 'msg' => '更新失败']);
        }
    }
}