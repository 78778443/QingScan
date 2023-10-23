{include file='public/head' /}

<?php
$searchArr = [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' =>'search'],
    ],
    'btnArr' => [
        ['text' => '手动备份', 'ext' => [
            "href" => url('backup'),
            "class" => "btn btn-sm btn-outline-secondary"
        ]]
    ]];

?>
{include file='public/search' /}


<div class="col-md-12 ">
    <div class="row tuchu">
        <div class="col-md-12 ">
            <table class="table  table-hover table-sm table-borderless">
                <thead class="table-light">
                <tr>
                    <th>编号</th>
                    <th>文件名</th>
                    <th>文件大小</th>
                    <th>备份时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <?php foreach ($list as $key=>$value) { ?>
                    <tr>
                        <td><?php echo $key+1 ?></td>
                        <td><?php echo $value['filename'] ?></td>
                        <td><?php echo $value['size'] ?></td>
                        <td><?php echo $value['create_time'] ?></td>
                        <td>
                            <!--<a href="<?php /*echo url('recovery',['time'=>$value['time']])*/?>"
                               class="btn btn-sm btn-outline-secondary">恢复</a>-->
                            <a href="<?php echo url('download',['time'=>$value['time']])?>"
                               class="btn btn-sm btn-outline-secondary">下载</a>
                            <a href="<?php echo url('del',['time'=>$value['time']])?>" class="btn btn-sm btn-outline-danger">删除</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    {include file='public/fenye' /}
</div>

{include file='public/footer' /}