<html>
<head>
    <title>白盒项目扫描结果 QingScan</title>
    <link rel="shortcut icon" href="/static/favicon.svg" type="image/x-icon"/>
    <script src="/static/js/jquery.min.js"></script>
    <!--    <script src="/static/js/bootstrap.min.js"></script>-->
    <link href="/static/bootstrap-5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/css/qingscan.css" rel="stylesheet">
    <script src="/static/bootstrap-5.1.3/js/bootstrap.min.js"></script>
</head>
<body style="background-color: #eeeeee; min-height: 1080px">
<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
$dengjiArrColor = ['Low' => 'secondary', 'Medium' => 'primary', 'High' => 'warning text-dark', 'Critical' => 'danger'];
$show_level = [
    1=>'强烈建议修复',
    2=>'建议修复',
    3=>'可选修复'
];
?>

<div class="col-md-12 ">
    <div class="row tuchu">
        <div class="col-md-12" style="margin-bottom: 10px;"><h2 class="text-center">基本信息</h2>
            <hr>
        </div>

        <div class="col-md-4">
            <h5 style="align-content: center"><span style="color:#888">id:</span> <?php echo $info['id'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">名称: </span><?php echo $info['name'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">扫描状态: </span><?php echo $info['status'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">项目描述: </span><?php echo $info['desc'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">ssh_url: </span><?php echo $info['ssh_url'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">创建时间: </span><?php echo $info['create_time'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">拉取方式:</span> <?php echo $info['pulling_mode'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">star:</span> <?php echo $info['star'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">密码:</span> <?php echo $info['password'] ?></h5></div>
    </div>
    <div class="row tuchu">

        <div class="col-md-12" style="margin-bottom: 10px;"><h2 class="text-center">工具扫描动态</h2>
            <hr>
        </div>
        <div class="col-md-4">
            <h5><span style="color:#888">Fortify:</span> <?php echo $info['scan_time'] ?></h5>
        </div>
        <!--<div class="col-md-4">
            <h5><span style="color:#888">SonarQube:</span> <?php /*echo $info['sonar_scan_time'] */?></h5>
        </div>-->
        <div class="col-md-4">
            <h5><span style="color:#888">kunlun_scan_time:</span> <?php echo $info['kunlun_scan_time'] ?></h5>
        </div>
        <div class="col-md-4">
            <h5><span style="color:#888">semgrep:</span> <?php echo $info['semgrep_scan_time'] ?></h5>
        </div>
        <div class="col-md-4">
            <h5><span style="color:#888">mobsfscan_scan_time:</span> <?php echo $info['mobsfscan_scan_time'] ?></h5>
        </div>
        <div class="col-md-4">
            <h5><span style="color:#888">murphysec_scan_time:</span> <?php echo $info['murphysec_scan_time'] ?></h5>
        </div>
        <div class="col-md-4">
            <h5><span style="color:#888">composer组件:</span> <?php echo $info['composer_scan_time'] ?></h5>
        </div>
        <div class="col-md-4">
            <h5><span style="color:#888">java组件:</span> <?php echo $info['java_scan_time'] ?></h5>
        </div>
        <div class="col-md-4">
            <h5><span style="color:#888">Python组件:</span> <?php echo $info['python_scan_time'] ?></h5>
        </div>
        <div class="col-md-4">
            <h5><span style="color:#888">河马WebShell:</span> <?php echo $info['webshell_scan_time'] ?></h5>
        </div>
    </div>
</div>

<div class="row tuchu_margin">
    <div class="col-auto  tuchu_col">
        <h4 class="text-center">
            Fortify
            <a href="<?php echo url('code/rescan', ['id'=>$info['id'],'tools_name' => 'fortify']) ?>"
               onClick="return confirm('确定要清空该工具数据重新扫描吗?')"
               class="btn btn-sm btn-outline-warning">重新扫描</a>
            <a href="<?php echo url('fortify/index', ['code_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-primary" style="float: right">查看更多</a>
        </h4>
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>漏洞类型</th>
                <th>危险等级</th>
                <th>污染来源</th>
                <th>执行位置</th>
                <!--<th>所属项目</th>-->
                <th>扫描时间</th>
                <th>状态</th>
            </tr>
            </thead>
            <?php foreach ($fortify as $value) {
                $value['Source'] = json_decode($value['Source'],true);
                $value['Primary'] = json_decode($value['Primary'],true);
                ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['Category'] ?></td>
                    <td>
                        <span class="badge rounded-pill bg-<?php echo $dengjiArrColor[$value['Friority']] ?>"><?php echo $value['Friority'] ?></span>
                    </td>
                    <?php
                        if ($projectArr[$value['code_id']]['is_online'] == 1) {
                            $url = isset($projectArr[$value['code_id']]) ? $projectArr[$value['code_id']]['ssh_url'] : '';
                            $url .= '/-/blob/master/';
                            $url .= $value['Source']['FilePath'] ?? '';
                        } else {
                            $url = url('get_code',['id'=>$value['id'],'type'=>1]);
                        }
                    ?>
                    <td title="<?php echo htmlentities($value['Source']['Snippet'] ?? '') ?>">
                        <a href="<?php echo $url; ?>"
                           target="_blank">
                            <?php echo $value['Source']['FileName'] ?? '' ?>
                        </a>
                    </td>
                    <?php
                        if ($projectArr[$value['code_id']]['is_online'] == 1) {
                            $url = isset($projectArr[$value['code_id']]) ? $projectArr[$value['code_id']]['ssh_url'] : '';
                            $url .= '/-/blob/master/'.$value['Primary']['FilePath'];
                        } else {
                            $url = url('get_code',['id'=>$value['id'],'type'=>1]);
                        }
                    ?>
                    <td title="<?php echo htmlentities($value['Primary']['Snippet'] ?? '') ?>">
                        <a href="<?php echo $url; ?>"
                           target="_blank">
                            <?php echo isset($value['Primary'])?$value['Primary']['FileName']:'' ?>
                        </a>
                    </td>
                    <!--<td><a href="<?php /*echo U('code_check/bug_list', ['code_id' => $value['code_id']]) */?>">
                            <?php /*echo isset($projectArr[$value['code_id']]) ? $projectArr[$value['code_id']]['name'] : '' */?></a>
                    </td>-->
                    <td><?php echo $value['create_time'] ?></td>
                    <td>
                        <select class="changCheckStatus form-select" data-id="<?php echo $value['id'] ?>">
                            <option value="0" <?php echo $value['check_status'] == 0 ? 'selected' : ''; ?> >未审核</option>
                            <option value="1" <?php echo $value['check_status'] == 1 ? 'selected' : ''; ?> >有效漏洞
                            </option>
                            <option value="2" <?php echo $value['check_status'] == 2 ? 'selected' : ''; ?> >无效漏洞
                            </option>
                        </select>
                    </td>
                </tr>
            <?php } ?>
            <?php if (empty($fortify)) { ?>
                <tr>
                    <td colspan="8" class="text-center"><?php echo getScanStatus($info['id'], 'fortifyScan', 2); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="col-auto  tuchu_col">
        <h4 class="text-center">
            Kunlun-M
            <a href="<?php echo url('code/rescan', ['id'=>$info['id'],'tools_name' => 'kunlun']) ?>"
               onClick="return confirm('确定要清空该工具数据重新扫描吗?')"
               class="btn btn-sm btn-outline-warning">重新扫描</a>
            <a href="<?php echo url('kunlun/index', ['code_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-primary" style="float: right">查看更多</a>
        </h4>
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th> CVI ID</th>
                <th>编程语言</th>
                <th>VulFile Path/Title</th>
                <th>来源</th>
                <th>level</th>
                <th>Type</th>
                <th>状态</th>
            </tr>
            </thead>
            <?php foreach ($kunlun as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['cvi_id'] ?></td>
                    <td><?php echo $value['language'] ?></td>
                    <td><?php echo $value['vulfile_path'] ?></td>
                    <td><?php echo $value['source_code'] ?></td>
                    <td><?php echo $value['is_active'] ?></td>
                    <td><?php echo $value['result_type'] ?></td>
                    <td>
                        <select class="changCheckStatus form-select" data-id="<?php echo $value['id'] ?>">
                            <option value="0" <?php echo $value['check_status'] == 0 ? 'selected' : ''; ?> >未审核
                            </option>
                            <option value="1" <?php echo $value['check_status'] == 1 ? 'selected' : ''; ?> >有效漏洞
                            </option>
                            <option value="2" <?php echo $value['check_status'] == 2 ? 'selected' : ''; ?> >无效漏洞
                            </option>
                        </select>
                    </td>
                </tr>
            <?php } ?>
            <?php if (empty($kunlun)) { ?>
                <tr>
                    <td colspan="9" class="text-center"><?php echo getScanStatus($info['id'], 'kunlunScan', 2); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="col-auto  tuchu_col">
        <h4 class="text-center">
            SemGrep
            <a href="<?php echo url('code/rescan', ['id'=>$info['id'],'tools_name' => 'semgrep']) ?>"
               onClick="return confirm('确定要清空该工具数据重新扫描吗?')"
               class="btn btn-sm btn-outline-warning">重新扫描</a>
            <a href="<?php echo url('semgrep/index', ['code_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-primary" style="float: right">查看更多</a>
        </h4>
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>漏洞类型</th>
                <th>危险等级</th>
                <th>污染来源</th>
                <th>代码行号</th>
                <!--<th>所属项目</th>-->
                <th>扫描时间</th>
                <th>状态</th>
            </tr>
            </thead>
            <?php foreach ($semgrep as $value) {
                $project = getCodeInfo($value['code_id']);
                ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo str_replace('data.tools.semgrep.', "", $value['check_id']) ?></td>
                    <td><?php echo $value['extra_severity'] ?></td>
                    <td>
                        <?php
                        $path = preg_replace("/\/data\/codeCheck\/[a-zA-Z0-9]*\//", "", $value['path']);
                        if ($projectArr[$value['code_id']]['is_online'] == 1) {
                            $url = getGitAddr($project['name'], $project['ssh_url'], $value['path'], $value['end_line']);
                        } else {
                            $url = url('get_code',['id'=>$value['id'],'type'=>2]);
                        }
                        ?>
                        <a title="<?php echo htmlentities($value['extra_lines']) ?>" href="<?php echo $url ?>"
                           target="_blank"><?php echo $path ?>
                        </a>
                    </td>
                    <td>{$value['end_line']}</td>
                    <!--<td><?php /*echo isset($projectArr[$value['code_id']]) ? $projectArr[$value['code_id']]['name'] : '' */?></td>-->
                    <td><?php echo $value['create_time'] ?></td>
                    <td>
                        <select class="changCheckStatus form-select" data-id="<?php echo $value['id'] ?>">
                            <option value="0" <?php echo $value['check_status'] == 0 ? 'selected' : ''; ?> >未审核
                            </option>
                            <option value="1" <?php echo $value['check_status'] == 1 ? 'selected' : ''; ?> >有效漏洞
                            </option>
                            <option value="2" <?php echo $value['check_status'] == 2 ? 'selected' : ''; ?> >无效漏洞
                            </option>
                        </select>
                    </td>
                </tr>
            <?php } ?>
            <?php if (empty($semgrep)) { ?>
                <tr>
                    <td colspan="8" class="text-center"><?php echo getScanStatus($info['id'], 'semgrepScan'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="col-auto  tuchu_col">
        <h4 class="text-center">
            mobsfscan
            <a href="<?php echo url('code/rescan', ['id'=>$info['id'],'tools_name' => 'mobsfscan']) ?>"
               onClick="return confirm('确定要清空该工具数据重新扫描吗?')"
               class="btn btn-sm btn-outline-warning">重新扫描</a>
            <a href="<?php echo url('mobsfscan/index', ['code_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-primary" style="float: right">查看更多</a>
        </h4>
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>漏洞类型</th>
                <th>cwe</th>
                <th>漏洞描述</th>
                <th>input_case</th>
                <th>masvs</th>
                <th>owasp_mobile</th>
                <th>参考地址</th>
                <th>危险等级</th>
                <th>扫描时间</th>
                <th>状态</th>
            </tr>
            </thead>
            <?php foreach ($mobsfscan as $value) {
                $project = getCodeInfo($value['code_id']);
                ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['type']; ?></td>
                    <td><?php echo $value['cwe']; ?></td>
                    <td><?php echo $value['description']; ?></td>
                    <td><?php echo $value['input_case']; ?></td>
                    <td><?php echo $value['masvs']; ?></td>
                    <td><?php echo $value['owasp_mobile']; ?></td>
                    <td><?php echo $value['reference']; ?></td>
                    <td><?php echo $value['severity']; ?></td>
                    <td><?php echo $value['create_time'] ?></td>
                    <td>
                        <select class="changCheckStatus form-select" data-id="<?php echo $value['id'] ?>">
                            <option value="0" <?php echo $value['check_status'] == 0 ? 'selected' : ''; ?> >未审核
                            </option>
                            <option value="1" <?php echo $value['check_status'] == 1 ? 'selected' : ''; ?> >有效漏洞
                            </option>
                            <option value="2" <?php echo $value['check_status'] == 2 ? 'selected' : ''; ?> >无效漏洞
                            </option>
                        </select>
                    </td>
                </tr>
            <?php } ?>
            <?php if (empty($mobsfscan)) { ?>
                <tr>
                    <td colspan="12" class="text-center"><?php echo getScanStatus($info['id'], 'mobsfscan'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="col-auto  tuchu_col">
        <h4 class="text-center">
            murphysec
            <a href="<?php echo url('code/rescan', ['id'=>$info['id'],'tools_name' => 'murphysec']) ?>"
               onClick="return confirm('确定要清空该工具数据重新扫描吗?')"
               class="btn btn-sm btn-outline-warning">重新扫描</a>
            <a href="<?php echo url('murphysec/index', ['code_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-primary" style="float: right">查看更多</a>
        </h4>
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>缺陷组件</th>
                <th>处置建议</th>
                <th>当前版本</th>
                <th>最小修复版本</th>
                <th>语言</th>
                <th>修复状态</th>
                <th>时间</th>
            </tr>
            </thead>
            <?php foreach ($murphysec as $value) {
                $project = getCodeInfo($value['code_id']);
                ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['comp_name'] ?></td>
                    <td><?php echo $show_level[$value['show_level']] ?></td>
                    <td><?php echo $value['version'] ?></td>
                    <td><?php echo $value['min_fixed_version'] ?></td>
                    <td><?php echo $value['language'] ?></td>
                    <td><?php echo $value['repair_status']==1?'未修复':'已修复' ?></td>
                    <td><?php echo $value['create_time'] ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($murphysec)) { ?>
                <tr>
                    <td colspan="12" class="text-center"><?php echo getScanStatus($info['id'], 'crawlergoScan'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="col-auto  tuchu_col">
        <h4 class="text-center">
            河马(WebShell)
            <a href="<?php echo url('code/rescan', ['id'=>$info['id'],'tools_name' => 'webshell']) ?>"
               onClick="return confirm('确定要清空该工具数据重新扫描吗?')"
               class="btn btn-sm btn-outline-warning">重新扫描</a>
            <a href="<?php echo url('code_webshell/index', ['code_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-primary" style="float: right">查看更多</a>
        </h4>
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>类型</th>
                <th>文件路径</th>
                <th>扫描时间</th>
                <th>状态</th>
            </tr>
            </thead>
            <?php foreach ($hema as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['type'] ?></td>
                    <td><?php echo str_replace('/data/codeCheck/', '', $value['filename']) ?></td>
                    <td><?php echo $value['create_time']; ?></td>
                    <td><select class="changCheckStatus form-select" data-id="<?php echo $value['id'] ?>">
                            <option value="0" <?php echo $value['check_status'] == 0 ? 'selected' : ''; ?> >未审核
                            </option>
                            <option value="1" <?php echo $value['check_status'] == 1 ? 'selected' : ''; ?> >有效漏洞
                            </option>
                            <option value="2" <?php echo $value['check_status'] == 2 ? 'selected' : ''; ?> >无效漏洞
                            </option>
                        </select>
                    </td>
                </tr>
            <?php } ?>
            <?php if (empty($hema)) { ?>
                <tr>
                    <td colspan="7"
                        class="text-center"><?php echo getScanStatus($info['id'], 'code_webshell_scan', 2); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="col-auto  tuchu_col">
        <h4 class="text-center">
            JAVA
            <a href="<?php echo url('code/rescan', ['id'=>$info['id'],'tools_name' => 'java']) ?>"
               onClick="return confirm('确定要清空该工具数据重新扫描吗?')"
               class="btn btn-sm btn-outline-warning">重新扫描</a>
            <a href="<?php echo url('code_java/index', ['code_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-primary" style="float: right">查看更多</a>
        </h4>
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>项目名称</th>
                <th>modelVersion</th>
                <th>groupId</th>
                <th>artifactId</th>
                <th>version</th>
                <th>时间</th>
            </tr>
            </thead>
            <?php foreach ($java as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['code_id'] ?></td>
                    <td><?php echo $value['modelVersion'] ?></td>
                    <td><?php echo $value['groupId'] ?></td>
                    <td><?php echo $value['artifactId'] ?></td>
                    <td><?php echo $value['version'] ?></td>
                    <td><?php echo $value['create_time'] ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($java)) { ?>
                <tr>
                    <td colspan="8" class="text-center"><?php echo getScanStatus($info['id'], 'code_java', 2); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>


    <div class="col-auto  tuchu_col">
        <h4 class="text-center">
            python依赖
            <a href="<?php echo url('code/rescan', ['id'=>$info['id'],'tools_name' => 'python']) ?>"
               onClick="return confirm('确定要清空该工具数据重新扫描吗?')"
               class="btn btn-sm btn-outline-warning">重新扫描</a>
            <a href="<?php echo url('code_python/index', ['code_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-primary" style="float: right">查看更多</a>
        </h4>
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>项目名称</th>
                <th>依赖库</th>
                <th>时间</th>
            </tr>
            </thead>
            <?php foreach ($python as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['code_id'] ?></td>
                    <td><?php echo $value['name'] ?></td>
                    <td><?php echo $value['create_time'] ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($python)) { ?>
                <tr>
                    <td colspan="8" class="text-center"><?php echo getScanStatus($info['id'], 'code_python', 2); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="col-auto  tuchu_col">
        <h4 class="text-center">
            PHP依赖(Composer)
            <a href="<?php echo url('code/rescan', ['id'=>$info['id'],'tools_name' => 'php']) ?>"
               onClick="return confirm('确定要清空该工具数据重新扫描吗?')"
               class="btn btn-sm btn-outline-warning">重新扫描</a>
            <a href="<?php echo url('code_composer/index', ['code_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-primary" style="float: right">查看更多</a>
        </h4>
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>项目名称</th>
                <th>name</th>
                <th>version</th>
                <th>source</th>
                <th>require</th>
                <th>时间</th>
            </tr>
            </thead>
            <?php foreach ($php as $value) {
                ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['code_id'] ?></td>
                    <td><?php echo $value['name'] ?></td>
                    <td><?php echo $value['version'] ?></td>
                    <td><pre><?php echo $value['source'] ?></pre></td>
                    <td><pre><?php echo $value['require'] ?></pre></td>
                    <td><?php echo $value['create_time'] ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($php)) { ?>
                <tr>
                    <td colspan="8" class="text-center"><?php echo getScanStatus($info['id'], 'code_php', 2); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

</div>
<footer class="footer navbar-fixed-bottom">
    <div class=" footer-bottom">
        <ul class="list-inline text-center">
            <li style="color:red;">QingScan 产品仅授权你在遵守《<a
                        href="https://baike.baidu.com/item/%E4%B8%AD%E5%8D%8E%E4%BA%BA%E6%B0%91%E5%85%B1%E5%92%8C%E5%9B%BD%E7%BD%91%E7%BB%9C%E5%AE%89%E5%85%A8%E6%B3%95"
                        target="_blank">中华人民共和国网络安全法</a>》前提下使用，请不要擅自对未获得授权的目标进行安全测试！！！
            </li>
        </ul>
    </div>
</footer>
</body>
</html>