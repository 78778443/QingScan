<?php


namespace app\controller;


use app\BaseController;
use app\model\AuthRuleModel;
use app\model\UserLogModel;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use think\exception\HttpResponseException;
use think\facade\Cookie;
use think\facade\Db;
use think\facade\Request;
use think\facade\Response;
use think\facade\View;

class Common extends BaseController
{
    protected $userId, $auth_group_id, $username, $menu, $userInfo;


    public function initialize()
    {
        parent::initialize();
        $this->userInfo = $this->isLogin('scan_user');
        if (!$this->userInfo) {
            header("Location: " . url('/login/index'));
            exit();
        }
        View::assign('userInfo', $this->userInfo);
        $this->userId = $this->userInfo ? $this->userInfo['id'] : 0;
        $this->username = $this->userInfo ? $this->userInfo['username'] : '';
        $this->auth_group_id = $this->userInfo['auth_group_id'];
        // 添加到rule表
        $href = cc_format(Request::controller() . '/' . Request::action());
        $data = ['href' => $href, 'title' => '未知', 'pid' => 43, 'level' => 3, 'created_at' => time()];
        Db::name('auth_rule')->extra('IGNORE')->insert($data);

        if (!$this->is_auth($href)) {
            die('权限不足,请联系管理员开通权限');
        }

        View::assign('href', $href);
        View::assign('title', env('website'));
    }

    // 权限判断
    private function is_auth($name)
    {

        if (in_array($this->username, explode(',', env('admins')))) return true;

        //管理员和开放地址,不鉴定权限
        $rules = Db::name('auth_group')->where('auth_group_id', $this->auth_group_id)->value('rules');

        if (Db::name('auth_rule')->where('href', $name)->value('is_open_auth') == 0) return true;
//var_dump(Db::name('auth_rule')->where('href', $name)->value('is_open_auth'));die;
        //其他地址需要鉴定权限
        $user_auth_rule = Db::name('auth_rule')->where('auth_rule_id', 'in', "({$rules})")->field('title,href,is_open_auth')->select()->toArray();
        $is_auth = false;
        foreach ($user_auth_rule as $v) {
            if (strtolower($name) == strtolower($v['href'])) {
                $is_auth = true;
            }
        }
        return $is_auth;

    }

    /**
     * 判断用户是否登录
     * @param name
     * @return int|array
     */
    public function is_login($name)
    {
        return session($name);
    }

    /**
     * 判断用户是否登录
     * @param $cookie_name
     * @return int|array
     */
    public function isLogin($cookie_name)
    {
        if (!$cookie_name) {
            return 0;
        }
        parse_str(think_decrypt(Cookie::get($cookie_name)), $arr);
        if (!$arr)
            return 0;
        return $arr;
    }

    public function getMyAppList()
    {
        $where[] = ['is_delete', '=', 0];

        //查询项目数据
        $projectArr = Db::table('app')->where($where)->field('id,name')->select()->toArray();
        $projectList = array_column($projectArr, 'name', 'id');
        return $projectList;
    }

    public function getMyCodeList()
    {
        $where[] = ['is_delete', '=', 0];

        //查询项目数据
        $projectArr = Db::table('code')->where($where)->field('id,name')->select()->toArray();
        return array_column($projectArr, 'name', 'id');
    }


