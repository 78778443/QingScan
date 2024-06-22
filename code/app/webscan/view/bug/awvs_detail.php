{include file='public/head' /}
<div class="col-md-1 " style="padding-right: 0;">
    {include file='public/blackLeftMenu' /}
</div>
<div class="col-md-11 " style="padding:0;">
    <?php
    $dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
    ?>
    <div class="tuchu">
        <div class="row">
            <?php foreach ($detail as $key => $item) {
                if (!in_array($key, ['id', 'affects_url', 'vt_name', 'details', 'recommendation', 'description'])) continue;
                ?>
                <div class="col-md-2 " style="color: #ccc; margin-bottom: 10px;">{$key}</div>
                <div class="col-md-10 "><?php echo empty($item) ? '' : $item; ?></div>
            <?php } ?>
            <div class="col-md-2 " style="color: #ccc; margin-bottom: 10px;">请求包</div>
            <div class="col-md-10 " style=" margin-bottom: 10px;">
                <textarea class="form-control" cols="100" rows="8" style="color: #666;" disabled>{:rtrim($detail['request'])}</textarea>
            </div>
            <div class="col-md-2 " style="color: #ccc; margin-bottom: 10px;">AI解读</div>
            <div class="col-md-10 ">
                <textarea class="form-control" cols="100" rows="8" disabled></textarea>
            </div>
        </div>
    </div>

</div>
{include file='public/footer' /}