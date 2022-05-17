<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">添加项目</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="<?= url("code/_add_code") ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">项目名称</label>
                        <input type="text" name="name" class="form-control" placeholder="应用名称" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">项目类型</label>
                        <select name="project_type" class="form-select" aria-label="Default select example" required>
                            <option value="1">PHP项目</option>
                            <option value="2">JAVA项目</option>
                            <option value="3">Python项目</option>
                            <option value="4">Golang项目</option>
                            <option value="5">APP项目</option>
                            <option value="6">其他</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">是否私有仓库</label>
                        <select name="is_private" class="form-select" aria-label="Default select example">
                            <option value="0">公共仓库</option>
                            <option value="1">私有仓库</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">拉取方式</label>
                        <select name="pulling_mode" class="form-select" aria-label="Default select example">
                            <option value="SSH">SSH</option>
                            <option value="HTTPS">HTTPS</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">地址</label>
                        <input type="text" name="ssh_url" class="form-control" placeholder="URL" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">账号</label>
                        <input type="text" name="username" class="form-control" placeholder="账号">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">密码</label>
                        <input type="text" name="password" class="form-control" placeholder="密码">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">私钥</label>
                        <input type="text" name="private_key" class="form-control" placeholder="私钥">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">需要调用的工具</label>
                        <div class="checkbox">
                            <?php
                            foreach ($tools_list as $k=>$v) {
                                ?>
                                <label>
                                    <input type="checkbox" name="tools[]" value="<?php echo $k;?>"><?php echo $v;?>
                                </label>
                            <?php }?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-info">提交</button>
                </div>
            </form>
        </div>
    </div>
</div>
