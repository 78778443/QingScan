{include file='public/head' /}


<div class="row tuchu">
    <div class="col-md-12 ">
        <?php if (!empty($list)) { ?>
            <table class="table  table-hover table-sm table-borderless">
                <thead class="table-light">
                <tr>
                    <?php
                    $keys = array_keys($list[0]);
                    foreach ($keys as $value) { ?>
                        <th>{$value}</th>
                    <?php } ?>
                </tr>
                </thead>
                <?php foreach ($list as $value) { ?>
                    <tr>
                        <?php foreach ($keys as $key) { ?>
                            <td><?php echo $value[$key] ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </table>
        <?php } else {
            echo "<h3 class='text-center'>列表没有数据</h3>";
        } ?>
    </div>
</div>
{include file='public/fenye' /}
{include file='public/footer' /}