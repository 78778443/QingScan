{include file='public/head' /}
<?php
$searchArr = [
    'action' => url('app_crawlergo/index'),
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
                <th>url</th>
                <th>扫描时间</th>
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
                    <td class="AutoNewline"><?php echo $value['url'] ?></td>
                    <td><?php echo $value['create_time']; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
{include file='public/fenye' /}
{include file='public/footer' /}