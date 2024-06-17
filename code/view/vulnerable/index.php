{include file='public/head' /}

<div class="col-md-1 " style="padding-right: 0;">
    {include file='public/vulLeftMenu' /}
</div>
<div class="col-md-11 " style="padding:0;">
    <?php
    $dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
    $searchArr = [
        'action' => $_SERVER['REQUEST_URI'],
        'method' => 'get',
        'inputs' => [
            ['type' => 'text', 'name' => 'search', 'placeholder' => "搜索的内容"],
        ],
        'btnArr' => [

        ]]; ?>
    {include file='public/search' /}

    <div class="row tuchu">
        <div class="col-md-12 ">
            {include file='public/batch_del' /}
            <table class="table  table-hover table-sm table-borderless">
                <thead class="table-light">
                <tr>
                    <th width="80">
                        <label>
                            <input type="checkbox" value="-1" onclick="quanxuan(this)">ID
                        </label>
                    </th>
                    <th>漏洞名称</th>
                    <th>等级</th>
                    <th style="width: 200px">操作</th>
                </tr>
                </thead>
                <?php foreach ($list as $value) {
                    if(empty($value['vuln_name'])) continue;
                    ?>
                    <tr>
                        <td>
                            <label>
                                <input type="checkbox" class="ids" name="ids[]" value="<?php echo $value['id'] ?>">
                                <?php echo $value['id'] ?> </label>
                        </td>
                        <td>{$value['vuln_name']}</td>
                        <td>{$value['risk_level']}</td>

                        <td>
                            <a href="<?php echo url('vulnerable/details', ['id' => $value['id']]) ?>"
                               class="btn btn-sm btn-outline-secondary">查看漏洞</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        {include file='public/fenye' /}
    </div>

</div>
{include file='public/footer' /}

<input type="hidden" id="batch_del_url" value="<?php echo url('vulnerable_batch_del') ?>">