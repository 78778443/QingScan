<?php
declare(strict_types=1);

namespace app\api;

/**
 * API 基础控制器
 * 统一 JSON 输出，不继承 app\controller\Common（其 initialize 会写 auth_rule 表并 die）
 */
class BaseController extends \app\BaseController
{
    /**
     * 分页 JSON 输出
     *
     * @param \think\db\Query $query 已包含筛选条件、未分页的查询构造器
     * @param int $pageSize 每页数量
     * @param callable|null $callback 可选，接收分页 items 数组，返回加工后的 items 数组（用于补充 app_name 等字段）
     * @return \think\response\Json
     */
    protected function paginateJson($query, int $pageSize = 20, ?callable $callback = null)
    {
        try {
            $paginate = $query->paginate($pageSize, false);
            $items    = $paginate->items();
            if ($callback) {
                $items = call_user_func($callback, $items);
            }
            return $this->apiReturn(
                1,
                $items,
                '',
                $paginate->total(),
                $paginate->lastPage(),
                $paginate->currentPage()
            );
        } catch (\Throwable $e) {
            return $this->apiReturn(0, [], $e->getMessage());
        }
    }

    /**
     * 覆写：父类会把空数组转成 ''，SPA 需要始终返回数组
     */
    protected function apiReturn($status, $data = [], $msg = '', $count = 0, $total_page = 0, $current_page = 0)
    {
        $result = [
            'code' => $status,
            'msg' => $msg,
            'data' => $data ?? [],
            'count' => $count ?? 0,
            'total_page' => $total_page ?? 0,
            'current_page' => $current_page ?? 0,
        ];
        return json($result);
    }
}
