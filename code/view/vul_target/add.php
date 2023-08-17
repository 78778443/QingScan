{include file='public/head' /}
<div class="row" style="height: 50px"></div>
<div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6 tuchu">
                <h3>添加收集目标</h3>
                <form method="post" action="">
                    <!--<div class="mb-3">
                        <label class="form-label">缺陷列表</label>
                        <select name="vul_id" class="form-select form-select-sm" required>
                            <option value="0" selected>pocsuite3</option>
                        </select>
                    </div>-->
                    <div class="mb-3">
                        <label class="form-label">缺陷id</label>
                        <input type="text" name="vul_id" class="form-control"  placeholder="">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">addr</label>
                        <input type="text" name="addr" class="form-control"  placeholder="">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ip</label>
                        <input type="text" name="ip" class="form-control"  placeholder="">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">port</label>
                        <input type="text" name="port" class="form-control"  placeholder="">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">query</label>
                        <input type="text" name="query" class="form-control"  placeholder="">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">是否存在漏洞</label>
                        <select name="is_vul" class="form-select form-select-sm">
                            <option value="0">未知</option>
                            <option value="1">存在</option>
                            <option value="2">不存在</option>
                        </select>
                    </div>
                    <div class="row" style="height: 10px"></div>
                    <button type="submit" class="btn btn-sm btn-outline-secondary">提交</button>
                    <a href="<?php echo url('index')?>" class="btn btn-sm btn-outline-secondary">返回</a>
                </form>
            </div>
            <div class="col-md-3"></div>
</div>
<div class="row" style="height: 50px"></div>
{include file='public/footer' /}