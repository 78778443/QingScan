<?php

namespace app\model;

class GroupModel extends BaseModel
{


    public static $tableName = "group";


    public static function getListByWhere($where, $limit = 10)
    {

        $list = self::getList($where, $limit);

        return $list;
    }

    /**
     * @param array $data
     */
    public static function addData(array $data)
    {

        $data = getArrayField($data, self::field());

        return self::add($data);
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
