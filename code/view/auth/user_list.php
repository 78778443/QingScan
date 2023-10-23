{include file='public/head' /}
<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
?>
<?php
$searchArr = [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' => "搜索的内容"],
    ],
    'btnArr' => [
        ['text' => '添加用户', 'ext' => [
            "href" => url('auth/userAdd'),
            "class" => "btn btn-sm btn-outline-secondary"
        ]
        ]
    ]]; ?>
{include file='public/search' /}
<div class="col-md-12 ">
    <div class="row tuchu">
        <!--            <div class="col-md-1"></div>-->
        <div class="col-md-12 ">
            <table class="table  table-hover table-sm table-borderless">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>用户名</th>
                    <th>昵称</th>
                    <th>用户组</th>
                    <th>用户组</th>
                    <th>性别</th>
                    <th>手机号码</th>
                    <th>邮箱地址</th>
                    <th>创建时间</th>
                    <th style="width: 200px">操作</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php echo $value['username'] ?></td>
                        <td><?php echo $value['nickname'] ?></td>
                        <td><?php echo $value['auth_group_name'] ?></td>
                        <td><?php echo $value['sex'] == 1 ? '男' : '女' ?></td>
                        <td><?php echo $value['phone'] ?></td>
                        <td><?php echo $value['email'] ?></td>
                        <td><?php echo $value['show_status'] ?></td>
                        <td><?php echo $value['created_at'] ?></td>
                        <td>
                            <a href="<?php echo url('auth/userEdit', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-secondary">编辑</a>
                            <a href="<?php echo url('auth/userDel', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    {include file='public/fenye' /}
</div>
{include file='public/footer' /}