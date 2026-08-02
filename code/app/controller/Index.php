<?php

namespace app\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\View;

class Index extends Common
{


    public function index()
    {
        return redirect('/web/');
    }

    public function tongji()
    {
        return redirect('/web/');
    }


    public function upgradeTips(){
        $result = curl_get(config('app.plugin_store.domain_name').'index/upgradeTips');
        $result = json_decode($result,true);
        if (!$result['code']) {
            return $this->apiReturn(0,[],'');
        }
        $path = \think\facade\App::getRootPath() . '../docker./data/update.lock';
        // 获取当前版本号
        $version = file_get_contents($path);
        $news_version = $result['data']['news_version'];
        if ($version < $news_version) {
            return $this->apiReturn(1,[],"当前系统最新版本为：{$news_version},是否升级");
        } else {
            return $this->apiReturn(0,[],"update.lock文件不存在");
        }
    }
}