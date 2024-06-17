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
<div class="row" style="--bs-gutter-x:0rem">
    <?php foreach ($data as $key => $value) { ?>
        <div class="col-md-6">
            <div class="row tuchu " style="min-height:300px;border-radius: 10px;">
                <div id="main<?= $key ?>"></div>
                <script type="text/javascript">
                    // 基于准备好的dom，初始化echarts实例
                    var myChart<?=$key?> = echarts.init(document.getElementById('main<?=$key?>'));
                    // 指定图表的配置项和数据
                    var option<?=$key?> = {
                        title: {text: '<?=$value['name']?>'},
                        tooltip: {},
                        xAxis: {data: <?= json_encode(array_column($value['subInfo'], 'name'), 256) ?>},
                        yAxis: {},
                        series: [{type: 'bar', data: <?= json_encode(array_column($value['subInfo'], 'value'), 256) ?>}]
                    };
                    // 使用刚指定的配置项和数据显示图表。
                    myChart<?=$key?>.setOption(option<?=$key?>);
                </script>
            </div>
        </div>
    <?php } ?>

</div>

{include file='public/footer' /}
<script type="text/javascript">
    $("#home").addClass("nav-active");
</script>
<script>
    $.ajax({
        type: "get",
        url: "<?php echo url('index/upgradeTips')?>",
        dataType: "json",
        success: function (res) {
            if (res.code == 1) {
                if (confirm(res.msg)) {
                    window.location.href = "<?php echo url('config/system_update')?>"
                }
            }
        }
    });
</script>