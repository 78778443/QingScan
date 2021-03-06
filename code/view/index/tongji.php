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
    <div class="col-4">
        <div class="tuchu" style="width:100%;height:400px" id="divContainer">
            <h2 class="text-center">QingScan ????????????</h2>
            <table class="table table-bordered table-hover table-striped">
                <tr>
                    <th>??????</th>
                    <th>??????</th>
                    <th>??????</th>
                    <th>??????</th>
                </tr>
                <?php foreach ($zanzhu as $info) { ?>
                    <tr>
                        <td><?php echo $info['name'] ?></td>
                        <td><?php echo $info['time'] ?></td>
                        <td><?php echo $info['amount'] ?>???</td>
                        <td><?php echo $info['message'] ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <th>????????????</th>
                    <th>---</th>
                    <th><?php echo array_sum(array_column($zanzhu, 'amount')) ?>???</th>
                    <th>???????????????QingScan???????????????~</th>
                </tr>
            </table>
            <img style="max-width:400px" src="http://oss.songboy.site/blog/20211231152624.png">
            <p>QingScan ?????????????????????????????????,????????????QingScan?????????????????????GitHub??????????????????Star,
                ???????????????<a href="https://github.com/78778443/QingScan" target="_blank">QingScan</a>???????????????????????????????????????~</p>
        </div>


    </div>
    <div class="col-4">
        <div class="tuchu" style="width:100%;height:400px" id="divContainer">
            <div class="text-center">
                <img style="max-width:300px" src="http://oss.songboy.site/blog/c774168de639d596f6d18352888eaaa.jpg">
                <h2 class="text-center">QingScan ?????????</h2>
                <p>QingScan ??????????????????????????????????????????????????????????????????????????????~</p>
            </div>
        </div>


    </div>
</div>

{include file='public/footer' /}
