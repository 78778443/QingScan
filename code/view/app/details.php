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
        <!--<form class="form-inline" method="get" action="">
                <div class="mb-3">
                    <label class="sr-only">类型</label>
                    <select class="form-control" name="type">
                        <option value="">请选择类型</option>
                        <?php /*foreach ($typeArr as $value) { */ ?>
                            <option value="<?php /*echo $value */ ?>" <?php /*echo ($GET['type'] ?? '' == $value) ? 'selected' : '' */ ?>><?php /*echo $value */ ?></option>
                        <?php /*} */ ?>
                    </select>
                </div>
                <input type="submit" class="btn btn-default">
            </form>-->
    </div>
</div>
<div class="row tuchu">
    <div class="accordion" id="accordionExample">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    whatweb（指纹识别）
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
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
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    oneforall（子域名）
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
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
            <h2 class="accordion-header" id="heading4">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                    dirmap（扫后台）
                </button>
            </h2>
            <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4"
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
                            <th style="width: 200px">操作</th>
                        </tr>
                        </thead>
                        <?php foreach ($dirmap as $value) { ?>
                            <tr>
                                <td><?php echo $value['id'] ?></td>
                                <td><?php echo $value['code'] ?></td>
                                <td><?php echo $value['size'] ?></td>
                                <td><?php echo $value['type'] ?></td>
                                <td><?php echo $value['url'] ?></td>
                                <td><?php echo date('Y-m-d H:i:s', substr($value['create_time'], 0, 10)) ?></td>
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
                    sqlmap（SQL注入）
                </button>
            </h2>
            <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="heading5"
                 data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>urls</th>
                            <th>param</th>
                            <th>type</th>
                            <th>title</th>
                            <th>payload</th>
                            <th>时间</th>
                        </tr>
                        </thead>
                        <?php foreach ($sqlmap as $value) { ?>
                            <tr>
                                <td><?php echo $value['id'] ?></td>
                                <td><?php echo $value['urls'] ?></td>
                                <td><?php echo $value['type'] ?></td>
                                <td><?php echo $value['title'] ?></td>
                                <td><?php echo $value['payload'] ?></td>
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
