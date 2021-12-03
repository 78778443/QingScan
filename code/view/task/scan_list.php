{include file='public/head' /}
    <div class="col-md-12 ">
        <div class="row tuchu">
            <div class="col-md-9">
                <form class="form-inline" method="get" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
                    <input type="text" name="name" class="form-control" placeholder="名称">
                    <input type="text" name="url" class="form-control" placeholder="URL">
                    <button type="submit" class="btn btn-outline-primary">搜索</button>
                </form>
            </div>
            <div class="col-md-2">
                <a href="/index.php?s=task/add_task" class="btn btn-outline-success">添加任务</a>
                <a href="/index.php?s=task/add_api_url" class="btn btn-outline-success">添加API任务</a>
            </div>
        </div>
        <div class="row tuchu">
            <!--            <div class="col-md-1"></div>-->
            <div class="col-md-12 ">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>URL</th>
                        <th>APP</th>
                        <th>创建时间</th>
                        <!--                    <td style="width: 70px">状态</td>-->
                        <th style="width: 200px">操作</th>
                    </tr>
                    </thead>
                    <?php foreach ($list as $value) { ?>
                        <tr>
                            <td><?php echo $value['id'] ?></td>
                            <td class="ellipsis-type"><a href="<?php echo $value['url'] ?>" target="_blank"><?php echo $value['url'] ?></a></td>
                            <td><?php echo $appArr[$value['app_id']] ?></td>
                            <td><?php echo $value['create_time'] ?></td>
                            <!--                        <td>--><? //= $statusArr[$value['scan_status']] ?><!--</td>-->
                            <td>
                                <a id="starScan" href="#" class="btn btn-sm btn-outline-success">开始扫描</a>
                                <a href="/index.php?s=task/bug_list&task_id=<?php echo $value['id'] ?>"
                                   class="btn btn-sm btn-outline-primary">查看漏洞</a>
                                <a href="#" class="btn btn-sm btn-outline-warning">删除</a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
                {include file='public/fenye' /}
    </div>
    <script>
        $("#starScan").click(function () {
            $.get("/index.php?s=task/_start_scan&url_id=<?php echo $value['id'] ?>", function (result) {
                alert("操作成功")
                location.reload();
            });
        });
    </script>
{include file='public/footer' /}