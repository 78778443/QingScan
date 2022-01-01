<?php

if (file_exists("install.lock")) {
    echo "<link href='../../static/bootstrap-5.1.3/css/bootstrap.min.css' rel='stylesheet'>";
    echo "<div class='container'><div style='margin-top: 100px;'></div><a  href='/' class=' btn btn-outline-info'>网站已安装,请直接打开首页~</a></div>";
    die;
}

function getDirFileName($path): array
{
    $arr = array();
    $arr[] = $path;
    if (is_file($path)) {

    } else {
        if (is_dir($path)) {
            $data = scandir($path);
            if (!empty($data)) {
                foreach ($data as $value) {
                    if ($value != '.' && $value != '..') {
                        $sub_path = $path . "/" . $value;
                        $temp = getDirFileName($sub_path);
                        $arr = array_merge($temp, $arr);
                    }
                }
            }
        }
    }
    return $arr;
}