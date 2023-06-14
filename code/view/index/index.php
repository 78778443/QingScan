{include file='public/head' /}
<script type="text/javascript" src="/static/js/echarts.min.js"></script>
<style>
    #divContainer {
        overflow: auto;
        height: 360px;
        width: 260px;
    }

    #divContainer::-webkit-scrollbar {
        border-width: 1px;
    }
</style>
<div class="row">
    <?php foreach ($data as $key => $value) { ?>
        <div class="col-md-6">
            <div class="row tuchu " style="min-height:300px;">

                <span style="color:#ccc;font-size:21px;">{$value['name']}:<span class="badge bg-primary">{$value['value']}</span></span>

                <?php foreach ($value['subInfo'] as $subVaule) { ?>
                    <div class="col-md-4">
                        <div style="font-size: 21px;">
                            <span style="width:100px;color:#666;" class="badge   text-right">{$subVaule['name']}</span>:
                            <a style="width:70px;" class="badge bg-info text-right" href="{$subVaule['href']}">{$subVaule['value']}</a>
                        </div>
                    </div>
                <?php } ?>

            </div>
        </div>
    <?php } ?>
</div>

{include file='public/footer' /}
<script>
    $.ajax({
        type: "get",
        url: "<?php echo url('index/upgradeTips')?>",
        dataType: "json",
        success: function (res) {
            if (res.code == 1) {
                if(confirm(res.msg)){
                    window.location.href = "<?php echo url('config/system_update')?>"
                }
            }
        }
    });
</script>