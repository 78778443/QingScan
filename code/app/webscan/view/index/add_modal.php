<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">添加检测目标</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="<?php echo url('index/_add') ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">URL地址</label>
                        <textarea class="form-control" name="url" placeholder="https://example.com/"
                                  rows="6"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">提交</button>
                </div>
            </form>
        </div>

    </div>
</div>
