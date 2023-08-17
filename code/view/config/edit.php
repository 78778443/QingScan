{include file='public/head' /}
<div class="row">
<div class="col-md-3"></div>
<div class="col-md-6 tuchu">
    <h1>修改</h1>
    <form method="post" action="<?php echo url('config/edit')?>">
        <input type="hidden" name="id" value="<?php echo $info['id']?>">
        <div class="mb-3">
            <label class="form-label">key</label>
            <input type="text" name="key" class="form-control" placeholder="请输入key" value="<?php echo $info['key']?>">
        </div>
        <div class="mb-3">
            <label class="form-label">name</label>
            <input type="text" name="name" class="form-control" placeholder="请输入name" value="<?php echo $info['name']?>">
        </div>
        <div class="mb-3">
            <label class="form-label">value</label>
            <input type="text" name="value" class="form-control" placeholder="请输入value" value="<?php echo $info['value']?>">
        </div>
        <button type="submit" class="btn btn-sm btn-outline-secondary">提交</button>
        <a href="<?php echo url('config/index')?>" class="btn btn-sm btn-outline-secondary">返回</a>
    </form>
</div>
<div class="col-md-3"></div>
</div>
{include file='public/footer' /}