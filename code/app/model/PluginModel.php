<?php

namespace app\model;

use think\facade\Db;

class PluginModel extends BaseModel
{
    public static $tableName = "plugin";

    /**
     * 添加扫描日志
     * @param int $appId
     * @param string $pluginName
     * @param int $scanType 类型 0app 1host 2code  3url
     * @param int $logType  进度 0开始扫描   1完成   2失败
     * @param int $isCustom 是否为自定义插件  1否   2是
     * @param array $data
     */
    public static function addScanLog(int $appId, string $pluginName, int $scanType, int $logType = 0, $isCustom=1,array $data = [])
    {
        $pluginName = explode('::', $pluginName)[1] ?? 'method_error';
        $data['app_id'] = $appId;
        $data['plugin_name'] = $pluginName;
        $data['scan_type'] = $scanType;
        $data['log_type'] = $logType;
        $data['is_custom'] = $isCustom;
        $data['content'] = (isset($data['content']) && !is_string($data['content'])) ? var_export($data['content'], true) : '';
        $data['content'] = substr($data['content'], 0, 4999);
        Db::table('plugin_scan_log')->extra("IGNORE")->insert($data);
    }
}
