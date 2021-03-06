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
                                <a href="javascript:;" class="btn btn-outline-warning" onclick="to_examine(<?php echo $info['id']?>)">??????</a>
                            </span>
                            <?php }?>
                            <span class="follow-vul j-follow-vul ">
                              <a href="<?php echo url('bug/awvs') ?>" class="btn btn-outline-primary">???????????????</a>
                            </span>
                                <span class="follow-vul j-follow-vul ">
                                <a href="<?php echo url('code_check/bug_detail', ['id' => $info['upper_id']]) ?>"
                                   class="btn btn-outline-warning">?????????</a>
                            </span>
                                <span class="follow-vul j-follow-vul ">
                                <a href="<?php echo url('code_check/bug_detail', ['id' => $info['lower_id']]) ?>"
                                   class="btn btn-outline-success">?????????</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row a-ts">
            <div class="a-st">
                <!--?????????????????? begin-->
                <div class="bug-msg">
                    <section class="vul-basic-info" >
                        <div class="row">
                            <div class="col-md-4">
                                <dl>
                                    <dt>?????????</dt>
                                    <dd class="text-gray"><?php echo $info['affects_url']?></dd>
                                </dl>
                                <dl>
                                    <dt>???????????????</dt>
                                    <dd class="text-gray">
                                        <select  class="changCheckStatus form-select"  data-id="<?php echo $info['id'] ?>">
                                            <option value="0" <?php echo $info['check_status'] == 0 ? 'selected' : ''; ?> >?????????</option>
                                            <option value="1" <?php echo $info['check_status'] == 1 ? 'selected' : ''; ?> >????????????</option>
                                            <option value="2" <?php echo $info['check_status'] == 2 ? 'selected' : ''; ?> >????????????</option>
                                        </select>
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-md-4">
                                <dl>
                                    <dt>?????????</dt>
                                    <dd>
                                        <?php echo $info['source']?>
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-md-4">
                                <dl data-type="CVE-ID">
                                    <dt>???????????????</dt>
                                    <dd>
                                        <?php echo $info['severity']?>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </section>
                </div>
                <!--?????????????????? end-->


                <!--???????????? begin-->
                <section class="vul-detail-section vul-detail-content">
                    <div class="clearfix">
                        <h3 class="pull-left">
                            ????????????
                        </h3>

                        <!--<div class="pull-right">
                            ?????????

                            <a class="user text-primary" href="/accounts/profile/None"></a>

                            ?????????&nbsp;<i class="sebug-icon icon-money-yellow"></i>&nbsp;<span
                                    class="text-primary">0KB</span>
                        </div>-->

                    </div>

                    <div class="content-holder padding-md">
                        <?php echo $info['details']?>
                    </div>

                    <!--<div class="clearfix contribute">
                        <div class="pull-left exchange-list">

                            ???&nbsp;<span class="text-primary">0</span>&nbsp; ?????????
                        </div>

                    </div>-->

                </section>
                <!--???????????? end-->

                <!--???????????? begin-->
                <section class="vul-detail-section vul-source">
                    <h3>????????????</h3>
                    <div class="padding-md">

                        <div id="j-md-source">
                            <?php echo $info['description']?>
                        </div>

                    </div>
                </section>

                <section class="vul-detail-section vul-source">
                    <h3>HTTP??????</h3>
                    <div class="padding-md">

                        <div id="j-md-source">
                            <?php echo $info['request']?>
                        </div>

                    </div>
                </section>

                <section class="vul-detail-section vul-source">
                    <h3>HTTP??????</h3>
                    <div class="padding-md">

                        <div id="j-md-source">
                            <?php echo $info['response_info']?>
                        </div>

                    </div>
                </section>

                <!--?????? PoC begin-->
                <section >
                    <div class="clearfix">
                        <h3 class="pull-left">??????????????????
                        </h3>

                    </div>
                    <div class="padding-md">
                        <?php echo $info['impact']?>
                    </div>

                </section>
                <!--?????? PoC end-->


                <!--???????????? begin-->
                <section class="vul-detail-section vul-solution">
                    <h3>????????????</h3>

                    <div class="solution-txt">
                        <div class="padding-md">
                            <?php echo $info['recommendation']?>
                        </div>
                    </div>
                </section>
                <!--???????????? end-->

                <!--????????????-->
                <section class="vul-detail-section">
                    <h3>????????????</h3>
                    <div class="solution-txt">
                        <div class="padding-md">
                            <div class="panel-body">
                                <?php echo $info['references']?>
                            </div>
                        </div>
                    </div>
                </section>
                <!--???????????? end-->
            </div>
        </div>
    </div>
</div>
{include file='public/footer' /}