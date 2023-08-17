{include file='public/head' /}
<div class="row">
<div class="col-md-3"></div>
<div class="col-md-6 tuchu">
    <h1>编辑</h1>
    <form method="post" action="">
        <div class="mb-3">
            <label class="form-label">脚本名称</label>
            <input type="text" name="name" class="form-control"  placeholder="" value="<?php echo $info['name']?>">
        </div>
        <div class="mb-3">
            <label class="form-label">cve_num</label>
            <input type="text" name="cve_num" class="form-control"   value="<?php echo $info['cve_num']?>">
        </div>
        <div class="mb-3">
            <label class="form-label">内容</label>
            <textarea class="form-control" rows="8" name="content"  ><?php echo $info['content']?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">工具类型</label>
            <select name="tool" class="form-select form-select-sm" readonly>
                <option value="1" <?php echo $info['tool'] == 0 ?'selected':'';?>>pocsuite3</option>
                <option value="1" <?php echo $info['tool'] == 1 ?'selected':'';?>>xray</option>
                <option value="0" <?php echo $info['tool'] == 2 ?'selected':'';?>>其他</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">状态</label>
            <select name="status" class="form-select form-select-sm">
                <option value="1" <?php echo $info['status'] == 1 ?'selected':'';?>>正常</option>
                <option value="0" <?php echo $info['status'] == 0 ?'selected':'';?>>禁用</option>
            </select>
        </div>
        <button type="submit" class="btn btn-sm btn-outline-secondary">提交</button>
        <a href="<?php echo url('index')?>" class="btn btn-sm btn-outline-secondary">返回</a>
    </form>
</div>
<div class="col-md-3"></div>
</div>
{include file='public/footer' /}