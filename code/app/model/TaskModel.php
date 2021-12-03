<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/8/15
 * Time: 上午10:54
 */


namespace app\model;

use QingPHP\Lib\MysqlLib;
use QingPHP\Lib\Page;
use think\facade\Db;

class TaskModel extends BaseModel
{

    public static $tableName = "task";

    public static function field()
    {
        $db = new MysqlLib();

        return $db->getFields(self::$tableName);
    }

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
        ]
        //记录到数据库
        UrlsModel::addData($newData);
        //爬虫任务
        UrlsModel::sendTask($id, $url);
    }


    public static function startScan(int $appId)
    {
        $appInfo = AppModel::getInfo($appId);
        if (empty($appInfo)) {
            ajaxReturn("appid不存在");
        }

        $urlArr = UrlsModel::getListByWhere();

    }


    public static function getTaskInfo($taskId)
    {

        //查询具体数据,并刷新缓存
        $result = self::getList(['id' => $taskId]);


        return $result[0] ?? false;

    }

    /**
     * @param  $where
     * @param  string $limit
     * @param  array  $otherParam
     * @return mixed
     */
    private static function getList($where, int $limit = 15, int $page = 1, array $otherParam = [])
    {
        $db = new MysqlLib(getMysql());

        $field = $otherParam['field'] ?? '*';
        $group = $otherParam['group'] ?? '';
        $order = $otherParam['order'] ?? 'id desc';

        $db = $db->table(self::$tableName);

        if (!empty($limit)) {
            $limit = ($page-1) * $limit . ",$limit";
            $db->limit($limit);
        }

        if ($group) {
            $db->group($group);
        }

        $result = $db->where($where)->order($order)->select($field);


        return $result;
    }

    public static function getListByWhere($where)
    {

        $list = self::getList($where, 1);

        return $list;
    }

    /**
     * 获取单条记录
     *
     * @param  int $id
     * @return array
     */
    public static function getInfo(int $id)
    {
        $where = ['id' => $id];

        $list = self::getList($where);

        return $list[0] ?? [];
    }

    /**
     * 内部方法，更新数据
     *
     * @param  array $where
     * @param  array $data
     * @return mixed
     */
    private static function updateByWhere(array $where, array $data)
    {
        $taskApi = new MysqlLib();

        //更新条件
        $taskApi = $taskApi->table('task')->where($where);

        //执行更新并返回数据
        $taskApi->update($data);
    }

    /**
     * 更新生成任务状态
     *
     * @param string $taskNum
     * @param int    $status
     */
    public static function updateStatus(string $taskNum, int $status)
    {
        $where = ['id' => $taskNum];
        $data = ['chat_status' => $status];
        self::updateByWhere($where, $data);
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
