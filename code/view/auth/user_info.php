{include file='public/head' /}
<div class="row" style="height: 50px"></div>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 tuchu">
        <h3>个人资料</h3>
        <form method="post" action="">
            <div class="mb-3">
                <label class="form-label">主页url</label>
                <input type="text" name="url" class="form-control" placeholder="请输入个人主页url" value="<?php echo isset($info['url'])?$info['url']:''?>">
            </div>
            <div class="mb-3">
                <label class="form-label">昵称</label>
                <input type="text" name="nickname" class="form-control" placeholder="请输入昵称" value="<?php echo $info['nickname']?>">
            </div>

            <div class="mb-3">
                <label class="form-label">性别：</label>
                <label class="radio-inline">
                    <input type="radio" name="sex" id="inlineRadio1" value="1" <?php if($info['status'] == 1 ) echo 'checked'?>> 男
                </label>
                <label class="radio-inline">
                    <input type="radio" name="sex" id="inlineRadio2" value="0" <?php if($info['status'] == 1 ) echo 'checked'?>> 女
                </label>
            </div>
            <div class="mb-3">
                <label class="form-label">手机号码</label>
                <input type="text" name="phone" class="form-control" placeholder="请输入手机号码" value="<?php echo $info['phone']?>">
            </div>
            <div class="mb-3">
                <label class="form-label">邮箱</label>
                <input type="text" name="email" class="form-control" placeholder="请输入邮箱" value="<?php echo $info['email']?>">
            </div>
            <div class="mb-3">
                <label class="form-label">钉钉token</label>
                <input type="text" name="dd_token" class="form-control" placeholder="请输入钉钉token" value="<?php echo $info['dd_token']?>">
            </div>
            <div class="mb-3">
                <label class="form-label">token</label><a href="javascript:;" id="getToken">刷新token</a>
                <input type="text" name="token" class="form-control" readonly value="<?php echo $info['token']?>">
            </div>
            <div class="row" style="height: 10px"></div>
            <button type="submit" class="btn btn-sm btn-outline-secondary">提交</button>
            <a href="javascript:history.go(-1);" class="btn btn-sm btn-outline-secondary">返回</a>
        </form>
    </div>
    <div class="col-md-3"></div>
</div>
<div class="row" style="height: 50px"></div>

{include file='public/footer' /}
<script>
    $('#getToken').click(function () {
        $.ajax({
            type: "get",
            url: "<?php echo url('getToken')?>",
            dataType: "json",
            success: function (res) {
                $('input[name=token]').val(res.data.token)
            }
        });
    });
</script>