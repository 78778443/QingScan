{include file='public/head' /}
<div class="col-md-12 ">
    <?php
    $searchArr = [
        'action' => url('app/index'),
        'method' => 'get',
        'inputs' => [
            ['type' => 'select', 'name' => 'statuscode', 'options' => $statuscodeArr, 'frist_option' => '状态码'],
            ['type' => 'select', 'name' => 'cms', 'options' => $cmsArr, 'frist_option' => 'CMS系统'],
            ['type' => 'select', 'name' => 'server', 'options' => $serverArr, 'frist_option' => '服务'],
        ],
        'btnArr' => [
            ['text' => '添加', 'ext' => [
                "class" => "btn btn-outline-success",
                "data-bs-toggle"=>"modal" ,
                "data-bs-target"=>"#exampleModal",
            ]]
        ]]; ?>
    {include file='public/search' /}


<div class="row tuchu">
    <!--            <div class="col-md-1"></div>-->



    <div class="row tuchu">
        <div class="col-md-12 ">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>名称</th>
                    <th>CMS</th>
                    <th>状态码</th>
                    <th>服务组件</th>
                    <th style="width: 70px">状态</th>
                    <th>是否存在waf</th>
                    <th>创建时间</th>
                    <th>awvs</th>
                    <th>whatweb</th>
                    <th>subdomain</th>
                    <th>icon图标</th>
                    <th>截图</th>
                    <th>xray</th>
                    <th>dirmap</th>
                    <th>是否内网</th>
                    <th style="width: 200px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['id'] ?></td>
                        <td class="ellipsis-type">
                            <a href="<?php echo $value['url'] ?>" target="_blank">
                                <?php echo $value['name'] ?>
                            </a>
                        </td>
                        <td><?php
                            if (isset($value['cms']) && is_array(json_decode($value['cms'], true))) {
                                echo implode("|", json_decode($value['cms'], true));
                            }
                            ?></td>
                        <td><?php echo $value['statuscode'] ?? '' ?></td>
                        <td><?php echo $value['server'] ?? '' ?></td>
                        <td><?php echo $statusArr[$value['status']] ?? '' ?></td>
                        <td><?php echo $value['is_waf'] ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($value['create_time'])) ?></td>
                        <td><?php echo date('m-d H:i', strtotime($value['awvs_scan_time'])) ?></td>
                        <td><?php echo date('m-d H:i', strtotime($value['whatweb_scan_time'])) ?></td>
                        <td><?php echo date('m-d H:i', strtotime($value['subdomain_scan_time'])) ?></td>
                        <td>
                            <img src="/<?php echo $value['icon']?>" alt="">
                        </td>
                        <td>
                            <img src="/<?php echo $value['url_screenshot']?>" alt="">
                        </td>
                        <td><?php echo date('m-d H:i', strtotime($value['xray_scan_time'])) ?></td>
                        <td><?php echo date('m-d H:i', strtotime($value['dirmap_scan_time'])) ?></td>
                        <td><?php echo $value['is_intranet'] ?></td>
                        <td>
                            <a href="javascript:;" onclick="start_agent(<?php echo $value['id']?>)"
                               class="btn btn-sm btn-outline-success">启动代理</a>
                            <a href="<?php echo url('details', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-primary">查看详情</a>
                                <a href="<?php echo url('app/qingkong', ['id' => $value['id']]) ?>"
                                   onClick="return confirm('确定要清空数据重新扫描吗?')"
                                   class="btn btn-sm btn-outline-warning">重新扫描</a>
                            <a href="<?php echo url('app/del', ['id' => $value['id']]) ?>"
                                   onClick="return confirm('确定要删除吗?')"
                               class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

        </div>
    </div>
    {include file='public/fenye' /}
</div>

<style>
    .modal-dialog {
        width: 600px;
    }
</style>
<!-- Modal -->
{include file='app/add_modal' /}
{include file='app/set_modal' /}

{include file='public/footer' /}
<script>
    function start_agent(id) {
        $.ajax({
            type: "post",
            url: "<?php echo url('start_agent')?>",
            data: {id: id},
            dataType: "json",
            success: function (data) {
                alert(data.msg)
            }
        });
    }
</script>
