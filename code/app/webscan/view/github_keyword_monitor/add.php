{include file='public/head' /}
<div class="row" style="height: 50px"></div>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 tuchu">
        <h3>添加</h3>
        <form method="post" action="">
            <div class="mb-3">
                <label class="form-label">关键字</label>
                <input type="text" name="title" class="form-control" placeholder="">
            </div>
            <div class="row" style="height: 10px"></div>
            <button type="submit" class="btn btn-sm btn-outline-secondary">提交</button>
            <a href="<?php echo url('index') ?>" class="btn btn-sm btn-outline-secondary">返回</a>
        </form>
    </div>
    <div class="col-md-3"></div>
</div>
<div class="row" style="height: 50px"></div>
{include file='public/footer' /}