{include file='public/head' /}


<div class="col-md-12 ">
    <?php
    $searchArr = [
        'action' => $_SERVER['REQUEST_URI'],
        'method' => 'get',
        'inputs' => [
            ['type' => 'text', 'name' => 'url', 'placeholder' => 'URL'],
        ],
        'btnArr' => [
            ['text' => '添加URL', 'ext' => [
                "href" => url('urls/add'),
                "class" => "btn btn-outline-success"
            ]]
        ]]; ?>
    {include file='public/search' /}


    <div class="row tuchu">
        <div class="col-md-12 ">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>URL</th>
                    <th>APP</th>
                    <th>ICP</th>
                    <th>邮箱</th>
                    <th>身份证号码</th>
                    <th>手机号码</th>
                    <th>sqlmap扫描时间</th>
                    <th>创建时间</th>
                    <!--<th>sqlmap</th>-->
                    <!--                    <td style="width: 70px">状态</td>-->
                    <th style="width: 200px">操作</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['id'] ?></td>
                        <td class="ellipsis-type"><a href="<?php echo $value['url'] ?>"
                                                     target="_blank"><?php echo $value['url'] ?></a></td>
                        <td>
                            <a href="<?php echo U('urls/index', ['app_id' => $value['app_id']]) ?>"><?php echo isset($appArr[$value['app_id']]) ? $appArr[$value['app_id']] : '' ?></a>
                        </td>
                        <td><?php echo $value['icp'] ?></td>
                        <td><?php echo $value['email'] ?></td>
                        <td><?php echo $value['id_card'] ?></td>
                        <td><?php echo $value['phone'] ?></td>
                        <td><?php
                            echo ($value['sqlmap_scan_time'] == "2000-01-01 00:00:00") ? "未扫描" : ((strtotime($value['sqlmap_scan_time']) > time()) ? '扫描失败' : $value['sqlmap_scan_time'])
                            ?></td>
                        <td><?php echo $value['create_time'] ?></td>
                        <!--<td><?php /*echo date('m-d H:i', strtotime($value['sqlmap_scan_time'])) */?></td>-->
                        <td>
                            <a href="<?php echo url('xray/details', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-primary">查看漏洞</a>
                            <a href="<?php echo url('urls/del', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    {include file='public/fenye' /}
</div>
{include file='public/footer' /}