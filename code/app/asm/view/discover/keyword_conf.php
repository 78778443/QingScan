{include file='public/head' /}
<div class="col-md-1 ">
    {include file='public/asmLeftMenu' /}
</div>
<div class="col-md-11 " style="padding:0;">
    <?php
    $searchArr = [
        'action' => $_SERVER['REQUEST_URI'],
        'method' => 'get',
        'inputs' => [
            ['type' => 'text', 'name' => 'keyword', 'placeholder' => "dedecms"],
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
            {include file='discover/sub_menu' /}
            <?php if (empty($fofa_user) || empty($fofa_token)) { ?>
                <div style="margin: 30px;">
                    <a class="btn btn-outline-warning"
                       href="{:URL('/config/index',['keyword'=>'fofa'])}">未配置FOFA账户,点击配置后方可使用</a>
                </div>
            <?php } else { ?>
                <table class="table  table-hover table-sm table-borderless">
                    <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>关键词</th>
                        <th>创建时间</th>
                        <th style="width: 200px">操作</th>
                    </tr>
                    </thead>
                    <?php foreach ($list as $value) { ?>
                        <tr>
                            <td><?php echo $value['id'] ?></td>
                            <td><?php echo $value['keyword'] ?></td>
                            <td><?php echo $value['create_time'] ?></td>
                            <td>
                                <a href="{:URL('_del_keyword',['id'=>$value['id']])}"
                                   class="btn btn-sm btn-outline-danger">删除</a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
        </div>
        {include file='public/fenye' /}

    </div>
    {include file='/discover/add_modal_keyword' /}

</div>
<script>
    $("#keyword_conf").addClass("active");
</script>
{include file='public/footer' /}