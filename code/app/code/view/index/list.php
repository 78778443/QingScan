{include file='public/head' /}

<div class="col-md-1 " style="padding-right: 0;" >
    {include file='public/whiteLeftMenu' /}
</div>
<div class="col-md-11 " style="padding: 0" >
    <?php
    $dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
    $searchArr = [
        'action' => $_SERVER['REQUEST_URI'],
        'method' => 'get',
        'inputs' => [
            ['type' => 'select', 'name' => 'Folder', 'options' => $dengjiArr, 'frist_option' => '危险等级'],
            ['type' => 'text', 'name' => 'search', 'placeholder' => "搜索"],
        ],
        'btnArr' => [
            ['text' => '添加', 'ext' => [
                "class" => "btn btn-sm btn-outline-secondary",
                "data-bs-toggle" => "modal",
                "data-bs-target" => "#staticBackdrop",
            ]]
        ]]; ?>
    {include file='public/search' /}
    <div class="row tuchu">
        <div class="col-md-12 ">
                <form class="row g-3" id="frmUpload" action="<?php echo url('app/batch_import') ?>" method="post"
                      enctype="multipart/form-data">
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
                        <th>
                            <label>
                                <input type="checkbox" value="-1" onclick="quanxuan(this)">ID
                            </label>
                        </th>
                        <th>名称</th>
                        <th>Fortify</th>
                        <th>Semgrep</th>
                        <th>mobsfscan</th>
                        <th>murphysec</th>
                        <th>webshell</th>
                        <th>扫描状态</th>
                        <th style="width: 200px">操作</th>
                    </tr>
                    </thead>
                    <?php foreach($list as $value) { ?>
                        <tr>
                            <td>
                                <label>
                                    <input type="checkbox" class="ids" name="ids[]" value="<?php echo $value['id'] ?>">
                                </label>
                             <?php echo $value['id'] ?></td>
                            <td><a href="<?php echo $value['ssh_url'] ?>"><?php echo $value['name'] ?></a></td>
                            <td>
                                <a title="扫描时间:<?php echo $value['scan_time'] ?>"
                                   href="<?php echo url('code/bug_list', ['code_id' => $value['id']]); ?>">
                                    <?php echo $fortifyNum[$value['id']] ?? 0 ?>
                                </a>
                            </td>
                            <td>
                                <a title="扫描时间:<?php echo $value['semgrep_scan_time'] ?>"
                                   href="<?php echo url('code/semgrep_list', ['code_id' => $value['id']]); ?>"><?php echo $semgrepNum[$value['id']] ?? 0 ?>
                                </a>
                            </td>
                            <td>
                                <a title="扫描时间:<?php echo $value['mobsfscan_scan_time'] ?>"
                                   href="<?php echo url('mobsfscan/index', ['code_id' => $value['id']]); ?>"><?php echo $mobsfscanNum[$value['id']] ?? 0; ?>
                                </a>
                            </td>
                            <td>
                                <a title="扫描时间:<?php echo $value['murphysec_scan_time'] ?>"
                                   href="<?php echo url('murphysec/index', ['code_id' => $value['id']]); ?>"><?php echo $murphysecNum[$value['id']] ?? 0; ?>
                                </a>
                            </td>
                            <td>
                                <a title="扫描时间:<?php echo $value['webshell_scan_time'] ?>"
                                   href="<?php echo url('code_webshell/index', ['code_id' => $value['id']]); ?>"><?php echo $hemaNum[$value['id']] ?? 0 ?>
                                </a>
                            </td>

                            <td><?php echo $value['status']; ?></td>
                            <td>
                                <a href="<?php echo url('code/index/details', ['id' => $value['id']]) ?>"
                                   class="btn btn-sm btn-outline-secondary">查看详情</a>
                                <?php
                                if ($value['is_online'] == 1) {
                                    ?>
                                    <a href="<?php echo url('code/index/edit_modal', ['id' => $value['id']]) ?>"
                                       class="btn btn-sm btn-outline-secondary">编辑</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (empty($list)) { ?>
                        <tr>
                            <td colspan="16" class="text-center">暂无目标</td>
                        </tr>
                    <?php } ?>
                </table>

        </div>

        {include file='public/fenye' /}

    </div>
</div>
{include file='index/tools' /}
{include file='index/add_modal' /}
{include file='public/footer' /}


<script>
    function quanxuan(obj) {
        var child = $('.table').find('input[type="checkbox"]');
        child.each(function (index, item) {
            if (obj.checked) {
                item.checked = true
            } else {
                item.checked = false
            }
        })
    }

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
            url: "<?php echo url('code/suspend_scan')?>",
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
            url: "<?php echo url('code/batch_del')?>",
            data: {ids: ids},
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

    function again_scan() {
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
            url: "<?php echo url('again_scan')?>",
            data: {ids: ids},
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
</script>