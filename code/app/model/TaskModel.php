<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/8/15
 * Time: 上午10:54
 */


namespace app\model;

use think\facade\Db;

class TaskModel extends BaseModel
{

    public static $tableName = "task";


    /**
     * 启动一个任务
     *
     * @param string $url
     */
    public static function startTask(int $id,string $url,int $user_id)
    {
        $newData = [
            'method' => 'GET',
            'app_id' => $id,
            'user_id'=>$user_id,
            'url' => $url,
            'status' => 1,
            'crawl_status' => 0,
            'scan_status' => 0,
            'header' => json_encode([]),
        ];
        //记录到数据库
        UrlsModel::addData($newData);
        //爬虫任务
        UrlsModel::sendTask($id, $url);
    }


    /**
     * @param array $data
     */
    public static function addTask(array $data)
    {
        self::add($data);
    }

    private static function add($data)
    {
        Db::table(self::$tableName)->save($data);
    }
}
