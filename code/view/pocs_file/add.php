{include file='public/head' /}
<div class="row" style="height: 50px"></div>
<div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6 tuchu">
                <h3>添加POC验证脚本</h3>
                <form method="post" action="">
                    <div class="mb-3">
                        <label class="form-label">脚本名称</label>
                        <input type="text" name="name" class="form-control"  placeholder="">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">cve_num</label>
                        <input type="text" name="cve_num" class="form-control"  placeholder="可选填">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">POC代码</label>
                        <textarea class="form-control" rows="8" name="content" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">工具类型</label>
                        <select name="tool" class="form-select" required>
                            <option value="0" selected>pocsuite3</option>
                            <option value="1">xray</option>
                            <option value="2">其他</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">状态</label>
                        <select name="status" class="form-select">
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