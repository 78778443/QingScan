{include file='public/head' /}
<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
?>
<?php
$searchArr = [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' => 'search'],
        ['type' => 'select', 'name' => 'level', 'options' => $dengjiArr, 'frist_option' => '危险等级'],
        ['type' => 'select', 'name' => 'Category', 'options' => $CategoryList, 'frist_option' => '类别'],
        ['type' => 'select', 'name' => 'code_id', 'options' => $projectList, 'frist_option' => '项目列表'],
        ['type' => 'select', 'name' => 'check_status', 'options' => $check_status_list, 'frist_option' => '审计状态', 'frist_option_value' => -1],
    ],
    'btnArr' => [
        ['text' => '添加URL', 'ext' => [
            "href" => url('urls/add'),
            "class" => "btn btn-outline-success"
        ]]
    ]
]; ?>


<div class="col-md-12 ">
    {include file='public/search' /}

    <div class="row tuchu">
        <div class="col-md-12">
            <form class="row g-3" id="frmUpload" action="<?php echo url('app/batch_import') ?>" method="post"
                  enctype="multipart/form-data">
                <div class="col-auto">
                    <a href="javascript:;" onclick="batch_del()"
                       class="btn btn-outline-success">批量删除</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row tuchu">
        <!--            <div class="col-md-1"></div>-->
        <div class="col-md-12 ">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>
                        <label>
                            <input type="checkbox" value="-1" onclick="quanxuan(this)">全选
                        </label>
                    </th>
                    <th>ID</th>
                    <th>APP</th>
                    <th>漏洞类型</th>
                    <th>危险等级</th>
                    <th>URL地址</th>
                    <th>创建时间</th>
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
                        <td><?php echo $value['app_name'] ?></td>
                        <td><?php echo $value['plugin'] ?></td>
                        <td><?php echo $dengjiArr[$value['hazard_level']] ?></td>
                        <td><?php echo json_decode($value['target'], true)['url'] ?></td>
                        <td><?php echo date('Y-m-d H:i:s', substr($value['create_time'], 0, 10)) ?></td>
                        <td><select class="changCheckStatus form-select" data-id="<?php echo $value['id'] ?>">
                                <option value="0" <?php echo $value['check_status'] == 0 ? 'selected' : ''; ?> >未审核
                                </option>
                                <option value="1" <?php echo $value['check_status'] == 1 ? 'selected' : ''; ?> >有效漏洞
                                </option>
                                <option value="2" <?php echo $value['check_status'] == 2 ? 'selected' : ''; ?> >无效漏洞
                                </option>
                            </select>
                        </td>
                        <td>
                            <a href="<?php echo url('xray/details', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-primary">查看漏洞</a>
                            <a href="<?php echo url('xray/del', ['id' => $value['id']]) ?>"
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