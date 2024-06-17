{include file='public/head' /}


<div class="col-md-1 " style="padding-right: 0;" >
    {include file='public/systemLeftMenu' /}
</div>
<div class="col-md-11 " style="padding:0;">
    <div class="row tuchu">
    <?php
    $searchArr = [
        'action' => $_SERVER['REQUEST_URI'],
        'method' => 'get',
        'inputs' => [
            ['type' => 'text', 'name' => 'search', 'placeholder' => "search"],
        ],
        'btnArr' => [
            ['text' => '添加', 'ext' => [
                "href" => url('config/add'),
                "class" => "btn btn-sm btn-outline-secondary"
            ]
            ]
        ]]; ?>
    {include file='public/search' /}
    <table class="table  table-hover table-sm table-borderless">
        <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>key</th>
            <th>name</th>
            <th>value</th>
        </tr>
        </thead>
        <?php foreach ($list as $value) { ?>
            <tr>
                <td><?php echo $value['id'] ?></td>
                <td><?php echo $value['key'] ?></td>
                <td><?php echo $value['name'] ?></td>
                <td><input type="text" class="form-control" value="<?php echo $value['value'] ?>"></td>

            </tr>
        <?php } ?>
    </table>
</div>
</div>


{include file='public/fenye' /}
</div>
{include file='public/footer' /}