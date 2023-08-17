{include file='public/head' /}

    <div class="col-md-3"></div>
    <div class="col-md-6 tuchu">
        <h1>添加API任务</h1>
        <form method="post" action="/index.php?s=urls/_add_api_url">
            <div class="mb-3">
                <label class="form-label">所属项目</label>
                <select name="app_id" class="form-select form-select-sm">
                    <?php foreach ($app_list as $item){ ?>
                        <option value="<?php echo $item['id']?>"><?php echo $item['name']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">URL地址</label>
                <input type="url" name="url" class="form-control" placeholder="URL">
            </div>
            <div class="mb-3">
                <label class="form-label">JSON数据内容</label>
                <textarea name="json_data" class="form-control" rows="8" placeholder="自定义cookie"></textarea>
            </div>
            <button type="submit" class="btn btn btn-sm btn-outline-secondary">提交</button>
            <a href="/index.php?s=urls/index" class="btn btn-sm btn-outline-secondary">返回</a>
        </form>
    </div>
    <div class="col-md-3"></div>

{include file='public/footer' /}
