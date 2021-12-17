{include file='public/head' /}
<div class="row" style="height: 50px"></div>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 tuchu">
        <h3>编辑自定义插件</h3>
        <form method="post" action="">
            <input type="hidden" name="id" class="form-control" value="<?php echo $info['id']?>">
            <div class="mb-3">
                <label class="form-label">插件名称</label>
                <input type="text" name="name" class="form-control" value="<?php echo $info['name']?>">
            </div>
            <div class="mb-3">
                <label class="form-label">插件执行命令</label>
                <input type="text" name="cmd" class="form-control" value="<?php echo $info['cmd']?>" placeholder="###URL###  可以替代变量URL地址">
            </div>
            <div class="mb-3">
                <label class="form-label">状态</label>
                <select name="status" class="form-select">
                    <option value="1" <?php echo $info['status'] == 1?'selected':''?>>启用</option>
                    <option value="0" <?php echo $info['status'] == 0?'selected':''?>>禁用</option>
                </select>
            </div>
            <div class="row" style="height: 10px"></div>
            <button type="submit" class="btn btn-outline-success">提交</button>
            <a href="<?php echo url('index') ?>" class="btn btn-outline-info">返回</a>
        </form>
    </div>
    <div class="col-md-3"></div>
</div>
<div class="row" style="height: 50px"></div>
{include file='public/footer' /}