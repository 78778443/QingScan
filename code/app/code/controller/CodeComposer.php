<?php


namespace app\code\controller;


use app\controller\Common;
use think\facade\Db;
use think\facade\View;
use think\Request;

class CodeComposer extends Common
{
    public function index(Request $request){
        $pageSize = 20;
        $where = [];
        $search = $request->param('search');
        if (!empty($search)) {
            $where[] = ['name|version|source|authors','like',"%{$search}%"];
        }

        $code_id = $request->param('code_id');
        if ($code_id) {
            $where[] = ['code_id','=',$code_id];
        }
        $list = Db::table('code_composer')->where($where)->order("id", 'desc')->paginate([
            'list_rows'=> $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        foreach ($data['list'] as &$v) {
            $v['code_name'] = Db::table('code')->where('id',$v['code_id'])->value('name');
        }
        $data['page'] = $list->render();
        $data['projectList'] = $this->getMyCodeList();
        return redirect('/web/');
    }

    // 批量删除
    public function batch_del(Request $request){
        return $this->batch_del_that($request,'code_composer');
    }

    /**
     * 获取Composer详情（API接口）
     */
    public function detail(Request $request)
    {
        $id = $request->param('id', 0, 'intval');
        if (empty($id)) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }

        $info = Db::table('code_composer')->find($id);
        if (empty($info)) {
            return json(['code' => 0, 'msg' => '数据不存在']);
        }

        $info['code_name'] = Db::table('code')->where('id', $info['code_id'])->value('name');
        return json(['code' => 1, 'data' => $info]);
    }
}