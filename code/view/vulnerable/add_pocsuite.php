{include file='public/head' /}
<div class="row" style="height: 50px"></div>
<div class="row">
<div class="col-md-3"></div>
<div class="col-md-6 tuchu">
    <h1>添加漏洞实例</h1>
    <form method="post" action="<?= url("add_pocsuite") ?>">
        <div class="modal-content">
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">url</label>
                    <input type="text" class="form-control" placeholder="" name="url" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">name</label>
                    <input name="name" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">ssv_id</label>
                    <input name="ssv_id" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">cms</label>
                    <input name="cms" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">version</label>
                    <input name="version" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">is_max</label>
                    <input name="is_max" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">tel</label>
                    <input name="tel" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">regaddress</label>
                    <input name="regaddress" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">ip</label>
                    <input name="ip" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">CompanyName</label>
                    <input name="CompanyName" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">SiteLicense</label>
                    <input name="SiteLicense" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">CompanyType</label>
                    <input name="CompanyType" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">regcapital</label>
                    <input name="regcapital" class="form-control" placeholder="" value="">
                </div>
            </div>
        </div><div class="row" style="height: 10px"></div>
            <div class="modal-footer">
                <a href="<?php echo url('index')?>" class="btn btn-sm btn-outline-secondary">返回</a>
                <button type="submit" class="btn btn-sm btn-outline-secondary">提交</button>
            </div>
        </div>
    </form>
</div>

<div class="col-md-3"></div>


{include file='public/footer' /}
