{include file='public/head' /}
<div class="row">
<div class="col-md-3"></div>
<div class="col-md-6 tuchu">
    <h1>IP测速</h1>
    <form method="post" action="<?php echo url('test_speed')?>">
        <input type="hidden" name="id" value="<?php echo $info['id']?>">
        <div class="mb-3">
            <label class="form-label">代理地址</label>
            <input type="text" name="host" class="form-control" disabled value="<?php echo $info['proxy']?>">
        </div>
        <div class="mb-3">
            <label class="form-label">访问地址</label>
            <input type="text" name="url" class="form-control" value="http://www.baidu.com">
        </div>
        <div class="mb-3">
            <label class="form-label">访问结果</label>
            <div style="width: 300px">
                <?php echo $info['result']?>
            </div>
        </div>
        <a href="<?php echo url('index')?>" class="btn btn-sm btn-outline-secondary">返回</a>
        <button type="submit" class="btn btn-sm btn-outline-secondary">测速</button>
    </form>
</div>
<div class="col-md-3"></div>
</div>
{include file='public/footer' /}