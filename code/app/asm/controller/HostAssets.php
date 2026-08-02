<?php

namespace app\asm\controller;

use app\asm\model\HostAssetsModel;
use app\asm\model\HidsQingtengModel;
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
        $hids_installed = Request::param('hids_installed', '');
        
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
        if ($hids_installed !== '') {
            $where['hids_installed'] = intval($hids_installed);
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
            'baidu' => '百度云',
            'huoshan' => '火山云',
            'yidong' => '移动云',
            'tianyi' => '天翼云',
            'aliyun' => '阿里云',
            'idc' => '线下IDC'
        ];
        
        // HIDS安装状态
        $hids_status = [
            '0' => '未安装',
            '1' => '已安装'
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
            'hids_installed_count' => Db::table('asm_host_assets')->where('hids_installed', 1)->count(),
            'platforms' => $platforms,
            'hids_status' => $hids_status,
            'instance_status' => $instance_status,
            'keyword' => $keyword,
            'cloud_platform' => $cloud_platform,
            'status' => $status,
            'hids_installed' => $hids_installed
        ]);
        
        return redirect('/web/asm/host');
    }
    
    // HIDS列表
    public function hids()
    {
        // 获取HIDS列表搜索条件
        $keyword = Request::param('keyword', '');
        
        // 构建HIDS查询条件
        $hids_where = [];
        if (!empty($keyword)) {
            $hids_where[] = ['ip_address', 'like', '%' . $keyword . '%'];
        }
        
        // 获取HIDS分页参数
        $limit = Request::param('limit', 20, 'intval');
        
        // 使用数据库查询的paginate方法获取带分页的数据
        $hids_page = Db::table('asm_hids_qingteng')
            ->where($hids_where)
            ->order('created_time desc')
            ->paginate([
                'list_rows' => $limit,
                'query' => Request::param()
            ]);
        
        // 获取分页后的数据列表
        $hids_list = $hids_page->items();
        
        // 解析原始JSON数据，提取需要的字段
        foreach ($hids_list as &$hids_item) {
            if (!empty($hids_item['original_json'])) {
                $original_data = json_decode($hids_item['original_json'], true);
                if ($original_data) {
                    // 提取系统信息
                    if (isset($original_data['system'])) {
                        $hids_item['os_name'] = $original_data['system']['os_name'] ?? '';
                        $hids_item['kernel_version'] = $original_data['system']['kernel_version'] ?? '';
                        $hids_item['hostname'] = $original_data['system']['hostname'] ?? '';
                    }
                    
                    // 提取在线状态（使用青藤云返回的state字段）
                    $hids_item['online_status'] = (isset($original_data['state']) && strtoupper($original_data['state']) === 'ONLINE') ? '在线' : '离线';
                    
                    // 提取实例名称
                    if (isset($original_data['instance_name'])) {
                        $hids_item['instance_name'] = $original_data['instance_name'];
                    }
                    
                    // 提取最后同步时间
                    $hids_item['sync_time'] = $hids_item['updated_time'] ?? $hids_item['created_time'] ?? '';
                }
            }
        }
        
        View::assign([
            'hids_list' => $hids_list,
            'page' => $hids_page,
            'keyword' => $keyword
        ]);
        
        return redirect('/web/asm/host');
    }
    
    // HIDS详情页面
    public function hidsDetail()
    {
        $id = Request::param('id', 0, 'intval');
        
        if (empty($id)) {
            $this->error('参数错误');
        }
        
        // 获取HIDS数据
        $hids_data = HidsQingtengModel::getById($id);
        
        if (empty($hids_data)) {
            $this->error('HIDS数据不存在');
        }
        
        // 解析原始JSON数据
        if (!empty($hids_data['original_json'])) {
            $hids_data['original_data'] = json_decode($hids_data['original_json'], true);
        } else {
            $hids_data['original_data'] = [];
        }
        
        View::assign('hids_data', $hids_data);
        return redirect('/web/asm/host');
    }
    
    // 更新HIDS状态
    public function updateHidsStatus()
    {
        $id = Request::param('id', '', 'intval');
        $hids_installed = Request::param('hids_installed', '', 'intval');
        $hids_version = Request::param('hids_version', '', 'trim');
        
        if (empty($id)) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }
        
        $data = [
            'hids_installed' => $hids_installed,
            'hids_version' => $hids_version,
            'hids_last_check' => date('Y-m-d H:i:s')
        ];
        
        $result = HostAssetsModel::updateHostAssets($id, $data);
        
        if ($result) {
            return json(['code' => 1, 'msg' => '更新成功']);
        } else {
            return json(['code' => 0, 'msg' => '更新失败']);
        }
    }
    
    // 导入主机资产
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
    
    // 同步云资产
    public function syncCloudAssets()
    {
        // 这里可以实现从云平台API同步最新数据的功能
        return json(['code' => 1, 'msg' => '同步功能开发中']);
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
        if ($host['cloud_platform'] == 'huoshan') {
            // 获取火山云原始信息
            $huoshan_data = Db::table('asm_cloud_huoshan')->where('resource_id', $host['instance_id'])->find();
            if (!empty($huoshan_data['original_json'])) {
                $original_data = json_decode($huoshan_data['original_json'], true);
            }
        } elseif ($host['cloud_platform'] == 'tianyi') {
            // 获取天翼云原始信息
            $tianyi_data = Db::table('asm_cloud_tianyi')->where('resource_id', $host['instance_id'])->find();

            if (!empty($tianyi_data['original_json'])) {
                $original_data = json_decode($tianyi_data['original_json'], true);
            }
        } elseif ($host['cloud_platform'] == 'aliyun') {
            // 获取阿里云原始信息
            $aliyun_data = Db::table('asm_cloud_aliyun')->where('resource_id', $host['instance_id'])->find();
            if (!empty($aliyun_data['original_json'])) {
                $original_data = json_decode($aliyun_data['original_json'], true);
            }
        } elseif ($host['cloud_platform'] == 'baidu') {
            // 获取百度云原始信息
            $baidu_data = Db::table('asm_cloud_baidu')->where('resource_id', $host['instance_id'])->find();
            if (!empty($baidu_data['original_json'])) {
                $original_data = json_decode($baidu_data['original_json'], true);
            }
        }
        
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
            'hids_installed' => 0
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