{include file='public/head' /}

<?php
$searchArr = [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' =>'search'],
    ]];
?>
{include file='public/search' /}

<div class="col-md-12 ">

    <div class="row tuchu">
        <div class="col-md-12 ">
            {include file='public/batch_del' /}
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>
                        <label>
                            <input type="checkbox" value="-1" onclick="quanxuan(this)">全选
                        </label>
                    </th>
                    <th>id</th>
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
                        <td>
                            <label>
                                <input type="checkbox" class="ids" name="ids[]" value="<?php echo $value['id'] ?>">
                            </label>
                        </td>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php echo $value['add_by'] ?></td>
                        <td><?php echo date('Y-m-d H:i:s',substr($value['add_time'],0,10)) ?></td>
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