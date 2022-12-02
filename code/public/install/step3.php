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
        <title>QingScan 安装step3</title>
        <link href="../../static/bootstrap-5.1.3/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <div class="container">
        <div class="row">
            <div class="col-3"></div>
            <div class="col-6">
                <div style="height:160px"></div>
                <div>
                    <?php
                    error_reporting(E_ALL);
                    // 检查数据库参数是否正确，修改系统配置文件
                    writingConf();

                    //更新python配置
                    setPythonConfig();

                    try {
                        //从SQL文件中提取SQL语句
                        $sqlArr = getSQLArr();
                        //批量执行SQL语句
                        batchExecuteSql($sqlArr);
                    }catch (Exception $e){
                        echo $e->getMessage();
                        exit();
                    }

                    addOldData();
                    ?>
                </div>
            </div>
            <div class="col-3"></div>
        </div>
    </div>
    </body>
    </html>

<?php
function setPythonConfig(){
    $filename = '/data/tools/reptile/config.yaml';
    $arr = @yaml_parse_file($filename);
    if ($arr) {
        $arr['mysql']['host'] = $_POST['DB_HOST'];
        $arr['mysql']['port'] = (int)($_POST['DB_PORT']);
        $arr['mysql']['username'] = (string)$_POST['DB_USER'];
        $arr['mysql']['password'] = (string)$_POST['DB_PASS'];
        $arr['mysql']['database'] = (string)$_POST['DB_NAME'];
        yaml_emit_file($filename, $arr);
    }
}


function batchExecuteSql($sqlArr)
{;
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
    $sql = "UPDATE user SET username=?,password=? where id = 1";
    $result = Db::name('user')->where('id',1)->update(['username'=>$_POST['username'],'password'=>$password,'update_time'=>time()]);
    if ($result) {
        echo " <a class=\"btn btn-lg btn-outline-success\" href='/' >导入数据成功!,进入首页</a>";
        file_put_contents('install.lock', '');

        // 更新版本号
        $sqlPath = '/root/qingscan/docker/data';
        $fileNameList = getDirFileName($sqlPath);
        unset($fileNameList[count($fileNameList) - 1]);
        unset($fileNameList[count($fileNameList) - 1]);
        if (!empty($fileNameList)) {
            $filepath = $fileNameList[0];
            $filename = substr($filepath,strripos($filepath,'/')+1,strlen($filepath));
            $newVersion = substr($filename,0,strripos($filename,'.'));
            file_put_contents($sqlPath.'/update.lock',$newVersion);
        }
    } else {
        echo "<h2 style='color: red'> 数据导入失败:</h2><code>{$sql}</code><br>";
    }
}

function getSqlArr()
{
    $str = file_get_contents('./qingscan.sql');
    //匹配删表语句
    $zhengze = "/DROP.*;/Us";
    preg_match_all($zhengze, $str, $shanbiao);
    //匹配表格数据
    $zhengze = "/CREATE.*;/Us";
    preg_match_all($zhengze, $str, $jianbiao);
    //匹配插入语句
    $zhengze = "/INSERT.*;/Us";
    preg_match_all($zhengze, $str, $charu);
    //匹配添加字段语句
    $zhengze = "/ALTER TABLE.*;/Us";
    preg_match_all($zhengze, $str, $filed);
    $arr = array_merge($shanbiao[0], $jianbiao[0], $charu[0],$filed[0]);
    array_unshift($arr, "SET FOREIGN_KEY_CHECKS = 0;");
    array_push($arr, "SET FOREIGN_KEY_CHECKS = 1;");

    return $arr;
}

function writingConf()
{
    $link = mysqli_connect($_POST['DB_HOST'], $_POST['DB_USER'], $_POST['DB_PASS'],$_POST['DB_NAME'],$_POST['DB_PORT']);
    if (!$link) {
        $error = mysqli_connect_errno();
        echo "<script>";
        echo "alert('数据库信息有误，错误信息：{$error}');";
        echo "window.history.back();";
        echo "</script>";
    }
    $config = require('../../config/database.php');
    $config['connections']['mysql']['hostname'] = $_POST['DB_HOST'];
    $config['connections']['mysql']['hostport'] = $_POST['DB_PORT'];
    $config['connections']['mysql']['username'] = $_POST['DB_USER'];
    $config['connections']['mysql']['password'] = $_POST['DB_PASS'];
    $config['connections']['mysql']['database'] = $_POST['DB_NAME'];
    $config['connections']['mysql']['charset'] = $_POST['DB_CHARSET'];

    /*$config['connections']['kunlun']['hostname'] = $_POST['DB_HOST'];
    $config['connections']['kunlun']['hostport'] = $_POST['DB_PORT'];
    $config['connections']['kunlun']['username'] = $_POST['DB_USER'];
    $config['connections']['kunlun']['password'] = $_POST['DB_PASS'];
    $config['connections']['kunlun']['database'] = 'kunlun';
    $config['connections']['kunlun']['charset'] = $_POST['DB_CHARSET'];*/

    $database = "<?php \n";
    $database .= 'return ' . var_export($config, true) . ';';
    $database .= "\n?>";

    if (!file_put_contents('../../config/database.php', $database)) {
        die("写入配置文件失败!");
    }

    Db::setConfig($config);
}