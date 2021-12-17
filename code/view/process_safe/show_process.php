{include file='public/head' /}
<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
?>

<?php
$searchArr = [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [

    ],
    'btnArr' => [
        ['text' => '返回列表', 'ext' => [
            "href" => url('index'),
            "class" => "btn btn-outline-success"
        ]
        ]
    ]]; ?>
{include file='public/search' /}
<div class="col-md-12 ">
    <div class="row tuchu">
        <table class="table table-bordered table-hover table-striped">
            <tbody>
            <?php foreach ($info as $val) {
                $arr = array_values(array_filter(explode('  ', $val)));
                if (empty($arr)) continue;
                ?>
                <tr>
                    <td><?php echo $arr[0] ?? '' ?></td>
                    <td><?php echo $arr[1] ?? '' ?></td>
                    <td><?php echo $arr[2] ?? '' ?></td>
                    <td><?php echo $arr[3] ?? '' ?></td>
                    <td><?php echo $arr[4] ?? '' ?></td>
                    <td><a href="<?php echo url('kill', ['pid' => $arr[1] ?? '']) ?>" class="btn btn-outline-danger">干掉他！</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
{include file='public/footer' /}