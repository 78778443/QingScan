{include file='public/head' /}
    <div class="col-md-12 ">
        <div class="row tuchu">
            <div class="col-md-9"></div>
            <div class="col-md-2">
                <a href="<?php echo $_SERVER['HTTP_REFERER'] ?? "#" ?>" class="btn btn-outline-info">返回</a>
            </div>
        </div>
        <div class="row tuchu">
            <div class="col-md-12">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>URL</th>
                        <th>缺陷类型</th>
                        <th>POC</th>
                        <th>APP</th>
                        <th>状态</th>
                        <th style="width: 150px">操作</th>
                    </tr>
                    </thead>
                    <?php foreach ($list as $value) { ?>
                        <tr>
                            <td><?php echo $value['id'] ?></td>
                            <td class="ellipsis-type"><a href="<?php echo $value['url'] ?>" target="_blank"><?php echo $value['url'] ?></a></td>
                            <td><?php echo $value['plugin'] ?></td>
                            <td><?php echo htmlentities($value['poc']) ?></td>
                            <td><?php echo $appArr[$value['app_id']] ?></td>
                            <td><select class="form-select">
                                    <option>待审核</option >
                                    <option>已处理</option>
                                    <option>已忽略</option>
                                    <option>有风险</option>
                                    <option>待复现</option>
                                </select></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-outline-primary">查看详情</a>
                                <a href="#" class="btn btn-sm btn-outline-warning">删除</a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
                {include file='public/fenye' /}
    </div>
{include file='public/footer' /}