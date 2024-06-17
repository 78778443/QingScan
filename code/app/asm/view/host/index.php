{include file='public/head' /}
<div class="col-md-1 " style="padding-right: 0;" >
    {include file='public/blackLeftMenu' /}
</div>
<div class="col-md-11 " style="padding:0;">
<?php
$searchArr = [
    'action' =>  $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'name', 'placeholder' => "HostName"],
        ['type' => 'text', 'name' => 'url', 'placeholder' => "URL"],
        ['type' => 'select', 'name' => 'app_id', 'options' => $projectList, 'frist_option' => '项目列表']
    ]]; ?>
{include file='public/search' /}

<div class="row tuchu">
    <!--            <div class="col-md-1"></div>-->
    <div class="col-md-12 ">
        <table class="table  table-hover table-sm table-borderless">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>所属项目</th>
                <th>域名</th>
                <th>HostName</th>
                <th>国家</th>
                <th>省份</th>
                <th>城市</th>
                <th>ISP</th>
                <!--<th>Nmap扫描时间</th>-->
                <th>创建时间</th>
                <!--                    <td style="width: 70px">状态</td>-->
                <th style="width: 200px">操作</th>
            </tr>
            </thead>
            <?php foreach ($list as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $projectList[$value['app_id']] ?></td>
                    <td><?php echo $value['domain'] ?></td>
                    <td><?php echo $value['host'] ?></td>
                    <td><?php echo $value['country'] ?></td>
                    <td><?php echo $value['region'] ?></td>
                    <td><?php echo $value['city'] ?></td>
                    <td><?php echo $value['isp'] ?></td>
                    <td><?php echo $value['create_time'] ?></td>
                    <!--                        <td>--><? //= $statusArr[$value['scan_status']] ?><!--</td>-->
                    <td>
                        <!--<a href="<?php /*echo url('code/bug_list',['id'=>$value['id']])*/?>"
                           class="btn btn-sm btn-outline-secondary">查看详情</a>-->
                        <a href="#" class="btn btn-sm btn-outline-danger">删除</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

</div>

{include file='public/fenye' /}</div>
{include file='public/footer' /}