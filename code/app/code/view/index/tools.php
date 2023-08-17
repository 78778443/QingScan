<div class="modal fade" id="toolBackdrop" tabindex="-1" role="dialog" aria-labelledby="toolBackdropLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="toolBackdropLabel">工具列表</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="<?= url("process_safe/edit_tools") ?>">
                <div class="modal-body">
                    <input type="hidden" name="type" id="type" value="1">
                    <input type="hidden" name="project_id" id="project_id" value="0">
                    <div class="mb-3">
                        <label class="form-label">项目名称：</label>
                        <div id="name" style="color:#0d6efd;">32132</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">需要调用的工具</label>
                        <div class="checkbox">
                            <?php
                            foreach ($tools_list as $k=>$v) {
                                ?>
                                <label>
                                    <input type="checkbox" name="tools[]" class="tools" value="<?php echo $k;?>"><?php echo $v;?>
                                </label>
                            <?php }?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">提交</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function tools(id,name,type) {
        $('#project_id').val(id)
        $('#type').val(type)
        $('#name').html(name)

        $.ajax({
            type: "get",
            url: "<?php echo url('process_safe/tools')?>",
            data: {project_id:id,type:type},
            dataType: "json",
            success: function (res) {
                $('.tools').each(function(index,item){
                    if (res.data && $.inArray($(item).val(),res.data) != -1) {
                        $(item).attr('checked','checked')
                    } else {
                        $(item).removeAttr('checked');
                    }
                })
                $('#toolBackdrop').modal('show')
            }
        });
    }
</script>