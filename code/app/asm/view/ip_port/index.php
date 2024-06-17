{include file='public/head' /}
<div class="col-md-1 " style="padding-right: 0;" >
    {include file='public/asmLeftMenu' /}
</div>
<div class="col-md-11 " style="padding:0;">
    <?php
    $searchArr = [
        'action' => $_SERVER['REQUEST_URI'],
        'method' => 'get',
        'inputs' => [
            ['type' => 'text', 'name' => 'domain', 'placeholder' => "baidu.com"],
        ]
    ];
    ?>
    {include file='public/search' /}

    <div class="row tuchu">
        <!--            <div class="col-md-1"></div>-->
        <div class="col-md-12 ">
            <table class="table  table-hover table-sm table-borderless">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>IP</th>
                    <th>端口</th>
                    <th>位置</th>
                    <th>ISP</th>
                    <th>创建时间</th>
                    <th style="width: 200px">操作</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php echo $value['ip'] ?></td>
                        <td><?php echo $value['port'] ?></td>
                        <td><?php echo $value['location'] ?></td>
                        <td><?php echo $value['isp'] ?></td>
                        <td><?php echo $value['create_time'] ?></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        {include file='public/fenye' /}

    </div>

</div>
{include file='public/footer' /}