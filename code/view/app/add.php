{include file='public/head' /}
<?php require_once __TPL__ . "/public/menu.php"; ?>

<?php

use  FormBuilder\Factory\Elm;

$action = '/index.php?s=app/_add';
$method = 'POST';
$name = Elm::input('name', '应用名称')->required();
$url = Elm::input('url', 'URL地址', '', 'url')->required();
$contact = Elm::input('contact', '联系人')->required();
$phone = Elm::input('phone', '手机号', '', 'phone');
$department = Elm::input('department', '所属部门')->required();


//验证规则

$validate = Elm::validateUrl();
$url->appendValidate($validate);


//创建表单
$form = Elm::createForm($action)->setMethod($method);

//添加组件
$form->setRule([$name, $url, $contact, $phone, $department]);

//生成表单页面
echo $formHtml = $form->view();
?>
<script>
    $(function () {
        $("body .el-form--label-right").addClass("tuchu");
        $("body .el-form--label-right").addClass("col-md-6");
    });
</script>
