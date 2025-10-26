{include file='public/head' /}
<div class="col-md-1 " style="padding-right: 0;">
    {include file='public/whiteLeftMenu' /}
</div>
<div class="col-md-11 " style="padding:0;">
<div class="col-12 tuchu"  >
    <div class="row">
        <?php foreach ($countList as $item) { ?>
            <div class="col-3">
                <div style="height: 155px;margin-bottom:20px;border-radius: 10px;padding:10px;border: 1px solid #eee;">
                    <p style="color: #ccc;font-weight:bold;">{$item['name']}</p>
                    <h4>{$item['num']}</h4>
                    <?php foreach ($item['lists'] as $tag => $num) { ?>
                        <span style="color: #ccc;">{$tag}</span> <span>{$num}</span>&nbsp;
                    <?php } ?>
                </div>

            </div>
        <?php } ?>

    </div>
    <div class="row">
        <div class="col-12">

            <div class="btn-group" role="group" aria-label="Basic outlined example">
                <a type="button"
                   class="btn btn-outline-primary  <?php echo isset($param['extra']) ? '' : 'active' ?> "
                   href="{:URL('index')}">全部</a>
                <a type="button"
                   class="btn btn-outline-primary <?php echo isset($param['extra']) && $param['extra'] == 'HIGHT' ? 'active' : '' ?>"
                   href="{:URL('index',['extra'=>'HIGHT'])}">高危</a>
                <a type="button"
                   class="btn btn-outline-primary <?php echo isset($param['extra']) && $param['extra'] == 'MEDIUM' ? 'active' : '' ?>"
                   href="{:URL('index',['extra'=>'MEDIUM'])}">中危</a>
                <a type="button"
                   class="btn btn-outline-primary <?php echo isset($param['extra']) && $param['extra'] == 'LOW' ? 'active' : '' ?>"
                   href="{:URL('index',['extra'=>'LOW'])}">低危</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div style="margin-top:20px;">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th style="color:#aaa;">id</th>
                        <th style="color:#aaa;">ruleId</th>
                        <th style="color:#aaa;">发现时间</th>
                        <th style="color:#aaa;">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($bugList['list'] as $item) { ?>
                        <tr>
                            <td>{$item['id']}</td>
                            <td>{$item['ruleId']}</td>

                            <td>{$item['create_time']}</td>
                            <td><a class="btn btn-sm btn-light" href="{:URL('detail',['id'=>$item['id']])}"
                                   aria-disabled="true">查看详情</a></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            {include file='public/fenye' /}
        </div>
    </div>
</div>
{include file='public/footer' /}