<form class="row g-3">
    <div class="col-auto">
        <a href="javascript:;>" onclick="batch_audit()"
           class="btn btn-outline-success">批量审核</a>
    </div>
    <div class="col-auto">
        <a href="javascript:;" onclick="batch_del()"
           class="btn btn-outline-danger">批量删除</a>
    </div>
</form>
<div class="modal fade" id="batch_auditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" style="padding-top: 10%;text-align: center">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">批量审核</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="checkbox">
                        <label>
                            <input type="radio" name="check_status" value="1">有效漏洞
                            <input type="radio" name="check_status" value="2">无效漏洞
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="submit" class="btn btn-outline-info">提交</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function batch_audit(id) {
        $('#batch_auditModal').modal('show')
    }

    $('#submit').click(function () {
        var check_status = 0;
        if ($("input[name='check_status']:checked").val() > 0) {
            check_status = $("input[name='check_status']:checked").val();
        }
        var ids = getIds()
        if (ids == '') {
            alert('请先选择要审核的数据')
            return false;
        }
        $.ajax({
            type: "post",
            url: "<?php echo url('batch_audit')?>",
            data: {ids: ids,check_status:check_status},
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
    });

    function batch_del() {
        var ids = getIds()
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

    function getIds(){
        var child = $('.table').find('.ids');
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
        return ids
    }
</script>