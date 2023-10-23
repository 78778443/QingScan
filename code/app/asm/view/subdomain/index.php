{include file='public/head' /}
<div class="col-md-1 " style="padding-right:0;">
    {include file='public/asmLeftMenu' /}
</div>
<div class="col-md-11 " style="padding-left:0;">
    <?php
    $searchArr = [
        'action' => $_SERVER['REQUEST_URI'],
        'method' => 'get',
        'inputs' => [
            ['type' => 'text', 'name' => 'sub_domain', 'placeholder' => "baidu.com"],
        ], 'btnArr' => [

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
                    <th>子域名</th>
                    <th>域名</th>
                    <!--<th>Nmap扫描时间</th>-->
                    <th>创建时间</th>
                    <!--                    <td style="width: 70px">状态</td>-->
                    <th style="width: 200px">操作</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php echo $value['sub_domain'] ?></td>
                        <td><?php echo $value['domain'] ?></td>
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