{include file='public/head' /}
<?php
$typeArr = [
    'whatweb' => 'whatweb',
    'oneforall' => 'oneforall',
    'hydra' => 'hydra',
    'dirmap' => 'dirmap',
    'sqlmap' => 'sqlmap',
];
?>
<div class="col-md-12 ">
    <div class="row tuchu">
        <div class="col-md-12" style="margin-bottom:10px;">
            <span style="font-size:18px;color:#ccc;">基本信息</span>
        </div>
        <div class="col-md-2">
            <span style="float: right"><span style="color:#ccc">id:</span> <?php echo $info['id'] ?></span>
        </div>
        <div class="col-md-2">
            <span style="float: right"><span
                        style="color:#ccc">状态:</span> <?php echo $info['status'] == 1 ? '启用' : '暂停' ?></span>
        </div>
        <div class="col-md-2">
            <span style="float: right"><span style="color:#ccc">名称: </span><?php echo $info['name'] ?></span></div>
        <div class="col-md-3">
            <span style="float: right"><span style="color:#ccc">URL: </span><?php echo $info['url'] ?></span></div>
        <div class="col-md-3">
            <span style="float: right"><span style="color:#ccc">创建: </span><?php echo $info['create_time'] ?></span>
        </div>
        <?php if ($info['username']) { ?>
            <div class="col-md-4">
                <span style="float: right"><span
                            style="color:#ccc">用户名称:</span> <?php echo $info['username'] ?></span>
            </div>
            <div class="col-md-4">
                <span style="float: right"><span style="color:#ccc">密码:</span> <?php echo $info['password'] ?></span>
            </div>
        <?php } ?>


</div>


