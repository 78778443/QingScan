{include file='public/head' /}

<?php
$searchArr = [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' =>'search'],
    ]];
?>
{include file='public/search' /}

<div class="row tuchu">
    <div class="col-md-12">
        <form class="row g-3" id="frmUpload" action="" method="post"
              enctype="multipart/form-data">
            <div class="col-auto">
                <a href="javascript:;" onclick="batch_del()"
                   class="btn btn-outline-success">批量删除</a>
            </div>
        </form>
    </div>
</div>

<div class="col-md-12 ">

    <div class="row tuchu">
        <div class="col-md-12 ">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>
                        <label>
                            <input type="checkbox" value="-1" onclick="quanxuan(this)">全选
                        </label>
                    </th>
                    <th>id</th>
                    <th>add_by</th>
                    <th>add_time</th>
                    <th>filters</th>
                    <th>keyword</th>
                    <th>md5</th>
                    <th>supplier</th>
                    <th>tags</th>
                    <th>title</th>
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
                        <td><?php echo $value['add_by'] ?></td>
                        <td><?php echo date('Y-m-d H:i:s',substr($value['add_time'],0,10)) ?></td>
                        <td><?php echo $value['filters'] ?></td>
                        <td><?php echo $value['keyword'] ?></td>
                        <td><?php echo $value['md5'] ?></td>
                        <td><?php echo $value['supplier'] ?></td>
                        <td><?php echo $value['tags'] ?></td>
                        <td><?php echo $value['title'] ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

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