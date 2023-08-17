<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">添加项目</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="<?= url("code/index/_add_code") ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Git地址(一行一个)</label>
                        <textarea required class="form-control" name="ssh_url" placeholder="https://gitee.com/songboy/QingScan.git"
                                  rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">选择工具</label>
                        <div class="checkbox">
                            <?php
                            foreach ($tools_list as $k=>$v) {
                                ?>
                                <label>
                                    <input type="checkbox" checked name="tools[]" value="<?php echo $k;?>"><?php echo $v;?>
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
