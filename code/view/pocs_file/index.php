{include file='public/head' /}
<div class="col-md-1 " style="padding-right: 0;" >
    {include file='public/vulLeftMenu' /}
</div>
<div class="col-md-11 " style="padding:0;">
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
                "class" => "btn btn-sm btn-outline-secondary",
                "data-bs-toggle" => "modal",
                "data-bs-target" => "#staticBackdrop",
            ]
            ]
        ]
    ]; ?>
    {include file='public/search' /}


    <div class="col-md-12 ">
        <div class="row tuchu">
            <!--            <div class="col-md-1"></div>-->
            <div class="col-md-12 ">
                {include file='public/batch_del' /}
                <table class="table  table-hover table-sm table-borderless">
                    <thead class="table-light">
                    <tr>
                        <th width="100">
                            <label>
                                <input type="checkbox" value="-1" onclick="quanxuan(this)">ID
                            </label>
                        </th>
                        <th>名称</th>
                        <th>脚本内容</th>
                        <th>状态</th>
                        <th>创建时间</th>
                        <th style="width: 200px">操作</th>
                    </tr>
                    </thead>
                    <?php foreach ($list as $value) { ?>
                        <tr>
                            <td>
                                <label>
                                    <input type="checkbox" class="ids" name="ids[]"
                                           value="<?php echo $value['id'] ?>"><?php echo $value['id'] ?>
                                </label>
                            </td>
                            <td><?php echo $value['name'] ?></td>
                            <td><textarea class="form-control" rows="4" disabled
                                          style="border: 1px dashed #eeeeee;background-color: #ffffff;"><?php echo $value['content'] ?></textarea>
                            </td>
                            <td><?php echo $value['status'] == 1 ? '正常' : '禁用' ?></td>
                            <td><?php echo $value['create_time'] ?></td>
                            <td>
                                <a href="<?php echo url('edit', ['id' => $value['id']]) ?>"
                                   class="btn btn-sm btn-outline-secondary">编辑</a>
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
</div>

{include file='pocs_file/add_modal' /}
{include file='public/footer' /}