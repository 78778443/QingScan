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
            ['type' => 'text', 'name' => 'keyword', 'value' => $keyword, 'placeholder' => "dedecms"],
        ], 'btnArr' => [

        ]
    ];
    ?>
    {include file='public/search' /}

    <div class="row tuchu">
        <div class="col-md-12 ">
            {include file='discover/sub_menu' /}

            <table class="table  table-hover table-sm table-borderless">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>标题</th>
                    <th>IP</th>
                    <th>host</th>
                    <th>端口</th>
                    <th>产品类型</th>
                    <th>创建时间</th>
                    <th  >操作</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php echo substr($value['title'], 0, 27) ?></td>
                        <td><?php echo $value['ip'] ?></td>
                        <td><?php echo $value['host'] ?></td>
                        <td><?php echo $value['port'] ?></td>
                        <td><?php echo $value['product_category'] ?></td>
                        <td><?php echo $value['create_time'] ?></td>
                        <td>
                            <a href="{:URL('_del',['id'=>$value['id']])}" class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        {include file='public/fenye' /}

    </div>

</div>
<script>
    $("#scan_result").addClass("active");
</script>
{include file='public/footer' /}