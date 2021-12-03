{include file='public/head' /}
<div class="row">
<div class="col-md-3"></div>
<div class="col-md-6 tuchu">
    <h1>编辑</h1>
    <form method="post" action="">
        <div class="mb-3">
            <label class="form-label">cve_num</label>
            <input type="text" name="cve_num" class="form-control" disabled value="<?php echo $info['cve_num']?>">
        </div>
        <div class="mb-3">
            <label class="form-label">文件名</label>
            <input type="text" name="filename" class="form-control" disabled value="<?php echo $info['filename']?>">
        </div>
        <div class="mb-3">
            <label class="form-label">内容</label>
            <textarea class="form-control" rows="8" name="content" readonly><?php echo $info['content']?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">是否为yaml格式</label>
            <select name="is_yaml" class="form-select" readonly>
                <option value="1" <?php echo $info['is_yaml'] == 1 ?'selected':'';?>>是</option>
                <option value="0" <?php echo $info['is_yaml'] == 0 ?'selected':'';?>>否</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">状态</label>
            <select name="status" class="form-select">
                <option value="1" <?php echo $info['status'] == 1 ?'selected':'';?>>正常</option>
                <option value="0" <?php echo $info['status'] == 0 ?'selected':'';?>>禁用</option>
            </select>
        </div>
        <button type="submit" class="btn btn-outline-success">提交</button>
        <a href="<?php echo url('index')?>" class="btn btn-outline-info">返回</a>
    </form>
</div>
<div class="col-md-3"></div>
</div>
{include file='public/footer' /}