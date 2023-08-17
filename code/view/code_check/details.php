{include file='public/head' /}
<?php $value = $info; ?>
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
                    <h1 >
                       <span class="pull-titile">
                          <?php echo $info['vt_name'];?>
                       </span>
                    </h1>
                </div>
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-12">
                        <div class="pull-right" style="line-height: 36px;">
                            <input type="hidden" id="to_examine_url" value="<?php echo url('to_examine/awvs')?>">
                            {include file='public/to_examine' /}
                            <?php if($info['check_status'] == 0){?>
                                <span class="follow-vul j-follow-vul ">
                                <a href="javascript:;" class="btn btn-sm btn-outline-secondary" onclick="to_examine(<?php echo $info['id']?>)">审核</a>
                            </span>
                            <?php }?>
                            <span class="follow-vul j-follow-vul ">
                              <a href="<?php echo url('bug/awvs') ?>" class="btn btn-sm btn-outline-secondary">返回列表页</a>
                            </span>
                                <span class="follow-vul j-follow-vul ">
                                <a href="<?php echo url('code_check/bug_detail', ['id' => $info['upper_id']]) ?>"
                                   class="btn btn-sm btn-outline-secondary">上一页</a>
                            </span>
                                <span class="follow-vul j-follow-vul ">
                                <a href="<?php echo url('code_check/bug_detail', ['id' => $info['lower_id']]) ?>"
                                   class="btn btn-sm btn-outline-secondary">下一页</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row a-ts">
            <div class="a-st">
                <!--漏洞基本信息 begin-->
                <div class="bug-msg">
                    <section class="vul-basic-info" >
                        <div class="row">
                            <div class="col-md-4">
                                <dl>
                                    <dt>网址：</dt>
                                    <dd class="text-gray"><?php echo $info['affects_url']?></dd>
                                </dl>
                                <dl>
                                    <dt>审核状态：</dt>
                                    <dd class="text-gray">
                                        <select  class="changCheckStatus form-select"  data-id="<?php echo $info['id'] ?>">
                                            <option value="0" <?php echo $info['check_status'] == 0 ? 'selected' : ''; ?> >未审核</option>
                                            <option value="1" <?php echo $info['check_status'] == 1 ? 'selected' : ''; ?> >有效漏洞</option>
                                            <option value="2" <?php echo $info['check_status'] == 2 ? 'selected' : ''; ?> >无效漏洞</option>
                                        </select>
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-md-4">
                                <dl>
                                    <dt>来源：</dt>
                                    <dd>
                                        <?php echo $info['source']?>
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-md-4">
                                <dl data-type="CVE-ID">
                                    <dt>危险程度：</dt>
                                    <dd>
                                        <?php echo $info['severity']?>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </section>
                </div>
                <!--漏洞基本信息 end-->


                <!--漏洞详情 begin-->
                <section class="vul-detail-section vul-detail-content">
                    <div class="clearfix">
                        <h3 class="pull-left">
                            攻击详情
                        </h3>

                        <!--<div class="pull-right">
                            贡献者

                            <a class="user text-primary" href="/accounts/profile/None"></a>

                            共获得&nbsp;<i class="sebug-icon icon-money-yellow"></i>&nbsp;<span
                                    class="text-primary">0KB</span>
                        </div>-->

                    </div>

                    <div class="content-holder padding-md">
                        <?php echo $info['details']?>
                    </div>

                    <!--<div class="clearfix contribute">
                        <div class="pull-left exchange-list">

                            共&nbsp;<span class="text-primary">0</span>&nbsp; 兑换了
                        </div>

                    </div>-->

                </section>
                <!--漏洞详情 end-->

                <!--漏洞来源 begin-->
                <section class="vul-detail-section vul-source">
                    <h3>漏洞描述</h3>
                    <div class="padding-md">

                        <div id="j-md-source">
                            <?php echo $info['description']?>
                        </div>

                    </div>
                </section>

                <section class="vul-detail-section vul-source">
                    <h3>HTTP请求</h3>
                    <div class="padding-md">

                        <div id="j-md-source">
                            <?php echo $info['request']?>
                        </div>

                    </div>
                </section>

                <section class="vul-detail-section vul-source">
                    <h3>HTTP响应</h3>
                    <div class="padding-md">

                        <div id="j-md-source">
                            <?php echo $info['response_info']?>
                        </div>

                    </div>
                </section>

                <!--漏洞 PoC begin-->
                <section >
                    <div class="clearfix">
                        <h3 class="pull-left">此漏洞的影响
                        </h3>

                    </div>
                    <div class="padding-md">
                        <?php echo $info['impact']?>
                    </div>

                </section>
                <!--漏洞 PoC end-->


                <!--解决方案 begin-->
                <section class="vul-detail-section vul-solution">
                    <h3>解决方案</h3>

                    <div class="solution-txt">
                        <div class="padding-md">
                            <?php echo $info['recommendation']?>
                        </div>
                    </div>
                </section>
                <!--解决方案 end-->

                <!--参考链接-->
                <section class="vul-detail-section">
                    <h3>参考资料</h3>
                    <div class="solution-txt">
                        <div class="padding-md">
                            <div class="panel-body">
                                <?php echo $info['references']?>
                            </div>
                        </div>
                    </div>
                </section>
                <!--参考链接 end-->
            </div>
        </div>
    </div>
</div>
{include file='public/footer' /}