    /**
     * 使用PHPEXECL导入
     *
     * @param string $file 文件地址
     * @param int $sheet 工作表sheet(传0则获取第一个sheet)
     * @param int $columnCnt 列数(传0则自动获取最大列)
     * @param array $options 操作选项
     *                          array mergeCells 合并单元格数组
     *                          array formula    公式数组
     *                          array format     单元格格式数组
     * @return array
     * @throws Exception
     */
    public function importExecl($file = '', $sheet = 0, $columnCnt = 0, &$options = [])
    {
        try {
            /* 转码 */
            //$file = iconv("gb2312", "utf-8", $file);

            if (empty($file) or !file_exists($file)) {
                return ['code' => 0, 'data' => [], 'msg' => '文件不存在!'];
            }
            /** @var Xlsx $objRead */
            $objRead = IOFactory::createReader('Xls');

            if (!$objRead->canRead($file)) {
                $objRead = new Csv();
                if (!$objRead->canRead($file)) {
                    return ['code' => 0, 'data' => [], 'msg' => '只支持导入Xls、Csv格式文件!'];
                }
            }

            /* 如果不需要获取特殊操作，则只读内容，可以大幅度提升读取Excel效率 */
            empty($options) && $objRead->setReadDataOnly(true);
            /* 建立excel对象 */
            $obj = $objRead->load($file);
            /* 获取指定的sheet表 */
            $currSheet = $obj->getSheet($sheet);

            if (isset($options['mergeCells'])) {
                /* 读取合并行列 */
                $options['mergeCells'] = $currSheet->getMergeCells();
            }

            if (0 == $columnCnt) {
                /* 取得最大的列号 */
                $columnH = $currSheet->getHighestColumn();
                /* 兼容原逻辑，循环时使用的是小于等于 */
                $columnCnt = Coordinate::columnIndexFromString($columnH);
            }

            /* 获取总行数 */
            $rowCnt = $currSheet->getHighestRow();
            $data = [];

            /* 读取内容 */
            for ($_row = 0; $_row <= $rowCnt; $_row++) {
                $isNull = true;
                for ($_column = 1; $_column <= $columnCnt; $_column++) {
                    $cellName = Coordinate::stringFromColumnIndex($_column);
                    $cellId = $cellName . $_row;
                    $cell = $currSheet->getCell($cellId);

                    if (isset($options['format'])) {
                        /* 获取格式 */
                        $format = $cell->getStyle()->getNumberFormat()->getFormatCode();
                        /* 记录格式 */
                        $options['format'][$_row][$cellName] = $format;
                    }

                    if (isset($options['formula'])) {
                        /* 获取公式，公式均为=号开头数据 */
                        $formula = $currSheet->getCell($cellId)->getValue();

                        if (0 === strpos($formula, '=')) {
                            $options['formula'][$cellName . $_row] = $formula;
                        }
                    }

                    if (isset($format) && 'm/d/yyyy' == $format) {
                        /* 日期格式翻转处理 */
                        $cell->getStyle()->getNumberFormat()->setFormatCode('yyyy/mm/dd');
                    }

                    $data[$_row][$cellName] = trim($currSheet->getCell($cellId)->getFormattedValue());

                    if (!empty($data[$_row][$cellName])) {
                        $isNull = false;
                    }
                }

                /* 判断是否整行数据为空，是的话删除该行数据 */
                if ($isNull) {
                    unset($data[$_row]);
                }
            }
            return ['code' => 1, 'data' => array_values($data), 'msg' => ''];
        } catch (\Exception $e) {
            return ['code' => 0, 'data' => [], 'msg' => $e->getMessage()];
        }
    }

    // 批量审核
    public function batch_audit_that($request, $table)
    {
        $ids = $request->param('ids');
        $check_status = $request->param('check_status');
        $this->addUserLog('mobsfscan', "批量审核数据[$ids]");
        if (!$ids) {
            return $this->apiReturn(0, [], '请先选择要审核的数据');
        }
        $map[] = ['id', 'in', $ids];

        if (Db::name($table)->where($map)->update(['check_status' => $check_status, 'update_time' => date('Y-m-d H:i:s', time())])) {
            return $this->apiReturn(1, [], '审核成功');
        } else {
            return $this->apiReturn(0, [], '审核失败');
        }
    }

    // 批量删除
    public function batch_del_that($request, $table)
    {
        $ids = $request->param('ids');
        $this->addUserLog($table, "批量删除数据[$ids]");
        if (!$ids) {
            return $this->apiReturn(0, [], '请先选择要删除的数据');
        }
        $map[] = ['id', 'in', $ids];

        if (Db::name($table)->where($map)->delete()) {
            return $this->apiReturn(1, [], '删除成功');
        } else {
            return $this->apiReturn(0, [], '删除失败');
        }
    }

    // 添加用户操作日志
    public function addUserLog($type, $content)
    {
        UserLogModel::addLog($this->userInfo['username'], $type, $content);
    }
}