<?php


namespace app\controller;


class Base
{

    public function __construct()
    {

        define("__TPL__", __APP__ . "/views");
        //        $this->checkLogin();
    }

    private function checkLogin()
    {

    }

    public function login()
    {
        $this->show('public/login');
    }


    /**
     * 加载模板文件
     *
     * @param $tplPath
     */
    public function show($tplPath, $data = [])
    {
        $filePath = __APP__ . "/views/{$tplPath}.php";
        if (!is_readable($filePath)) {
            echo '模板文件' . $filePath . '不存在!';
            die;
        }

        foreach ($data as $key => $val) {
            $$key = $val;
        }

        include_once $filePath;
    }

    public function Location($url = null)
    {
        $location = null;
        if (!$url) {
            $url = $_SERVER['PHP_SELF'];
        } elseif (substr($url, 0, 1) == '?') {
            $url = $_SERVER['PHP_SELF'] . $url;
        }
        if (substr($url, 0, 7) === 'http://' or substr($url, 0, 8) === 'https://') {
            $location = $url;
        } else {
            $port = null;
            if ($_SERVER['SERVER_PORT'] == 443) {
                $protocol = 'https://';
            } else {
                $protocol = 'http://';
                $checkPoint = explode(":", $_SERVER['HTTP_HOST']);
                if (count($checkPoint) == 0) {
                    $port = ':' . $_SERVER['SERVER_PORT'];
                }
            }
            $dir = dirname($_SERVER['SCRIPT_NAME']);
            if ($dir === "/") {
                $dir = "";
            }
            if (substr($url, 0, 2) === './') {
                if ($dir) {
                    $location = $protocol . $_SERVER['HTTP_HOST'] . $port . '/' . $dir . '/' . basename($url);
                } else {
                    $location = $protocol . $_SERVER['HTTP_HOST'] . $port . '/' . basename($url);
                }
            } elseif (substr($url, 0, 1) === "/") {
                $location = $protocol . $_SERVER['HTTP_HOST'] . $port . $url;
            } else {
                $location = $protocol . $_SERVER['HTTP_HOST'] . $port . $dir . '/' . $url;
            }
        }
        header("Location: $location");
        exit();

    }
}