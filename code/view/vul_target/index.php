{include file='public/head' /}


<div class="row tuchu">
    <div class="col-md-12 ">
        <?php if (!empty($list)) { ?>
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>地址</th>
                    <th>IP</th>
                    <th>端口</th>
                    <th>fofa关键字</th>
                    <th>创建时间</th>
                    <th>审计状态</th>
                    <th>缺陷名称</th>
                    <th>操作</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php echo $value['addr'] ?></td>
                        <td><?php echo $value['ip'] ?></td>
                        <td><?php echo $value['port'] ?></td>
                        <td><?php echo $value['query'] ?></td>
                        <td><?php echo $value['create_time'] ?></td>
                        <td><?php echo $value['is_vul'] ?></td>
                        <td><?php echo $value['vul_id'] ?></td>
                        <td>
                            <?php if ($value['content']) { ?>
                                <a class="btn btn-outline-info">POC验证</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else {
            echo "<h3 class='text-center'>列表没有数据</h3>";
        } ?>
    </div>
</div>
{include file='public/fenye' /}
{include file='public/footer' /}