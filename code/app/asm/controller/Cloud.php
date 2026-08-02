<?php

namespace app\asm\controller;

use app\BaseController;
use app\asm\model\CloudModel;
use think\facade\View;
use think\facade\Db;
use think\facade\Request;

class Cloud extends BaseController
{
    // 火山云列表页
    public function huoshan()
    {
        $pageSize = 20;
        $where = [];
        
        $list = Db::table('asm_cloud_huoshan')
            ->where($where)
            ->order('create_time desc')
            ->paginate([
                'list_rows' => $pageSize,
                'query' => Request::param(),
            ]);
        
        View::assign('list', $list->items());
        View::assign('page', $list);
        
        return redirect('/web/asm/host');
    }
    
    // 天翼云列表页
    public function tianyi()
    {
        $pageSize = 20;
        $where = [];
        
        // 获取团队筛选参数
        $teamId = Request::param('team_id', 'all', 'trim');
        if ($teamId !== 'all') {
            $where['team_id'] = $teamId;
        }
        
        $list = Db::table('asm_cloud_tianyi')
            ->where($where)
            ->order('create_time desc')
            ->paginate([
                'list_rows' => $pageSize,
                'query' => Request::param(),
            ]);
        
        View::assign('list', $list->items());
        View::assign('page', $list);
        View::assign('current_team', $teamId);
        
        return redirect('/web/asm/host');
    }
    
    // 阿里云列表页
    public function aliyun()
    {
        $pageSize = 20;
        $where = [];
        
        $list = Db::table('asm_cloud_aliyun')
            ->where($where)
            ->order('create_time desc')
            ->paginate([
                'list_rows' => $pageSize,
                'query' => Request::param(),
            ]);
        
        View::assign('list', $list->items());
        View::assign('page', $list);
        
        return redirect('/web/asm/host');
    }
    
    // 移动云列表页
    public function yidong()
    {
        $pageSize = 20;
        $where = [];
        
        $list = Db::table('asm_cloud_yidong')
            ->where($where)
            ->order('create_time desc')
            ->paginate([
                'list_rows' => $pageSize,
                'query' => Request::param(),
            ]);
        
        View::assign('list', $list->items());
        View::assign('page', $list);
        
        return redirect('/web/asm/host');
    }
    
    // 百度云列表页
    public function baidu()
    {
        $pageSize = 20;
        $where = [];
        
        $list = Db::table('asm_cloud_baidu')
            ->where($where)
            ->order('create_time desc')
            ->paginate([
                'list_rows' => $pageSize,
                'query' => Request::param(),
            ]);
        
        View::assign('list', $list->items());
        View::assign('page', $list);
        
        return redirect('/web/asm/host');
    }
 }