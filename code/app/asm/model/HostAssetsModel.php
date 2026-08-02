<?php

namespace app\asm\model;

use app\model\BaseModel;
use think\facade\Db;

class HostAssetsModel extends BaseModel
{
    // 主机资产列表
    public static function getHostAssetsList($where = [], $page = 1, $limit = 20)
    {
        return Db::table('asm_host_assets')
            ->where($where)
            ->page($page, $limit)
            ->order('create_time desc')
            ->select();
    }
    
    // 主机资产总数
    public static function getHostAssetsCount($where = [])
    {
        return Db::table('asm_host_assets')->where($where)->count();
    }
    
    // 添加主机资产
    public static function addHostAssets($data)
    {
        try {
            return Db::table('asm_host_assets')->insertGetId($data);
        } catch (\Exception $e) {
            // 记录错误信息
            file_put_contents('/tmp/host_assets_error.log', 
                date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n" .
                "Data: " . print_r($data, true) . "\n\n",
                FILE_APPEND
            );
            // 重新抛出异常
            throw $e;
        }
    }
    
    // 批量添加主机资产
    public static function batchAddHostAssets($data)
    {
        return Db::table('asm_host_assets')->insertAll($data);
    }
    
    // 更新主机资产
    public static function updateHostAssets($id, $data)
    {
        return Db::table('asm_host_assets')->where('id', $id)->update($data);
    }
    
    // 根据实例ID和平台更新主机资产
    public static function updateByInstanceIdAndPlatform($instanceId, $platform, $data)
    {
        return Db::table('asm_host_assets')
            ->where('instance_id', $instanceId)
            ->where('cloud_platform', $platform)
            ->update($data);
    }
    
    // 删除主机资产
    public static function deleteHostAssets($id)
    {
        return Db::table('asm_host_assets')->where('id', $id)->delete();
    }
    
    // 获取单个主机资产
    public static function getHostAssetsById($id)
    {
        return Db::table('asm_host_assets')->where('id', $id)->find();
    }
    
    // 根据实例ID和平台获取主机资产
    public static function getByInstanceIdAndPlatform($instanceId, $platform)
    {
        return Db::table('asm_host_assets')
            ->where('instance_id', $instanceId)
            ->where('cloud_platform', $platform)
            ->find();
    }
    
    // 批量更新所有主机资产
    public static function updateAll($data)
    {
        return Db::table('asm_host_assets')->where('id', '>', 0)->update($data);
    }
    
    // 更新HIDS状态
    
    // 主机资产统计数据
    public static function getHostAssetsStats()
    {
        $stats = [];
        
        // 总机器数量
        $stats['total_count'] = Db::table('asm_host_assets')->count();
        
        // 新增数量（最近7天）
        $stats['new_count'] = Db::table('asm_host_assets')
            ->where('create_time', '>=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->count();
        
        // 云平台分布
        $stats['cloud_platform_stats'] = Db::table('asm_host_assets')
            ->field('cloud_platform, count(*) as count')
            ->group('cloud_platform')
            ->select();
        
        // 操作系统类型分布
        $stats['os_type_stats'] = Db::table('asm_host_assets')
            ->field('os_type, count(*) as count')
            ->where('os_type', '!=', '')
            ->group('os_type')
            ->select();
        
        // 状态分布
        $stats['status_stats'] = Db::table('asm_host_assets')
            ->field('status, count(*) as count')
            ->group('status')
            ->select();
        
        // 最近30天的新增趋势
        $trend = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $count = Db::table('asm_host_assets')
                ->whereDay('create_time', $date)
                ->count();
            $trend[] = [
                'date' => $date,
                'count' => $count
            ];
        }
        $stats['daily_trend'] = $trend;
        
        // 漏洞数量统计（通过vul_target表关联）
        $stats['vul_count'] = Db::name('vul_target')
            ->alias('vt')
            ->join('asm_host_assets ha', 'vt.ip = ha.private_ip')
            ->where('vt.is_vul', 1)
            ->count('DISTINCT vt.id');
        
        // VPC分布
        $stats['vpc_stats'] = Db::table('asm_host_assets')
            ->field('vpc_name, count(*) as count')
            ->where('vpc_name', '!=', '')
            ->group('vpc_name')
            ->select();
        
        // 实例类型分布
        $stats['instance_type_stats'] = Db::table('asm_host_assets')
            ->field('instance_type, count(*) as count')
            ->where('instance_type', '!=', '')
            ->group('instance_type')
            ->select();
        
        // CPU核数分布
        $stats['cpu_stats'] = Db::table('asm_host_assets')
            ->field('cpu, count(*) as count')
            ->where('cpu', '>', 0)
            ->group('cpu')
            ->order('cpu', 'asc')
            ->select();
        
        return $stats;
    }
}