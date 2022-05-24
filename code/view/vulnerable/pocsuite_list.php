{include file='public/head' /}

<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
?>
<?php
$searchArr = [
    'action' =>  $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' => "搜索的内容"],
    ],
    'btnArr' => [
        ['text' => '添加', 'ext' => [
            "href" => url('add_pocsuite'),
            "class" => "btn btn-outline-success"
        ]
        ]
    ]
]; ?>

{include file='public/search' /}

<div class="row tuchu">
    <div class="col-md-12">
        <form class="row g-3" id="frmUpload" action="<?php echo url('batch_import') ?>" method="post"
              enctype="multipart/form-data">
            <div class="col-auto">
                <input type="file" class="form-control form-control" name="file" accept=".xls,.csv" required/>
            </div>
            <div class="col-auto">
                <input type="submit" class="btn btn-outline-info" value="批量添加项目">
            </div>
            <div class="col-auto">
                <a href="<?php echo url('downloaAppTemplate') ?>"
                   class="btn btn-outline-success">下载模板</a>
            </div>
        </form>
    </div>
</div>

<div class="row tuchu">
    <div class="col-md-12 ">
        {include file='public/batch_del' /}
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th width="80">
                    <label>
                        <input type="checkbox" value="-1" onclick="quanxuan(this)">全选
                    </label>
                </th>
                <th>ID</th>
                <th>名称</th>
                <th>URL</th>
                <th>CMS</th>
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
                    <td><?php echo $value['name'] ?></td>
                    <td><?php echo $value['url'] ?></td>
                    <td><?php echo $value['cms'] ?></td>

                    <td>
<!--                        <a href="/index.php?s=code_check/bug_detail&id=--><?php //echo $value['id'] ?><!--"-->
<!--                           class="btn btn-sm btn-outline-primary">查看漏洞</a>-->
                        <a href="<?php echo url('vulnerable/pocsuite_del',['id'=>$value['id']])?>" class="btn btn-sm btn-outline-danger">删除</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>
{include file='public/fenye' /}
{include file='public/footer' /}

<input type="hidden" id="batch_del_url" value="<?php echo url('pocsuite_batch_del')?>">
