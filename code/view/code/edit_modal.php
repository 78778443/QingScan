{include file='public/head' /}
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 tuchu">
        <h1>编辑</h1>
        <form method="post" action="<?= url("code/edit_modal") ?>">
            <input type="hidden" name="id" value="<?php echo $info['id'] ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">添加项目</h4>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">项目名称</label>
                        <input type="text" name="name" class="form-control" placeholder="应用名称" required
                               value="<?php echo $info['name'] ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">项目类型</label>
                        <select name="project_type" class="form-select">
                            <option value="1" <?php if ($info['project_type'] == 1) echo 'selected' ?>>PHP项目
                            </option>
                            <option value="2" <?php if ($info['project_type'] == 2) echo 'selected' ?>>JAVA项目
                            </option>
                            <option value="3" <?php if ($info['project_type'] == 3) echo 'selected' ?>>Python项目
                            </option>
                            <option value="4" <?php if ($info['project_type'] == 4) echo 'selected' ?>>Golang项目
                            </option>
                            <option value="5" <?php if ($info['project_type'] == 5) echo 'selected' ?>>APP项目
                            </option>
                            <option value="6" <?php if ($info['project_type'] == 6) echo 'selected' ?>>其他
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">是否私有仓库</label>
                        <select name="is_private" class="form-select">
                            <option value="0" <?php if ($info['is_private'] == 0) echo 'selected' ?>>公共仓库
                            </option>
                            <option value="1" <?php if ($info['is_private'] == 1) echo 'selected' ?>>私有仓库
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">拉取方式</label>
                        <select name="pulling_mode" class="form-select">
                            <option value="SSH" <?php if ($info['pulling_mode'] == 'SSH') echo 'selected' ?>>SSH
                            </option>
                            <option value="HTTPS" <?php if ($info['pulling_mode'] == 'HTTPS') echo 'selected' ?>>HTTPS
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">地址</label>
                        <input type="text" name="ssh_url" class="form-control" placeholder="URL" required
                               value="<?php echo $info['ssh_url'] ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">账号</label>
                        <input type="text" name="username" class="form-control" placeholder="账号"
                               value="<?php echo $info['username'] ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">密码</label>
                        <input type="text" name="password" class="form-control" placeholder="密码"
                               value="<?php echo $info['password'] ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">私钥</label>
                        <textarea name="private_key" id="" cols="100"
                                  rows="15"><?php echo $info['private_key'] ?></textarea>
                        <!--<input type="text" name="private_key" class="form-control" placeholder="私钥"
                               value="<?php /*echo $info['private_key'] */ ?>">-->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="submit" class="btn btn-outline-info">提交</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-3"></div>
</div>
{include file='public/footer' /}
