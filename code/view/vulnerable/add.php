{include file='public/head' /}
<div class="row" style="height: 50px"></div>
<div class="row">
<div class="col-md-3"></div>
<div class="col-md-6 tuchu">
    <h1>添加</h1>
    <form method="post" action="<?= url("add") ?>">
        <div class="modal-content">
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">名称</label>
                    <input type="text" class="form-control" placeholder="" name="name" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">nature</label>
                    <input name="nature" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">VUL-ID</label>
                    <input name="vul_num" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">CVE-ID</label>
                    <input name="cve_num" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">CNVD-ID</label>
                    <input name="cnvd_num" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">CNVD-ID</label>
                    <input name="cnnvd_num" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">SRC-ID</label>
                    <input name="src_num" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">漏洞等级</label>
                    <select name="vul_level" class="form-select form-select-sm" aria-label="Default select example">
                        <option value="严重" >严重</option>
                        <option value="高危" >高危</option>
                        <option value="中危" >高危</option>
                        <option value="低危" >高危</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">漏洞类型</label>
                    <input name="vul_type" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">cwe</label>
                    <input name="cwe" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">vul_cvss</label>
                    <input name="vul_cvss" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">cvss_vector</label>
                    <input name="cvss_vector" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">open_time</label>
                    <input name="open_time" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">vul_repair_time</label>
                    <input name="vul_repair_time" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">vul_source</label>
                    <input name="vul_source" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">临时解决方案</label>
                    <input name="temp_plan" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">temp_plan_s3</label>
                    <input name="temp_plan_s3" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">formal_plan</label>
                    <input name="formal_plan" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">patch_s3</label>
                    <input name="patch_s3" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">参考链接</label>
                    <input name="patch_url" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">官方解决方案</label>
                    <input name="patch_use_func" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">cpe</label>
                    <input name="cpe" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">影响组件</label>
                    <input name="product_name" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">product_open</label>
                    <select name="product_open" class="form-select form-select-sm" aria-label="Default select example">
                        <option value="是" >是</option>
                        <option value="否" >否</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">product_store</label>
                    <input name="product_store" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">store_website</label>
                    <input name="store_website" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">assem_name</label>
                    <input name="assem_name" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">影响版本</label>
                    <input name="affect_ver" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">ver_open_date</label>
                    <input name="ver_open_date" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">sub_update_url</label>
                    <input name="sub_update_url" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">git_url</label>
                    <input name="git_url" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">git_commit_id</label>
                    <input name="git_commit_id" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">git_fixed_commit_id</label>
                    <input name="git_fixed_commit_id" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">平台分类</label>
                    <input name="product_cate" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">行业</label>
                    <input name="product_field" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">项目类型</label>
                    <input name="product_type" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">fofa_max</label>
                    <input name="fofa_max" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">fofa_con</label>
                    <input name="fofa_con" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">is_pass</label>
                    <input name="is_pass" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">is_sub_attack</label>
                    <input name="is_sub_attack" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">temp_plan_s3_hash</label>
                    <input name="temp_plan_s3_hash" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">patch_s3_hash</label>
                    <input name="patch_s3_hash" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">is_pass_attack</label>
                    <select name="is_pass_attack" class="form-select form-select-sm" aria-label="Default select example">
                        <option value="1" >是</option>
                        <option value="0" >否</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">作者</label>
                    <input name="auditor" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">cause</label>
                    <input name="cause" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">scan_time</label>
                    <input name="scan_time" class="form-control" placeholder="" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">是否有POC</label>
                    <select name="is_poc" class="form-select form-select-sm" aria-label="Default select example">
                        <option value="1" >是</option>
                        <option value="0" >否</option>
                    </select>
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
</div><div class="row" style="height: 50px"></div>


{include file='public/footer' /}
