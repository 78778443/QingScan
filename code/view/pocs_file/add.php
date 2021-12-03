{include file='public/head' /}
<div class="row" style="height: 50px"></div>
<div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6 tuchu">
                <h3>添加</h3>
                <form method="post" action="">
                    <div class="mb-3">
                        <label class="form-label">cve_num</label>
                        <input type="text" name="cve_num" class="form-control" required placeholder="">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">文件名</label>
                        <input type="text" name="filename" class="form-control" required placeholder="">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">内容</label>
                        <textarea class="form-control" rows="8" name="content" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">是否为yaml格式</label>
                        <select name="is_yaml" class="form-select" required>
                            <option value="1">是</option>
                            <option value="0" selected>否</option>
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