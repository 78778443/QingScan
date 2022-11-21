{include file='public/head' /}
<div class="col-md-12 ">
    <?php
    $searchArr = [
        'action' => url('app/index'),
        'method' => 'get',
        'inputs' => [
            ['type' => 'select', 'name' => 'statuscode', 'options' => $statuscodeArr, 'frist_option' => '状态码'],
            ['type' => 'select', 'name' => 'cms', 'options' => $cmsArr, 'frist_option' => 'CMS系统'],
            ['type' => 'select', 'name' => 'server', 'options' => $serverArr, 'frist_option' => '服务'],
        ],
        'btnArr' => [
            ['text' => '添加', 'ext' => [
                "class" => "btn btn-outline-success",
                "data-bs-toggle" => "modal",
                "data-bs-target" => "#exampleModal",
            ]]
        ]]; ?>
    {include file='public/search' /}

    <div class="row tuchu">
        <div class="col-md-12">
            <form class="row g-3" id="frmUpload" action="<?php echo url('app/batch_import') ?>" method="post"
                  enctype="multipart/form-data">
                <div class="col-auto">
                    <input type="file" class="form-control form-control" name="file" accept=".xls,.csv" required/>
                </div>
                <div class="col-auto">
                    <input type="submit" class="btn btn-outline-info" value="批量添加项目">
                </div>
                <div class="col-auto">
                    <a href="<?php echo url('app/downloaAppTemplate') ?>"
                       class="btn btn-outline-success">下载模板</a>
                </div>
            </form>
        </div>
    </div>
    <div class="row tuchu">
        <div class="col-md-12 ">
            <form class="row g-3">
                <div class="col-auto">
                    <a href="javascript:;>" onclick="suspend_scan(1)"
                       class="btn btn-outline-success">启用扫描</a>
                </div>
                <div class="col-auto">
                    <a href="javascript:;>" onclick="suspend_scan(2)"
                       class="btn btn-outline-success">暂停扫描</a>
                </div>
                <div class="col-auto">
                    <a href="javascript:;" onclick="again_scan()"
                       class="btn btn-outline-success">重新扫描</a>
                </div>
                <div class="col-auto">
                    <a href="javascript:;" onclick="batch_del()"
                       class="btn btn-outline-danger">批量删除</a>
                </div>
            </form>

            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th width="70">
                        <label>
                            <input type="checkbox" value="-1" onclick="quanxuan(this)">全选
                        </label>
                    </th>
                    <th>ID</th>
                    <th>名称</th>
                    <th>rad</th>
                    <th>awvs</th>
                    <th>xray</th>
                    <th>sqlmap</th>
                    <th>oneforall</th>
                    <th>dirmap</th>
                    <th>vulmap</th>
                    <th>nmap</th>
                    <th>主机数量</th>
                    <th>nuclei</th>
                    <th>hydra</th>
                    <th>crawlergo</th>
                    <th>创建时间</th>
                    <th>扫描状态</th>
                    <th style="width: 200px">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td>
                            <label>
                                <input type="checkbox" class="ids" name="ids[]" value="<?php echo $value['id'] ?>">
                            </label>
                        </td>
                        <td><?php echo $value['id'] ?></td>
                        <td class="ellipsis-type">
                            <a href="{$value['url']}" title="{$value['url']}" target="_blank">{$value['name']} </a>
                        </td>
                        <td>
                            <a title="扫描时间:<?php echo $value['crawler_time'] ?>"
                               href="<?php echo url('urls/index', ['app_id' => $value['id']]); ?>"><?php echo $value['urls_num'] ?? 0 ?>
                            </a>
                        </td>
                        <td>
                            <a title="扫描时间:<?php echo $value['awvs_scan_time'] ?>"
                               href="<?php echo url('bug/awvs', ['app_id' => $value['id']]); ?>"><?php echo $value['awvs_num'] ?? 0 ?>
                            </a>
                        </td>
                        <td>
                            <a title="扫描时间:<?php echo $value['xray_scan_time'] ?>"
                               href="<?php echo url('xray/index', ['app_id' => $value['id']]); ?>"><?php echo $value['xray_num'] ?? 0 ?>
                            </a>
                        </td>
                        <td>
                            <a
                               href="<?php echo url('sqlmap/index', ['app_id' => $value['id']]); ?>"><?php echo $value['sqlmap_num'] ?? 0 ?>
                            </a>
                        </td>
                        <td>
                            <a title="扫描时间:<?php echo $value['subdomain_scan_time'] ?>"
                               href="<?php echo url('one_for_all/index', ['app_id' => $value['id']]); ?>"><?php echo $value['oneforall_num'] ?? 0 ?>
                            </a>
                        </td>
                        <td>
                            <a title="扫描时间:<?php echo $value['dirmap_scan_time'] ?>"
                               href="<?php echo url('dirmap/index', ['app_id' => $value['id']]); ?>"><?php echo $value['dirmap_num'] ?? 0 ?>
                            </a>
                        </td>
                        <td>
                            <a title="扫描时间:<?php echo $value['vulmap_scan_time'] ?>"
                               href="<?php echo url('vulmap/index', ['app_id' => $value['id']]); ?>"><?php echo $value['vulmap_num'] ?? 0 ?>
                            </a>
                        </td>
                        <td>
                            <a
                               href="<?php echo url('host_port/index', ['app_id' => $value['id']]); ?>"><?php echo $value['namp_num'] ?? 0 ?>
                            </a>
                        </td>
                        <td>
                            <a
                               href="<?php echo url('host/index', ['app_id' => $value['id']]); ?>"><?php echo $value['host_num'] ?? 0 ?>
                            </a>
                        </td>
                        <td>
                            <a title="扫描时间:<?php echo $value['nuclei_scan_time'] ?>"
                               href="<?php echo url('app_nuclei/index', ['app_id' => $value['id']]); ?>"><?php echo $value['nuclei_num'] ?? 0 ?>
                            </a>
                        </td>
                        <td>
                            <a
                               href="<?php echo url('hydra/index', ['app_id' => $value['id']]); ?>"><?php echo $value['hydra_num'] ?? 0 ?>
                            </a>
                        </td>
                        <td>
                            <a title="扫描时间:<?php echo $value['crawlergo_scan_time'] ?>"
                               href="<?php echo url('app_crawlergo/index', ['app_id' => $value['id']]); ?>"><?php echo $value['crawlergo_num'] ?? 0 ?>
                            </a>
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($value['create_time'])) ?></td>
                        <td><?php echo $value['status']; ?></td>
                        <td>
                            <?php if($value['xray_agent_port'] ?? ''){?>
                                <a href="javascript:;" onclick="start_agent(<?php echo $value['id'] ?>)"
                                   class="btn btn-sm btn-outline-success" style="margin-bottom: 5px">关闭代理</a>
                            <?php }else{?>
                                <a href="javascript:;" onclick="start_agent(<?php echo $value['id'] ?>)"
                                   class="btn btn-sm btn-outline-success" style="margin-bottom: 5px">启动代理</a>
                            <?php }?>
                            <a href="javascript:;" onclick="tools(<?php echo $value['id'];?>,'<?php echo $value['name'];?>',1)"
                               class="btn btn-sm btn-outline-warning" style="margin-bottom: 5px">工具列表</a>
                            <a href="<?php echo url('details', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-primary">查看详情</a>
                            <!--<a href="<?php /*echo url('app/qingkong', ['id' => $value['id']]) */?>"
                               onClick="return confirm('确定要清空数据重新扫描吗?')"
                               class="btn btn-sm btn-outline-warning">重新扫描</a>-->
                            <!--<a href="<?php /*echo url('app/del', ['id' => $value['id']]) */?>"
                               onClick="return confirm('确定要删除吗?')"
                               class="btn btn-sm btn-outline-danger">删除</a>-->
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
                <?php if(empty($list)){?>
                    <tr><td colspan="18" class="text-center">暂无目标</td></tr>
                <?php }?>
            </table>
        </div>
        {include file='public/fenye' /}
    </div>
    {include file='app/add_modal' /}
    {include file='app/set_modal' /}
    {include file='process_safe/tools' /}
    {include file='public/footer' /}
    <script>
        function start_agent(id) {
            $.ajax({
                type: "post",
                url: "<?php echo url('start_agent')?>",
                data: {id: id},
                dataType: "json",
                success: function (data) {
                    alert(data.msg)
                    if (data.code) {
                        window.setTimeout(function(){
                            location.reload();
                        },1000);
                    }
                }
            });
        }

        function suspend_scan(status){
            var child = $('.table').find('input[type="checkbox"]');
            var ids = ''
            child.each(function(index, item){
                if (item.value != -1 && item.checked) {
                    if (ids == '') {
                        ids = item.value
                    } else {
                        ids = ids+','+item.value
                    }
                }
            })

            $.ajax({
                type: "post",
                url: "<?php echo url('app/suspend_scan')?>",
                data: {ids: ids,status:status},
                dataType: "json",
                success: function (data) {
                    alert(data.msg)
                    if (data.code == 1) {
                        window.setTimeout(function () {
                            location.reload();
                        }, 2000)
                    }
                }
            });
        }

        function batch_del(){
            var child = $('.table').find('input[type="checkbox"]');
            var ids = ''
            child.each(function(index, item){
                if (item.value != -1 && item.checked) {
                    if (ids == '') {
                        ids = item.value
                    } else {
                        ids = ids+','+item.value
                    }
                }
            })

            $.ajax({
                type: "post",
                url: "<?php echo url('app/batch_del')?>",
                data: {ids: ids},
                dataType: "json",
                success: function (data) {
                    alert(data.msg)
                    if (data.code == 1) {
                        window.setTimeout(function () {
                            location.reload();
                        }, 2000)
                    }
                }
            });
        }

        function again_scan(){
            var child = $('.table').find('input[type="checkbox"]');
            var ids = ''
            child.each(function(index, item){
                if (item.value != -1 && item.checked) {
                    if (ids == '') {
                        ids = item.value
                    } else {
                        ids = ids+','+item.value
                    }
                }
            })

            $.ajax({
                type: "post",
                url: "<?php echo url('app/again_scan')?>",
                data: {ids: ids},
                dataType: "json",
                success: function (data) {
                    alert(data.msg)
                    if (data.code == 1) {
                        window.setTimeout(function () {
                            location.reload();
                        }, 2000)
                    }
                }
            });
        }
    </script>
