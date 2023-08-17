{include file='public/head' /}

<div class="row">
    <div class="col-2"></div>
    <div class="col-8">
        <div class="row tuchu">
            <h2 class="text-center">
               <span>
                  <?php echo str_replace('data.tools.semgrep.', "", $info['check_id']); ?>
               </span>
            </h2>
        </div>
        <div class="row tuchu">

            <!--漏洞基本信息 begin-->
            <section class="vul-basic-info">
                <div class="row">
                    <div class="col-auto">
                        <dl>
                            <dt>所属文件：</dt>
                            <dd>
                                <?php
                                    $path = preg_replace("/\/data\/codeCheck\/[a-zA-Z0-9]*\//", "", $info['path']);
                                    if ($project['is_online'] == 1) {
                                        $url = getGitAddr($project['name'], $project['ssh_url'], $info['path'], $info['end_line']);
                                    } else {
                                        $url = url('get_code',['id'=>$info['id'],'type'=>2]);
                                    }
                                ?>
                                <a href="<?php echo $url; ?>"
                                   target="_blank"><?php echo $path ?>
                                </a>
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
                                <select class="changCheckStatus form-select"
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
                    <h3 class="pull-left">漏洞描述</h3>

                </div>
                <div class="padding-md">

                    <section>
                        <div class="circle"><?php echo $info['extra_message'] ?></div>
                    </section>
                    <br>
                </div>

            </section>
            <!--漏洞 PoC end-->
            <section class="vul-detail-section vul-detail-content">
                <div class="clearfix">
                    <h3 class="pull-left">
                        错误代码
                    </h3>
                </div>
                <div class="content-holder padding-md">
                    <textarea class="form-control" rows="10" disabled>
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
                      <a href="<?php echo url('code/semgrep_list') ?>" class="btn btn-sm btn-outline-secondary">返回列表页</a>
                    </span>
                    <span class="follow-vul j-follow-vul ">
                        <a href="<?php echo url('code/semgrep_details', ['id' => $info['upper_id']]) ?>"
                           class="btn btn-sm btn-outline-secondary">上一页</a>
                    </span>
                    <span class="follow-vul j-follow-vul ">
                        <a href="<?php echo url('code/semgrep_details', ['id' => $info['lower_id']]) ?>"
                           class="btn btn-sm btn-outline-secondary">下一页</a>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-2"></div>
</div>
{include file='public/to_examine' /}
{include file='public/footer' /}