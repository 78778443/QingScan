{include file='public/head' /}

<?php
$searchArr = [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' => "search"],
        ['type' => 'select', 'name' => 'code_id', 'options' => $projectList, 'frist_option' => '项目列表'],
    ]];
?>
{include file='public/search' /}


<div class="col-md-12 ">
    <div class="row tuchu">
        <div class="col-md-12 ">
            <form class="row g-3"
                  enctype="multipart/form-data">
                <div class="col-auto">
                    <a href="javascript:;" onclick="batch_repair()"
                       class="btn btn-outline-success">批量修复</a>
                </div>
                <div class="col-auto">
                    <a href="javascript:;" onclick="batch_del()"
                       class="btn btn-outline-danger">批量删除</a>
                </div>
            </form>
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th width="80">
                        <label>
                            <input type="checkbox" value="-1" onclick="quanxuan(this)">全选
                        </label>
                    </th>
                    <th>ID</th>
                    <th>所属项目</th>
                    <th>缺陷组件</th>
                    <th>处置建议</th>
                    <th>当前版本</th>
                    <th>最小修复版本</th>
                    <th>语言</th>
                    <th>修复状态</th>
                    <th>时间</th>
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
                        <td><?php echo $value['code_name'] ?></td>
                        <td><?php echo $value['comp_name'] ?></td>
                        <td><?php echo $show_level[$value['show_level']] ?></td>
                        <td><?php echo $value['version'] ?></td>
                        <td><?php echo $value['min_fixed_version'] ?></td>
                        <td><?php echo $value['language'] ?></td>
                        <td><?php echo $value['repair_status'] ?></td>
                        <td><?php echo $value['create_time'] ?></td>
                        <td>
                            <a href="<?php echo url('murphysec/del',['id'=>$value['id']])?>" class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    {include file='public/fenye' /}
</div>
{include file='public/footer' /}

<script>
    function batch_repair(){
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
            url: "<?php echo url('batch_repair')?>",
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

    function batch_del(){
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
            url: "<?php echo url('batch_del')?>",
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