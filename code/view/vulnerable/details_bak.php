{include file='public/head' /}

<div class="tuchu col-md-12 text-center">
    <h1><?php echo $info['name'] ?? ''; ?></h1>
</div>


<div class="tuchu col-md-12">
    <div class="row">
        <div class="col-md-4"><span class="key">CVE-ID：</span> <?php echo trim($info['cve_num']) ?> </div>
        <div class="col-md-4"><span class="key">SRC-ID：</span><?php echo $info['src_num'] ?></div>
        <div class="col-md-4"><span class="key">漏洞等级：</span><?php echo $info['vul_level'] ?></div>
        <div class="col-md-4"><span class="key">影响版本：</span><?php echo $info['affect_ver'] ?></div>
        <div class="col-md-4"><span class="key">项目类型：</span><?php echo $info['product_type'] ?></div>
        <div class="col-md-4"><span class="key">提交者：</span><?php echo mb_substr($info['user_name'], 3,) ?></div>
        <div class="col-md-4"><span class="key">CNVD-ID：</span> <?php echo $info['cnvd_num'] ?></div>
        <div class="col-md-4"><span class="key">CWE-ID：</span>
            <?php echo $info['cwe'] ?>
        </div>
        <div class="col-md-4"><span class="key">CVSS分数：</span>
            <?php echo $info['vul_cvss'] ?>
        </div>
        <div class="col-md-4"><span class="key">行业：</span>
            <?php echo $info['product_field'] ?>
        </div>
        <div class="col-md-4"><span class="key">fofa数量：</span>
            <?php echo $info['fofa_max'] ?>
        </div>
        <div class="col-md-4"><span class="key">CNNVD-ID：</span>
            <?php echo $info['cnnvd_num'] ?> </div>
        <div class="col-md-4"><span class="key">漏洞类别：</span>
            <?php echo $info['vul_type'] ?> </div>
        <div class="col-md-4"><span class="key">影响组件：</span>
            <?php echo $info['product_name'] ?>
        </div>
        <div class="col-md-4"><span class="key">平台分类：</span>
            <?php echo $info['product_cate'] ?>
        </div>
        <div class="col-md-4"><span class="key">提交时间：</span>
            <?php echo substr($info['open_time'], 0, 10) ?>
        </div>
    </div>
</div>

<div class="tuchu col-md-12">
    <div class="row">
        <style>
            .key {
                color: #999;
            }

            .col-md-4 {
                margin-top: 10px;
            }
        </style>
        <div class="col-md-4"><span class="key">cvss_vector</span>：<span><?php echo $info['cvss_vector'] ?></span>
        </div>
        <div class="col-md-4"><span
                    class="key">vul_repair_time</span>：<span><?php echo $info['vul_repair_time'] ?></span></div>
        <div class="col-md-4"><span class="key">vul_source</span>：<span><?php echo $info['vul_source'] ?></span>
        </div>
        <div class="col-md-4"><span class="key">temp_plan_s3</span>：<span><?php echo $info['temp_plan_s3'] ?></span>
        </div>
        <div class="col-md-4"><span class="key">formal_plan</span>：<span><?php echo $info['formal_plan'] ?></span>
        </div>
        <div class="col-md-4"><span class="key">patch_s3</span>：<span><?php echo $info['patch_s3'] ?></span></div>
        <div class="col-md-4"><span class="key">cpe</span>：<span><?php echo $info['cpe'] ?></span></div>
        <div class="col-md-4"><span
                    class="key">store_website</span>：<span><?php echo $info['store_website'] ?></span>
        </div>
        <div class="col-md-4"><span
                    class="key">product_store</span>：<span><?php echo $info['product_store'] ?></span>
        </div>
        <div class="col-md-4"><span class="key">assem_name</span>：<span><?php echo $info['assem_name'] ?></span>
        </div>
        <div class="col-md-4"><span class="key">affect_ver</span>：<span><?php echo $info['affect_ver'] ?></span>
        </div>
        <div class="col-md-4"><span
                    class="key">ver_open_date</span>：<span><?php echo $info['ver_open_date'] ?></span>
        </div>
        <div class="col-md-4"><span
                    class="key">sub_update_url</span>：<span><?php echo $info['sub_update_url'] ?></span>
        </div>
        <div class="col-md-4"><span class="key">git_url</span>：<span><?php echo $info['git_url'] ?></span></div>
        <div class="col-md-4"><span
                    class="key">git_commit_id</span>：<span><?php echo $info['git_commit_id'] ?></span>
        </div>
        <div class="col-md-4"><span
                    class="key">git_fixed_commit_id</span>：<span><?php echo $info['git_fixed_commit_id'] ?></span>
        </div>
        <div class="col-md-4"><span class="key">fofa_con</span>：<span><?php echo $info['fofa_con'] ?></span></div>
        <div class="col-md-4"><span
                    class="key">is_sub_attack</span>：<span><?php echo $info['is_sub_attack'] ?></span>
        </div>
        <div class="col-md-4"><span
                    class="key">temp_plan_s3_hash</span>：<span><?php echo $info['temp_plan_s3_hash'] ?></span></div>
        <div class="col-md-4"><span
                    class="key">patch_s3_hash</span>：<span><?php echo $info['patch_s3_hash'] ?></span>
        </div>
        <div class="col-md-4"><span
                    class="key">is_pass_attack</span>：<span><?php echo $info['is_pass_attack'] ?></span>
        </div>
        <div class="col-md-4"><span class="key">auditor</span>：<span><?php echo $info['auditor'] ?></span></div>
        <div class="col-md-4"><span class="key">cause</span>：<span><?php echo $info['cause'] ?></span></div>
        <div class="col-md-4"><span class="key">scan_time</span>：<span><?php echo $info['scan_time'] ?></span></div>
        <div class="col-md-4"><span class="key">来源</span>：<span><?php echo $info['patch_url'] ?></span></div>

        <div class="col-md-4"><span class="key">PoC</span>：<span> <?php echo $info['is_poc'] ?></span></div>
        <div class="col-md-4"><span class="key"> 参考链接 </span>：<span> <?php echo $info['patch_url'] ?></span></div>
        <div class="col-md-4"><span class="key">官方解决方案</span>：<span><?php echo $info['patch_use_func'] ?></span>
        </div>
    </div>
</div>
<div class="tuchu col-md-12 text-center">
    <span class="follow-vul j-follow-vul ">
      <a href="<?php echo url('vulnerable/index') ?>" class="btn btn-sm btn-outline-secondary">返回列表页</a>
    </span>
    <span class="follow-vul j-follow-vul ">
        <a href="<?php echo url('vulnerable/details', ['id' => $info['upper_id']]) ?>"
           class="btn btn-sm btn-outline-secondary">上一页</a>
    </span>
    <span class="follow-vul j-follow-vul ">
        <a href="<?php echo url('vulnerable/details', ['id' => $info['lower_id']]) ?>"
           class="btn btn-sm btn-outline-secondary">下一页</a>
    </span>
</div>
{include file='public/to_examine' /}
{include file='public/footer' /}