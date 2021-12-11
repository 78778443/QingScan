<?php
require_once "common.php";
header("content-type:text/html;charset=utf-8");
$path = $_SERVER['HTTP_REFERER'];
if (!basename($path) == 'step2.php') {
    echo basename($path);
    exit('非法请求!');
}
include "../../vendor/autoload.php";

use think\facade\Db;

?>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>轻松渗透测试系统</title>
        <link href="../../static/bootstrap-5.1.3/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <div class="container">
        <?php
        error_reporting(E_ALL);
        //检查数据库参数是否正确，修改系统配置文件
        writingConf();

        //从SQL文件中提取SQL语句
        $sqlArr = getSQLArr();

        //批量执行SQL语句
//        batchExecuteSql($sqlArr);

        addOldData();
        ?>
    </div>
    </body>
    </html>

<?php

function batchExecuteSql($sqlArr)
{
    foreach ($sqlArr as $sql) {
        $result = Db::execute($sql);
        if ($result === 0) {
//            echo "执行SQL语句成功:<pre>{$sql}</pre><br>";
        } elseif (strstr($sql, "INSERT") && $result === 1) {
//            echo "执行SQL语句成功:<pre>{$sql}</pre><br>";
        } else {
            echo "<h2 style='color: red'> 执行SQL语句失败:</h2><code>{$sql}</code><br>";
        }
    }
}

function addOldData()
{
    $str = $_POST['password'] . $_POST['username'];
    $password = '' === $str ? '' : md5(md5(sha1($str) . 'xt1l3a21uo0tu2oxtds3wWte23dsxix2d3in7yuhui32yuapatmdsnnzdazh1612ongxxin2z') . '###xt');;
    //导入最新的数据格式
    $sql = "UPDATE  user SET username='{$_POST['username']}',password='$password' ORDER BY id ASC LIMIT 1";
    echo $sql;
    Db::execute($sql);
//    if (Db::execute($sql) === 1) {
        echo "导入数据成功!" . PHP_EOL;
        echo "<form action='/'>
                <input class=\"btn btn-outline-success\" type='submit' value='进入首页'/>
              </form>";
        file_put_contents('install.lock', '');
//    }
}

function getSqlArr()
{
    $str = file_get_contents("./qingscan.sql");
    //匹配删表语句
    $zhengze = "/DROP.*;/Us";
    preg_match_all($zhengze, $str, $shanbiao);
    //匹配表格数据
    $zhengze = "/CREATE.*;/Us";
    preg_match_all($zhengze, $str, $jianbiao);
    //匹配插入语句
    $zhengze = "/INSERT.*;/Us";
    preg_match_all($zhengze, $str, $charu);

    $arr = array_merge($shanbiao[0], $jianbiao[0], $charu[0]);
    array_unshift($arr, "SET FOREIGN_KEY_CHECKS = 0;");
    array_push($arr, "SET FOREIGN_KEY_CHECKS = 1;");

    return $arr;
}

function writingConf()
{
    $link = mysqli_connect($_POST['DB_HOST'], $_POST['DB_USER'], $_POST['DB_PASS']);
    if (!$link) {
        echo "<script>";
        echo "alert('数据库信息有误');";
        echo "window.history.back();";
        echo "</script>";
    }
    $config = require('../../config/database.php');
    $config['connections']['mysql']['hostname'] = $_POST['DB_HOST'];
    $config['connections']['mysql']['username'] = $_POST['DB_USER'];
    $config['connections']['mysql']['password'] = $_POST['DB_PASS'];
    $config['connections']['mysql']['database'] = $_POST['DB_NAME'];
    $config['connections']['mysql']['charset'] = $_POST['DB_CHARSET'];

    $database = "<?php \n";
    $database .= 'return ' . var_export($config, true) . ';';
    $database .= "\n?>";


    if (!file_put_contents('../../config/database.php', $database)) {
        die("写入配置文件失败!");
    }

    Db::setConfig($config);
}