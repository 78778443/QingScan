<input type="hidden" id="id" value="0">
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">请输入插件兑换码</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label >插件兑换码</label>
                        <input type="email" class="form-control" maxlength="32" name="code" placeholder="请输入插件兑换码">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <!--<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>-->
                <button type="button" class="btn btn-outline-primary" id="submit">确定</button>
            </div>
        </div>
    </div>
</div>
<script>
    function exchange_code(id) {
        $('#id').val(id)
        $('#exampleModal').modal('show')
    }

    $('#submit').click(function () {
        var id = $('#id').val();
        var code = $('input[name=code]').val();
        if (!code || !id) {
            alert('参数错误')
            return ;
        }
        $.ajax({
            type: "post",
            url: "<?php echo url('install')?>",
            data: {id: id,code: code,},
            dataType: "json",
            success: function (data) {
                alert(data.msg)
                if (data.code == 1) {
                    window.setTimeout(function () {
                        location.reload();
                    }, 1000)
                }
            }
        });
    });
</script>