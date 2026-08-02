<?php

namespace app\asm\controller;

use app\asm\model\HostAssetsModel;
use app\controller\Common;
use think\facade\View;
use think\facade\Db;
use think\facade\Request;

class HostAssets extends Common
{
    // 主机汇总列表
    public function index()
    {
        // 获取搜索条件
        $keyword = Request::param('keyword', '');
        $cloud_platform = Request::param('cloud_platform', '');
        $status = Request::param('status', '');
        
        // 构建查询条件
        $where = [];
        if (!empty($keyword)) {
            $where[] = ['instance_name|display_name|private_ip|public_ip', 'like', '%' . $keyword . '%'];
        }
        if (!empty($cloud_platform)) {
            $where['cloud_platform'] = $cloud_platform;
        }
        if (!empty($status)) {
            $where['status'] = $status;
        }
        
        // 获取分页参数
        $page = Request::param('page', 1, 'intval');
        $limit = Request::param('limit', 20, 'intval');
        
        // 获取数据
        $host_page = Db::table('asm_host_assets')
            ->where($where)
            ->order('create_time desc')
            ->paginate([
                'list_rows' => $limit,
                'page' => $page,
                'query' => Request::param()
            ]);
        
        // 获取分页数据列表
        $list = $host_page->items();
        
        // 平台类型
        $platforms = [
            'idc' => '线下IDC'
        ];
        
        // 实例状态
        $instance_status = [
            'RUNNING' => '运行中',
            'Running' => '运行中',  // 百度云返回的是首字母大写的状态
            'STOPPED' => '已停止',
            'Stopped' => '已停止',  // 百度云返回的是首字母大写的状态
            'TERMINATED' => '已终止',
            'Terminated' => '已终止',  // 百度云返回的是首字母大写的状态
            'CREATING' => '创建中',
            'Creating' => '创建中',  // 百度云返回的是首字母大写的状态
            'STARTING' => '启动中',
            'Starting' => '启动中',  // 百度云返回的是首字母大写的状态
            'STOPPING' => '停止中',
            'Stopping' => '停止中',  // 百度云返回的是首字母大写的状态
            'REBOOTING' => '重启中',
            'Rebooting' => '重启中',  // 百度云返回的是首字母大写的状态
            'SHUTOFF' => '已关闭'
        ];
        
        View::assign([
            'list' => $list,
            'page' => $host_page,
            'paginator' => $host_page,
            'total' => $host_page->total(),
            'running_count' => Db::table('asm_host_assets')->where('status', 'in', ['RUNNING', 'Running', 'running'])->count(),
            'stopped_count' => Db::table('asm_host_assets')->where('status', 'in', ['STOPPED', 'Stopped', 'stopped', 'SHUTOFF'])->count(),
            'platforms' => $platforms,
            'instance_status' => $instance_status,
            'keyword' => $keyword,
            'cloud_platform' => $cloud_platform,
            'status' => $status,
        ]);
        
        return redirect('/web/asm/host');
    }
    
    // HIDS列表

    public function import()
    {
        return redirect('/web/asm/host');
    }
    
    // 执行导入
    public function doImport()
    {
        // 这里可以实现从CSV或其他格式导入线下IDC数据的功能
        return json(['code' => 1, 'msg' => '导入功能开发中']);
    }
    
    // 查看主机资产详情
    public function detail()
    {
        $id = Request::param('id', '', 'intval');
        
        if (empty($id)) {
            $this->error('参数错误');
        }
        
        $host = HostAssetsModel::getHostAssetsById($id);
        
        if (empty($host)) {
            $this->error('主机资产不存在');
        }
        
        // 解析安全组数据
        if (!empty($host['security_groups'])) {
            $host['security_groups'] = json_decode($host['security_groups'], true);
        } else {
            $host['security_groups'] = [];
        }
        
        // 获取原始信息
        $original_data = [];

        View::assign([
            'host' => $host,
            'original_data' => $original_data
        ]);
        
        return redirect('/web/asm/host');
    }
    
