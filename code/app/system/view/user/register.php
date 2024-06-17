<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?=env('website')?> 登录</title>

    <!-- Bootstrap core CSS -->
    <link href="/static/4.6/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body style="background-image: url(/static/images/login-bg.png);">
<div class="container">
    <div class="row" style="margin:100px;"></div>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <form class="form-signin"   action="<?php echo url('login/register') ?>" method="POST">
                <div class="text-center mb-4">
                    <img class="mb-4" src="/static/favicon.svg" alt="" width="72" height="72">
                    <h1 class="h3 mb-3 font-weight-normal">注册账号</h1>
                </div>

                <div class="form-label-group">
                    <input type="text" name="username"  class="form-control" placeholder="用户名" required=""
                           autofocus="">
                    <label ></label>
                    <label ></label>
                </div>

                <div class="form-label-group">
                    <input type="password" name="password" class="form-control" placeholder="密码" required="">
                    <label></label>
                    <label></label>
                </div>

                <div class="form-label-group">
                    <input type="text" name="nickname" class="form-control" placeholder="昵称" required="">
                    <label></label>
                    <label></label>
                </div>

                <div class="form-label-group">
                    <button class="btn btn-lg btn-outline-info btn-block" type="submit">注册</button>
                    <a href="<?php echo url('login/index')?>">返回登陆</a>
                </div>
            </form>
        </div>
        <div class="col-md-4"></div>
    </div>
</div>

</body>
</html>