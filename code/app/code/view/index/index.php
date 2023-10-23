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
    <div class="col-md-9">
        <form class="form-inline" method="get" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
            <input type="text" name="name" class="form-control" placeholder="输入搜索的内容">
            <button type="submit" class="btn btn-sm btn-outline-secondary">搜索</button>
        </form>
    </div>
</div>

<div class="row ">
    <div class="col-md-2">
        <div class="row tuchu">
            <table class="table">
                <tr>
                    <th colspan="2">类型分布</th>
                </tr>
                <tr>
                    <td><a href="">网站</a></td>
                    <td>100</td>
                </tr>
                <tr>
                    <td><a href="">协议</a></td>
                    <td>80</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-md-10 ">
        <div class=" row tuchu">
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
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php echo $value['project_hash'] ?></td>
                        <td><?php echo htmlentities($value['author']) ?></td>
                        <td title="<?php echo htmlentities($value['content']) ?>"><?php echo str_replace("\n", "<br>", $value['bugFile']); ?></td>
                        <td><?php echo $value['create_time'] ?></td>
                        <td>
                            <a href="#"
                               class="btn btn-sm btn-out-outline-primary">查看详情</a>
                            <a href="#" class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
{include file='public/fenye' /}
</div>
{include file='public/footer' /}