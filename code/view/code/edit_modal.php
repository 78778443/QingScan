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
                        <input type="text" name="password" class="form-control" placeholder="URL"
                               value="<?php echo $info['password'] ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">下次fortify扫描时间</label>
                        <input type="text" class="form-control" name="fortify_scan_time"
                               value="<?php echo $info['scan_time'] ?>"/>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">下次fortify扫描时间</label>
                        <input type="text" class="form-control" name="semgrep_scan_time"
                               value="<?php echo $info['semgrep_scan_time'] ?>"/>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">下次kunlun扫描时间</label>
                        <input type="text" class="form-control" name="kunlun_scan_time"
                               value="<?php echo $info['kunlun_scan_time'] ?>"/>
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
