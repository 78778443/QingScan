{include file='public/head' /}
<div class="row" style="height: 50px"></div>
<div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6 tuchu">
                <h1>添加菜单</h1>
                <form method="post" action="">
                    <div class="mb-3">
                        <label class="form-label">父类</label>
                        <select name="pid" class="form-select form-select-sm" aria-label="Default select">
                            <option value="0">默认顶级</option>
                            <?php foreach ($auth_rule as $item){ ?>
                            <option value="<?php echo $item['auth_rule_id']?>"><?php echo $item['ltitle']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">权限名称</label>
                        <input type="text" name="title" class="form-control" placeholder="请输入权限名称" value="">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">控制器/方法</label>
                        <input type="text" name="href" class="form-control" placeholder="请输入控制器/方法">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">是否验证权限</label>
                        <select name="is_open_auth" class="form-select form-select-sm" aria-label="Default select">
                            <option value="1">是</option>
                            <option value="0">否</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">是否显示</label>
                        <select name="menu_status" class="form-select form-select-sm" aria-label="Default select">
                            <option value="1">显示</option>
                            <option value="0">隐藏</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">排序</label>
                        <input type="text" name="sort" class="form-control" placeholder="请输入排序">
                    </div>
                    <div class="row" style="height: 10px"></div>
                    <button type="submit" class="btn btn-sm btn-outline-secondary">提交</button>
                    <a href="<?php echo url('auth/auth_rule')?>" class="btn btn-sm btn-outline-secondary">返回</a>
                </form>
            </div>
            <div class="col-md-3"></div>
</div>
    <div class="row" style="height: 50px"></div>
{include file='public/footer' /}