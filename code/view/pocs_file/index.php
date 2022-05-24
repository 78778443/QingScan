{include file='public/head' /}
<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
?>

<?php
$searchArr = [
    'action' => url('index'),
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' => "搜索"],
    ],
    'btnArr' => [
        ['text' => '添加', 'ext' => [
            "href" => url('add'),
            "class" => "btn btn-outline-success"
        ]
    ]
]]; ?>
{include file='public/search' /}

<div class="row tuchu">
    <div class="col-md-12">
        <form class="row g-3" id="frmUpload" action="<?php echo url('pocs_file/batch_import') ?>" method="post"
              enctype="multipart/form-data">
            <div class="col-auto">
                <input type="file" class="form-control form-control" name="file" accept=".xls,.csv" required/>
            </div>
            <div class="col-auto">
                <input type="submit" class="btn btn-outline-info" value="批量添加项目">
            </div>
            <div class="col-auto">
                <a href="<?php echo url('pocs_file/downloaAppTemplate') ?>"
                   class="btn btn-outline-success">下载模板</a>
            </div>
        </form>
    </div>
</div>

<div class="col-md-12 ">
    <div class="row tuchu">
        <!--            <div class="col-md-1"></div>-->
        <div class="col-md-12 ">
            {include file='public/batch_del' /}
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th width="100">
                        <label>
                            <input type="checkbox" value="-1" onclick="quanxuan(this)">全选
                        </label>
                    </th>
                    <th>ID</th>
                    <th>cve_num</th>
                    <th>名称</th>
                    <th>状态</th>
                    <th>创建时间</th>
                    <th style="width: 200px">操作</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td>
                            <label>
                                <input type="checkbox" class="ids" name="ids[]" value="<?php echo $value['id'] ?>">
                            </label>
                        </td>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php echo $value['cve_num'] ?></td>
                        <td><?php echo $value['name'] ?></td>
                        <td><?php echo $value['status'] == 1 ? '正常' : '禁用' ?></td>
                        <td><?php echo $value['create_time'] ?></td>
                        <td>
                            <a href="<?php echo url('edit', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-success">编辑</a>
                            <a href="<?php echo url('del', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    {include file='public/fenye' /}
</div>
{include file='public/footer' /}