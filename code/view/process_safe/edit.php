{include file='public/head' /}

<div class="row" style="height: 50px"></div>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 tuchu">
        <h1>编辑守护进程</h1>
        <form method="post" action="">
            <div class="mb-3">
                <label class="form-label">key</label>
                <input type="text" name="key" class="form-control" value="<?php echo $info['key'] ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">value</label>
                <input type="text" name="value" class="form-control" value="<?php echo $info['value'] ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">工具类型</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="0" <?php echo $info['type'] == 0 ? 'selected' : ''; ?>>黑盒扫描</option>
                    <option value="1" <?php echo $info['type'] == 1 ? 'selected' : ''; ?>>白盒审计</option>
                    <option value="2" <?php echo $info['type'] == 2 ? 'selected' : ''; ?>>专项利用</option>
                    <option value="3" <?php echo $info['type'] == 3 ? 'selected' : ''; ?>>其他</option>
                    <option value="4" <?php echo $info['type'] == 4 ? 'selected' : ''; ?>>信息收集</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">备注</label>
                <input type="text" name="note" class="form-control" value="<?php echo $info['note'] ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">状态</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="1" <?php echo $info['status'] == 1 ? 'selected' : ''; ?>>正常</option>
                    <option value="0" <?php echo $info['status'] == 0 ? 'selected' : ''; ?>>禁用</option>
                </select>
            </div>
            <button type="submit" class="btn btn-sm btn-outline-secondary">提交</button>
            <a href="<?php echo url('index') ?>" class="btn btn-sm btn-outline-secondary">返回</a>
        </form>
    </div>
    <div class="col-md-3"></div>
</div>
<div class="row" style="height: 50px"></div>
{include file='public/footer' /}