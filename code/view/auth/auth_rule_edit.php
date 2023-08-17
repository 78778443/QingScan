{include file='public/head' /}
<div class="row" style="height: 50px"></div>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 tuchu">
        <h1>修改菜单</h1>
        <form method="post" action="<?php echo url('auth/ruleEdit') ?>">
            <input type="hidden" name="auth_rule_id" value="<?php echo $info['auth_rule_id'] ?>">


            <div class="mb-3">
                <label class="form-label">父类</label>
                <select name="pid" class="form-select form-select-sm" aria-label="Default select">
                    <option value="0">默认顶级</option>
                    <?php foreach ($list as $item) { ?>
                        <option value="<?php echo $item['auth_rule_id'] ?>" <?php if ($info['pid'] == $item['auth_rule_id']) echo 'selected' ?>><?php echo $item['ltitle'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">权限名称</label>
                <input type="text" name="title" class="form-control" placeholder="请输入权限名称"
                       value="<?php echo $info['title'] ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">控制器/方法</label>
                <input type="text" name="href" class="form-control" placeholder="请输入控制器/方法"
                       value="<?php echo $info['href'] ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">是否验证权限</label>
                <select name="is_open_auth" class="form-select form-select-sm" aria-label="Default select">
                    <option value="1" <?php if ($info['is_open_auth'] == 1) echo 'selected' ?>>是</option>
                    <option value="0" <?php if ($info['is_open_auth'] == 0) echo 'selected' ?>>否</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">是否显示</label>
                <select name="menu_status" class="form-select form-select-sm" aria-label="Default select">
                    <option value="1" <?php if ($info['menu_status'] == 1) echo 'selected' ?>>显示</option>
                    <option value="0" <?php if ($info['menu_status'] == 0) echo 'selected' ?>>隐藏</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">排序</label>
                <input type="text" name="sort" class="form-control" placeholder="请输入排序"
                       value="<?php echo $info['sort'] ?>">
            </div>
            <button type="submit" class="btn btn-sm btn-outline-secondary">提交</button>
            <a href="<?php echo url('auth/auth_rule') ?>" class="btn btn-sm btn-outline-secondary">返回</a>
        </form>
    </div>
    <div class="col-md-3"></div>
</div>
<div class="row" style="height: 50px"></div>

{include file='public/footer' /}