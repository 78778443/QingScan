{include file='public/head' /}

<?php
$searchArr = [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
    ]];
?>
{include file='public/search' /}

<div class="col-md-12 ">

    <div class="row tuchu">
        <div class="col-md-12 ">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>add_by</th>
                    <th>add_time</th>
                    <th>filters</th>
                    <th>keyword</th>
                    <th>md5</th>
                    <th>supplier</th>
                    <th>tags</th>
                    <th>title</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['add_by'] ?></td>
                        <td><?php echo $value['add_time'] ?></td>
                        <td><?php echo $value['filters'] ?></td>
                        <td><?php echo $value['keyword'] ?></td>
                        <td><?php echo $value['md5'] ?></td>
                        <td><?php echo $value['supplier'] ?></td>
                        <td><?php echo $value['tags'] ?></td>
                        <td><?php echo $value['title'] ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    {include file='public/fenye' /}
</div>
{include file='public/footer' /}