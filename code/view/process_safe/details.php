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
        min-width: 100%;
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

    a, dd {
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
                          <?php echo $info['cve_num']; ?>
                       </span>
                    </h1>

                </div>
            </div>
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-12">
                    <div class="pull-right" style="line-height: 36px;">
                        <span class="follow-vul j-follow-vul ">
                          <a href="<?php echo url('index') ?>" class="btn btn-outline-primary">???????????????</a>
                        </span>
                        <span class="follow-vul j-follow-vul ">
                            <a href="<?php echo url('details', ['id' => $info['upper_id']]) ?>"
                               class="btn btn-outline-warning">?????????</a>
                        </span>
                        <span class="follow-vul j-follow-vul ">
                            <a href="<?php echo url('details', ['id' => $info['lower_id']]) ?>"
                               class="btn btn-outline-success">?????????</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row a-ts">
            <div class="a-st">
                <!--?????????????????? begin-->
                <div class="bug-msg">
                    <section class="vul-basic-info" >
                        <div class="clearfix">
                            <h3 class="pull-left">
                                ????????????
                            </h3>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <dl>
                                    <dt>???????????????</dt>
                                    <dd class="text-gray"><?php echo $info['Category']; ?></dd>
                                </dl>
                                <dl>
                                    <dt>???????????????</dt>
                                    <dd class="text-gray"><?php echo $info['create_time']; ?></dd>
                                </dl>

                            </div>
                            <div class="col-md-4">
                                <dl>
                                    <dt>???????????????</dt>
                                    <dd><?php echo $projectArr[$info['code_id']]['name'] ?></dd>
                                </dl>
                                <dl>
                                    <dt>???????????????</dt>
                                    <dd>
                                        <select  class="changCheckStatus form-select"  data-id="<?php echo $info['id'] ?>">
                                            <option value="0" <?php echo $info['check_status'] == 0 ? 'selected' : ''; ?> >?????????</option>
                                            <option value="1" <?php echo $info['check_status'] == 1 ? 'selected' : ''; ?> >????????????</option>
                                            <option value="2" <?php echo $info['check_status'] == 2 ? 'selected' : ''; ?> >????????????</option>
                                        </select>
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-md-4">
                                <dl data-type="CVE-ID">
                                    <dt>???????????????</dt>
                                    <dd><?php echo $info['Friority'] ?></dd>
                                </dl>

                            </div>
                            <div class="col-md-12">
                                <dl>
                                    <dt>???????????????</dt>
                                    <dd class="text-gray"> <?php echo $info['Abstract'] ?></dd>
                                </dl>
                            </div>
                        </div>
                    </section>
                </div>
                <!--?????????????????? end-->


                <?php
                $Source = $info['Source'];
                if (!empty($Source)) { ?>
                    <div class="bug-msg">
                        <section class="vul-basic-info" >
                            <div class="clearfix">
                                <h3 class="pull-left">
                                    ????????????
                                </h3>

                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <dl>
                                        <dt>???????????????</dt>
                                        <dd class="text-gray"><?php echo $Source['FilePath'] ?></dd>
                                    </dl>
                                    <dl>
                                        <dt>?????????</dt>
                                        <dd><?php echo $Source['LineStart'] ?></dd>
                                    </dl>
                                </div>
                                <div class="col-md-4">


                                    <dl>
                                        <dt>???????????????</dt>
                                        <dd>
                                            <?php echo $Source['TargetFunction'] ?>
                                        </dd>
                                    </dl>

                                </div>
                            </div>
                            <div class="row">
                                <div id="sourceCode">
                                    <pre>
                                        <div contenteditable="false"
                                             style="border:none"> <?php echo syntax_highlight($Source['Snippet']) ?>
                                        </div>
                                    </pre>
                                </div>
                            </div>

                        </section>
                    </div>
                <?php } ?>

                <?php
                $Primary = $info['Primary'];
                ?>
                <div class="bug-msg">
                    <section class="vul-basic-info" >
                        <div class="clearfix">
                            <h3 class="pull-left">
                                ???????????????
                            </h3>

                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <dl>
                                    <dt>????????????</dt>
                                    <dd class="text-gray"><?php echo $Primary['FilePath'] ?></dd>
                                </dl>
                                <dl>
                                    <dt>?????????</dt>
                                    <dd><?php echo $Primary['LineStart'] ?></dd>
                                </dl>
                            </div>
                            <div class="col-md-4">


                                <dl>
                                    <dt>???????????????</dt>
                                    <dd>
                                        <?php echo $Primary['TargetFunction'] ?>
                                    </dd>
                                </dl>

                            </div>
                        </div>
                        <div class="row">
                            <div id="sourceCode">
                                    <pre>
                                        <div contenteditable="false"
                                             style="border:none"> <?php echo syntax_highlight($Primary['Snippet']) ?>
                                        </div>
                                    </pre>
                            </div>
                        </div>

                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
{include file='public/footer' /}