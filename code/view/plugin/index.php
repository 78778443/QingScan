{include file='public/head' /}
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
                "class" => "btn btn-outline-success"
            ]]
        ]];

    ?>
    {include file='public/search' /}

<div class="col-md-12 ">
    <div class="row tuchu">
        <div class="col-md-12 ">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>插件类型</th>
                    <th>插件名称</th>
                    <th>扫描类型</th>
                    <th>执行命令</th>
                    <th>工具位置</th>
                    <th>扫描结果类型</th>
                    <th>扫描结果位置</th>
                    <th>状态</th>
                    <th>添加时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <?php
                $typeArr = ['APP','HOST','CODE','URL'];
                foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php echo $value['type']==1?'插件扫描':'结果分析' ?></td>
                        <td><?php echo $value['name'] ?></td>
                        <td><?php echo $typeArr[$value['scan_type']] ?? '' ?></td>
                        <td><?php echo $value['cmd'] ?></td>
                        <td><?php echo $value['tool_path'] ?></td>
                        <td><?php echo $value['result_type'] ?></td>
                        <td><?php echo $value['result_file'] ?></td>
                        <td><?php echo $value['status']==1?'启用':'禁用' ?></td>
                        <td><?php echo $value['create_time'] ?></td>
                        <td>
                            <a href="<?php echo url('edit',['id'=>$value['id']])?>"
                               class="btn btn-sm btn-outline-success">编辑</a>
                            <a href="<?php echo url('del',['id'=>$value['id']])?>" class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    {include file='public/fenye' /}
</div>
{include file='public/footer' /}