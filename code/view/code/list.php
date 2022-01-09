{include file='public/head' /}

<?php
$searchArr = [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' => "搜索"],
    ],
    'btnArr' => [
        ['text' => '添加', 'ext' => [
            "class" => "btn btn-outline-success",
            "data-bs-toggle" => "modal",
            "data-bs-target" => "#staticBackdrop",
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
                            <th>clone方式</th>
                            <th>Fortify</th>
                            <th>Semgrep</th>
                            <th>webshell</th>
                            <th>PHP依赖</th>
                            <th>Python依赖</th>
                            <th>java依赖</th>
                            <th style="width: 200px">操作</th>
                        </tr>
                        </thead>
                        <?php foreach ($list as $value) { ?>
                            <tr>
                                <td><?php echo $value['id'] ?></td>
                                <td><?php echo $value['name'] ?></td>
                                <td> <?php echo $value['ssh_url'] ?> </td>
                                <td><?php echo $value['pulling_mode'] ?></td>
                                <td>
                                    <a title="扫描时间:<?php echo $value['scan_time'] ?>"
                                       href="<?php echo url('code/bug_list', ['id' => $value['id']]); ?>">
                                        <?php echo $fortifyNum[$value['id']] ?? 0 ?>
                                    </a>
                                </td>
                                <td>
                                    <a title="扫描时间:<?php echo $value['semgrep_scan_time'] ?>"
                                       href="<?php echo url('code/semgrep_list', ['id' => $value['id']]); ?>"><?php echo $semgrepNum[$value['id']] ?? 0 ?>
                                    </a>
                                </td>
                                <td>
                                    <a title="扫描时间:<?php echo $value['webshell_scan_time'] ?>"
                                       href="<?php echo url('code_webshell/index', ['id' => $value['id']]); ?>"><?php echo $hemaNum[$value['id']] ?? 0 ?>
                                    </a>
                                </td>
                                <td>
                                    <a title="扫描时间:<?php echo $value['composer_scan_time'] ?>"
                                       href="<?php echo url('code_composer/index', ['id' => $value['id']]); ?>"><?php echo $phpNum[$value['id']] ?? 0 ?>
                                    </a>
                                </td>
                                <td>
                                    <a title="扫描时间:<?php echo $value['python_scan_time'] ?>"
                                       href="<?php echo url('code_python/index', ['id' => $value['id']]); ?>"><?php echo $pythonNum[$value['id']] ?? 0 ?>
                                    </a>
                                </td>
                                <td>
                                    <a title="扫描时间:<?php echo $value['java_scan_time'] ?>"
                                       href="<?php echo url('code_java/index', ['id' => $value['id']]); ?>"><?php echo $javaNum[$value['id']] ?? 0 ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?php echo url('code/details', ['id' => $value['id']]) ?>"
                                       class="btn btn-sm btn-outline-primary">查看</a>
                                    <a href="<?php echo url('code/edit_modal', ['id' => $value['id']]) ?>"
                                       class="btn btn-sm btn-outline-success">编辑</a>
                                    <a href="<?php echo url('code/rescan', ['id' => $value['id']]) ?>"
                                       class="btn btn-sm btn-outline-warning">重新扫描</a>
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