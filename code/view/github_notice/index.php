{include file='public/head' /}
<div class="col-md-1 " style="padding-right: 0;" >
    {include file='public/vulLeftMenu' /}
</div>
<div class="col-md-11 " style="padding:0;">
<?php
$searchArr = [
    'action' => url('github_notice/index'),
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' => "搜索的内容"],
        //['type' => 'select', 'name' => 'app_id', 'options' => $projectList, 'frist_option' => '项目列表'],
    ]];
?>
{include file='public/search' /}

<div class="row tuchu">
    <!--            <div class="col-md-1"></div>-->
    <div class="col-md-12 ">
        {include file='public/batch_del' /}
        <table class="table  table-hover table-sm table-borderless">
            <thead class="table-light">
            <tr>
                <th width="70">
                    <label>
                        <input type="checkbox" value="-1" onclick="quanxuan(this)">全选
                    </label>
                </th>
                <th>ID</th>
                <th>title</th>
                <th>level</th>
                <th>cve_id</th>
                <th>cwes</th>
                <th>发布时间</th>
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
                    <td><?php echo $value['title'] ?></td>
                    <td><?php echo $value['level'] ?></td>
                    <td><?php echo $value['cve_id'] ?></td>
                    <td><?php echo $value['cwes'] ?></td>
                    <td><?php echo $value['github_release_date'] ?></td>
                    <td>
                        <a href="<?php echo url('github_notice/del',['id'=>$value['id']])?>" class="btn btn-sm btn-outline-danger">删除</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
<input type="hidden" id="to_examine_url" value="<?php echo url('to_examine/xray')?>">

{include file='public/to_examine' /}
{include file='public/fenye' /}</div>
{include file='public/footer' /}