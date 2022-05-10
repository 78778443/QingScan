{include file='public/head' /}
<div class="row" style="height: 50px"></div>
<div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6 tuchu">
                <h1>添加守护进程</h1>
                <form method="post" action="">
                    <div class="mb-3">
                        <label>key</label>
                        <input type="text" name="key" class="form-control" placeholder="">
                    </div>
                    <div class="mb-3">
                        <label>value</label>
                        <input type="text" name="value" class="form-control" placeholder="">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">工具类型</label>
                        <select name="type" class="form-select">
                            <option value="0">黑盒扫描</option>
                            <option value="1">白盒审计</option>
                            <option value="2">专项利用</option>
                            <option value="3">其他</option>
                            <option value="4">信息收集</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>备注</label>
                        <input type="text" name="note" class="form-control" placeholder="详情">
                    </div>
                    <div class="mb-3">
                        <label>状态</label>
                        <select name="status" class="form-select" aria-label="Default select">
                            <option value="1">正常</option>
                            <option value="0">禁用</option>
                        </select>
                    </div>
                    <div class="row" style="height: 10px"></div>
                    <button type="submit" class="btn btn-outline-success">提交</button>
                    <a href="<?php echo url('index')?>" class="btn btn-outline-info">返回</a>
                </form>
            </div>
            <div class="col-md-3"></div>
</div>
<div class="row" style="height: 50px"></div>
{include file='public/footer' /}