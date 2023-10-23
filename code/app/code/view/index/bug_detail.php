{include file='public/head' /}
    <script type="text/javascript" src="scripts/shCore.js"></script>
    <script type="text/javascript" src="scripts/shBrushJScript.js"></script>
    <link type="text/css" rel="stylesheet" href="styles/shCoreDefault.css">
    <div class="row">

        <div class="col-md-9 ">
            <div class=" row tuchu">
                <table class="table  table-hover table-sm table-borderless">
                    <thead class="table-light">
                    <tr>
                        <th style="width:150px;">项目信息</th>
                        <th>项目内容</th>
                    </tr>
                    </thead>
                    <tr>
                        <td>项目地址</td>
                        <td><a href="<?php echo $project['web_url'] ?>" target="_blank"><?php echo $project['web_url'] ?></a></td>
                    </tr>
                    <tr>
                        <td>漏洞类型</td>
                        <td><?php echo $base['Category'] ?></td>
                    </tr>
                    <tr>
                        <td>危害等级</td>
                        <td><?php echo $base['Folder'] ?></td>
                    </tr>
                    <tr>
                        <td>项目ID</td>
                        <td><?php echo $base['code_id'] ?></td>
                    </tr>
                    <tr>
                        <td>漏洞描述</td>
                        <td><?php echo isset($base['Abstract'])?$base['Abstract']:'' ?></td>
                    </tr>
                </table>
            </div>
            <?php if (!empty($Source)) { ?>
                <div class=" row tuchu">
                    <table class="table  table-hover table-sm table-borderless">
                        <thead class="table-light">
                        <tr>
                            <th style="width:150px;">检测项</th>
                            <th>污染源信息</th>
                        </tr>
                        </thead>
                        <tr>
                            <td>参数来源</td>
                            <td>
                                <a title="<?php echo $project['web_url'] ?>/-/blob/master/<?php echo $Source['FilePath'] ?>"
                                   href="<?php echo $project['web_url'] ?>/-/blob/master/<?php echo $Source['FilePath'] ?>"
                                   target="_blank"><?php echo $Source['FilePath'] ?></a>
                            </td>
                        </tr>
                        <tr>
                            <td>行号</td>
                            <td><?php echo isset($Source['LineStart'])?$Source['LineStart']:'' ?></td>
                        </tr>
                        <tr>
                            <td>漏洞位置</td>
                            <td>
                                <pre><div contenteditable="false" style="border:none">
                                <?php echo syntax_highlight($Source['Snippet']) ?>
                                </div></pre>
                            </td>
                        </tr>
                        <tr>
                            <td>目标函数</td>
                            <td><?php echo isset($Source['TargetFunction'])?$Source['TargetFunction']:'' ?></td>
                        </tr>
                        <tr>
                            <td>源码内容</td>
                            <td>
                                <a class="btn btn-default" id="sourceBtn">查看源码</a>
                                <div id="sourceCode" style="display:none">
                                <pre><div contenteditable="false" style="border:none">
                                <?php echo getCode($project['id'], $Source['FilePath']) ?>
                                </div></pre>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            <?php } ?>
            <div class=" row tuchu">
                <table class="table  table-hover table-sm table-borderless">
                    <thead class="table-light">
                    <tr>
                        <th style="width:150px;">检测项</th>
                        <th>执行点信息</th>
                    </tr>
                    </thead>
                    <tr>
                        <td>触发文件</td>
                        <td>
                            <a title="<?php echo $project['web_url'] ?>/-/blob/master/<?php echo $Primary['FilePath'] ?>"
                               href="<?php echo $project['web_url'] ?>/-/blob/master/<?php echo $Primary['FilePath'] ?>"
                               target="_blank"><?php echo $Primary['FilePath'] ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td>行号</td>
                        <td><?php echo $Primary['LineStart'] ?></td>
                    </tr>
                    <tr>
                        <td>漏洞位置</td>
                        <td>
                                <pre><div contenteditable="false" style="border:none">
                                <?php echo syntax_highlight($Primary['Snippet']) ?>
                                </div></pre>
                        </td>
                    </tr>
                    <tr>
                        <td>目标函数</td>
                        <td><?php echo isset($Primary['TargetFunction'])?$Primary['TargetFunction']:'' ?></td>
                    </tr>
                    <tr>
                        <td>源码内容</td>
                        <td>
                            <a class="btn btn-default" id="PrimaryBtn">查看源码</a>
                            <div id="PrimaryCode" style="display:none">
                                <pre><div contenteditable="false" style="border:none">
                                <?php echo getCode($project['id'], $Primary['FilePath']) ?>
                                </div></pre>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-md-3 ">
            <div class=" row tuchu">
                <a class="btn btn-sm btn-outline-secondary"
                   href="/index.php?s=code_check/bug_list&=<?php echo $base['code_id'] ?>">返回列表</a>
                <a class="btn btn-sm btn-outline-secondary" href="/index.php?s=code_check/bug_detail&id=<?php echo $base['id'] + 1 ?>">下一个</a>
                <a class="btn btn-sm btn-outline-secondary" href="/index.php?s=code_check/bug_detail&id=<?php echo $base['id'] - 1 ?>">上一个</a>
            </div>
            <div class=" row tuchu">
                <form class="form-horizontal"
                      action="<?php echo U('code_check/_bug_comment', ['id' => $base['id']]) ?>">
                    <div class="mb-3">
                        <label for="inputEmail3" class="col-sm-2 control-label">评论</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="comment" rows="5"><?php echo $base['comment'] ?></textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="inputEmail3" class="col-sm-2 control-label">审核状态</label>
                        <div class="col-sm-10">
                            <label class="radio-inline">
                                <input type="radio"
                                       name="check_status" <?php echo ($base['check_status'] == 0) ? 'checked' : '' ?>
                                       value="0"> 未处理
                            </label>
                            <label class="radio-inline">
                                <input type="radio"
                                       name="check_status" <?php echo ($base['check_status'] == 1) ? 'checked' : '' ?>
                                       value="1"> 有效漏洞
                            </label>
                            <label class="radio-inline">
                                <input type="radio"
                                       name="check_status" <?php echo ($base['check_status'] == 2) ? 'checked' : '' ?>
                                       value="2"> 无效漏洞
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-default">添加评论</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            $("#sourceBtn").click(function () {
                if ($("#sourceBtn").html() == "隐藏源码") {
                    $("#sourceCode").css("display", "none");
                    $("#sourceBtn").html("查看源码");
                } else {
                    $("#sourceCode").css("display", "block");
                    $("#sourceBtn").html("隐藏源码");
                }
            });

            $("#PrimaryBtn").click(function () {
                if ($("#PrimaryBtn").html() == "隐藏源码") {
                    $("#PrimaryCode").css("display", "none");
                    $("#PrimaryBtn").html("查看源码");
                } else {
                    $("#PrimaryCode").css("display", "block");
                    $("#PrimaryBtn").html("隐藏源码");
                }
            });
        });

    </script>
{include file='public/footer' /}
