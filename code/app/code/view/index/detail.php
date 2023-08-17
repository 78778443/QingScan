<?php

$str = file_get_contents("/mnt/d/permeate.xml");

$obj = simplexml_load_string($str, "SimpleXMLElement", LIBXML_NOCDATA);
$test = json_decode(json_encode($obj), true);

$countList = $test['ReportSection'][0]['SubSection'][1]['IssueListing']['Chart']['GroupingSection'];

$list = $test['ReportSection'][2]['SubSection']['IssueListing']['Chart']['GroupingSection'];
foreach ($list as $key => $value) {
    foreach ($value['Issue'] as $k => $val) {
        if (strpos($val['Source']['FileName'], '.php') === false) {
            unset($value['Issue'][$k]);
        }
    }

    if (empty($value['Issue'])) {
        unset($list[$key]);
    }
}
?>
<html>
<head>
    <!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <!-- 可选的 Bootstrap 主题文件（一般不用引入） -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap-theme.min.css">

    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <script src="https://cdn.bootcdn.net/ajax/libs/echarts/5.1.1/echarts.min.js"></script>
</head>
<body>
<style>
    .code {
        width: 70%;
    }
</style>
<div class="container">


    <div id="main" style="width: 600px;height:600px;"></div>
    <script type="text/javascript">
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById('main'));
        // 指定图表的配置项和数据
        var option = {
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b}: {c} ({d}%)'
            },
            series: [
                {
                    name: '漏洞等级',
                    type: 'pie',
                    selectedMode: 'single',
                    radius: [0, '30%'],
                    label: {
                        position: 'inner',
                        fontSize: 14,
                    },
                    labelLine: {
                        show: false
                    },
                    data: [
                        <?php foreach ($countList as $value) {
                            echo "{value: {$value['@attributes']['count']}, name: '{$value['groupTitle']}'},";
                        } ?>
                    ]
                },
                {
                    name: '漏洞类型',
                    type: 'pie',
                    radius: ['45%', '60%'],
                    labelLine: {
                        length: 30,
                    },
                    label: {
                        formatter: '{a|{a}}{abg|}\n{hr|}\n  {b|{b}：}{c}  {per|{d}%}  ',
                        backgroundColor: '#F6F8FC',
                        borderColor: '#8C8D8E',
                        borderWidth: 1,
                        borderRadius: 4,

                        rich: {
                            a: {
                                color: '#6E7079',
                                lineHeight: 22,
                                align: 'center'
                            },
                            hr: {
                                borderColor: '#8C8D8E',
                                width: '100%',
                                borderWidth: 1,
                                height: 0
                            },
                            b: {
                                color: '#4C5058',
                                fontSize: 14,
                                fontWeight: 'bold',
                                lineHeight: 33
                            },
                            per: {
                                color: '#fff',
                                backgroundColor: '#4C5058',
                                padding: [3, 4],
                                borderRadius: 4
                            }
                        }
                    },
                    data: [
                        <?php foreach ($list as $value) {
                            $num = count($value['Issue']);
                            echo "{value: {$num}, name: '{$value['groupTitle']}'},";
                        } ?>
                    ]
                }
            ]
        };
        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
    </script>


    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <?php foreach ($list as $value) { ?>
            <li class="nav-item">
                <a class="nav-link" id="<?php echo md5($value['groupTitle']) ?>-tab" data-toggle="tab"
                   href="#" role="tab" aria-controls="<?php echo md5($value['groupTitle']) ?>"
                ><?php echo $value['groupTitle'] ?></a>
            </li>
        <?php } ?>
    </ul>

    <!--    <div class="tab-content" id="myTabContent">-->
    <?php foreach ($list

    as $value) { ?>
    <div class="tab-pane fade show" id="<?php echo md5($value['groupTitle']) ?>" role="tabpanel"
         aria-labelledby="<?php echo md5($value['groupTitle']) ?>-tab">
        <h1><?php echo $value['groupTitle'] ?></h1>
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">漏洞等级</th>
                <th scope="col">文件名</th>
                <th scope="col">行号</th>
                <th scope="col">代码段</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($value['Issue'] as $val) {
                if (strpos($val['Source']['FileName'], '.php') !== false) { ?>
                    <tr>
                        <td><?php echo $val['Friority'] ?></td>
                        <td title="<?php echo $val['Source']['FilePath'] ?>"><?php echo $val['Primary']['FileName'] ?></td>
                        <td title="<?php echo $val['Abstract'] ?>"><?php echo $val['Source']['LineStart'] ?></td>
                        <td>
                            <pre> <code title="<?php echo htmlspecialchars($val['Primary']['Snippet']) ?>"> <?php echo htmlspecialchars($val['Source']['Snippet']) ?></code> </pre>
                        </td>
                    </tr>
                <?php }
            } ?>
            </tbody>
        </table>
    <?php } ?>
    </div>
    <!--    </div>-->
</div>
</body>
</html>
