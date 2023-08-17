{include file='public/head' /}
<div class="row" style="height: 50px"></div>
<div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6 tuchu">
                <h1>添加扫描任务</h1>
                <form method="post" action="<?php echo url('urls/_add')?>">
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
                        <label class="form-label">启用爬虫</label>
                        <select name="is_crawl" class="form-select form-select-sm">
                            <option value="1">启用</option>
                            <option value="0">不启用</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">自定义header</label>
                        <textarea class="form-control" rows="3" placeholder="填写header消息" name="header"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">自定义Cookie</label>
                        <textarea class="form-control" rows="3" placeholder="自定义cookie"></textarea>
                    </div>
                    <div class="row" style="height: 10px"></div>
                    <button type="submit" class="btn btn-sm btn-outline-secondary">提交</button>
                    <a href="<?php echo url('urls/index')?>" class="btn btn-sm btn-outline-secondary">返回</a>
                </form>
            </div>
            <div class="col-md-3"></div>
</div>
<div class="row" style="height: 50px"></div>
{include file='public/footer' /}