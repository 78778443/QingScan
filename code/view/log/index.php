{include file='public/head' /}

<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
?>

<?php
$searchArr = [
    'action' =>  $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' => "搜索"],
    ],
    'btnArr' => [
        ['text' => '清空日志',
            'ext' => [
                "href" => url('log/clear_all'),
                "class" => "btn btn-outline-danger"
            ]
        ]
    ]]; ?>
{include file='public/search' /}
<div class="col-md-12 ">
    <div class="row tuchu">
        <div class="col-md-12 ">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>项目名称</th>
                    <th>内容</th>
                    <th>记录时间</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php echo $value['app'] ?></td>
                        <td><?php echo $value['content'] ?></td>
                        <td><?php echo $value['create_time'] ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    <input type="hidden" id="to_examine_url" value="<?php echo url('to_examine/xray')?>">

    {include file='public/to_examine' /}
    {include file='public/fenye' /}
</div>
{include file='public/footer' /}