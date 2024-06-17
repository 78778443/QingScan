{include file='public/head' /}
<div class="col-md-1 " style="padding-right: 0;" >
    {include file='public/asmLeftMenu' /}
</div>
<div class="col-md-11 " style="padding:0;">

    <div class="col-md-12 ">
        <?php
        $searchArr = [
            'action' => $_SERVER['REQUEST_URI'],
            'method' => 'get',
            'inputs' => [
                ['type' => 'text', 'name' => 'search', 'placeholder' => '请输入要搜索的关键字'],
            ],
            'btnArr' => [
                ['text' => '添加URL', 'ext' => [
                    "href" => url('urls/add'),
                    "class" => "btn btn-sm btn-outline-secondary"
                ]]
            ]]; ?>
        {include file='public/search' /}

        <div class="row tuchu">
            <div class="col-md-12 ">
                {include file='public/batch_del' /}
                <table class="table  table-hover table-sm table-borderless">
                    <thead class="table-light">
                    <tr>
                        <th width="70">
                            <label>
                                <input type="checkbox" value="-1" onclick="quanxuan(this)">ID
                            </label>
                        </th>
                        <th>URL</th>
                        <th>标题</th>
                        <th>状态码</th>
                        <th>server</th>
                        <th>ISP</th>
                        <th>IP</th>
                        <th>创建时间</th>
                        <!--<th>sqlmap</th>-->
                        <!--                    <td style="width: 70px">状态</td>-->
                        <th style="width: 200px">操作</th>
                    </tr>
                    </thead>
                    <?php foreach ($list as $value) { ?>
                        <tr>
                            <td>
                                <label>
                                    <input type="checkbox" class="ids" name="ids[]" value="<?php echo $value['id'] ?>"><?php echo $value['id'] ?>
                                </label>
                            </td>

                            <td class="ellipsis-type">
                                <a href="<?php echo $value['url'] ?>" target="_blank"><?php echo $value['url'] ?></a></td>
                            <td class="truncate-td" title="<?php echo $value['title'] ?>"><?php echo $value['title'] ?></td>
                            <td><?php echo $value['status'] ?></td>
                            <td><?php echo $value['server'] ?></td>
                            <td><?php echo $value['isp'] ?></td>
                            <td class="truncate-td" title="<?php echo$value['ip'] ?>(<?php echo$value['address']?>)"><?php echo$value['ip'] ?> <?php echo$value['address']?> </td>
                            <td><?php echo $value['create_time'] ?></td>
                            <td>
                                <a href="<?php echo url('urls/del', ['id' => $value['id']]) ?>"
                                   class="btn btn-sm btn-outline-danger">删除</a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            {include file='public/fenye' /}
        </div>

    </div>
</div>
{include file='public/footer' /}