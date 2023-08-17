<?php

namespace app\asm\controller;

use app\controller\Common;
use app\model\UrlsModel;
use think\facade\Db;
use think\facade\View;
use think\Request;


class Urls extends Common
{

    public function index(Request $request)
    {
        $pageSize = 15;
        $search = $request->param('search');
        $where = [];
        if (!empty($search))   $where[] = ['url', 'like', "%{$search}%"];

        $list = Db::table('asm_urls')->where($where)->order("id", 'desc')->paginate([
            'list_rows' => $pageSize,//每页数量
            'query' => $request->param(),
        ]);
        $data['list'] = $list->items();
        // 获取分页显示
        $data['page'] = $list->render();

        $data['projectList'] = $this->getMyAppList();

        return View::fetch('index', $data);
    }


    public function add()
    {
        $data['app_list'] = Db::table('app')->select()->toArray();
        return View::fetch('add', $data);
    }

    public function _add(Request $request)
    {
        $data = $request->post();
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $data['user_id'] = $this->userId;
        }
        UrlsModel::addData($data);
        $this->success('添加成功', 'index');
    }

    public function _add_api_url(Request $request)
    {
        $data = $request->post();
        if ($this->auth_group_id != 5 && !in_array($this->userId, config('app.ADMINISTRATOR'))) {
            $data['user_id'] = $this->userId;
        }
        UrlsModel::addData($data);
        $this->success('添加成功', 'index');
    }

    public function getHeader()
    {
        $urlList = true;
        while ($urlList) {
            $urlList = Db::table('asm_urls')->where(['response_header' => null])->limit(100)->field('id,url')->select()->toArray();
            foreach ($urlList as $item) {
                $header = get_headers($item['url']);

                $data = [];
                foreach ($header as $key => $value) {
                    if (strpos($value, 'Date: Sat,') !== false) {
                        unset($header[$key]);
                    }
                }


                $data['content'] = json_encode(array_values($header));
                $data['hash'] = md5(json_encode($header));

                if (Db::table('text')->where(['hash' => $data['hash']])->find() == null) {
                    Db::table('text')->insert($data);
                }
                Db::table('asm_urls')->where(['id' => $item['id']])->update(['response_header' => $data['hash']]);

            }
        }
    }

    public function updatefile()
    {

        while (true) {
            $urlList = Db::table('asm_urls')->where(['file_name' => null])->limit(10)->field('id,url')->select()->toArray();

            foreach ($urlList as &$value) {
                if ($value['url'][strlen($value['url']) - 1] != '/') {
                    $value['file_name'] = basename($value['url']);
                } else {
                    $value['file_name'] = 'dir';
                }


                Db::table('asm_urls')->save($value);

            }
            sleep(3);
        }

    }

    public function del(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        if (!$id) {
            $this->error('参数不能为空');
        }
        $map[] = ['id', '=', $id];
        
        if (Db::name('urls')->where($map)->update(['is_delete' => 1])) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }


    // 批量删除
    public function batch_del(Request $request){
        $ids = $request->param('ids');
        if (!$ids) {
            return $this->apiReturn(0,[],'请先选择要删除的数据');
        }
        $map[] = ['id','in',$ids];
        
        if (Db::name('urls')->where($map)->update(['is_delete' => 1])) {
            return $this->apiReturn(1,[],'批量删除成功');
        } else {
            return $this->apiReturn(0,[],'批量删除失败');
        }
    }
}
