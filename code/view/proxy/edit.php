{include file='public/head' /}
<div class="row">
<div class="col-md-3"></div>
<div class="col-md-6 tuchu">
    <h1>编辑</h1>
    <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo $info['id']?>">
        <div class="mb-3">
            <label class="form-label">IP</label>
            <input type="text" name="host" class="form-control" value="<?php echo $info['host']?>">
        </div>
        <div class="mb-3">
            <label class="form-label">端口</label>
            <input type="text" name="port" class="form-control" value="<?php echo $info['port']?>">
        </div>
        <button type="submit" class="btn btn-sm btn-outline-secondary">提交</button>
        <a href="<?php echo url('index')?>" class="btn btn-sm btn-outline-secondary">返回</a>
    </form>
</div>
<div class="col-md-3"></div>
</div>
{include file='public/footer' /}