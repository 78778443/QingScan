{include file='public/head' /}
<div class="row" style="height: 50px"></div>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 tuchu">
        <h3>添加自定义插件</h3>
        <form method="post" action="">
            <div class="mb-3">
                <label class="form-label">插件名称</label>
                <input type="text" name="name" class="form-control" placeholder="例如 sqlmap" required>
            </div>
            <div class="mb-3">
                <label class="form-label">命令类型</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="plugin_type1" checked value="1">
                    <label class="form-check-label" for="plugin_type1">工具扫描</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="plugin_type2" value="2">
                    <label class="form-check-label" for="plugin_type2">结果分析</label>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">插件执行命令</label>
                <input type="text" name="cmd" class="form-control" placeholder="_####_  代表扫描地址" required>
            </div>
            <div class="mb-3">
                <label class="form-label">扫描类型</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="scan_type" id="type0" checked value="0">
                    <label class="form-check-label" for="type0">域名</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="scan_type" id="type1" value="1">
                    <label class="form-check-label" for="type1">主机</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="scan_type" id="type2" value="2">
                    <label class="form-check-label" for="type2">代码</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="scan_type" id="type3" value="3">
                    <label class="form-check-label" for="type3">URL</label>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">状态</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="1">启用</option>
                    <option value="0">禁用</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">工具存放位置</label>
                <input type="text" name="tool_path" class="form-control" placeholder="工具存放位置(选填)">
            </div>
            <div class="mb-3">
                <label class="form-label">扫描结果类型</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="result_type" id="result_type0" checked value="json">
                    <label class="form-check-label" for="result_type0">json</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="result_type" id="result_type1" value="csv">
                    <label class="form-check-label" for="result_type1">csv</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="result_type" id="result_type2" value="txt">
                    <label class="form-check-label" for="result_type2">txt</label>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">扫描结果存放位置</label>
                <input type="text" name="result_file" class="form-control" placeholder="扫描结果存放位置(选填)">
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