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
                          <a href="<?php echo url('index') ?>" class="btn btn-sm btn-outline-secondary">返回列表页</a>
                        </span>
                        <span class="follow-vul j-follow-vul ">
                            <a href="<?php echo url('details', ['id' => $info['upper_id']]) ?>"
                               class="btn btn-sm btn-outline-secondary">上一页</a>
                        </span>
                        <span class="follow-vul j-follow-vul ">
                            <a href="<?php echo url('details', ['id' => $info['lower_id']]) ?>"
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
                        <div class="clearfix">
                            <h3 class="pull-left">
                                基本信息
                            </h3>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <dl>
                                    <dt>漏洞类型：</dt>
                                    <dd class="text-gray"><?php echo $info['Category']; ?></dd>
                                </dl>
                                <dl>
                                    <dt>发现时间：</dt>
                                    <dd class="text-gray"><?php echo $info['create_time']; ?></dd>
                                </dl>

                            </div>
                            <div class="col-md-4">
                                <dl>
                                    <dt>所属项目：</dt>
                                    <dd><?php echo $projectArr[$info['code_id']]['name'] ?></dd>
                                </dl>
                                <dl>
                                    <dt>审核状态：</dt>
                                    <dd>
                                        <select  class="changCheckStatus form-select"  data-id="<?php echo $info['id'] ?>">
                                            <option value="0" <?php echo $info['check_status'] == 0 ? 'selected' : ''; ?> >未审核</option>
                                            <option value="1" <?php echo $info['check_status'] == 1 ? 'selected' : ''; ?> >有效漏洞</option>
                                            <option value="2" <?php echo $info['check_status'] == 2 ? 'selected' : ''; ?> >无效漏洞</option>
                                        </select>
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-md-4">
                                <dl data-type="CVE-ID">
                                    <dt>危险等级：</dt>
                                    <dd><?php echo $info['Friority'] ?></dd>
                                </dl>

                            </div>
                            <div class="col-md-12">
                                <dl>
                                    <dt>漏洞描述：</dt>
                                    <dd class="text-gray"> <?php echo $info['Abstract'] ?></dd>
                                </dl>
                            </div>
                        </div>
                    </section>
                </div>
                <!--漏洞基本信息 end-->


                <?php
                $Source = $info['Source'];
                if (!empty($Source)) { ?>
                    <div class="bug-msg">
                        <section class="vul-basic-info" >
                            <div class="clearfix">
                                <h3 class="pull-left">
                                    污染来源
                                </h3>

                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <dl>
                                        <dt>参数来源：</dt>
                                        <dd class="text-gray"><?php echo $Source['FilePath'] ?></dd>
                                    </dl>
                                    <dl>
                                        <dt>行号：</dt>
                                        <dd><?php echo $Source['LineStart'] ?></dd>
                                    </dl>
                                </div>
                                <div class="col-md-4">


                                    <dl>
                                        <dt>目标函数：</dt>
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
                                触发点信息
                            </h3>

                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <dl>
                                    <dt>执行点：</dt>
                                    <dd class="text-gray"><?php echo $Primary['FilePath'] ?></dd>
                                </dl>
                                <dl>
                                    <dt>行号：</dt>
                                    <dd><?php echo $Primary['LineStart'] ?></dd>
                                </dl>
                            </div>
                            <div class="col-md-4">


                                <dl>
                                    <dt>目标函数：</dt>
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