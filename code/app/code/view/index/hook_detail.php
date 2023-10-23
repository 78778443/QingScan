{include file='public/head' /}

?>
    <div class="row tuchu">
        <div class="col-md-12 ">
            <table class="table  table-hover table-sm table-borderless">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>漏洞类型</th>
                </tr>
                </thead>
                <?php foreach ($detail as $key => $value) { ?>
                    <tr>
                        <td><?php echo $key ?></td>
                        <td><?php
                            if ($key == 'results') {
                                foreach ($value as $k => $v) {
                                    dump($v);
                                }
                            } else {
                                echo $value;
                            }


                            ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
{include file='public/footer' /}