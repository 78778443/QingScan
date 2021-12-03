<?php


namespace app\model;


use think\facade\Db;

class PythonLibraryModel extends BaseModel
{
    public static function code_python()
    {
        while (true) {
            ini_set('max_execution_time', 0);
            $list = Db::name('code')->whereTime('python_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))
                ->where('is_delete', 0)->limit(1)->orderRand()->field('id,name,user_id')->select()->toArray();
            //$list = Db::name('code')->where('id',20)->where('is_delete', 0)->field('id,name,user_id')->select()->toArray();
            foreach ($list as $k => $v) {
                self::scanTime('code',$v['id'],'python_scan_time');

                $data = [];
                $filepath = "/data/codeCheck/{$v['name']}";
                $fileArr = getFilePath($filepath,'requirements.txt');
                if (!$fileArr) {
                    addlog("项目目录不存在:{$filepath}");
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
            }
            sleep(10);
        }

    }

    public function a(){
        /*$filename = "/data/codeCheck/{$v['name']}/requirements.txt";
        if (!is_dir("/data/codeCheck/{$v['name']}")) {
            addlog("项目目录不存在:{$filename}");
            self::scanTime('code',$v['id'],'python_scan_time');
            continue;
        }
        if (file_exists($filename)) {
            //打开一个文件
            $file = fopen($filename, "r");
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
        } else {
            addlog("项目组件文件不存在:{$filename}");
            self::scanTime('code',$v['id'],'python_scan_time');
        }
        /*if ($data) {
            self::scanTime('code',$v['id'],'python_scan_time');
            //Db::name('code')->where('id', $v['id'])->update(['compose_scan_time' => date('Y-m-d H:i:s', time())]);
            Db::name('code_python')->insertAll($data);
        }*/
    }
}