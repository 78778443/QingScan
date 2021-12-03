{include file='public/head' /}
<style>
    .bug-msg {
        background-color: #fff;
    }

    .vul-basic-info {
        background-color: #fff;
        padding: 20px;
        margin-bottom: 10px;
        font-size: 12px;
    }

    .vul-detail-section {
        padding: 20px;
        line-height: 2;
        color: #444;
        margin-bottom: 10px;
        background-color: #fff;
        word-wrap: break-word;
    }

    .page-vul-detail-wrapper {
        padding-bottom: 90px;
    }

    .vul-title-wrapper {
        width: 100%;
        padding: 41px 0 33px;
    }

    .vul-title-wrapper h1 {
        margin: 0;
        font-size: 24px;
        font-weight: 400;
        width: 80%;
        min-width: 80%;
        line-height: 1.5;
        word-wrap: break-word;
        word-break: break-all;
        text-align: center;
        position: relative;
    }

    .vul-basic-info dl {
        color: #aaa;
        overflow: hidden;
        margin-bottom: 11px;
    }

    dl {
        display: block;
        margin-block-start: 1em;
        margin-block-end: 1em;
        margin-inline-start: 0px;
        margin-inline-end: 0px;
    }

    .vul-basic-info dd, .vul-basic-info dt {
        line-height: 20px;
        float: left;
    }

    a ,dd{
        color: #333;
        text-decoration: none;
    }
