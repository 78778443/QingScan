{include file='public/head' /}
<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
$typeArr = ['黑盒扫描','白盒审计','专项利用','其他','信息收集'];
?>

<?php
$searchArr = [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'select', 'name' => 'type', 'options' => $typeArr, 'frist_option' => '工具类型'],
    ],
    'btnArr' => [
        ['text' => '添加守护进程', 'ext' => [
            "href" => url('add'),
            "class" => "btn btn-sm btn-outline-secondary"
        ]],
        ['text' => '查看进程', 'ext' => [
            "href" => url('showProcess'),
            "class" => "btn btn-sm btn-outline-secondary"
        ]]
    ]

]; ?>
{include file='public/search' /}
<div class="col-md-12 ">
    <div class="row tuchu">
        <!--            <div class="col-md-1"></div>-->
        <div class="col-md-12 ">
            <form class="row g-3" id="frmUpload" action="" method="post" enctype="multipart/form-data">
                <div class="col-auto">
                    <a href="javascript:;" onclick="update_status(1)"
                       class="btn btn-sm btn-outline-secondary">启用</a>
                </div>

                <div class="col-auto">
                    <a href="javascript:;" onclick="update_status(2)"
                       class="btn btn-sm btn-outline-secondary">禁用</a>
                </div>
            </form>
            <table class="table  table-hover table-sm table-borderless">
                <thead class="table-light">
                <tr>
                    <th>
                        <label>
                            <input type="checkbox" value="-1" onclick="quanxuan(this)">全选
                        </label>
                    </th>
                    <th>ID</th>
                    <th>key</th>
                    <th>value</th>
                    <th>工具类型</th>
                    <th>备注</th>
                    <th>状态</th>
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
                        <td><?php echo $value['key'] ?></td>
                        <td><?php echo $value['value'] ?></td>
                        <td><?php echo $typeArr[$value['type']] ?></td>
                        <td><?php echo $value['note'] ?></td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input changCheckBoxStatus" data-id="<?php echo $value['id'] ?>"
                                       type="checkbox" <?php echo $value['status'] == 0 ? '' : 'checked'; ?>>
                            </div>
                        </td>
                        <td>
                            <a href="<?php echo url('edit', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-secondary">编辑</a>
                            <a href="<?php echo url('del', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    <input type="hidden" id="to_examine_url" value="<?php echo url('to_examine/process_safe') ?>">
    {include file='public/to_examine' /}
    {include file='public/fenye' /}
</div>
{include file='public/footer' /}
<script>
    function quanxuan(obj){
        var child = $('.table').find('.ids');
        child.each(function(index, item){
            if (obj.checked) {
                item.checked = true
            } else {
                item.checked = false
            }
        })
    }

    function update_status(type){
        var child = $('.table').find('.ids');
        var ids = ''
        child.each(function(index, item){
            if (item.value != -1 && item.checked) {
                if (ids == '') {
                    ids = item.value
                } else {
                    ids = ids+','+item.value
                }
            }
        })

        $.ajax({
            type: "post",
            url: "<?php echo url('process_safe/update_status')?>",
            data: {ids: ids,type:type},
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