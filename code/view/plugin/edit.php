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
                <input type="text" name="name" class="form-control" value="<?php echo $info['name']?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">命令类型</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="plugin_type1" <?php echo ($info['type'] == 1)? 'checked' : '';?> value="1">
                    <label class="form-check-label" for="plugin_type1">工具扫描</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" id="plugin_type2" <?php echo ($info['type'] == 2)? 'checked' : '';?> value="2">
                    <label class="form-check-label" for="plugin_type2">结果分析</label>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">插件执行命令</label>
                <input type="text" name="cmd" class="form-control" value="<?php echo $info['cmd']?>" placeholder="_####_  代表扫描地址" required>
            </div>
            <div class="mb-3">
                <label class="form-label">扫描类型</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="scan_type" id="type0" <?php echo ($info['scan_type'] == 0)? 'checked' : '';?> value="0">
                    <label class="form-check-label" for="type0">域名</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="scan_type" id="type1"<?php echo ($info['scan_type'] == 1)? 'checked' : '';?> value="1">
                    <label class="form-check-label" for="type1">主机</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="scan_type" id="type2"<?php echo ($info['scan_type'] == 2)? 'checked' : '';?> value="2">
                    <label class="form-check-label" for="type2">代码</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="scan_type" id="type3"<?php echo ($info['scan_type'] == 3)? 'checked' : '';?> value="3">
                    <label class="form-check-label" for="type3">URL</label>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">状态</label>
                <select name="status" class="form-select">
                    <option value="1" <?php echo $info['status'] == 1?'selected':''?>>启用</option>
                    <option value="0" <?php echo $info['status'] == 0?'selected':''?>>禁用</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">工具存放位置</label>
                <input type="text" name="tool_path" class="form-control" value="<?php echo $info['tool_path']?>" placeholder="工具存放位置(选填)">
            </div>
            <div class="mb-3">
                <label class="form-label">扫描结果类型</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="result_type" id="result_type0" <?php echo ($info['result_type'] == 'json')? 'checked' : '';?> checked value="json">
                    <label class="form-check-label" for="result_type0">json</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="result_type" id="result_type1" <?php echo ($info['result_type'] == 'csv')? 'checked' : '';?> value="csv">
                    <label class="form-check-label" for="result_type1">csv</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="result_type" id="result_type2" <?php echo ($info['result_type'] == 'txt')? 'checked' : '';?> value="txt">
                    <label class="form-check-label" for="result_type2">txt</label>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">扫描结果存放位置</label>
                <input type="text" name="result_file" class="form-control" value="<?php echo $info['result_file']?>" placeholder="扫描结果存放位置(选填)">
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