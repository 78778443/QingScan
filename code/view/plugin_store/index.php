{include file='public/head' /}
<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
?>
<?php
$searchArr = [
    'action' =>  $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' => "搜索"],
    ]];
?>
<div class="col-md-12 ">
    <div class="row tuchu">
        <div class="col-md-12 ">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>插件名称</th>
                    <th>插件标识</th>
                    <th>描述</th>
                    <th>版本号</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['title'] ?></td>
                        <td><?php echo $value['name'] ?></td>
                        <td><?php echo $value['description'] ?></td>
                        <td><?php echo $value['version'] ?></td>
                        <td><?php echo $value['status']?></td>
                        <td>
                            <?php if($value['is_install']){?>
                                <a href="<?php echo url('plugin_store/install', ['id' => $value['id']]) ?>"
                                   onClick="return confirm('确定要清空数据重新扫描吗?')"
                                   class="btn btn-sm btn-outline-warning">禁用</a>
                                <a href="<?php echo url('app/del', ['id' => $value['id']]) ?>"
                                   onClick="return confirm('确定要删除吗?')"
                                   class="btn btn-sm btn-outline-danger">卸载</a>
                            <?php }else{?>
                                <a href="<?php echo url('plugin_store/install', ['id' => $value['id']]) ?>"
                                   class="btn btn-sm btn-outline-primary">安装</a>
                            <?php }?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    <input type="hidden" id="to_examine_url" value="<?php echo url('to_examine/node') ?>">
    {include file='public/to_examine' /}
    {include file='public/fenye' /}
</div>
{include file='public/footer' /}