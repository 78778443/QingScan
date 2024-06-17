{include file='public/head' /}
<div class="col-md-1 " style="padding-right: 0;" >
    {include file='public/blackLeftMenu' /}
</div>
<div class="col-md-11 " style="padding:0;">
<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
?>
<div class="col-md-12 ">
    <?php
    $searchArr = [
        'action' => $_SERVER['REQUEST_URI'],
        'method' => 'get',
        'inputs' => [
            ['type' => 'text', 'name' => 'search', 'placeholder' => 'search'],
            ['type' => 'select', 'name' => 'app_id', 'options' => $projectList, 'frist_option' => '项目列表']
    ]]; ?>
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
                    <th>所属项目</th>
                    <th>target</th>
                    <th>http_status</th>
                    <th>plugins</th>
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
                        <td><?php echo $value['app_name'] ?></td>
                        <td><?php dump(json_decode($value['target'], true)[0]); ?></td>
                        <td><?php dump(json_decode($value['http_status'], true)[0]); ?></td>
                        <td><?php dump(json_decode($value['plugins'], true)[0]); ?></td>
                        <td><?php echo $value['create_time'] ?></td>
                        <td>
                            <!--                            <a href="-->
                            <?php //echo url('xray/details',['id'=>$value['id']])?><!--"-->
                            <!--                               class="btn btn-sm btn-outline-secondary">查看漏洞</a>-->
                            <a href="<?php echo url('whatweb/del', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    <input type="hidden" id="to_examine_url" value="<?php echo url('to_examine/xray') ?>">

    {include file='public/to_examine' /}
    {include file='public/fenye' /}
</div></div>
{include file='public/footer' /}