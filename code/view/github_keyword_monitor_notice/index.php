{include file='public/head' /}
<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
?>


    <?php
    $searchArr = [
        'action' => $_SERVER['REQUEST_URI'],
        'method' => 'get',
        'inputs' => [
            ['type' => 'text', 'name' => 'search', 'placeholder' =>'search'],
        ],
        /*'btnArr' => [
            ['text' => '添加', 'ext' => [
                "href" => url('add'),
                "class" => "btn btn-outline-success"
            ]]
        ]*/
    ];

    ?>
    {include file='public/search' /}


<div class="col-md-12 ">
    <div class="row tuchu">
        <div class="col-md-12 ">
            {include file='public/batch_del' /}
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th width="80">
                        <label>
                            <input type="checkbox" value="-1" onclick="quanxuan(this)">全选
                        </label>
                    </th>
                    <th>ID</th>
                    <th>关键字</th>
                    <th>名称</th>
                    <th>文件路径</th>
                    <th>url地址</th>
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
                        <td><?php echo $value['title'] ?></td>
                        <td><?php echo $value['name'] ?></td>
                        <td><?php echo $value['path'] ?></td>
                        <td><?php echo $value['html_url'] ?></td>
                        <td><?php echo $value['create_time'] ?></td>
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