</style>
<div class="page-vul-detail-wrapper" style="margin-top: -50px">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="vul-title-wrapper clearfix">
                    <h1 class="pull-left" id="j-vul-title" data-vul-id="99367">
                       <span class="pull-titile">
                          <?php echo $info['name'];?>
                       </span>
                    </h1>
                </div>
            </div>
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-12">
                    <div class="pull-right" style="line-height: 36px;">
                        <span class="follow-vul j-follow-vul ">
                          <a href="<?php echo url('vulnerable/index') ?>" class="btn btn-outline-primary">返回列表页</a>
                        </span>
                        <span class="follow-vul j-follow-vul ">
                            <a href="<?php echo url('vulnerable/details', ['id' => $info['upper_id']]) ?>"
                               class="btn btn-outline-warning">上一页</a>
                        </span>
                        <span class="follow-vul j-follow-vul ">
                            <a href="<?php echo url('vulnerable/details', ['id' => $info['lower_id']]) ?>"
                               class="btn btn-outline-success">下一页</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row a-ts">
            <div class="a-st">
                <!--漏洞基本信息 begin-->
                <div class="bug-msg">
                    <section class="vul-basic-info" id="j-vul-basic-info">
                        <div class="row">
                            <div class="col-md-4">
                                <dl>
                                    <dt>CVE-ID：</dt>
                                    <dd class="text-gray"><?php echo $info['cve_num']?></dd>
                                </dl>
                                <dl>
                                    <dt>SRC-ID：</dt>
                                    <dd><?php echo $info['src_num']?></dd>
                                </dl>
                                <dl>
                                    <dt>漏洞等级：</dt>
                                    <dd><?php echo $info['vul_level']?></dd>
                                </dl>
                                <dl>
                                    <dt>影响版本：</dt>
                                    <dd><?php echo $info['affect_ver']?></dd>
                                </dl>
                                <dl>
                                    <dt>项目类型：</dt>
                                    <dd><?php echo $info['product_type']?></dd>
                                </dl>
                                <dl>
                                    <dt>提交者：</dt>
                                    <dd><?php echo mb_substr($info['user_name'],3,)?></dd>
                                </dl>
                            </div>
                            <div class="col-md-4">
                                <dl>
                                    <dt>CNVD-ID：</dt>
                                    <dd><?php echo $info['cnvd_num']?></dd>
                                </dl>

                                <dl>
                                    <dt>CWE-ID：</dt>
                                    <dd class="hover-scroll">
                                        <?php echo $info['cwe']?>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>CVSS分数：</dt>
                                    <dd class="hover-scroll">
                                        <?php echo $info['vul_cvss']?>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>行业：</dt>
                                    <dd class="hover-scroll">
                                        <?php echo $info['product_field']?>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>fofa数量：</dt>
                                    <dd><?php echo $info['fofa_max']?></dd>
                                </dl>
                            </div>
                            <div class="col-md-4">
                                <dl data-type="CVE-ID">
                                    <dt>CNNVD-ID：</dt>
                                    <dd>
                                        <?php echo $info['cnnvd_num']?>
                                    </dd>
                                </dl>
                                <dl data-type="CNNVD-ID">
                                    <dt>漏洞类别：</dt>
                                    <dd>
                                        <?php echo $info['vul_type']?>
                                    </dd>
                                </dl>
                                <dl data-type="CNNVD-ID">
                                    <dt>影响组件：</dt>
                                    <dd>
                                        <?php echo $info['product_name']?>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>平台分类：</dt>
                                    <dd class="hover-scroll">
                                        <?php echo $info['product_cate']?>
                                    </dd>
                                </dl>
                                <dl>
                                    <dt>提交时间：</dt>
                                    <dd><?php echo substr($info['open_time'],0,10)?></dd>
                                </dl>
                            </div>
                        </div>
                    </section>
                </div>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">cvss_vector</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['cvss_vector']?></div>
                        </section>
                        <br>
                    </div>

                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">vul_repair_time</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['vul_repair_time']?></div>
                        </section>
                        <br>
                    </div>

                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">vul_source</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['vul_source']?></div>
                        </section>
                        <br>
                    </div>

                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">temp_plan_s3</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['temp_plan_s3']?></div>
                        </section>
                        <br>
                    </div>

                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">formal_plan</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['formal_plan']?></div>
                        </section>
                        <br>
                    </div>

                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">patch_s3</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['patch_s3']?></div>
                        </section>
                        <br>
                    </div>

                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">cpe</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['cpe']?></div>
                        </section>
                        <br>
                    </div>

                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">store_website</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['store_website']?></div>
                        </section>
                        <br>
                    </div>
                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">product_store</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['product_store']?></div>
                        </section>
                        <br>
                    </div>
                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">assem_name</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['assem_name']?></div>
                        </section>
                        <br>
                    </div>
                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">affect_ver</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['affect_ver']?></div>
                        </section>
                        <br>
                    </div>
                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">ver_open_date</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['ver_open_date']?></div>
                        </section>
                        <br>
                    </div>
                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">sub_update_url</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['sub_update_url']?></div>
                        </section>
                        <br>
                    </div>
                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">git_url</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['git_url']?></div>
                        </section>
                        <br>
                    </div>
                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">git_commit_id</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['git_commit_id']?></div>
                        </section>
                        <br>
                    </div>
                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">git_fixed_commit_id</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['git_fixed_commit_id']?></div>
                        </section>
                        <br>
                    </div>
                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">fofa_con</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['fofa_con']?></div>
                        </section>
                        <br>
                    </div>
                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">is_sub_attack</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['is_sub_attack']?></div>
                        </section>
                        <br>
                    </div>
                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">temp_plan_s3_hash</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['temp_plan_s3_hash']?></div>
                        </section>
                        <br>
                    </div>
                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">patch_s3_hash</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['patch_s3_hash']?></div>
                        </section>
                        <br>
                    </div>
                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">is_pass_attack</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['is_pass_attack']?></div>
                        </section>
                        <br>
                    </div>
                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">auditor</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['auditor']?></div>
                        </section>
                        <br>
                    </div>
                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">cause</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['cause']?></div>
                        </section>
                        <br>
                    </div>
                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">scan_time</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['scan_time']?></div>
                        </section>
                        <br>
                    </div>
                </section>
                <section class="vul-detail-section vul-poc">
                    <div class="clearfix">
                        <h3 class="pull-left">来源</h3>
                    </div>
                    <div class="padding-md">
                        <section class="vul-need-contribute">
                            <div class="circle"><?php echo $info['patch_url']?></div>
                        </section>
                        <br>
                    </div>

                </section>

                <!--<section class="vul-detail-section vul-detail-content">
                    <div class="clearfix">
                        <h3 class="pull-left">
                            漏洞详情
                        </h3>
                    </div>
                    <div class="content-holder padding-md">
                        <?php /*echo $info['extra_lines']*/?>
                    </div>
                </section>-->

                <section class="vul-detail-section vul-detail-content">
                    <div class="clearfix">
                        <h3 class="pull-left">
                            PoC
                        </h3>
                    </div>
                    <div class="content-holder padding-md">
                        <?php echo $info['is_poc']?>
                    </div>
                </section>

                <section class="vul-detail-section vul-detail-content">
                    <div class="clearfix">
                        <h3 class="pull-left">
                            参考链接
                        </h3>
                    </div>
                    <div class="content-holder padding-md">
                        <?php echo $info['patch_url']?>
                    </div>
                </section>

                <section class="vul-detail-section vul-detail-content">
                    <div class="clearfix">
                        <h3 class="pull-left">
                            解决方案
                        </h3>
                    </div>
                    <div class="solution-txt">
                        <div class="padding-md">

                            <div class="panel-body">
                                <b>临时解决方案</b>
                                <p>
                                    <?php echo $info['temp_plan']?>
                                </p>
                            </div>
                            <div class="panel-body">
                                <b>官方解决方案</b>
                                <p>
                                    <?php echo $info['patch_use_func']?>
                                </p>
                            </div>
                            <!--<div class="panel-body">
                                <b>防护方案</b>
                                <p>

                                    暂无防护方案

                                </p>
                            </div>-->
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
{include file='public/to_examine' /}
{include file='public/footer' /}