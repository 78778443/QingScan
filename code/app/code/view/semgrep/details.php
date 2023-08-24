{include file='public/head' /}

    <div class="col-12">
        <div class="row tuchu">
            <h3 class="text-center" style="margin-bottom: 30px;color: #666;">
               <span>
                  <?php echo str_replace('data.tools.semgrep.', "", $info['check_id']); ?>
               </span>
            </h3>
            <!--漏洞基本信息 begin-->
            <section class="vul-basic-info">
                <div class="row">
                    <div class="col-auto">
                        <dl>
                            <dt>所属文件：</dt>
                            <dd>
                                    <?php
                                    $a = $info['project_name'];
                                    $tmpStr = preg_replace("/\/.*?\/$a/", "", $info['path']);
                                    echo $tmpStr;
                                    ?>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-auto">
                        <dl>
                            <dt>发现时间：</dt>
                            <dd><?php echo $info['create_time'] ?></dd>
                        </dl>
                    </div>
                    <div class="col-auto">
                        <dl>
                            <dt>所属项目：</dt>
                            <dd><?php echo $info['project_name'] ?></dd>
                        </dl>
                    </div>
                    <div class="col-auto">
                        <dl>
                            <dt>审核状态：</dt>
                            <dd class="hover-scroll">
                                <select class="changCheckStatus form-select form-control"
                                        data-id="<?php echo $info['id'] ?>">
                                    <option value="0" <?php echo $info['check_status'] == 0 ? 'selected' : ''; ?> >
                                        未审核
                                    </option>
                                    <option value="1" <?php echo $info['check_status'] == 1 ? 'selected' : ''; ?> >
                                        有效漏洞
                                    </option>
                                    <option value="2" <?php echo $info['check_status'] == 2 ? 'selected' : ''; ?> >
                                        无效漏洞
                                    </option>
                                </select>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-auto">
                        <dl data-type="CVE-ID">
                            <dt>危险等级：</dt>
                            <dd>
                                <?php echo $info['extra_severity'] ?>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-auto">
                        <dl data-type="CNNVD-ID">
                            <dt>缺陷位置：</dt>
                            <dd>
                                第<?php echo $info['start_offset'] ?>行
                            </dd>
                        </dl>

                    </div>
                </div>
            </section>

            <!--漏洞基本信息 end-->
        </div>
        <div class="row tuchu">
            <!--漏洞 PoC begin-->
            <section>
                <div class="clearfix">
                    <h3 class="pull-left" style="color: #999;">漏洞描述</h3>

                </div>
                <div class="padding-md">

                    <section>
                        <div class="circle" style="color: #666;"><?php echo $info['extra_message'] ?></div>
                    </section>
                    <br>
                </div>

            </section>
            <!--漏洞 PoC end-->
            <section class="vul-detail-section vul-detail-content">
                <div class="clearfix">
                    <h3 class="pull-left"  style="color: #999;">
                        错误代码
                    </h3>
                </div>
                <div class="content-holder padding-md">
                    <textarea class="form-control" rows="10" disabled style="background-color: #fff;">
                        <?php echo $info['extra_lines'] ?>
                    </textarea>

                </div>
            </section>
        </div>
        <div class="row tuchu">
            <div class="col-md-12">
                <div class="text-center" style="line-height: 36px;">
                    <input type="hidden" id="to_examine_url" value="<?php echo url('to_examine/semgrep') ?>">
                    <?php if ($info['check_status'] == 0) { ?>
                        <span class="follow-vul j-follow-vul ">
                            <a href="javascript:;" class="btn btn-sm btn-outline-secondary"
                               onclick="to_examine(<?php echo $info['id'] ?>)">审核</a>
                        </span>
                    <?php } ?>
                    <span class="follow-vul j-follow-vul ">
                      <a href="<?php echo url('semgrep/index') ?>"
                         class="btn btn-sm btn-outline-secondary">返回列表页</a>
                    </span>
                    <span class="follow-vul j-follow-vul ">
                        <a href="<?php echo url('semgrep/details', ['id' => $info['upper_id']]) ?>"
                           class="btn btn-sm btn-outline-secondary">上一页</a>
                    </span>
                    <span class="follow-vul j-follow-vul ">
                        <a href="<?php echo url('semgrep/details', ['id' => $info['lower_id']]) ?>"
                           class="btn btn-sm btn-outline-secondary">下一页</a>
                    </span>
                </div>
            </div>
        </div>
    </div>

<style>
    dt{
        color: #999;
    }
</style>
{include file='public/to_examine' /}
{include file='public/footer' /}