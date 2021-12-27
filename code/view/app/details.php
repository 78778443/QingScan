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
            <h5><span style="color:#888">爬虫:</span> <?php echo $info['crawler_time'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">awvs扫描时间:</span> <?php echo $info['awvs_scan_time'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">子域名扫描时间: </span><?php echo $info['subdomain_time'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">是否删除:</span> <?php echo $info['is_delete'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">用户名称:</span> <?php echo $info['username'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">密码:</span> <?php echo $info['password'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">指纹扫描时间:</span> <?php echo $info['whatweb_scan_time'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">V2子域名扫描时间:</span> <?php echo $info['subdomain_scan_time'] ?></h5></div>
        <div class="col-md-4">
            <h5><span style="color:#888">截图: </span><?php echo $info['screenshot_time'] ?></h5></div>
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
    </div>
</div>


<div class="row tuchu">
    <div class="accordion" id="accordionExample">
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading1">
                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                    app信息
                </button>
            </h2>
            <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1"
                 data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
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
                            <th>时间</th>
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
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                    whatweb（指纹识别）
                </button>
            </h2>
            <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2"
                 data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
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
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                    Nmap列表
                </button>
            </h2>
            <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3"
                 data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
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
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading4">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                    vulmap信息
                </button>
            </h2>
            <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4"
                 data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
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
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading5">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                    主机列表
                </button>
            </h2>
            <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="heading5"
                 data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
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
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading6">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
                    sqlmap（SQL注入）
                </button>
            </h2>
            <div id="collapse6" class="accordion-collapse collapse" aria-labelledby="heading6"
                 data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
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
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading7">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse7" aria-expanded="false" aria-controls="collapse7">
                    oneforall（子域名）
                </button>
            </h2>
            <div id="collapse7" class="accordion-collapse collapse" aria-labelledby="heading7"
                 data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
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
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    hydra（主机暴力破解）
                </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                 data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
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
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading8">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse8" aria-expanded="false" aria-controls="collapse8">
                    dirmap（扫后台）
                </button>
            </h2>
            <div id="collapse8" class="accordion-collapse collapse" aria-labelledby="heading8"
                 data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
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
                    </table>
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="heading8">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse9" aria-expanded="false" aria-controls="collapse9">
                    DisMap（CMS指纹识别）
                </button>
            </h2>
            <div id="collapse9" class="accordion-collapse collapse" aria-labelledby="heading8"
                 data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
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
                    </table>
                </div>
            </div>
        </div>


    </div>
</div>
</div>
{include file='public/footer' /}
