<html>
<head>
    <title>黑盒项目扫描结果</title>
    <link rel="shortcut icon" href="/static/favicon.svg" type="image/x-icon"/>
    <script src="/static/js/jquery.min.js"></script>
    <!--    <script src="/static/js/bootstrap.min.js"></script>-->
    <link href="/static/bootstrap-5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/css/qingscan.css" rel="stylesheet">
    <script src="/static/bootstrap-5.1.3/js/bootstrap.min.js"></script>
</head>
<body style="background-color: #eeeeee; min-height: 1080px">
<div class="container-fluid">
<div class="col-md-12 ">
    <div class="row tuchu">
        <div class="col-md-4">
            <h5 style="align-content: center"><span style="color:#888">id:</span> <?php echo $info['id'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">状态:</span> <?php echo $info['status'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">名称: </span><?php echo $info['name'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">URL: </span><?php echo $info['url'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">创建: </span><?php echo $info['create_time'] ?></h5></div>

        <div class="col-md-4">
            <h5><span style="color:#888">是否删除:</span> <?php echo $info['is_delete'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">用户名称:</span> <?php echo $info['username'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">密码:</span> <?php echo $info['password'] ?></h5></div>
    </div>
    <div class="row tuchu">
        <div class="col-md-4">
            <h5><span style="color:#888">RAD(爬虫):</span> <?php echo $info['crawler_time'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">whatweb(指纹扫描):</span> <?php echo $info['whatweb_scan_time'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">V2子域名扫描时间:</span> <?php echo $info['subdomain_scan_time'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">获取基本信息时间: </span><?php echo $info['screenshot_time'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">xray扫描时间：</span><?php echo $info['xray_scan_time'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">dirmap扫描时间:</span> <?php echo $info['dirmap_scan_time'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">DisMap扫描时间:</span> <?php echo $info['dismap_scan_time'] ?></h5>
        </div>
        <div class="col-md-4">
            <h5><span style="color:#888">Crawlergo扫描时间:</span> <?php echo $info['crawlergo_scan_time'] ?></h5>
        </div>
        <div class="col-md-4">
            <h5><span style="color:#888">Vulmap扫描时间:</span> <?php echo $info['vulmap_scan_time'] ?></h5>
        </div>
        <div class="col-md-4">
            <h5><span style="color:#888">awvs扫描时间:</span> <?php echo $info['awvs_scan_time'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">子域名扫描时间: </span><?php echo $info['subdomain_time'] ?></h5></div>
    </div>
</div>


<div class="row tuchu_margin">
    <div class="col-auto  tuchu_col">
        <h4 class="text-center">
            RAD(URL爬虫)
        </h4>
        <table class="table  table-hover table-sm table-borderless">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>URL</th>
                <th>APP</th>
                <th>ICP</th>
                <th>邮箱</th>
                <th>sqlmap扫描时间</th>
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
                    <td><?php echo $value['email'] ?></td>
                    <td><?php
                        echo ($value['sqlmap_scan_time'] == "2000-01-01 00:00:00") ? "未扫描" : ((strtotime($value['sqlmap_scan_time']) > time()) ? '扫描失败' : $value['sqlmap_scan_time'])
                        ?></td>
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
    <div class="col-auto  tuchu_col">
        <h4 class="text-center">
            crawlergo(URL爬虫扫描)
        </h4>
        <table class="table  table-hover table-sm table-borderless">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>url</th>
                <th>扫描时间</th>
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

    <div class="col-auto  tuchu_col">
        <h4 class="text-center">
            AWVS(综合扫描)
        </h4>
        <table class="table  table-hover table-sm table-borderless">
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

    <div class="col-auto  tuchu_col">
        <h4 class="text-center">
            nuclei(POC扫描)
        </h4>
        <table class="table  table-hover table-sm table-borderless">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>name</th>
                <th>host</th>
                <th>扫描时间</th>
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

    <div class="col-auto  tuchu_col">
        <h4 class="text-center">
            XRAY(黑盒+POC)
        </h4>
        <table class="table  table-hover table-sm table-borderless">
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
    <div class="col-auto  tuchu_col">
        <h4 class="text-center">
            app信息
        </h4>
        <table class="table  table-hover table-sm table-borderless">
            <thead class="table-light">
            <tr>
                <th>app_id</th>
                <th>cms</th>
                <th>server</th>
                <th>statuscode</th>
                <th>title</th>
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
                    <td class="AutoNewline"><?php echo $value['title'] ?></td>
                    <td><?php echo $value['length'] ?></td>
                    <td><?php echo $value['page_title'] ?></td>
                    <td><?php echo $value['header'] ?></td>
                    <td><?php echo $value['icon'] ?></td>
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

    <div class="col-auto  tuchu_col">
        <h4 class="text-center"> whatweb（指纹识别）</h4>
        <table class="table  table-hover table-sm table-borderless">
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

    <div class="col-auto  tuchu_col">
        <h4 class="text-center">sqlmap（SQL注入）</h4>
        <table class="table  table-hover table-sm table-borderless">
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

    <div class="col-auto  tuchu_col">
        <h4 class="text-center">oneforall（子域名）</h4>
        <table class="table  table-hover table-sm table-borderless">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>域名</th>
                <th>端口</th>
                <th>ip</th>
                <th>状态</th>
            </tr>
            </thead>
            <?php foreach ($oneforall as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['subdomain'] ?></td>
                    <td><?php echo $value['port'] ?></td>
                    <td><?php echo $value['ip'] ?></td>
                    <td><?php echo $value['status'] ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($oneforall)) { ?>
                <tr>
                    <td colspan="5" class="text-center"><?php echo getScanStatus($info['id'], 'subdomainScan'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="col-auto  tuchu_col">
        <h4 class="text-center">hydra（主机暴力破解）</h4>
        <table class="table  table-hover table-sm table-borderless">
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

    <div class="col-auto  tuchu_col">
        <h4 class="text-center"> dirmap（扫后台）</h4>
        <table class="table  table-hover table-sm table-borderless">
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
    <div class="col-auto  tuchu_col">
        <h4 class="text-center">Nmap列表</h4>
        <table class="table  table-hover table-sm table-borderless">
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
                <th>is_delete</th>
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
                    <td><?php echo $value['is_delete'] ?></td>
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

    <div class="col-auto  tuchu_col">
        <h4 class="text-center">vulmap信息</h4>
        <table class="table  table-hover table-sm table-borderless">
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
    <div class="col-auto  tuchu_col">
        <h4 class="text-center">主机列表</h4>
        <table class="table  table-hover table-sm table-borderless">
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
                <th>is_delete</th>
                <th>user_id</th>
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
                    <td><?php echo $value['is_delete'] ?></td>
                    <td><?php echo $value['user_id'] ?></td>
                </tr>
            <?php } ?>
            <?php if (empty($host)) { ?>
                <tr>
                    <td colspan="16" class="text-center"><?php echo getScanStatus($info['id'], 'autoAddHost'); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="col-auto  tuchu_col">
        <h4 class="text-center">DisMap（CMS指纹识别）</h4>
        <table class="table  table-hover table-sm table-borderless">
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
</div>
</div>
<footer class="footer navbar-fixed-bottom">
    <div class=" footer-bottom">
        <ul class="list-inline text-center">
            <li style="color:#ccc;">QingScan 产品仅授权你在遵守《<a
                        href="https://baike.baidu.com/item/%E4%B8%AD%E5%8D%8E%E4%BA%BA%E6%B0%91%E5%85%B1%E5%92%8C%E5%9B%BD%E7%BD%91%E7%BB%9C%E5%AE%89%E5%85%A8%E6%B3%95"
                        target="_blank">中华人民共和国网络安全法</a>》前提下使用，如果你有二次开发需求,可以微信联系我<code>songboy8888</code>.
            </li>
        </ul>
    </div>
</footer>
</body>
</html>