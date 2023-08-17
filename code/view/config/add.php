{include file='public/head' /}
<div class="row" style="height: 50px"></div>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 tuchu">
        <h1>添加</h1>
        <form method="post" action="<?php echo url('config/add') ?>">
            <div class="mb-3">
                <label>key</label>
                <input type="text" name="key" class="form-control" placeholder="请输入key">
            </div>
            <div class="mb-3">
                <label>name</label>
                <input type="text" name="name" class="form-control" placeholder="请输入name">
            </div>
            <div class="mb-3">
                <label>value</label>
                <input type="text" name="value" class="form-control" placeholder="请输入value">
            </div>
            <div class="row" style="height: 10px"></div>
            <button type="submit" class="btn btn-sm btn-outline-secondary">提交</button>
            <a href="<?php echo url('config/index') ?>" class="btn btn-sm btn-outline-secondary">返回</a>
        </form>
    </div>
    <div class="col-md-3"></div>
</div>
<div class="row" style="height: 50px"></div>
{include file='public/footer' /}