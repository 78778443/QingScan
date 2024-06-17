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
        ], 'btnArr' => [
            ['text' => '添加', 'ext' => [
                "class" => "btn btn-sm btn-outline-secondary",
                "data-bs-toggle" => "modal",
                "data-bs-target" => "#exampleModal",
            ]]
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
                    <th>主机</th>
                    <th>域名</th>
                    <th>主体信息</th>
                    <th>创建时间</th>
                    <th style="width: 200px">操作</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php echo $value['host'] ?></td>
                        <td><?php echo $value['domain'] ?></td>
                        <td><?php echo '-' ?></td>
                        <td><?php echo $value['create_time'] ?></td>
                        <td>
                            <a href="{:URL('_addTarget',['id'=>$value['id']])}"
                               class="btn btn-sm btn-outline-warning">添加扫描</a>
                            <a href="#" class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        {include file='public/fenye' /}

    </div>
    {include file='/domain/add_modal' /}

</div>
{include file='public/footer' /}