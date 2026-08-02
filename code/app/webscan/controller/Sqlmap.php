<?php


namespace app\webscan\controller;


use app\controller\Common;
use think\facade\Db;
use think\facade\View;
use think\Request;

class Sqlmap extends Common
{
    public function index(Request $request){
        $pageSize = 20;
        $where = [];
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['type|title|payload','like',"%{$search}%"];
        }

        $app_id = $request->param('app_id');
        if (!empty($app_id)) {
            $where[] = ['app_id','=',$app_id];
        }
        $list = Db::table('urls_sqlmap')->where($where)->order("id", 'desc')->paginate([
            'list_rows'=> $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$val) {
            $url = '';
            try {
                // 尝试从urls表获取URL
                $url = Db::name('urls')->where('id', $val['urls_id'])->value('url');
            } catch (\Exception $e) {
                // urls表不存在，尝试从asm_urls表获取
                try {
                    $url = Db::table('asm_urls')->where('id', $val['urls_id'])->value('url');
                } catch (\Exception $e2) {
                    // 两个表都不存在，URL留空
                }
            }
            $val['url'] = $url ?: '';
        }
        $data['page'] = $list->render();

        $data['projectList'] = $this->getMyAppList();

        return redirect('/web/webscan/sqlmap');
    }


    public function del(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        if (!$id) {
            $this->error('参数不能为空');
        }
        $map[] = ['id', '=', $id];
        
        if (Db::name('urls_sqlmap')->where($map)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }


    // 批量删除
    public function batch_del(Request $request){
        return $this->batch_del_that($request,'urls_sqlmap');
    }
}