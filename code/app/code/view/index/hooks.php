{include file='public/head' /}
    <style>
        .line-limit-length {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
    <div class="row tuchu">
        <div class="col-md-12 ">
            <form class="form-inline" method="get" action="/index.php">
                <input type="hidden" name="s" value="code_check/hooks">

                <div class="mb-3">
                    <select class="form-select form-select-sm" name="author">
                        <option value="">提交人</option>
                        <?php foreach ($authList as $value) { ?>
                            <option value="<?php echo $value ?>" <?php echo ($_GET['author'] ?? '' == $value) ? 'selected' : '' ?>><?php echo $value ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <select class="form-select form-select-sm" name="code_id">
                        <option value="">项目列表</option>
                        <?php foreach ($projectList as $value) { ?>
                            <option value="<?php echo $value ?>" <?php echo ($_GET['code_id'] ?? '' == $value) ? 'selected' : '' ?>><?php echo $projectArr[$value]['path'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <input type="submit" class="btn btn-default">
            </form>
        </div>
    </div>
    <div class=" row tuchu">
        <div class="col-md-12">
            <table class="table  table-hover table-sm table-borderless">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>项目ID</th>
                    <th>提交人</th>
                    <th>文件列表</th>
                    <th>提交时间</th>
                    <th style="width: 200px">操作</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) {
                    ?>
                    <tr>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php echo $value['name'] ?></td>
                        <td><?php echo htmlentities($value['author']) ?></td>
                        <td>
                            <?php foreach (explode("\n", $value['bugFile']) as $val) { ?>
                                <a target="_blank"
                                   href="<?php echo $value['web_url'] ?>/-/blob/master<?php echo $val ?>"><?php echo $val ?></a>
                                <br>
                            <?php } ?>
                        </td>
                        <td><?php echo $value['create_time'] ?></td>
                        <td>
                            <a href="<?= U('code_check/hook_detail', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-secondary">查看详情</a>
                            <a href="#" class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

        {include file='public/fenye' /}

    </div>
{include file='public/footer' /}