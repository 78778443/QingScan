{include file='public/head' /}
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<div class="row">
    <?php foreach ($list as $key => $value) { ?>
        <div class="col-4">
            <div id="<?php echo $value['key'] ?>" class="tuchu" style="width:100%;height:400px"></div>
            <script type="text/javascript">
                var dom = document.getElementById("<?php echo $value['key']?>");
                var myChart = echarts.init(dom);
                var app = {};
                var option;
                option = {
                    title: {
                        text: '<?php echo $value['title']?>',
                        x: 'center'
                    },
                    tooltip: {
                        trigger: 'item'
                    },
                    series: [
                        {
                            name: '<?php echo $value['title']?>',
                            type: 'pie',
                            radius: ['40%', '70%'],
                            avoidLabelOverlap: false,
                            itemStyle: {
                                borderRadius: 10,
                                borderColor: '#fff',
                                borderWidth: 2
                            },
                            emphasis: {
                                label: {
                                    show: true,
                                    fontSize: '16',
                                    fontWeight: 'bold'
                                }
                            },
                            data: <?php echo json_encode($value['data']);?>
                        }
                    ]
                };

                if (option && typeof option === 'object') {
                    myChart.setOption(option);
                }
            </script>
        </div>
    <?php } ?>
</div>

{include file='public/footer' /}
