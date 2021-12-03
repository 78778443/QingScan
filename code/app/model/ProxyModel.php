<?php

namespace app\model;

class ProxyModel extends BaseModel
{
    public static function freeAgent(){
        while (true) {
            ini_set('max_execution_time', 0);

            systemLog('cd /data/tools/reptile && python3 ./freeAgent.py');

            sleep(120);
        }
    }
}