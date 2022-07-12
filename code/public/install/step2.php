<?php
require_once "common.php";
header("content-type:text/html;charset=utf-8");
$path = $_SERVER['HTTP_REFERER'];
if (!basename($path) == 'step1.php') {
    echo basename($path);
    exit('非法请求!');
}
?>
<html>
<head>
    <title>QingScan 安装step2</title>
    <link href="../../static/bootstrap-5.1.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <h2>填写配置信息</h2>
            <form class="form-horizontal" action='step3.php' method='post'>
                数据库地址：<input class="form-control" type='text' name='DB_HOST' value='mysql_addr'/><br/>
                数据库端口号：<input class="form-control" type='text' name='DB_PORT' value='3306'/><br/>
                数据库用户名：<input class="form-control" type='text' name='DB_USER' value='root'/><br/>
                数据库密码：<input class="form-control" type='password' name='DB_PASS' value="123" /><br/>
                数据库名称：<input class="form-control" type='text' name='DB_NAME' value='QingScan'/><br/>
                数据库字符集：<input class="form-control" type='text' name='DB_CHARSET' value='utf8mb4'/><br/>

                <hr/>
                管理员：<input class="form-control" type='text' name='username' placeholder="例如:admin" required/><br/>
                管理员密码:<input class="form-control" type='password' name='password' placeholder="" required/><br/>
                <input type='submit' class="btn btn-outline-success" value='下一步'/>

            </form>

        </div>
        <div class="col-md-3"></div>
    </div>
</div>
</body>
</html>