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
        width: 90%;
        min-width: 90%;
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

    a {
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
                      <?php echo $info['host'];?>
                   </span>
                    </h1>
                </div>
            </div>
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-12">
                    <div class="pull-right" style="line-height: 36px;">
                        <span class="follow-vul j-follow-vul ">
                          <a href="<?php echo url('hostPort/index') ?>" class="btn btn-sm btn-outline-secondary">返回列表页</a>
                        </span>
                        <span class="follow-vul j-follow-vul ">
                            <a href="<?php echo url('host_port/details', ['id' => $info['upper_id']]) ?>"
                               class="btn btn-sm btn-outline-secondary">上一页</a>
                        </span>
                        <span class="follow-vul j-follow-vul ">
                            <a href="<?php echo url('host_port/details', ['id' => $info['lower_id']]) ?>"
                               class="btn btn-sm btn-outline-secondary">下一页</a>
                        </span>
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
                                    <dt>端口类型：</dt>
                                    <dd >
                                        <?php echo $info['type']?>
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-md-4">
                                <dl>
                                    <dt>端口号：</dt>
                                    <dd>
                                        <?php echo $info['port']?>
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-md-4">
                                <dl data-type="CVE-ID">
                                    <dt>服务名称：</dt>
                                    <dd>
                                        <?php echo $info['service']?>
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
                            headers
                        </h3>
                    </div>
                    <div class="content-holder padding-md">
                        <?php echo $info['headers']?>
                    </div>

                </section>
                <!--漏洞详情 end-->

                <!--漏洞 PoC begin-->
                <section >
                    <div class="clearfix">
                        <h3 class="pull-left">html
                        </h3>
                    </div>
                    <div class="padding-md">
                        <?php echo $info['html']?>
                    </div>

                </section>
                <!--漏洞 PoC end-->
            </div>
        </div>
    </div>
</div>
{include file='public/footer' /}