<div class="row tuchu_margin">
    <div class="col-12  tuchu_col">
        <span class="text-center">
            RAD <span style="color:#ccc;">(URL爬虫)</span></span>
        <a href="<?php echo url('app/rescan', ['id' => $info['id'], 'tools_name' => 'rad']) ?>"
           onClick="return confirm('确定要清空该工具数据ReScan吗?')"
           class="btn btn-sm btn-outline-secondary" style="float: right">ReScan</a>
        <a href="<?php echo url('urls/index', ['app_id' => $info['id']]) ?>"
           class="btn btn-sm btn-outline-secondary" style="float: right">more</a>
        </span>
        <table class="table  table-hover table-sm table-borderless">
            <thead class="table-light">
            <tr style="color:#ccc;">
                <th>ID</th>
                <th>URL</th>
                <th>APP</th>
                <th>ICP</th>
                <th>邮箱</th>
                <th>创建时间</th>
            </tr>
            </thead>
            <?php foreach ($urls as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td class="ellipsis-type"><a href="<?php echo $value['url'] ?>"
                                                 target="_blank"><?php echo $value['url'] ?></a></td>
                    <td>
                        <a href="<?php echo U('urls/index', ['app_id' => $value['app_id']]) ?>"><?php echo isset($appArr[$value['app_id']]) ? $appArr[$value['app_id']] : '' ?></a>
                    </td>
                    <td><?php echo $value['icp'] ?></td>
                    <td><?php echo $value['email'] ?? ''?></td>
                    <td><?php echo $value['create_time'] ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($urls)) { ?>
                <tr>
                    <td colspan="7" class="text-center"><?php echo getScanStatus($info['id'], 'rad'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="col-12  tuchu_col">
        <span class="text-center">
            <span>crawlergo<span style="color:#ccc;">(URL爬虫扫描)</span></span>
            <a href="<?php echo url('app/rescan', ['id' => $info['id'], 'tools_name' => 'crawlergoScan']) ?>"
               onClick="return confirm('确定要清空该工具数据ReScan吗?')"
               class="btn btn-sm btn-outline-secondary" style="float: right">ReScan</a>
            <a href="<?php echo url('app_crawlergo/index', ['app_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-secondary" style="float: right">more</a>
        </span>
        <table class="table  table-hover table-sm" style="color:#ccc;">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>url</th>
                <th></th>
            </tr>
            </thead>
            <?php foreach ($crawlergo as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td class="AutoNewline"><?php echo $value['url'] ?></td>
                    <td><?php echo $value['create_time']; ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($crawlergo)) { ?>
                <tr>
                    <td colspan="3" class="text-center"><?php echo getScanStatus($info['id'], 'crawlergoScan'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="col-12  tuchu_col">
        <span class="text-center">
            AWVS <span style="color:#ccc;">(综合扫描)</span>
            <a href="<?php echo url('app/rescan', ['id' => $info['id'], 'tools_name' => 'awvsScan']) ?>"
               onClick="return confirm('确定要清空该工具数据ReScan吗?')"
               class="btn btn-sm btn-outline-secondary" style="float: right">ReScan</a>
            <a href="<?php echo url('bug/awvs', ['app_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-secondary" style="float: right">more</a>
        </span>
        <table class="table  table-hover table-sm" style="color:#ccc;">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>severity</th>
                <th>URL</th>
                <th>发现时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <?php foreach ($awvs as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['vt_name'] ?></td>
                    <td><?php echo $value['affects_url'] ?></td>
                    <td><?php echo $value['create_time'] ?></td>
                    <td>
                        <a href="<?php echo url('code_check/bug_detail', ['id' => $value['id']]) ?>"
                           class="btn btn-sm btn-outline-secondary">查看漏洞</a>
                    </td>
                </tr>
            <?php } ?>
            <?php if (empty($awvs)) { ?>
                <tr>
                    <td colspan="5" class="text-center"><?php echo getScanStatus($info['id'], 'awvsScan'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="col-12  tuchu_col">
        <span class="text-center">
            nuclei <span style="color:#ccc;">(POC扫描)</span>
            <a href="<?php echo url('app/rescan', ['id' => $info['id'], 'tools_name' => 'nucleiScan']) ?>"
               onClick="return confirm('确定要清空该工具数据ReScan吗?')"
               class="btn btn-sm btn-outline-secondary" style="float: right">ReScan</a>
            <a href="<?php echo url('app_nuclei/index', ['app_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-secondary" style="float: right">more</a>
        </span>
        <table class="table  table-hover table-sm" style="color:#ccc;">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>name</th>
                <th>host</th>
                <th></th>
            </tr>
            </thead>
            <?php foreach ($nuclei as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['name'] ?></td>
                    <td><?php echo $value['host'] ?></td>
                    <td><?php echo $value['create_time']; ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($nuclei)) { ?>
                <tr>
                    <td colspan="4" class="text-center"><?php echo getScanStatus($info['id'], 'nucleiScan'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="col-12  tuchu_col">
        <span class="text-center">
            XRAY <span style="color:#ccc;">(黑盒+POC)</span>
            <a href="<?php echo url('app/rescan', ['id' => $info['id'], 'tools_name' => 'xray']) ?>"
               onClick="return confirm('确定要清空该工具数据ReScan吗?')"
               class="btn btn-sm btn-outline-secondary" style="float: right">ReScan</a>
            <a href="<?php echo url('xray/index', ['app_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-secondary" style="float: right">more</a>
        </span>
        <table class="table  table-hover table-sm" style="color:#ccc;">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>漏洞类型</th>
                <th>URL地址</th>
                <th>创建时间</th>
            </tr>
            </thead>
            <?php foreach ($xray as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['plugin'] ?></td>
                    <td><?php echo json_decode($value['target'], true)['url'] ?></td>
                    <td><?php echo date('Y-m-d H:i:s', substr($value['create_time'], 0, 10)) ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($xray)) { ?>
                <tr>
                    <td colspan="9" class="text-center"><?php echo getScanStatus($info['id'], 'xray'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="col-12  tuchu_col">
        <span class="text-center">
            app信息
            <a href="<?php echo url('app/rescan', ['id' => $info['id'], 'tools_name' => 'getBaseInfo']) ?>"
               onClick="return confirm('确定要清空该工具数据ReScan吗?')"
               class="btn btn-sm btn-outline-secondary" style="float: right">ReScan</a>
        </span>
        <table class="table  table-hover table-sm" style="color:#ccc;">
            <thead class="table-light">
            <tr>
                <th>app_id</th>
                <th>cms</th>
                <th>server</th>
                <th>statuscode</th>
                <th>length</th>
                <th>page_title</th>
                <th>header</th>
                <th>icon</th>
                <th>url_screenshot</th>
                <!--                <th>时间</th>-->
            </tr>
            </thead>
            <?php foreach ($app_info as $value) { ?>
                <tr>
                    <td><?php echo $value['app_id'] ?></td>
                    <td><?php echo $value['cms'] ?></td>
                    <td><?php echo $value['server'] ?></td>
                    <td><?php echo $value['statuscode'] ?></td>
                    <td><?php echo $value['length'] ?></td>
                    <td><?php echo $value['page_title'] ?></td>
                    <td><?php echo $value['header'] ?></td>
                    <td><img src="<?php echo str_replace('/root/qingscan/code/public/', "", $value['icon']) ?>"></td>
                    <td><?php echo $value['url_screenshot'] ?></td>
                    <!--                    <td>--><?php //echo $value['create_time'] ?><!--</td>-->
                </tr>
            <?php } ?>
            <?php if (empty($app_info)) { ?>
                <tr>
                    <td colspan="11" class="text-center"><?php echo getScanStatus($info['id'], 'getBaseInfo'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="col-12  tuchu_col">
        <span class="text-center">
            whatweb <span style="color:#ccc;">（指纹识别）</span>
            <a href="<?php echo url('app/rescan', ['id' => $info['id'], 'tools_name' => 'whatweb']) ?>"
               onClick="return confirm('确定要清空该工具数据ReScan吗?')"
               class="btn btn-sm btn-outline-secondary" style="float: right">ReScan</a>
            <a href="<?php echo url('whatweb/index', ['app_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-secondary" style="float: right">more</a>
        </span>
        <table class="table  table-hover table-sm" style="color:#ccc;">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>target</th>
                <th>http_status</th>
                <th>request_config</th>
                <th>plugins</th>
                <th>发布时间</th>
            </tr>
            </thead>
            <?php foreach ($whatweb as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['target'] ?></td>
                    <td><?php echo $value['http_status'] ?></td>
                    <td><?php echo $value['request_config'] ?></td>
                    <td><?php echo $value['plugins'] ?></td>
                    <td><?php echo $value['create_time'] ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($whatweb)) { ?>
                <tr>
                    <td colspan="6" class="text-center"><?php echo getScanStatus($info['id'], 'whatweb'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="col-12  tuchu_col">
        <span class="text-center">
            sqlmap <span style="color:#ccc;">（SQL注入）</span>
            <a href="<?php echo url('app/rescan', ['id' => $info['id'], 'tools_name' => 'sqlmapScan']) ?>"
               onClick="return confirm('确定要清空该工具数据ReScan吗?')"
               class="btn btn-sm btn-outline-secondary" style="float: right">ReScan</a>
            <a href="<?php echo url('sqlmap/index', ['app_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-secondary" style="float: right">more</a>
        </span>
        <table class="table  table-hover table-sm" style="color:#ccc;">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>urls_id</th>
                <th>type</th>
                <th>title</th>
                <th>payload</th>
                <th>dbms</th>
                <th>application</th>
                <th>时间</th>
            </tr>
            </thead>
            <?php foreach ($sqlmap as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['urls_id'] ?></td>
                    <td><?php echo $value['type'] ?></td>
                    <td><?php echo $value['title'] ?></td>
                    <td class="AutoNewline"><?php echo $value['payload'] ?></td>
                    <td><?php echo $value['dbms'] ?></td>
                    <td><?php echo $value['application'] ?></td>
                    <td><?php echo $value['create_time'] ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($sqlmap)) { ?>
                <tr>
                    <td colspan="8" class="text-center"><?php echo getScanStatus($info['id'], 'sqlmapScan'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="col-12  tuchu_col">
        <span class="text-center">
            oneforall <span style="color:#ccc;">（子域名）</span>
            <a href="<?php echo url('app/rescan', ['id' => $info['id'], 'tools_name' => 'subdomainScan']) ?>"
               onClick="return confirm('确定要清空该工具数据ReScan吗?')"
               class="btn btn-sm btn-outline-secondary" style="float: right">ReScan</a>
            <a href="<?php echo url('one_for_all/index', ['app_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-secondary" style="float: right">more</a>
        </span>
        <table class="table  table-hover table-sm" style="color:#ccc;">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>域名</th>
                <th>端口</th>
                <th>ip</th>
                <th>状态</th>
                <th>创建时间</th>
            </tr>
            </thead>
            <?php foreach ($oneforall as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['subdomain'] ?></td>
                    <td><?php echo $value['port'] ?></td>
                    <td><?php echo $value['ip'] ?></td>
                    <td><?php echo $value['status'] ?></td>
                    <td><?php echo $value['create_time'] ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($oneforall)) { ?>
                <tr>
                    <td colspan="6" class="text-center"><?php echo getScanStatus($info['id'], 'subdomainScan'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="col-12  tuchu_col">
        <span class="text-center">
            hydra <span style="color:#ccc;">（主机暴力破解）</span>
            <a href="<?php echo url('app/rescan', ['id' => $info['id'], 'tools_name' => 'sshScan']) ?>"
               onClick="return confirm('确定要清空该工具数据ReScan吗?')"
               class="btn btn-sm btn-outline-secondary" style="float: right">ReScan</a>
            <a href="<?php echo url('hydra/index', ['app_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-secondary" style="float: right">more</a>
        </span>
        <table class="table  table-hover table-sm" style="color:#ccc;">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>host</th>
                <th>type</th>
                <th>username</th>
                <th>状态</th>
            </tr>
            </thead>
            <?php foreach ($hydra as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['host'] ?></td>
                    <td><?php echo $value['type'] ?></td>
                    <td><?php echo $value['username'] ?></td>
                    <td><?php echo date('Y-m-d H:i:s', substr($value['create_time'], 0, 10)) ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($hydra)) { ?>
                <tr>
                    <td colspan="5" class="text-center"><?php echo getScanStatus($host_id, 'sshScan'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="col-12  tuchu_col">
        <span class="text-center">
            dirmap <span style="color:#ccc;">（扫后台）</span>
            <a href="<?php echo url('app/rescan', ['id' => $info['id'], 'tools_name' => 'dirmapScan']) ?>"
               onClick="return confirm('确定要清空该工具数据ReScan吗?')"
               class="btn btn-sm btn-outline-secondary" style="float: right">ReScan</a>
            <a href="<?php echo url('dirmap/index', ['app_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-secondary" style="float: right">more</a>
        </span>
        <table class="table  table-hover table-sm" style="color:#ccc;">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>code</th>
                <th>size</th>
                <th>type</th>
                <th>url</th>
                <th>时间</th>
            </tr>
            </thead>
            <?php foreach ($dirmap as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['code'] ?></td>
                    <td><?php echo $value['size'] ?></td>
                    <td><?php echo $value['type'] ?></td>
                    <td><?php echo $value['url'] ?></td>
                    <td><?php echo $value['create_time'] ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($dirmap)) { ?>
                <tr>
                    <td colspan="6" class="text-center"><?php echo getScanStatus($info['id'], 'dirmapScan'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="col-12  tuchu_col">
        <span class="text-center">
            masscan列表
            <a href="<?php echo url('app/rescan', ['id' => $info['id'], 'tools_name' => 'NmapPortScan']) ?>"
               onClick="return confirm('确定要清空该工具数据ReScan吗?')"
               class="btn btn-sm btn-outline-secondary" style="float: right">ReScan</a>
            <a href="<?php echo url('host_port/index', ['app_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-secondary" style="float: right">more</a>
        </span>
        <table class="table  table-hover table-sm" style="color:#ccc;">
            <thead class="table-light">
            <tr>
                <th>id</th>
                <th>port</th>
                <th>host</th>
                <th>type</th>
                <th>service</th>
                <th>is_close</th>
                <th>create_time</th>
                <th>update_time</th>
                <th>os</th>
                <th>html</th>
                <th>headers</th>
                <th>scan_time</th>
            </tr>
            </thead>
            <?php foreach ($host_port as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['port'] ?></td>
                    <td><?php echo $value['host'] ?></td>
                    <td><?php echo $value['type'] ?></td>
                    <td><?php echo $value['service'] ?></td>
                    <td><?php echo $value['is_close'] ?></td>
                    <td class="AutoNewline"><?php echo $value['create_time'] ?></td>
                    <td><?php echo $value['update_time'] ?></td>
                    <td><?php echo $value['os'] ?></td>
                    <td><?php echo $value['html'] ?></td>
                    <td><?php echo $value['headers'] ?></td>
                    <td><?php echo $value['scan_time'] ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($host_port)) { ?>
                <tr>
                    <td colspan="13" class="text-center"><?php echo getScanStatus($info['id'], 'NmapPortScan'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="col-12  tuchu_col">
        <span class="text-center">
            vulmap信息
            <a href="<?php echo url('app/rescan', ['id' => $info['id'], 'tools_name' => 'vulmapPocTest']) ?>"
               onClick="return confirm('确定要清空该工具数据ReScan吗?')"
               class="btn btn-sm btn-outline-secondary" style="float: right">ReScan</a>
            <a href="<?php echo url('vulmap/index', ['app_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-secondary" style="float: right">more</a>
        </span>
        <table class="table  table-hover table-sm" style="color:#ccc;">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>app_id</th>
                <th>urls_id</th>
                <th>author</th>
                <th>description</th>
                <th>host</th>
                <th>port</th>
                <th>param</th>
                <th>request</th>
                <th>payload</th>
                <th>response</th>
                <th>url</th>
                <th>plugin</th>
                <th>target</th>
                <th>vuln_class</th>
                <th>create_time</th>
            </tr>
            </thead>
            <?php foreach ($app_vulmap as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['app_id'] ?></td>
                    <td><?php echo $value['user_id'] ?></td>
                    <td><?php echo $value['author'] ?></td>
                    <td><?php echo $value['description'] ?></td>
                    <td><?php echo $value['host'] ?></td>
                    <td><?php echo $value['port'] ?></td>
                    <td class="AutoNewline"><?php echo $value['param'] ?></td>
                    <td><?php echo $value['request'] ?></td>
                    <td><?php echo $value['payload'] ?></td>
                    <td><?php echo $value['response'] ?></td>
                    <td><?php echo $value['url'] ?></td>
                    <td><?php echo $value['plugin'] ?></td>
                    <td><?php echo $value['target'] ?></td>
                    <td><?php echo $value['vuln_class'] ?></td>
                    <td><?php echo $value['create_time'] ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($app_vulmap)) { ?>
                <tr>
                    <td colspan="16" class="text-center"><?php echo getScanStatus($info['id'], 'vulmapPocTest'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="col-12  tuchu_col">
        <span class="text-center">
            主机列表
            <a href="<?php echo url('app/rescan', ['id' => $info['id'], 'tools_name' => 'autoAddHost']) ?>"
               onClick="return confirm('确定要清空该工具数据ReScan吗?')"
               class="btn btn-sm btn-outline-secondary" style="float: right">ReScan</a>
            <a href="<?php echo url('host/index', ['app_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-secondary" style="float: right">more</a>
        </span>
        <table class="table  table-hover table-sm" style="color:#ccc;">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>app_id</th>
                <th>domain</th>
                <th>host</th>
                <th>status</th>
                <th>create_time</th>
                <th>isp</th>
                <th>country</th>
                <th>region</th>
                <th>city</th>
                <th>area</th>
                <th>hydra_scan_time</th>
                <th>port_scan_time</th>
                <th>target</th>
            </tr>
            </thead>
            <?php foreach ($host as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['app_id'] ?></td>
                    <td><?php echo $value['domain'] ?></td>
                    <td><?php echo $value['host'] ?></td>
                    <td><?php echo $value['status'] ?></td>
                    <td><?php echo $value['create_time'] ?></td>
                    <td><?php echo $value['isp'] ?></td>
                    <td class="AutoNewline"><?php echo $value['country'] ?></td>
                    <td><?php echo $value['region'] ?></td>
                    <td><?php echo $value['city'] ?></td>
                    <td><?php echo $value['area'] ?></td>
                    <td><?php echo $value['hydra_scan_time'] ?></td>
                    <td><?php echo $value['port_scan_time'] ?></td>
                    <td><?php echo $value['ip_scan_time'] ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($host)) { ?>
                <tr>
                    <td colspan="16" class="text-center"><?php echo getScanStatus($info['id'], 'autoAddHost'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="col-12  tuchu_col">
        <span class="text-center">
            DisMap <span style="color:#ccc;">（CMS指纹识别）</span>
            <a href="<?php echo url('app/rescan', ['id' => $info['id'], 'tools_name' => 'dismapScan']) ?>"
               onClick="return confirm('确定要清空该工具数据ReScan吗?')"
               class="btn btn-sm btn-outline-secondary" style="float: right">ReScan</a>
        </span>
        <table class="table  table-hover table-sm" style="color:#ccc;">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>result</th>
                <th>时间</th>
            </tr>
            </thead>
            <?php foreach ($app_dismap as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td>{$value['result']}</td>
                    <td><?php echo $value['create_time'] ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($app_dismap)) { ?>
                <tr>
                    <td colspan="3" class="text-center"><?php echo getScanStatus($info['id'], 'dismapScan'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="col-12  tuchu_col">
        <span class="text-center">
            自定义插件
            <a href="<?php echo url('app/rescan', ['id' => $info['id'], 'tools_name' => 'plugin']) ?>"
               onClick="return confirm('确定要清空该工具数据ReScan吗?')"
               class="btn btn-sm btn-outline-secondary" style="float: right">ReScan</a>
            <a href="<?php echo url('plugin_result/index', ['app_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-secondary" style="float: right">more</a>
        </span>
        <table class="table  table-hover table-sm" style="color:#ccc;">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>插件名称</th>
                <th>内容</th>
            </tr>
            </thead>
            <?php foreach ($pluginScanLog as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['plugin_name'] ?></td>
                    <td><?php echo htmlspecialchars($value['content']) ?>/td>
                </tr>
            <?php } ?>
            <?php if (empty($app_dismap)) { ?>
                <tr>
                    <td colspan="3" class="text-center">暂无数据</td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="col-12  tuchu_col">
        <span class="text-center">
            github <span style="color:#ccc;">关键词监控结果</span>
            <!--<a href="<?php /*echo url('app/rescan', ['id'=>$info['id'],'tools_name' => 'plugin']) */ ?>"
               onClick="return confirm('确定要清空该工具数据ReScan吗?')"
               class="btn btn-sm btn-outline-secondary"  style="float: right">ReScan</a>-->
            <a href="<?php echo url('github_keyword_monitor_notice/index', ['app_id' => $info['id']]) ?>"
               class="btn btn-sm btn-outline-secondary" style="float: right">more</a>
        </span>
        <table class="table  table-hover table-sm" style="color:#ccc;">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>关键词名称</th>
                <th>github名称</th>
                <th>路径</th>
                <th>url地址</th>
                <th>时间</th>
            </tr>
            </thead>
            <?php foreach ($monitor_notice as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['keyword'] ?></td>
                    <td><?php echo $value['name'] ?></td>
                    <td><?php echo $value['path'] ?></td>
                    <td><?php echo $value['html_url'] ?></td>
                    <td><?php echo $value['create_time'] ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($app_dismap)) { ?>
                <tr>
                    <td colspan="6" class="text-center">暂无数据</td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <!--    <div class="col-12  tuchu_col">-->
    <!--        <button id="download_pdf" class="btn btn-primary">导出为PDF</button>-->
    <!--    </div>-->
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $("#download_pdf").click(function () {

        //可以是$("#id或类选择器").html()或val()
        var element = $(":root").html()

        html2pdf().from(element).set({
            margin: 1,
            filename: 'resume.pdf',
            html2canvas: {scale: 2},
            jsPDF: {orientation: 'portrait', unit: 'in', format: 'letter', compressPDF: false}
        }).save();


    });
</script>
{include file='public/footer' /}
