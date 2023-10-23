{include file='public/head' /}
<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];

$searchArr = [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' => "search"],
    ],
    'btnArr' => [
        ['text' => '添加菜单', 'ext' => [
            "href" => url('auth/ruleAdd'),
            "class" => "btn btn-sm btn-outline-secondary"
        ]
        ]
    ]]; ?>
{include file='public/search' /}
<div class="col-md-12 ">
    <div class="row tuchu">
        <!--            <div class="col-md-1"></div>-->
        <div class="col-md-12 ">
            <table class="table  table-hover table-sm table-borderless">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>权限名称</th>
                    <th>控制器/方法</th>
                    <th>是否验证权限</th>
                    <th>是否显示</th>
                    <th>排序</th>
                    <th style="width: 200px">操作</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['auth_rule_id'] ?></td>
                        <td><?php echo $value['ltitle'] ?></td>
                        <td><?php echo $value['href'] ?></td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input changAuth " data-id="<?php echo $value['auth_rule_id'] ?>"
                                       type="checkbox" <?php echo $value['is_open_auth'] == 0 ? '' : 'checked'; ?>>
                            </div>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input changCheckBoxStatus" data-id="<?php echo $value['auth_rule_id'] ?>"
                                       type="checkbox" <?php echo $value['menu_status'] == 0 ? '' : 'checked'; ?>>
                            </div>
                        </td>
                        <td><?php echo $value['sort'] ?></td>
                        <td>
                            <!--<a href="<?php /*echo url('auth/userEdit',['auth_rule_id'=>$value['auth_rule_id']])*/?>"
                               class="btn btn-sm btn-outline-secondary">添加子类</a>-->
                            <a href="<?php echo url('auth/ruleEdit',['auth_rule_id'=>$value['auth_rule_id']])?>"
                               class="btn btn-sm btn-outline-secondary">编辑</a>
                            <a href="<?php echo url('auth/ruleDel',['auth_rule_id'=>$value['auth_rule_id']])?>" class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    <input type="hidden" id="to_examine_url" value="<?php echo url('to_examine/auth_rule_status') ?>">
    {include file='public/to_examine' /}
</div>
{include file='public/footer' /}
<script>
    $(".changAuth").change(function () {
        id = $(this).data('id');
        check_status = $(this).is(":checked");
        if (check_status == false) {
            check_status = 0;
        } else {
            check_status = 1;
        }
        $.ajax({
            type: "post",
            url: "<?php echo url('to_examine/auth_rule_auth')?>",
            data: {check_status: check_status, id: id},
            dataType: "json",
            success: function (data) {
                if (data.code == 1) {
                    location.reload();
                }
            }
        });
    });
</script>