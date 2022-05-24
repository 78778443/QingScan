<form class="row g-3" id="frmUpload" action="" method="post"
      enctype="multipart/form-data">
    <div class="col-auto">
        <a href="javascript:;" onclick="batch_del()"
           class="btn btn-outline-danger">批量删除</a>
    </div>
</form>

<script>
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
        if (ids == '') {
            alert('请先选择要删除的数据')
            return false
        }
        url = $('#batch_del_url').val();
        if (url == 'undefined') {
            url = "<?php echo url('batch_del')?>"
        }
        $.ajax({
            type: "post",
            url: url,
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