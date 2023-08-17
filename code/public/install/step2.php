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
<body style="background-color: #eeeeee;">
<div class="container">
    <div class="row" >
        <div class="col-md-3"></div>
        <div class="col-md-6" style="background-color:#fff;margin: 20px;padding:20px;border: 1px dotted  #ccc;border-radius: 10px;">

            <form class="form-horizontal" action='step3.php' method='post'>
                <h3 style=" color: #aaa;">配置数据库</h3>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text">数据库地址</span>
                    <input type="text" class="form-control" name='DB_HOST' value='mysql_addr' aria-label="Username">
                </div>
                <br>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text">数据库端口</span>
                    <input type="text" class="form-control" name='DB_PORT' value='3306' aria-label="Username">
                </div><br>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text">数据库账户</span>
                    <input type="text" class="form-control" name='DB_USER' value='root' aria-label="DB_USER">
                </div><br>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text">数据库密码</span>
                    <input type="text" class="form-control" name='DB_PASS' value='123' aria-label="DB_PASS">
                </div><br>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text">数据库名称</span>
                    <input type="text" class="form-control" name='DB_NAME' value='QingScan' aria-label="DB_NAME">
                </div><br>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text">数据字符集</span>
                    <input type="text" class="form-control" name='DB_CHARSET' value='utf8mb4' aria-label="DB_CHARSET">
                </div>
                <br>
                <h3 style=" color: #aaa;">添加管理员</h3>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text">账户</span>
                    <input type="text" class="form-control" name='username' placeholder="例如:admin" required aria-label="DB_CHARSET">
                </div><br>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text">密码</span>
                    <input type="text" class="form-control" name='password'  placeholder="" required  aria-label="DB_CHARSET">
                </div> <br>
                <input type='submit' class="btn btn-outline-info" value='下一步'/>

            </form>

        </div>
        <div class="col-md-3"></div>
    </div>
</div>
</body>
</html>