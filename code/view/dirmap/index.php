{include file='public/head' /}
<?php
$searchArr = [
    'action' => url('hydra/index'),
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' => "搜索的内容"],
        ['type' => 'select', 'name' => 'app_id', 'options' => $projectList, 'frist_option' => '项目列表'],
    ]];
?>
{include file='public/search' /}
<div class="row tuchu">
    <!--            <div class="col-md-1"></div>-->
    <div class="col-md-12 ">
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>APP</th>
                <th>code</th>
                <th>size</th>
                <th>type</th>
                <th>url</th>
                <th>时间</th>
                <th style="width: 200px">操作</th>
            </tr>
            </thead>
            <?php foreach ($list as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['app_name'] ?></td>
                    <td><?php echo $value['code'] ?></td>
                    <td><?php echo $value['size'] ?></td>
                    <td><?php echo $value['type'] ?></td>
                    <td><?php echo $value['url'] ?></td>
                    <td><?php echo $value['create_time'] ?></td>
                    <td>
                        <a href="<?php echo url('xray/details',['id'=>$value['id']])?>"
                           class="btn btn-sm btn-outline-primary">查看漏洞</a>
                        <a href="<?php echo url('xray/del',['id'=>$value['id']])?>" class="btn btn-sm btn-outline-danger">删除</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
<input type="hidden" id="to_examine_url" value="<?php echo url('to_examine/xray')?>">

{include file='public/to_examine' /}
{include file='public/fenye' /}

{include file='public/footer' /}