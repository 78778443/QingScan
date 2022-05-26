{include file='public/head' /}
<?php
$searchArr = [
    'action' => url('user_log/index'),
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' => "搜索的内容"],
    ],
    'btnArr' => [
        ['text' => '清空日志',
            'ext' => [
                "href" => url('user_log/clear_all'),
                "class" => "btn btn-outline-danger"
            ]
        ]
    ]];
?>
{include file='public/search' /}
<div class="row tuchu">
    <!--            <div class="col-md-1"></div>-->
    <div class="col-md-12 ">
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>用户名</th>
                <th>类型</th>
                <th>详情</th>
                <th>ip</th>
                <th>登录时间</th>
            </tr>
            </thead>
            <?php foreach ($list as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['username'] ?></td>
                    <td><?php echo $value['type'] ?></td>
                    <td><?php echo $value['content'] ?></td>
                    <td><?php echo $value['ip'] ?></td>
                    <td><?php echo $value['create_time']; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
{include file='public/fenye' /}
{include file='public/footer' /}