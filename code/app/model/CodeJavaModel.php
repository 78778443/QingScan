<?php


namespace app\model;


use think\facade\Db;

class CodeJavaModel extends BaseModel
{
    public static function code_java()
    {
        while (true) {
            ini_set('max_execution_time', 0);
            $list = Db::name('code')->whereTime('java_scan_time', '<=', date('Y-m-d H:i:s', time() - (86400 * 15)))
                ->where('is_delete', 0)->limit(1)->orderRand()->field('id,name,user_id')->select()->toArray();
            foreach ($list as $k => $v) {
                self::scanTime('code',$v['id'],'java_scan_time');

                $filepath = "/data/codeCheck/{$v['name']}";
                $fileArr = getFilePath($filepath,'pom.xml');
                if (!$fileArr) {
                    addlog("项目目录不存在:{$filepath}");
                    continue;
                }
                foreach ($fileArr as $val) {
                    $result = xmlToArray($val['file']);
                    if ($result) {
                        $data = [
                            'user_id' => $v['user_id'],
                            'code_id' => $v['id'],
                            'modelVersion' => $result['modelVersion'],
                            'groupId' => $result['groupId'],
                            'artifactId' => $result['artifactId'],
                            'version' => $result['version'],
                            'modules' => json_encode($result['modules']),
                            'packaging' => $result['packaging'],
                            'name' => $result['name'],
                            'comment' => $result['comment'] ? json_encode($result['comment']) : '',
                            'url' => $result['url'],
                            'properties' => json_encode($result['properties']),
                            'dependencies' => json_encode($result['dependencies']),
                            'build' => json_encode($result['build']),
                            'create_time' => date('Y-m-d H:i:s', time()),
                        ];
                        Db::name('code_java')->insert($data);
                    } else {
                        addlog("项目文件内容为空:{$val['file']}");
                        continue;
                    }
                }
            }
            sleep(10);
        }
    }
}