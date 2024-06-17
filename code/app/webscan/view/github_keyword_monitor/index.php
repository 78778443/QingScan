{include file='public/head' /}
<div class="col-md-1 " style="padding-right: 0;" >
    {include file='public/blackLeftMenu' /}
</div>
<div class="col-md-11 " style="padding:0;">
<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
?>


    <?php
    $searchArr = [
        'action' => $_SERVER['REQUEST_URI'],
        'method' => 'get',
        'inputs' => [
            ['type' => 'text', 'name' => 'search', 'placeholder' =>'search'],
        ],
        'btnArr' => [
            ['text' => '添加', 'ext' => [
                "href" => url('add'),
                "class" => "btn btn-sm btn-outline-secondary"
            ]]
        ]];

    ?>
    {include file='public/search' /}

<div class="col-md-12 ">
    <div class="row tuchu">
        <div class="col-md-12 ">
            <table class="table  table-hover table-sm table-borderless">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>关键字</th>
                    <th>添加时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php echo $value['title'] ?></td>
                        <td><?php echo $value['create_time'] ?></td>
                        <td>
<!--                            <a href="--><?php //echo url('edit',['id'=>$value['id']])?><!--"-->
<!--                               class="btn btn-sm btn-outline-secondary">编辑</a>-->
                            <a href="<?php echo url('del',['id'=>$value['id']])?>" class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    {include file='public/fenye' /}
</div></div>
{include file='public/footer' /}