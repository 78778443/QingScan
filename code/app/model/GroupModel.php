<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/8/15
 * Time: 上午10:54
 */


namespace app\model;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use QingPHP\Lib\MysqlLib;

class GroupModel extends BaseModel
{


    public static $tableName = "group";


    public static function field()
    {
        $db = new MysqlLib();

        return $db->getFields(self::$tableName);


    }


    /**
     * @param  $where
     * @param  string $limit
     * @param  array  $otherParam
     * @return mixed
     */
    private static function getList($where, $limit = '', array $otherParam = [])
    {
        $db = new MysqlLib(getMysql());

        $field = $otherParam['field'] ?? '*';
        $group = $otherParam['group'] ?? '';
        $order = $otherParam['order'] ?? 'id desc';

        $db = $db->table(self::$tableName);

        if (!empty($limit)) {
            $db->limit($limit);
        }

        if ($group) {
            $db->group($group);
        }

        $result = $db->where($where)->order($order)->select($field);


        return $result;
    }

    public static function getListByWhere($where, $limit = 10)
    {

        $list = self::getList($where, $limit);

        return $list;
    }

    public static function getListByWherePage($where, $page, $pageSize = 15)
    {

        $list = self::getList($where, $pageSize, $page);


        $count = self::getCount($where);

        return ['list' => $list, 'count' => $count, 'pageSize' => $pageSize];
    }

    private static function getCount($where, array $otherParam = [])
    {
        $db = new MysqlLib(getMysql());
        $group = $otherParam['group'] ?? '';

        $db = $db->table(self::$tableName);

        if ($group) {
            $db->group($group);
        }

        $result = $db->where($where)->count();


        return $result[0]['num'] ?? 0;
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
        $crawlerApi = new MysqlLib();

        //更新条件
        $crawlerApi = $crawlerApi->table(self::$tableName)->where($where);

        //执行更新并返回数据
        $crawlerApi->update($data);
    }


    /**
     * @param array $data
     */
    public static function addData(array $data)
    {

        $data = getArrayField($data, self::field());

        return self::add($data);
    }

    private static function add($data)
    {
        $db = new MysqlLib();


        return $db->table(self::$tableName)->insert($data);
    }

    public static function getGroupName()
    {
        $groupList = self::getList(['id > 0']);

        $groupList = array_column($groupList, 'name', 'id');
        return $groupList;
    }

    public static function getTargetNum(int $id)
    {
        $where['group_id'] = $id;


        $count = AppModel::getCount($where);

        return intval($count);
    }

    public static function getVulnNum(int $id)
    {

        $where['group_id'] = $id;
        $appList = AppModel::getListByWhere($where, 50);

        $appIds = array_column($appList, 'id');

        $count = 0;
        if ($appIds) {
            $where = ['app_id' => ['in', $appIds]];
            $count = XrayModel::getCount($where);
        }


        return $count;
    }

}
