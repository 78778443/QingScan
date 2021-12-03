<?php


namespace app\model;


use think\facade\Db;
use function Sodium\add;

class GithubKeywordMonitorModel extends BaseModel
{
    public static function keywordMonitor()
    {
        ini_set('max_execution_time', 0);

        systemLog('cd /data/tools/reptile && python3 ./githubKeywordMonitor.py');

        sleep(1200);
    }
}