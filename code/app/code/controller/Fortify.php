<?php

namespace app\code\controller;

use app\controller\Common;
use think\facade\Db;
use think\facade\View;
use think\Request;

class Fortify extends Common
{
    public function index(Request $request){
        $page = $request->param('page', 1);
        $search = $request->param('search', '');
        $pageSize = 25;
        $pid = $request->param('code_id');
        $Folder = $request->param('Folder');
        $Category = $request->param('Category');
        $filetype = $request->param('filetype');
        $Primary_filename = $request->param('Primary_filename');
        $check_status = $request->param('check_status', '-2');

        //准备查询条件
        $where = ['is_delete' => 0];
        $where = $pid ? array_merge($where, ['code_id' => $pid]) : $where;
        $where = $Primary_filename ? array_merge($where, ['Primary_filename' => $Primary_filename]) : $where;
        $where = !empty($Folder) ? array_merge($where, ['Folder' => $Folder]) : $where;
        $where = !empty($Category) ? array_merge($where, ['Category' => $Category]) : $where;
        if (!empty($search)) {
            $where[] = ['Primary', 'like', "%{$search}%"];
        }

        if (in_array($check_status, [0, 1, 2])) {
            $where = array_merge($where, ['check_status' => $check_status]);
        }
        $map[] = ['is_delete', '=', 0];


        $fortifyApi = Db::table('fortify')->where($where)->order('id', 'desc');;
        $fortifyApi = $fortifyApi->where("Folder != 'Low'");
        if (!empty($filetype)) {
            $fortifyApi= $fortifyApi->where('Primary_filename', 'like', "%.$filetype");
        }
        $fortifyCountApi = Db::table('fortify')->where($where)->where("Folder != 'Low'");
        //获取分类分组
        $categoryList = Db::table('fortify')->where($map)->where("Folder != 'Low'")->group('Category')->field('Category')->select()->toArray();
        $CategoryList = array_column($categoryList, 'Category');
        //查询项目数据
        $projectArr = Db::table('code')->where($map)->select()->toArray();
        $projectArr = array_column($projectArr, null, 'id');
        //获取文件分组
        $fileList = Db::table('fortify')->where("Folder != 'Low'")->where($map)->field('Primary_filename')->group('Primary_filename')->select()->toArray();
        $fileList = array_map('basename', array_column($fileList, 'Primary_filename'));
        //查询项目列表
        $fortifyProjectList = Db::table('fortify')->where($map)->where("Folder != 'Low'")->field('code_id')->group('code_id')->select()->toArray();
        $fortifyProjectList = array_column($fortifyProjectList, 'code_id');
        $fortifyProjectList = Db::table('code')->whereIn('id', $fortifyProjectList)->field('id,name')->select()->toArray();
        $fortifyProjectList = array_column($fortifyProjectList, 'name', 'id');
        $objData = $fortifyApi->order('id', 'desc')->paginate(['list_rows' => $pageSize, 'query' => $request->param()]);
        $list = $objData->items();
        $pageRaw = $objData->render();
        //获取列表数据
        //$list = $fortifyApi->order('id', 'desc')->limit($pageSize)->page($page)->select()->toArray();
        foreach ($list as &$value) {
            $value['Source'] =  is_json($value['Source']) ? json_decode($value['Source'], true) : $value['Source'];
            $value['Primary'] = json_decode($value['Primary'], true);
        }
        // 获取分页显示
        //$pageRaw = $fortifyApi->paginate($pageSize)->render();

        foreach ($projectArr as $k => $v) {
            // 判断类型
            if (preg_match('/gitee\.com/', $v['ssh_url'])) {   // 码云
                $path = substr($v['ssh_url'], strripos($v['ssh_url'], ':') + 1, strlen($v['ssh_url']));
                $projectArr[$k]['domain_name'] = "https://gitee.com/{$path}/blob/main";
            } elseif (preg_match('/github\.com/', $v['ssh_url'])) {    // github
                $path = substr($v['ssh_url'], strripos($v['ssh_url'], 'github.com/') + 1, strlen($v['ssh_url']));
                $projectArr[$k]['domain_name'] = "https://github.com/{$path}/blob/main";
            }
        }
        //分配数据
        $data = [
            'search' => $search,
            'GET' => $_GET,
            'page' => $pageRaw,
            'list' => $list,
            'count' => $fortifyCountApi->count(), 'pageSize' => $pageSize,
            'CategoryList' => $CategoryList,
            'projectArr' => $projectArr,
            'fortifyProjectList' => $fortifyProjectList,
            'fileList' => $fileList,
            'check_status_list' => ['未审计', '有效漏洞', '无效漏洞']
        ];

        return View::fetch('index', $data);
    }


    public function details(Request $request)
    {
        $id = $request->param('id');
        if (!$id) {
            $this->error('参数不能为空');
        }
        $where[] = ['id', '=', $id];
        $map = [];

        $info = Db::table('fortify')->where($where)->find();
        if (!$info) {
            $this->error('数据不存在');
        }
        $info['Source'] = is_json($info['Source']) ? json_decode($info['Source'], true) : $info['Source'];
        $info['Primary'] = json_decode($info['Primary'], true);

        $upper_id = Db::name('fortify')->where('id', '<', $id)->where($map)->order('id', 'desc')->value('id');
        $info['upper_id'] = $upper_id ?: $id;
        $lower_id = Db::name('fortify')->where('id', '>', $id)->where($map)->order('id', 'asc')->value('id');
        $info['lower_id'] = $lower_id ?: $id;
        $data['info'] = $info;
        $projectArr = Db::table('code')->where($map)->select()->toArray();
        $data['projectArr'] = array_column($projectArr, null, 'id');
        return View::fetch('details', $data);
    }

    public function batch_audit(Request $request){
        return $this->batch_audit_that($request,'fortify');
    }

    public function del(Request $request)
    {
        $id = $request->param('id');
        $map[] = ['id', '=', $id];
        
        if (Db::name('fortify')->where($map)->delete()) {
            return redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->error('删除失败');
        }
    }

    // 批量删除
    public function batch_del(Request $request)
    {
        return $this->batch_del_that($request,'fortify');
    }
}