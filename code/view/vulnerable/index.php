{include file='public/head' /}

<div class="col-md-1 " style="padding-right:0;">
    {include file='public/vulLeftMenu' /}
</div>
<div class="col-md-11 " style="padding-left:0;">
    <?php
    $dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
    $searchArr = [
        'action' => $_SERVER['REQUEST_URI'],
        'method' => 'get',
        'inputs' => [
            ['type' => 'text', 'name' => 'search', 'placeholder' => "搜索的内容"],
            ['type' => 'select', 'name' => 'vul_level', 'options' => $vul_level, 'frist_option' => '危险等级'],
            ['type' => 'select', 'name' => 'product_field', 'options' => $product_field, 'frist_option' => '行业'],
            ['type' => 'select', 'name' => 'product_type', 'options' => $product_type, 'frist_option' => '项目类型'],
            ['type' => 'select', 'name' => 'product_cate', 'options' => $product_cate, 'frist_option' => '平台分类'],
            ['type' => 'select', 'name' => 'check_status', 'options' => $check_status_list, 'frist_option' => '审计状态', 'frist_option_value' => -1],
        ],
        'btnArr' => [
            ['text' => '添加', 'ext' => [
                "href" => url('vulnerable/add'),
                "class" => "btn btn-sm btn-outline-secondary"
            ]]
        ]]; ?>
    {include file='public/search' /}

    <div class="row tuchu">
        <div class="col-md-12 ">
            {include file='public/batch_del' /}
            <table class="table  table-hover table-sm">
                <thead>
                <tr>
                    <th width="80">
                        <label>
                            <input type="checkbox" value="-1" onclick="quanxuan(this)">全选
                        </label>
                    </th>
                    <th>ID</th>
                    <th>名称</th>
                    <th>CVE编号</th>
                    <th>CNVD编号</th>
                    <th>漏洞等级</th>
                    <th>行业</th>
                    <th>项目类型</th>
                    <th>平台分类</th>
                    <th>fofa数量</th>
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
                        <td><?php echo $value['cve_num'] ?></td>
                        <td><?php echo $value['cnvd_num'] ?></td>
                        <td><?php echo $value['vul_level'] ?></td>
                        <td><?php echo $value['product_field'] ?></td>
                        <td><?php echo $value['product_type'] ?></td>
                        <td><?php echo $value['product_cate'] ?></td>
                        <td><?php echo $value['fofa_max'] ?></td>

                        <td>
                            <a href="<?php echo url('vulnerable/details', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-secondary">查看漏洞</a>
                            <a href="<?php echo url('vulnerable/edit', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-secondary">编辑</a>
                            <a href="<?php echo url('vulnerable/vulnerable_del', ['id' => $value['id']]) ?>"
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

<input type="hidden" id="batch_del_url" value="<?php echo url('vulnerable_batch_del') ?>">