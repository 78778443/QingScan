{include file='public/head' /}
<div class="row">
    <div class="col-2"></div>
    <div class="col-8">
        <div class="row tuchu">
            <h2 class="text-center">
                       <span >
                          <?php echo $info['Category']; ?>
                       </span>
            </h2>
        </div>
        <div class="row tuchu">
            <div class="a-st">
                <!--漏洞基本信息 begin-->
                <div class="bug-msg">
                    <section class="vul-basic-info">
                        <div class="clearfix">
                            <h3 class="pull-left">
                                基本信息
                            </h3>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <dl>
                                    <dt>漏洞类型：</dt>
                                    <dd class="text-gray"><?php echo htmlentities($info['Category']); ?></dd>
                                </dl>
                                <dl>
                                    <dt>发现时间：</dt>
                                    <dd class="text-gray"><?php echo $info['create_time']; ?></dd>
                                </dl>

                            </div>
                            <div class="col-md-4">
                                <dl>
                                    <dt>所属项目：</dt>
                                    <dd><?php echo htmlentities($projectArr[$info['code_id']]['name']); ?></dd>
                                </dl>
                                <dl>
                                    <dt>审核状态：</dt>
                                    <dd>
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
                            <div class="col-md-4">
                                <dl data-type="CVE-ID">
                                    <dt>危险等级：</dt>
                                    <dd><?php echo $info['Friority'] ?></dd>
                                </dl>

                            </div>

                        </div>
                    </section>
                </div>
            </div>
        </div>
        <div class="row tuchu">

            <?php
            $Source = $info['Source'];

            if (!empty($Source)) { ?>
                <div class="bug-msg">
                    <section class="vul-basic-info">
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
                                        <?php echo isset($Source['TargetFunction'])?$Source['TargetFunction']:'' ?>
                                    </dd>
                                </dl>

                            </div>
                        </div>
                        <div class="row">
                            <textarea class="form-control" rows="10" disabled>
                                <?php echo htmlspecialchars(syntax_highlight($Source['Snippet'])) ?>
                            </textarea>
                        </div>

                    </section>
                </div>
            <?php } ?>

            <?php
            $Primary = $info['Primary'];
            ?>
            <div class="bug-msg">
                <section class="vul-basic-info">
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
                                    <?php echo isset($Primary['TargetFunction'])?$Primary['TargetFunction']:'' ?>
                                </dd>
                            </dl>

                        </div>
                    </div>
                    <div class="row">
                            <textarea class="form-control" rows="10" disabled>
                                <?php echo $Primary['Snippet']; ?>
                            </textarea>
                    </div>

                </section>
            </div>
        </div>
        <div class="row tuchu">
            <div class="col-md-12">
                <div class="text-center" >
                    <input type="hidden" id="to_examine_url" value="<?php echo url('to_examine/fortify') ?>">
                    {include file='public/to_examine' /}
                    <?php if ($info['check_status'] == 0) { ?>
                        <span class="follow-vul j-follow-vul ">
                                <a href="javascript:;" class="btn btn-sm btn-outline-secondary"
                                   onclick="to_examine(<?php echo $info['id'] ?>)">审核</a>
                            </span>
                    <?php } ?>
                    <span class="follow-vul j-follow-vul ">
                          <a href="<?php echo url('code/bug_list') ?>" class="btn btn-sm btn-outline-secondary">返回列表页</a>
                        </span>
                    <span class="follow-vul j-follow-vul ">
                            <a href="<?php echo url('code/bug_details', ['id' => $info['upper_id']]) ?>"
                               class="btn btn-sm btn-outline-secondary">上一页</a>
                        </span>
                    <span class="follow-vul j-follow-vul ">
                            <a href="<?php echo url('code/bug_details', ['id' => $info['lower_id']]) ?>"
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