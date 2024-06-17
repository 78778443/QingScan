<?php


namespace app\model;


use think\facade\Db;

class PythonLibraryModel extends BaseModel
{
    public static function code_python()
    {
        $codePath = "./data/codeCheck";
            $where[] = ['project_type','in',[3,6]];
            $list = self::getCodeStayScanList('python_scan_time',$where);
            foreach ($list as $k => $v) {
                PluginModel::addScanLog($v['id'], __METHOD__, 2);
                

                $value = $v;
                $prName = cleanString($value['name']);
                $codeUrl = $value['ssh_url'];
                downCode($codePath, $prName, $codeUrl, $value['is_private'], $value['username'], $value['password'], $value['private_key']);
                $filepath = "./data/codeCheck/{$prName}";

                $data = [];
                $fileArr = getFilePath($filepath, 'requirements.txt');
                if (!$fileArr) {
                    PluginModel::addScanLog($v['id'], __METHOD__, 2, 2);
                    addlog("Python依赖扫描失败,未找到依赖文件:{$filepath}");
                    continue;
                }
                foreach ($fileArr as $val) {
                    //打开一个文件
                    $file = fopen($val['file'], "r");
                    //检测指正是否到达文件的未端
                    while (!feof($file)) {
                        $result = fgets($file);
                        if (!empty($result)) {
                            $data[] = [
                                'user_id' => $v['user_id'],
                                'code_id' => $v['id'],
                                'name' => $result,
                                'create_time' => date('Y-m-d H:i:s', time())
                            ];
                        }
                    }
                    //关闭被打开的文件
                    fclose($file);
                }
                if ($data) {
                    Db::name('code_python')->insertAll($data);
                }
                PluginModel::addScanLog($v['id'], __METHOD__, 1, 2);
            }

    }
}