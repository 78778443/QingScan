<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">添加目标</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="<?php echo url('_add_keyword') ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">数据类型</label>
                        <select name="type" class="form-select form-select-sm">
                                <option value="domain">域名</option>
                                <option value="domain">IP</option>
                                <option value="domain">端口</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">关键词</label>
                        <input type="text" name="keyword" class="form-control" placeholder="dedecms">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">提交</button>
                </div>
            </form>
        </div>

    </div>
</div>
