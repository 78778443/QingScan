{include file='public/head' /}

<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
?>
<div class="row tuchu">
    <div class="col-md-12 ">
        <form class="form-inline" method="get" action="/index.php">
            <input type="hidden" name="s" value="code_check/bug_list">
            <div class="mb-3">
                <label class="sr-only">搜索</label>
                <input type="text" name="search" class="form-control" value=""
                       placeholder="搜索的内容">
            </div>

            <input type="submit" class="btn btn-default">
        </form>
    </div>
</div>
<div class="row tuchu">
    <div class="col-md-12 ">
        <table class="table  table-hover table-sm table-borderless">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>名称</th>
                <th>URL</th>
                <th>CMS</th>
                <th style="width: 200px">操作</th>
            </tr>
            </thead>
            <?php foreach ($list as $value) { ?>
                <tr>
                    <td><?php echo $value['id'] ?></td>
                    <td><?php echo $value['name'] ?></td>
                    <td><?php echo $value['url'] ?></td>
                    <td><?php echo $value['cms'] ?></td>

                    <td>
                        <a href="/index.php?s=code_check/bug_detail&id=<?php echo $value['id'] ?>"
                           class="btn btn-sm btn-outline-secondary">查看漏洞</a>
                        <a href="#" class="btn btn-sm btn-outline-danger" disabled>删除</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
{include file='public/fenye' /}
{include file='public/footer' /}