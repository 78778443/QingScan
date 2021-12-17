{include file='public/head' /}
<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
?>

<?php
$searchArr = [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' => "search"],
    ]];
?>
{include file='public/search' /}


    <div class="row tuchu">
        <div class="col-md-12 ">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>urls</th>
                    <th>type</th>
                    <th>title</th>
                    <th>payload</th>
                    <th>dbms</th>
                    <th>application</th>
                    <th>时间</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <td><?php echo $value['id'] ?></td>
                        <td><?php echo $value['urls_id'] ?></td>
                        <td><?php echo $value['type'] ?></td>
                        <td><?php echo $value['title'] ?></td>
                        <td  class="AutoNewline"><?php echo $value['payload'] ?></td>
                        <td><?php echo $value['dbms'] ?></td>
                        <td><?php echo $value['application'] ?></td>
                        <td><?php echo $value['create_time'] ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    <input type="hidden" id="to_examine_url" value="<?php echo url('to_examine/xray')?>">

    {include file='public/to_examine' /}
    {include file='public/fenye' /}
</div>
{include file='public/footer' /}