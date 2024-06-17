{include file='public/head' /}
<div class="col-md-1 " style="padding-right: 0;" >
    {include file='public/systemLeftMenu' /}
</div>
<div class="col-md-11 " style="padding:0;">
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
                "class" => "btn btn-sm btn-outline-danger"
            ]
        ]
    ]];
?>
{include file='public/search' /}
<div class="row tuchu">
    <!--            <div class="col-md-1"></div>-->

        <table class="table  table-hover table-sm table-borderless">
            <thead class="table-light">
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
    {include file='public/fenye' /}
    </div>

</div>

{include file='public/footer' /}