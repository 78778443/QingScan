{include file='public/head' /}
<div class="row" style="height: 50px"></div>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 tuchu">
        <h3>修改个人密码</h3>
        <form method="post" action="">
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">原密码</label>
                <input type="password" name="password" class="form-control" placeholder="请输入原密码">
            </div>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">新密码</label>
                <input type="password" name="news_password" class="form-control" placeholder="请输入新密码">
            </div>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">确认密码</label>
                <input type="password" name="news_password1" class="form-control" placeholder="请输入确认密码">
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