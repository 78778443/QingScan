{include file='public/head' /}
<?php
$searchArr = [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' => "search"],
    ],
    'btnArr' => [
        ['text' => '添加', 'ext' => [
            "href" => url('config/add'),
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
                    <th>key</th>
                    <th>name</th>
                    <th>value</th>
                    <th style="width: 200px">操作</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php echo $value['key'] ?></td>
                        <td><?php echo $value['name'] ?></td>
                        <td><?php echo $value['value'] ?></td>
                        <td>
                            <a href="<?php echo url('config/edit',['id'=>$value['id']])?>"
                               class="btn btn-sm btn-outline-success">编辑</a>
                            <a href="<?php echo url('config/del',['id'=>$value['id']])?>" class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    {include file='public/fenye' /}
</div>
{include file='public/footer' /}