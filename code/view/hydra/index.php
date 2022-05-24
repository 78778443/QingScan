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
        {include file='public/batch_del' /}
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th width=70>
                    <label>
                        <input type="checkbox" value="-1" onclick="quanxuan(this)">全选
                    </label>
                </th>
                <th>ID</th>
                <th>所属项目</th>
                <th>host</th>
                <th>type</th>
                <th>username</th>
                <th>状态</th>
                <th style="width: 200px">操作</th>
            </tr>
            </thead>
            <?php foreach ($list as $value) { ?>
                <tr>
                    <td>
                        <label>
                            <input type="checkbox" class="ids" name="ids[]" value="<?php echo $value['id'] ?>">
                        </label>
                    </td>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $projectList[$value['app_id']]['name'] ?></td>
                    <td><?php echo $value['host'] ?></td>
                    <td><?php echo $value['type'] ?></td>
                    <td><?php echo $value['username'] ?></td>
                    <td><?php echo date('Y-m-d H:i:s', substr($value['create_time'],0,10)) ?></td>
                    <td>
                        <!--<a href="<?php /*echo url('xray/details',['id'=>$value['id']])*/?>"
                           class="btn btn-sm btn-outline-primary">查看漏洞</a>-->
                        <a href="<?php echo url('hydra/del',['id'=>$value['id']])?>" class="btn btn-sm btn-outline-danger">删除</a>
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