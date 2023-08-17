{include file='public/head' /}
<div class="row tuchu">
    <div class="col-md-12">

        <div class="mb-3">
            <label class="form-label">id</label>
            <input type="text" class="form-control" value="{$info['plugin_id']}" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">app_id</label>
            <input type="text" class="form-control" value="{$info['app_id']}" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">plugin_id</label>
            <input type="text" class="form-control" value="{$info['plugin_id']}" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">执行结果</label>
            <textarea class="form-control" rows="15" disabled="disabled">{$info['content']}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">执行时间</label>
            <input type="text" class="form-control" value="{$info['create_time']}" disabled>
        </div>
        <div class="mb-3">
            <a class="btn btn-sm btn-outline-secondary" href="{:url('PluginResult/index')}">返回列表</a>
        </div>
    </div>
</div>

{include file='public/footer' /}
