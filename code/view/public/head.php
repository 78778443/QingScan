<html>
<head>
    <title><?php echo $title ?? 'QunarSec' ?></title>
    <link rel="shortcut icon" href="/static/favicon.svg" type="image/x-icon"/>
    <script src="/static/js/jquery.min.js"></script>
    <link href="/static/bootstrap-5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/css/qingscan.css" rel="stylesheet">
    <script src="/static/bootstrap-5.1.3/js/bootstrap.min.js"></script>
</head>
<body style="background-color: #eeeeee; ">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary" style="padding: 0px;">

    <div class="container">

        <div class="collapse navbar-collapse" id="navbarsExample08">
            <ul class="navbar-nav me-auto">
                <li class="nav-item "><a class="nav-link" id="home" aria-current="page"
                                         href="/index/index.html">
                        <img src="/icon/home.svg" style="width:21px;">
                        主页</a></li>
                <li class="nav-item"><a class="nav-link" id="webscan" href="/webscan/index.html" aria-expanded="false">
                        <img src="/icon/webscan.svg" style="width:21px;">
                        网站扫描</a>
                </li>
                <li class="nav-item"><a class="nav-link" id="codeaudit" href="/code/index.html">
                        <img src="/icon/code.svg" style="width:21px;">代码审计 </a></li>
                <li class="nav-item"><a class="nav-link" id="cveuse" href="/vulnerable/index.html">
                        <img src="/icon/cve.svg" style="width:21px;">
                        0Day </a></li>
                <li class="nav-item"><a class="nav-link" id="asm" href="/asm/domain/index.html">
                        <img src="/icon/asm.svg" style="width:21px;">
                        资产发现 </a></li>
                <li class="nav-item  dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown08" data-bs-toggle="dropdown"
                       aria-expanded="false">
                        <img src="/icon/setting.svg" style="width:21px;">
                        系统管理</a>
                    <ul class="dropdown-menu" aria-labelledby="dropdown08" style="z-index: 999999;">
                        <li>
                            <a class="dropdown-item" href="/auth/user_list.html" style="">
                                用户列表 </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/auth/auth_group_list.html" style="">
                                角色管理 </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/auth/auth_rule.html" style="">
                                菜单管理 </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/config/index.html" style="">
                                配置管理 </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="/user_log/index.html" style="">
                                登录日志 </a>
                        </li>

                    </ul>
                </li>
            </ul>

            <div class="text-end">
                <ul class="nav navbar-nav navbar-right hidden-sm">
                    <li class="nav-item dropdown ">
                        <a href="#" class="nav-link dropdown-toggle1" id="dropdown08" data-bs-toggle="dropdown"
                           aria-expanded="false">
                            <img class="navbar-brand"
                                 style="height: 40px;width: 40px;margin: 0px;border: white solid 2px;border-radius: 40px;padding: 0px;"
                                 src="/static/images/user-face.jpg">
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdown08">
                            <li>
                                <a class="dropdown-item" href="{:url('/auth/user_info')}">
                                    个人资料
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{:url('/auth/user_password')}">
                                    修改密码
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{:url('/login/logout')}">
                                    退出登录
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row" >

