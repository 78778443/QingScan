<?php

namespace app\webscan\model;

use app\model\BaseModel;
use app\model\ConfigModel;
use app\model\PluginModel;
use think\facade\Db;


class AwvsModel extends BaseModel
{
    public static function awvsScan()
    {
        $where = ['tool' => 'scan_app_awvs', 'status' => 0];
        $list = Db::table('task_scan')->where($where)->limit(10)->select()->toArray();
        foreach ($list as $task) {


            $val = json_decode($task['ext_info'], true);


            $awvs_url = ConfigModel::value('awvs_url');
            $awvs_token = ConfigModel::value('awvs_token');

            PluginModel::addScanLog($val['id'], __METHOD__, 0);


            $url = $val['url'];
            $id = $val['id'];
            if (!$awvs_url || !$awvs_token) {
                $errMsg = ["执行AWVS扫描任务失败,未找到有效得配置信息", $awvs_url, $awvs_token, $id, $url];
                PluginModel::addScanLog($val['id'], __METHOD__, 0, 2, 1, ["content" => $errMsg]);
                addlog($errMsg);
                continue;
            }

            if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                PluginModel::addScanLog($val['id'], __METHOD__, 0, 2, 1, ["content" => ["URL地址不正确", $id, $url]]);
                addlog(["URL地址不正确", $id, $url]);
                continue;
            }
            if (!Db::table('awvs_app')->where(['app_id' => $id])->count('id')) {
                $errMsg = ['请等待awvs扫描结果'];
                addlog($errMsg);
            }


            //获取目标,如果不存在则添加目标
            $targetId = self::getTargetId($id, $url, $awvs_url, $awvs_token, $val['user_id']);
            if (!$targetId) {
                $errMsg = ["任务发送到AWVS失败,请检查QingScan是否能访问到AWVS服务，以及token有效性~", $id, $url];
                PluginModel::addScanLog($val['id'], __METHOD__, 0, 2, 1, ["content" => $errMsg]);
                addlog($errMsg);
                continue;
            }

            //获取扫描状态
            $retArr = self::getScanStatus($targetId, $awvs_url, $awvs_token);

            if (isset($retArr['code']) && $retArr['code'] == 404) {
                addlog(["未在AWVS中找到此目标ID", $targetId]);
                Db::table('awvs_app')->where(['target_id' => $targetId])->delete();
                continue;
            }

            //API未授权
            if (isset($retArr['code']) && $retArr['code'] == 401) {
                $errMsg = ["AWVS未授权,请参照wiki配置地址和token...", $id, $url];
                addlog($errMsg);
                PluginModel::addScanLog($val['id'], __METHOD__, 0, 2, 1, ["content" => $errMsg]);
                continue;
            }


            //判断目标扫描状态
            if (isset($retArr['last_scan_session_status']) && $retArr['last_scan_session_status'] == 'completed') {
                self::addVulnList($retArr['last_scan_id'], $retArr['last_scan_session_id'], $awvs_url, $awvs_token, $val['user_id'], $val['id']);
                PluginModel::addScanLog($val['id'], __METHOD__, 0, 1);

                Db::table('task_scan')->where(['id' => $task['id']])->update(['status' => 1]);
            }
        }

    }

    public static function addVulnList($scanId, $scanSessionId, $awvs_url, $awvs_token, $user_id, $appId)
    {
        $vulnList = self::getVulnList($scanId, $scanSessionId, $awvs_url, $awvs_token);

        foreach ($vulnList['vulnerabilities'] as $value) {
            $detail = self::getDetail($scanId, $scanSessionId, $value['vuln_id'], $awvs_url, $awvs_token);
            $value = array_merge($value, $detail);
            foreach ($value as $k => $v) {
                $value[$k] = is_string($v) ? $v : json_encode($v, JSON_UNESCAPED_UNICODE);
            }
            $value['user_id'] = $user_id;
            $value['app_id'] = $appId;
            Db::table('awvs_vuln')->extra('IGNORE')->insert($value);
        }
    }

    public static function getDetail($scanId, $scanSessionId, $vulnId, $awvs_url, $awvs_token)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $awvs_url . "/api/v1/scans/{$scanId}/results/{$scanSessionId}/vulnerabilities/{$vulnId}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $headers = array();
        $headers[] = 'X-Auth: ' . $awvs_token;
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return json_decode($result, true);
    }

    public static function getVulnList($scanId, $scanSessionId, $awvs_url, $awvs_token)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $awvs_url . "/api/v1/scans/{$scanId}/results/{$scanSessionId}/vulnerabilities?l=100&s=severity:desc");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $headers = array();
        $headers[] = 'X-Auth: ' . $awvs_token;
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return json_decode($result, true);

    }

    public static function startScan($targetId, $awvs_url, $awvs_token)
    {
        $postData = '{"profile_id":"11111111-1111-1111-1111-111111111112","ui_session_id":"c312cb2615120dd71d8bb954ed803bbd","incremental":false,"schedule":{"disable":false,"start_date":null,"time_sensitive":false},"target_id":"' . $targetId . '"}';
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $awvs_url . '/api/v1/scans');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'X-Auth: ' . $awvs_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);


        return json_decode($result, true);

    }

    public static function getScanStatus($targetId, $awvs_url, $awvs_token)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $awvs_url . '/api/v1/targets/' . $targetId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $headers = array();
        $headers[] = 'X-Auth: ' . $awvs_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            addlog(["获取AWVS扫描状态失败", curl_error($ch)]);
        }
        curl_close($ch);

        $retArr = json_decode($result, true);

        return $retArr;
    }

    public static function getTargetId($id, $url, $awvs_url, $awvs_token, $user_id)
    {
        $appInfo = Db::table('awvs_app')->where(['app_id' => $id])->find();
        if (empty($appInfo)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $awvs_url . "/api/v1/targets");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"address\": \"{$url}\",\"description\": \"xxxx\",\"criticality\": \"10\"}");
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'X-Auth: ' . $awvs_token;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
                return false;
            }
            curl_close($ch);
            $appInfo = json_decode($result, true);
            if ($appInfo) {
                $appInfo['app_id'] = $id;
                $appInfo['user_id'] = $user_id;
                Db::table('awvs_app')->insert($appInfo);
            } else {
                if (!isset($appInfo['target_id'])) {
                    return false;
                }
            }
            //添加扫描任务
            $scanRet = self::startScan($appInfo['target_id'], $awvs_url, $awvs_token);
            if (!isset($scanRet['profile_id'])) echo "创建扫描任务失败 {$appInfo['target_id']} ";

        }
        return $appInfo['target_id'];
    }

    public static function addDataAll(int $codeId, string $jsonPath)
    {
        $data = json_decode(file_get_contents($jsonPath), true);

        foreach ($data['results'] as $v1) {
            $data = [];
            foreach ($v1 as $k2 => $v2) {
                if (is_array($v2)) {
                    foreach ($v2 as $k3 => $v3) {
                        $data["{$k2}_{$k3}"] = is_string($v3) ? $v3 : json_encode($v3, JSON_UNESCAPED_UNICODE);
                    }
                } else {
                    $data[$k2] = $v2;
                }
            }
            $data['code_id'] = $codeId;
            Db::table('semgrep')->insert($data);

        }
    }
}
