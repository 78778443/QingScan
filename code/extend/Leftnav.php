<?php

class Leftnav
{
    /*
     * 自定义菜单排列
     */
    static public function menu($cate, $lefthtml = '|— ', $pid = 0, $lvl = 0, $leftpin = 0)
    {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $v['lvl'] = $lvl + 1;
                $v['leftpin'] = $leftpin + 0;
                $v['lefthtml'] = str_repeat($lefthtml, $lvl);
                $v['ltitle'] = $v['lefthtml'] . $v['title'];
                $arr[] = $v;
                $arr = array_merge($arr, self::menu($cate, $lefthtml, $v['auth_rule_id'], $lvl + 1, $leftpin + 20));
            }
        }
        return $arr;
    }

    static public function my_sort_department($cate, $lefthtml = '|— ', $pid = 0, $lvl = 0, $leftpin = 0)
    {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $v['lvl'] = $lvl + 1;
                $v['leftpin'] = $leftpin + 0;
                $v['lefthtml'] = str_repeat($lefthtml, $lvl);
                $v['ltitle'] = $v['lefthtml'] . $v['title'];
                $arr[] = $v;
                $arr = array_merge($arr, self::my_sort_department($cate, $lefthtml, $v['department_id'], $lvl + 1, $leftpin + 20));
            }
        }
        return $arr;
    }

    static public function my_sort_top_department($cate, $lefthtml = '|— ', $pid = 0, $lvl = 0, $leftpin = 0)
    {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['department_id'] == $pid) {
                $v['lvl'] = $lvl + 1;
                $v['leftpin'] = $leftpin + 0;
                $v['lefthtml'] = str_repeat($lefthtml, $lvl);
                $v['ltitle'] = $v['lefthtml'] . $v['title'];
                $arr[] = $v;
                $arr = array_merge($arr, self::my_sort_top_department($cate, $lefthtml, $v['pid'], $lvl + 1, $leftpin + 20));
            }
        }
        return $arr;
    }

    static public function cate($cate, $lefthtml = '|— ', $pid = 0, $lvl = 0, $leftpin = 0)
    {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['parentid'] == $pid) {
                $v['lvl'] = $lvl + 1;
                $v['leftpin'] = $leftpin + 0;
                $v['lefthtml'] = str_repeat($lefthtml, $lvl);
                $arr[] = $v;
                $arr = array_merge($arr, self::menu($cate, $lefthtml, $v['id'], $lvl + 1, $leftpin + 20));
            }
        }

        return $arr;
    }

    static public function auth($cate, $pid = 0, $rules)
    {
        $arr = array();
        $rulesArr = explode(',', $rules);
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                if (in_array($v['auth_rule_id'], $rulesArr)) {
                    $v['checked'] = true;
                }
                $v['open'] = true;
                $arr[] = $v;
                $arr = array_merge($arr, self::auth($cate, $v['auth_rule_id'], $rules));
            }
        }
        return $arr;
    }

    static public function category($cate, $lefthtml = '|— ', $pid = 0, $lvl = 0, $leftpin = 0)
    {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $v['lvl'] = $lvl + 1;
                $v['leftpin'] = $leftpin + 0;
                $v['lefthtml'] = str_repeat($lefthtml, $lvl);
                $v['ltitle'] = $v['lefthtml'] . $v['name'];
                $arr[] = $v;
                $arr = array_merge($arr, self::category($cate, $lefthtml, $v['id'], $lvl + 1, $leftpin + 20));
            }
        }
        return $arr;
    }


    /*
     * $column_one 顶级栏目
     * $column_two 所有栏目
     * 用法匹配column_leftid 进行数组组合
     */
    static public function index_top($column_one, $column_two)
    {
        $arr = array();
        foreach ($column_one as $v) {
            $v['sub'] = self::index_toptwo($column_two, $v['id']);
            $arr[] = $v;
        }
        return $arr;
    }

    static public function index_toptwo($column_two, $c_id)
    {
        $arry = array();
        foreach ($column_two as $v) {
            if ($v['parentid'] == $c_id) {
                $arry[] = $v;
            }
        }
        return $arry;
    }


    public static function menuList($data, $pid = 0)
    {
        $arr = array();
        $i = 0;
        $k = 0;
        foreach ($data as $v) {
            if ($v['pid'] == $pid) {                      //匹配子记录
                $i++;
                $v['a'] = $k . '_' . $i;
                $v['is_href'] = strtolower($v['href']);
                $v['children'] = self::menuList($data, $v['auth_rule_id']); //递归获取子记录
                if ($v['children'] == null) {
                    unset($v['children']);             //如果子元素为空则unset()进行删除，说明已经到该分支的最后一个元素了（可选）
                }
                $k++;
                $arr[] = $v;                           //将记录存入新数组
            }
        }
        return $arr;
    }

    public static function index_children_top($data, $children_pid = 0)
    {
        foreach ($data as $v) {
            if ($v['id'] == $children_pid) {
                $arr = self::index_children_top($data, $v['pid']); //递归获取子记录
                if ($arr == null) {
                    unset($arr);             //如果子元素为空则unset()进行删除，说明已经到该分支的最后一个元素了（可选）
                }
                if ($v['pid'] === 0)
                    return $v;
                break;
            }
        }
        return $arr;
    }

    public static function rightMenuList($data, $pid = 0)
    {
        $arr = array();
        $i = 0;
        $k = 0;
        foreach ($data as $v) {
            $jump = explode('/', $v['jump']);
            $v['name'] = $jump[0];
            $v['spread'] = false;
            if ($pid == 0) {
                unset($v['jump']);
            }
            if ($v['pid'] == $pid) {                      //匹配子记录
                $i++;
                $v['list'] = self::rightMenuList($data, $v['auth_rule_id']); //递归获取子记录
                if ($v['list'] == null) {
                    unset($v['list']);             //如果子元素为空则unset()进行删除，说明已经到该分支的最后一个元素了（可选）
                }
                $k++;
                $arr[] = $v;                           //将记录存入新数组
            }
        }
        return $arr;
    }
}
?>