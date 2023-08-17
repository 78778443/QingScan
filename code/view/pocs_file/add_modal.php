<div class="modal fade" id="staticBackdrop" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">添加脚本</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="add">
                    <div class="mb-3">
                        <label class="form-label">脚本名称</label>
                        <input type="text" name="name" class="form-control" placeholder="">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">POC代码</label>
                        <textarea class="form-control" rows="8" name="content" required></textarea>
                    </div>

                    <div class="row" style="height: 10px"></div>
                    <button type="submit" class="btn btn-sm btn-outline-secondary">提交</button>
                    <a href="<?php echo url('index') ?>" class="btn btn-sm btn-outline-secondary">返回</a>
                </form>

            </div>

        </div>
    </div>
</div>
