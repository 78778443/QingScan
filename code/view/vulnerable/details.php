{include file='public/head' /}

<div class="col-md-1 " style="padding-right: 0;" >
    {include file='public/vulLeftMenu' /}
</div>


<div class=" col-md-11">

    <div class="row tuchu">
        <h4><?php echo $info['vuln_name']; ?></h4>
        <?php foreach ($info as $key => $value) {
            if (in_array($value, ['null', ''])) continue;
            ?>
            <div class="col-md-2"><span class="key" style="color: #aaa;text-align: right;">{$key}：</span></div>
            <div class="col-md-10">{$value}</div>
        <?php } ?>
    </div>
</div>

{include file='public/footer' /}