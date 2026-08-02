<?php


namespace app\code\controller;


use app\controller\Common;
use app\Request;
use think\facade\Db;
use think\facade\View;

class CodeCheck extends Common
{
    public function index(Request $request)
    {
        $pageSize = 20;

        //查询列表数据
        $list = Db::table('code_check')
            ->order('id', 'desc')
            ->paginate([
                'list_rows' => $pageSize,
                'query' => $request->param(),
            ]);

        $data['list'] = $list->items();
        $data['page'] = $list->render();
        $data['authList'] = [];
        $data['projectList'] = [];
        $data['projectArr'] = [];

        foreach ($data['list'] as &$value) {
            if (!empty($value['content'])) {
                preg_match_all("/\/tmp.*?\.php/", $value['content'], $result);
                $result[0] = array_unique($result[0] ?? []);
                $tempStr = implode("\n", $result[0] ?? []);
                $tempStr = preg_replace("/\/tmp\/.*-\d{2}\//", "/", $tempStr);
                $value['bugFile'] = $tempStr;
            } else {
                $value['bugFile'] = '';
            }
            $value['name'] = '';
            $value['web_url'] = '';
        }

        return redirect('/web/');
    }

    public function bug_detail()
    {
        $id = getParam('id');
        if (!$id) {
            return redirect('index','参数错误');
        }
        $where[] = ['id','=',$id];

        $info = Db::table('awvs_vuln')->where($where)->find();
        if (!$info) {
            return redirect('index','0');
        }
        $upper_id = Db::name('awvs_vuln')->where('id','<',$id)->order('id','desc')->value('id');
        $info['upper_id'] = $upper_id?:$id;
        $lower_id = Db::name('awvs_vuln')->where('id','>',$id)->order('id','asc')->value('id');
        $info['lower_id'] = $lower_id?:$id;

        $data['info'] = $info;
        //var_dump($info);exit;
        return redirect('/web/');
    }

    /**
     * 获取代码检查详情（API接口）
     */
    public function detail(Request $request)
    {
        $id = $request->param('id', 0, 'intval');
        if (empty($id)) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }

        $info = Db::table('code_check')->find($id);
        if (empty($info)) {
            return json(['code' => 0, 'msg' => '数据不存在']);
        }

        return json(['code' => 1, 'data' => $info]);
    }
}