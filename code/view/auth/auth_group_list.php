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
        ['text' => '添加用户组', 'ext' => [
            "href" => url('auth/authGroupAdd'),
            "class" => "btn btn-outline-success"
        ]
        ]
    ]]; ?>
{include file='public/search' /}
<div class="col-md-12 ">
    <div class="row tuchu">
        <!--            <div class="col-md-1"></div>-->
        <div class="col-md-12 ">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>用户组名</th>
                    <th>添加时间</th>
                    <th>修改时间</th>
                    <th>状态</th>
                    <th style="width: 200px">操作</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['auth_group_id'] ?></td>
                        <td><?php echo $value['title'] ?></td>
                        <td><?php echo date('Y-m-d H:i:s',$value['created_at']) ?></td>
                        <td><?php echo date('Y-m-d H:i:s',$value['update_time']) ?></td>
                        <td><?php echo $value['show_status'] ?></td>
                        <td>
                            <a href="<?php echo url('auth/authGroupAccess',['auth_group_id'=>$value['auth_group_id']])?>"
                               class="btn btn-sm btn-outline-primary">分配权限</a>
                            <a href="<?php echo url('auth/authGroupEdit',['auth_group_id'=>$value['auth_group_id']])?>"
                               class="btn btn-sm btn-outline-success">编辑</a>
                            <a href="<?php echo url('auth/authGroupDel',['auth_group_id'=>$value['auth_group_id']])?>" class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
{include file='public/footer' /}