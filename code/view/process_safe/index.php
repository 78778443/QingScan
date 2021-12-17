{include file='public/head' /}
<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
?>

<?php
$searchArr = [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
    ],
    'btnArr' => [
        ['text' => '添加守护进程', 'ext' => [
            "href" => url('add'),
            "class" => "btn btn-outline-success"
        ]],
        ['text' => '查看进程', 'ext' => [
            "href" => url('showProcess'),
            "class" => "btn btn-outline-info"
        ]]
    ]

]; ?>
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
                    <th>value</th>
                    <th>备注</th>
                    <th>状态</th>
                    <th style="width: 200px">操作</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php echo $value['key'] ?></td>
                        <td><?php echo $value['value'] ?></td>
                        <td><?php echo $value['note'] ?></td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input changCheckBoxStatus" data-id="<?php echo $value['id'] ?>"
                                       type="checkbox" <?php echo $value['status'] == 0 ? '' : 'checked'; ?>>
                            </div>
                        </td>
                        <td>
                            <a href="<?php echo url('edit', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-success">编辑</a>
                            <a href="<?php echo url('del', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    <input type="hidden" id="to_examine_url" value="<?php echo url('to_examine/process_safe') ?>">
    {include file='public/to_examine' /}
    {include file='public/fenye' /}
</div>
{include file='public/footer' /}