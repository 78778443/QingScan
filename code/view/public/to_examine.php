<input type="hidden" id="to_examine_id" value="0">
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" style="padding-top: 10%;text-align: center">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">审核</h5>
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
                    <button type="button" id="submit" class="btn btn-sm btn-outline-secondary">提交</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function to_examine(id) {
        $('#to_examine_id').val(id)
        $('#exampleModal').modal('show')
    }

    $('#submit').click(function () {
        var check_status = 0;
        if ($("input[name='check_status']:checked").val() > 0) {
            check_status = $("input[name='check_status']:checked").val();
        }
        var id = $('#to_examine_id').val()
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