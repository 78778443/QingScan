{include file='public/head' /}

<?php
$searchArr = [
    'action' =>  $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' => "搜索"],
    ],
    'btnArr' => [
        ['text' => '添加', 'ext' => [
                "class" => "btn btn-outline-success",
                "data-bs-toggle"=>"modal" ,
                "data-bs-target"=>"#staticBackdrop",
        ]]
    ]]; ?>
{include file='public/search' /}
<div class="row">
    <div class="col-md-12 ">
        <div class="row ">
            <div class="col-md-12 ">
                <div class=" row tuchu">
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>项目名称</th>
                            <th>项目地址</th>
                            <th>拉去方式</th>
                            <th>缺陷数量</th>
                            <th>Fortify扫描时间</th>
                            <th>Semgrep扫描时间</th>
                            <th>Kunlun扫描时间</th>
                            <th style="width: 200px">操作</th>
                        </tr>
                        </thead>
                        <?php foreach ($list as $value) { ?>
                            <tr>
                                <td><?php echo $value['id'] ?></td>
                                <td><?php echo $value['name'] ?></td>
                                <td> <?php echo $value['ssh_url'] ?> </td>
                                <td><?php echo $value['pulling_mode'] ?></td>
                                <td><?php echo $num_arr[$value['id']] ?? 0 ?></td>
                                <td><?php echo $value['scan_time'] ?></td>
                                <td><?php echo $value['semgrep_scan_time'] ?></td>
                                <td><?php echo $value['kunlun_scan_time'] ?></td>
                                <td>
                                    <a href="<?php echo url('code/bug_list', ['project_id' => $value['id']]) ?>"
                                       class="btn btn-sm btn-outline-primary">查看漏洞</a>
                                    <a href="<?php echo url('code/edit_modal', ['id' => $value['id']]) ?>"
                                       class="btn btn-sm btn-outline-success">编辑</a>
                                    <a href="<?php echo url('code/code_del', ['id' => $value['id']]) ?>"
                                       class="btn btn-sm btn-outline-danger">删除</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
{include file='public/fenye' /}
{include file='code/add_modal' /}
{include file='public/footer' /}