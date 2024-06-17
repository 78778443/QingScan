{include file='public/head' /}
<div class="col-md-1 " style="padding-right: 0;" >
    {include file='public/blackLeftMenu' /}
</div>
<div class="col-md-11 " style="padding:0;">
<?php
$searchArr = [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' =>'输入搜索的内容'],
        ['type' => 'select', 'name' => 'host', 'options' => $host, 'frist_option' => '主机名称'],
        ['type' => 'select', 'name' => 'port', 'options' => $port, 'frist_option' => '端口号'],
        ['type' => 'select', 'name' => 'service', 'options' => $service, 'frist_option' => '组件名称'],
        ['type' => 'select', 'name' => 'check_status', 'options' => $check_status_list, 'frist_option' => '审计状态','frist_option_value'=>-1],
    ]]; ?>
{include file='public/search' /}

<div class="row">
    <div class="col-md-2">
        <div class="row tuchu">
            <?php foreach ($classify as $value) { ?>
                <table class="table">
                    <tr>
                        <th colspan="2"><?php echo $value[0] ?></th>
                    </tr>
                    <?php foreach ($value[1] as $val) { ?>
                        <tr>
                            <td>
                                <a href=" {$_SERVER['REQUEST_URI']}&{$value[2]}={$val['name']} ?>"><?php echo $val['name'] ?></a>
                            </td>
                            <td><?php echo $val['num'] ?></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
        </div>
    </div>
    <div class="col-md-10 ">
        <div class=" tuchu">
            <table class="table  table-hover table-sm table-borderless">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>HostName</th>
                    <th>Port</th>
                    <th>端口类型</th>
                    <th>服务名称</th>
                    <th>创建时间</th>
                    <th style="width: 200px">操作</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['id'] ?></td>
                        <td><a href=""><?php echo $value['host'] ?></a></td>
                        <td><?php echo $value['port'] ?></td>
                        <td><?php echo $value['type'] ?></td>
                        <td><?php echo $value['service'] ?></td>
                        <td><?php echo $value['create_time'] ?></td>
                        <td>
                            <a href="<?php echo url('host_port/details',['id'=>$value['id']])?>"
                               class="btn btn-sm btn-outline-secondary">查看详情</a>
                            <a href="<?php echo url('host_port/del',['id'=>$value['id']])?>" class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        {include file='public/fenye' /}
    </div>
</div></div>
<script>
    /*$("#starScan").click(function () {
        id = 100;
        $.get("/index.php?s=host/_start_scan&url_id=" + id, function (result) {
            alert("操作成功")
            location.reload();
        });
    });*/
</script>
{include file='public/footer' /}