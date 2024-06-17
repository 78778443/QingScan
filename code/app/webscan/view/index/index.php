{include file='public/head' /}
<div class="col-md-1 " style="padding-right: 0;" >
    {include file='public/blackLeftMenu' /}
</div>
<div class="col-md-11 " style="padding:0;">
    <?php
    $searchArr = [
        'action' => url('index/index'),
        'method' => 'get',
        'inputs' => [
            ['type' => 'select', 'name' => 'statuscode', 'options' => $statuscodeArr, 'frist_option' => '状态码'],
            ['type' => 'select', 'name' => 'cms', 'options' => $cmsArr, 'frist_option' => 'CMS系统'],
            ['type' => 'select', 'name' => 'server', 'options' => $serverArr, 'frist_option' => '服务'],
        ],
        'btnArr' => [
            ['text' => '添加', 'ext' => [
                "class" => "btn btn-sm btn-outline-secondary",
                "data-bs-toggle" => "modal",
                "data-bs-target" => "#exampleModal",
            ]]
        ]]; ?>
    {include file='public/search' /}
    <div class="row tuchu">
        <div class="col-md-12 ">
            <form class="row g-3">
                <div class="col-auto">
                    <a href="javascript:;>" onclick="suspend_scan(1)"
                       class="btn btn-sm btn-outline-secondary">启用扫描</a>
                </div>
                <div class="col-auto">
                    <a href="javascript:;>" onclick="suspend_scan(2)"
                       class="btn btn-sm btn-outline-secondary">暂停扫描</a>
                </div>
                <div class="col-auto">
                    <a href="javascript:;" onclick="again_scan()"
                       class="btn btn-sm btn-outline-secondary">重新扫描</a>
                </div>
                <div class="col-auto">
                    <a href="javascript:;" onclick="batch_del()"
                       class="btn btn-sm btn-outline-danger">批量删除</a>
                </div>
            </form>
            <table class="table  table-hover table-sm table-borderless">
                <thead class="table-light">
                <tr>
                    <th style="width:70px;">
                        <label>
                            <input type="checkbox" value="-1" onclick="quanxuan(this)">
                            ID</label>
                    </th>
                    <th>名称</th>
                    <th>漏洞</th>
                    <th>资产</th>
                    <th>创建时间</th>
                    <th style="width: 200px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td>
                            <label>
                                <input type="checkbox" class="ids" name="ids[]" value="<?php echo $value['id'] ?>">
                                <?php echo $value['id'] ?></label>
                        </td>
                        <td class="ellipsis-type">
                            <a href="<?= $value['url'] ?? '' ?>" title="<?= $value['url'] ?? '' ?>"
                               target="_blank"><?= $value['name'] ?? '' ?> </a>
                        </td>
                        <td>
                            <?php echo $value['awvs_num'] + $value['xray_num'] + $value['sqlmap_num'] + $value['vulmap_num'] ?>
                        </td>
                        <td>
                            <?php echo $value['urls_num'] + $value['namp_num'] + $value['sqlmap_num'] + $value['vulmap_num'] ?>
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($value['create_time'])) ?></td>
                        <td>
                            <a href="<?php echo url('details', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-secondary">查看详情</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
                <?php if (empty($list)) { ?>
                    <tr>
                        <td colspan="18" class="text-center">暂无目标</td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        {include file='public/fenye' /}
    </div>
    {include file='index/add_modal' /}
    {include file='index/set_modal' /}

    <script>
        function suspend_scan(status) {
            var child = $('.table').find('input[type="checkbox"]');
            var ids = ''
            child.each(function (index, item) {
                if (item.value != -1 && item.checked) {
                    if (ids == '') {
                        ids = item.value
                    } else {
                        ids = ids + ',' + item.value
                    }
                }
            })

            $.ajax({
                type: "post",
                url: "<?php echo url('index/suspend_scan')?>",
                data: {ids: ids, status: status},
                dataType: "json",
                success: function (data) {
                    alert(data.msg)
                    if (data.code == 1) {
                        window.setTimeout(function () {
                            location.reload();
                        }, 2000)
                    }
                }
            });
        }

        function batch_del() {
            var child = $('.table').find('input[type="checkbox"]');
            var ids = ''
            child.each(function (index, item) {
                if (item.value != -1 && item.checked) {
                    if (ids == '') {
                        ids = item.value
                    } else {
                        ids = ids + ',' + item.value
                    }
                }
            })

            $.ajax({
                type: "post",
                url: "<?php echo url('index/batch_del')?>",
                data: {ids: ids},
                dataType: "json",
                success: function (data) {
                    alert(data.msg)
                    window.setTimeout(function () {
                        location.reload();
                    }, 1000)
                }
            });
        }


    </script>
</div>

{include file='public/footer' /}