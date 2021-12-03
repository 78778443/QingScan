<html>
<head>
    <title>QingScan 扫描系统</title>
    <link rel="shortcut icon" href="/static/favicon.svg" type="image/x-icon"/>
    <script src="/static/js/jquery.min.js"></script>
    <!--    <script src="/static/js/bootstrap.min.js"></script>-->
    <link href="/static/bootstrap-5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="/static/bootstrap-5.1.3/js/bootstrap.min.js"></script>

    <style>
        header a:hover {
            text-decoration: none;
        }

        .qiantuchu {
            background-color: #eee;
            padding: 20px;
            margin-top: 10px;
            margin-bottom: 10px;
            margin-left: 5px;
            margin-right: 5px;
        }


        .tuchu {
            background-color: #ffffff;
            padding: 20px;
            margin-top: 10px;
            margin-bottom: 10px;
            margin-left: 5px;
            margin-right: 5px;
        }

        .fenye {
            background-color: #ffffff;
            padding: 20px;
            margin-top: 10px;
            margin-left: 5px;
            margin-right: 5px;
            margin-bottom: 80px;
        }

        .footer {
            background-color: #ffffff;
            padding: 10px;
        }

        .ellipsis-type {
            max-width: 360px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }
    </style>
</head>
<body style="background-color: #eeeeee; min-height: 1080px">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" aria-label="Tenth navbar example">
    <div class="container">
        <a class="navbar-brand" href="/">QingScan</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample08"
                aria-controls="navbarsExample08" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExample08">
            <ul class="navbar-nav me-auto">
                <?php foreach ($menu_list as $k => $v) { ?>
                    <li class="nav-item  <?php if ($k != 0) echo 'dropdown' ?>">
                        <?php if (isset($v['children'])) { ?>
                            <a class="nav-link dropdown-toggle" href="#" id="dropdown08" data-bs-toggle="dropdown"
                               aria-expanded="false"><?php echo $v['title'] ?></a>
                        <?php } else { ?>
                            <a class="nav-link" aria-current="page"
                               href="<?php echo url($v['href']) ?>"><?php echo $v['title'] ?></a>
                        <?php } ?>
                        <?php if (isset($v['children'])) { ?>
                            <ul class="dropdown-menu" aria-labelledby="dropdown08">
                                <?php foreach ($v['children'] as $key => $val) { ?>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo url($val['href']) ?>">
                                            <?php echo $val['title'] ?>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </li>
                <?php } ?>
            </ul>

            <div class="text-end">
                <ul class="nav navbar-nav navbar-right hidden-sm">
                    <li class="nav-item dropdown ">
                        <a href="#" class="nav-link dropdown-toggle" id="dropdown08" data-bs-toggle="dropdown"
                           aria-expanded="false"><?php echo $userInfo['nickname'] ?>
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu" aria-labelledby="dropdown08">
                            <li>
                                <a class="dropdown-item" href="{:url('auth/user_info')}">
                                    个人资料
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{:url('auth/user_password')}">
                                    修改密码
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{:url('login/logout')}">
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