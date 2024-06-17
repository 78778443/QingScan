{include file='public/head' /}
<div class="col-md-1 " style="padding-right: 0;" >
    {include file='public/blackLeftMenu' /}
</div>
<div class="col-md-11 " style="padding:0;">
<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
?>

<?php
$searchArr = [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' => "搜索的内容"],
        ['type' => 'select', 'name' => 'Folder', 'options' => $dengjiArr, 'frist_option' => '危险等级'],
        ['type' => 'select', 'name' => 'Category', 'options' => $CategoryList, 'frist_option' => '漏洞类别'],
        ['type' => 'select', 'name' => 'Primary_filename', 'options' => $fileList, 'frist_option' => 'url筛选'],
        ['type' => 'select', 'name' => 'app_id', 'options' => $projectList, 'frist_option' => '项目列表'],
        ['type' => 'select', 'name' => 'check_status', 'options' => $check_status_list, 'frist_option' => '审计状态','frist_option_velue'=>-1],
    ]];
?>
{include file='public/search' /}

<div class="row tuchu">
    <div class="col-md-12 ">
        {include file='public/batch_del' /}
        <table class="table  table-hover table-sm table-borderless">
            <thead class="table-light">
            <tr>
                <th width="70">
                    <label>
                        <input type="checkbox" value="-1" onclick="quanxuan(this)">全选
                    </label>
                </th>
                <th>ID</th>
                <th>所属项目</th>
                <th>severity</th>
                <th>URL</th>
                <th>发现时间</th>
                <th>状态</th>
                <th style="width: 200px">操作</th>
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
                    <td><?php echo isset($projectList[$value['app_id']]) ? $projectList[$value['app_id']] : '' ?></td>
                    <td><?php echo $value['vt_name'] ?></td>
                    <td><?php echo $value['affects_url'] ?></td>
                    <td><?php echo $value['create_time'] ?></td>
                    <td><select  class="changCheckStatus form-select" data-id="<?php echo $value['id'] ?>">
                            <option value="0" <?php echo $value['check_status'] == 0 ? 'selected' : ''; ?> >未审核</option>
                            <option value="1" <?php echo $value['check_status'] == 1 ? 'selected' : ''; ?> >有效漏洞</option>
                            <option value="2" <?php echo $value['check_status'] == 2 ? 'selected' : ''; ?> >无效漏洞</option>
                        </select></td>
                    <td>
                        <a href="<?php echo url('code_check/bug_detail',['id'=>$value['id']])?>"
                           class="btn btn-sm btn-outline-secondary">查看漏洞</a>

                        <a href="<?php echo url('bug/awvs_del',['id'=>$value['id']])?>" class="btn btn-sm btn-outline-danger">删除</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

<input type="hidden" id="to_examine_url" value="<?php echo url('to_examine/awvs')?>">
<input type="hidden" id="batch_del_url" value="<?php echo url('awvs_batch_del')?>">

{include file='public/to_examine' /}
{include file='public/fenye' /}</div>
{include file='public/footer' /}