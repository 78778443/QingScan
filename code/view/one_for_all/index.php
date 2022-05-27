{include file='public/head' /}
<?php
$searchArr = [
    'action' => url('one_for_all/index'),
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
                <th width="70">
                    <label>
                        <input type="checkbox" value="-1" onclick="quanxuan(this)">全选
                    </label>
                </th>
                <th>ID</th>
                <th>所属项目</th>
                <th>域名</th>
                <th>端口</th>
                <th>ip</th>
                <th>状态</th>
                <th>创建时间</th>
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
                    <td><?php echo $value['app_name'] ?></td>
                    <td><?php echo $value['subdomain'] ?></td>
                    <td><?php echo $value['port'] ?></td>
                    <td><?php echo $value['ip'] ?></td>
                    <td><?php echo $value['status'] ?></td>
                    <td><?php echo $value['create_time'] ?></td>
                    <td>
                        <a href="<?php echo url('one_for_all/del',['id'=>$value['id']])?>" class="btn btn-sm btn-outline-danger">删除</a>
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