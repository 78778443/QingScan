{include file='public/head' /}
<div class="col-md-1 " style="padding-right: 0;" >
    {include file='public/blackLeftMenu' /}
</div>
<div class="col-md-11 " style="padding:0;">
<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
?>
    <?php
    $searchArr = [
        'action' => $_SERVER['REQUEST_URI'],
        'method' => 'get',
        'inputs' => [
            ['type' => 'text', 'name' => 'search', 'placeholder' =>'search'],
        ],
        'btnArr' => [
            ['text' => '关键词', 'ext' => [
                "href" => url('webscan/github_keyword_monitor/index'),
                "class" => "btn btn-sm btn-outline-secondary"
            ]]
        ]
    ];

    ?>
    {include file='public/search' /}

<div class="col-md-12 ">
    <div class="row tuchu">
        <div class="col-md-12 ">
            {include file='public/batch_del' /}
            <table class="table  table-hover table-sm table-borderless">
                <thead class="table-light">
                <tr>
                    <th width="70">
                        <label>
                            <input type="checkbox" value="-1" onclick="quanxuan(this)">全选
                        </label>
                    </th>
                    <th>ID</th>
                    <th>关键字</th>
                    <th>名称</th>
                    <th>文件路径</th>
                    <th>url地址</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td>
                            <label>
                                <input type="checkbox" class="ids" name="ids[]" value="<?php echo $value['id'] ?>">
                            </label>
                        </td>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php echo $value['title'] ?></td>
                        <td><?php echo $value['name'] ?></td>
                        <td><?php echo $value['path'] ?></td>
                        <td><?php echo $value['html_url'] ?></td>
                        <td><?php echo $value['create_time'] ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    {include file='public/fenye' /}
</div></div>
{include file='public/footer' /}