    // 添加线下IDC主机
    public function addIdcHost()
    {
        return redirect('/web/asm/host');
    }
    
    // 保存线下IDC主机
    public function saveIdcHost()
    {
        $data = Request::post();
        
        // 验证数据
        if (empty($data['instance_name']) || empty($data['private_ip'])) {
            return json(['code' => 0, 'msg' => '实例名称和私有IP不能为空']);
        }
        
        // 构建数据
        $host_data = [
            'instance_id' => 'idc_' . uniqid(),
            'instance_name' => $data['instance_name'],
            'display_name' => $data['display_name'] ?: $data['instance_name'],
            'cloud_platform' => 'idc',
            'status' => 'running',
            'private_ip' => $data['private_ip'],
            'public_ip' => $data['public_ip'],
            'mac_address' => $data['mac_address'],
            'os_type' => $data['os_type'],
            'os_name' => $data['os_name'],
            'cpu' => $data['cpu'],
            'memory' => $data['memory'],
            'instance_type' => $data['instance_type'],
            'vpc_id' => $data['vpc_id'],
            'vpc_name' => $data['vpc_name'],
            'security_groups' => json_encode([]),
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
        ];
        
        // 保存数据
        $result = HostAssetsModel::addHostAssets($host_data);
        
        if ($result) {
            return json(['code' => 1, 'msg' => '添加成功', 'url' => url('/asm/hostassets/index')]);
        } else {
            return json(['code' => 0, 'msg' => '添加失败']);
        }
    }
    
    // 编辑主机资产
    public function edit()
    {
        $id = Request::param('id', '', 'intval');
        
        if (empty($id)) {
            $this->error('参数错误');
        }
        
        $host = HostAssetsModel::getHostAssetsById($id);
        
        if (empty($host)) {
            $this->error('主机资产不存在');
        }
        
        View::assign([
            'host' => $host
        ]);
        
        return redirect('/web/asm/host');
    }
    
    // 更新主机资产
    public function update()
    {
        $id = Request::param('id', '', 'intval');
        $data = Request::post();
        
        if (empty($id)) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }
        
        // 更新数据
        $update_data = [
            'display_name' => $data['display_name'],
            'public_ip' => $data['public_ip'],
            'mac_address' => $data['mac_address'],
            'os_type' => $data['os_type'],
            'os_name' => $data['os_name'],
            'cpu' => $data['cpu'],
            'memory' => $data['memory'],
            'instance_type' => $data['instance_type'],
            'vpc_id' => $data['vpc_id'],
            'vpc_name' => $data['vpc_name'],
            'update_time' => date('Y-m-d H:i:s')
        ];
        
        $result = HostAssetsModel::updateHostAssets($id, $update_data);
        
        if ($result) {
            return json(['code' => 1, 'msg' => '更新成功', 'url' => url('/asm/hostassets/index')]);
        } else {
            return json(['code' => 0, 'msg' => '更新失败']);
        }
    }
    
    // 删除主机资产
    public function delete()
    {
        $id = Request::param('id', '', 'intval');
        
        if (empty($id)) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }
        
        // 检查是否为线下IDC主机
        $host = HostAssetsModel::getHostAssetsById($id);
        if (empty($host)) {
            return json(['code' => 0, 'msg' => '主机资产不存在']);
        }
        
        // 只有线下IDC主机可以删除
        if ($host['cloud_platform'] != 'idc') {
            return json(['code' => 0, 'msg' => '仅可删除线下IDC主机']);
        }
        
        $result = HostAssetsModel::deleteHostAssets($id);
        
        if ($result) {
            return json(['code' => 1, 'msg' => '删除成功']);
        } else {
            return json(['code' => 0, 'msg' => '删除失败']);
        }
    }
    
    // 主机概览页面
    public function overview()
    {
        // 获取统计数据
        $stats = HostAssetsModel::getHostAssetsStats();
        
        // 分配数据到视图
        View::assign([
            'stats' => $stats
        ]);
        
        return redirect('/web/asm/host');
    }
}