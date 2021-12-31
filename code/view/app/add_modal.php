<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">添加</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="<?php echo url('app/_add') ?>">
                <input type="hidden" name="username" class="form-control" placeholder="账号">
                <input type="hidden" name="password" class="form-control" placeholder="URL">
                <div class="modal-body">
                    <h3>基本信息</h3>
                    <div class="mb-3">
                        <label class="form-label">应用名称</label>
                        <input type="text" name="name" class="form-control" placeholder="应用名称" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL地址</label>
                        <input type="url" name="url" class="form-control" placeholder="URL" required>
                    </div>
<!--                    <div class="mb-3">-->
<!--                        <label class="form-label">账号</label>-->
<!--                        <input type="text" name="username" class="form-control" placeholder="账号">-->
<!--                    </div>-->
<!--                    <div class="mb-3">-->
<!--                        <label class="form-label">密码</label>-->
<!--                        <input type="text" name="password" class="form-control" placeholder="URL">-->
<!--                    </div>-->
                    <div class="mb-3">
                        <label class="form-label">是否xary扫描</label>
                        <select name="is_xray" class="form-select" aria-label="Default select">
                            <option value="1">是</option>
                            <option value="0">否</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">是否awvs扫描</label>
                        <select name="is_awvs" class="form-select" aria-label="Default select">
                            <option value="1">是</option>
                            <option value="0">否</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">是否whatweb扫描</label>
                        <select name="is_whatweb" class="form-select" aria-label="Default select">
                            <option value="1">是</option>
                            <option value="0">否</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">是否OneForAll扫描</label>
                        <select name="is_one_for_all" class="form-select" aria-label="Default select">
                            <option value="1">是</option>
                            <option value="0">否</option>
                        </select>
                    </div>
                    <!--<div class="mb-3">
                        <label class="form-label">是否hydra扫描</label>
                        <select name="is_hydra" class="form-control">
                            <option value="1">是</option>
                            <option value="0">否</option>
                        </select>
                    </div>-->
                    <div class="mb-3">
                        <label class="form-label">是否dirmap扫描</label>
                        <select name="is_dirmap" class="form-select" aria-label="Default select">
                            <option value="1">是</option>
                            <option value="0">否</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">是否为内网</label>
                        <select name="is_intranet" class="form-select" aria-label="Default select">
                            <option value="1">是</option>
                            <option value="0">否</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-info">提交</button>
                </div>
            </form>
        </div>

    </div>
</div>
