{include file='public/head' /}
<div class="col-md-1 " style="padding-right: 0;" >
    {include file='public/systemLeftMenu' /}
</div>
<div class="col-md-11 " style="padding:0;">
<?php
$colorArr = ['secondary','success'];
$statusDescArr = ['待执行','已执行'];
?>
    <div class="row tuchu">
        <div class="col-md-12 ">

            <table class="table  table-hover table-sm table-borderless">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>数据来源</th>
                    <th>队列名称</th>
                    <th>目标</th>
                    <th>状态</th>
                    <th>创建时间</th>
                    <th>更新时间</th>
                    <th>执行命令</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td>{$value['id']}</td>
                        <td>{$value['target_table']}</td>
                        <td>{$value['tool']}</td>
                        <td>{$value['target']}</td>
                        <td>
                            <span class="badge text-bg-{$colorArr[$value['status']]}">
                                {$statusDescArr[$value['status']]}
                            </span>
                            </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($value['create_time'])) ?></td>
                        <td><?php echo   $value['update_time']  ?></td>
                        <td><input class="form-control" value="<?php echo  'php think scan '.$value['tool'].' -vvv'  ?>" disabled /></td>
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

    <script>
        function start_agent(id) {
            $.ajax({
                type: "post",
                url: "<?php echo url('start_agent')?>",
                data: {id: id},
                dataType: "json",
                success: function (data) {
                    alert(data.msg)
                    if (data.code) {
                        window.setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                }
            });
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