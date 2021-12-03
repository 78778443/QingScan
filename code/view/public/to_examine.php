<input type="hidden" id="check_status" value="0">
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="exampleModalLabel">请选择审核状态</h4>
            </div>
            <div class="modal-body">
                <form>
                    <!--<div class="radio">
                        <label>
                            <input type="radio" name="check_status" value="0" checked>
                            待审核
                        </label>
                    </div>-->
                    <div class="radio">
                        <label>
                            <input type="radio" name="check_status" value="1">
                            有效漏洞
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="check_status" value="2">
                            无效漏洞
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-outline-primary" id="submit">确定</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3>提示</h3>
            </div>
            <div class="modal-body">
                <p id="message">确定要关闭警告框信息？</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-info" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<script>
    function to_examine(id) {
        $('#check_status').val(id)
        $('#exampleModal').modal()
    }

    $('#submit').click(function () {
        var check_status = 0;
        if ($("input[name='check_status']:checked").val() > 0) {
            check_status = $("input[name='check_status']:checked").val();
        }
        var id = $('#check_status').val()
        $.ajax({
            type: "post",
            url: $('#to_examine_url').val(),
            data: {check_status: check_status, id: id},
            dataType: "json",
            success: function (data) {
                $('#message').html(data.msg)
                if (data.code == 1) {
                    // window.setTimeout(function () {
                    location.reload();
                    // }, 2000)
                }
            }
        });
    });
    $(".changCheckStatus").change(function () {
        id = $(this).data('id');
        check_status = $(this).val();
        $.ajax({
            type: "post",
            url: $('#to_examine_url').val(),
            data: {check_status: check_status, id: id},
            dataType: "json",
            success: function (data) {
                if (data.code == 1) {
                    location.reload();
                }
            }
        });
    });
    
    $(".changCheckBoxStatus").change(function () {
        id = $(this).data('id');
        // check_status = $(this).val();
        check_status = $(this).is(":checked");
        if (check_status == false) {
            check_status = 0;
        } else {
            check_status = 1;
        }

        $.ajax({
            type: "post",
            url: $('#to_examine_url').val(),
            data: {check_status: check_status, id: id},
            dataType: "json",
            success: function (data) {
                if (data.code == 1) {
                    location.reload();
                }
            }
        });
    });
</script>