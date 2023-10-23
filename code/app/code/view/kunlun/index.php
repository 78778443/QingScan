{include file='public/head' /}

<?php
$dengjiArr = ['Low', 'Medium', 'High', 'Critical'];
?>

<?php
$searchArr = [
    'action' => $_SERVER['REQUEST_URI'],
    'method' => 'get',
    'inputs' => [
        ['type' => 'text', 'name' => 'search', 'placeholder' => "搜索的内容"],
        ['type' => 'select', 'name' => 'level', 'options' => $dengjiArr, 'frist_option' => '危险等级'],
        ['type' => 'select', 'name' => 'Category', 'options' => $CategoryList, 'frist_option' => '漏洞类别'],
        ['type' => 'select', 'name' => 'code_id', 'options' => $projectList, 'frist_option' => '项目列表'],
        ['type' => 'select', 'name' => 'filename', 'options' => $fileList, 'frist_option' => '文件筛选'],
        ['type' => 'select', 'name' => 'check_status', 'options' => $check_status_list, 'frist_option' => '审计状态'],
    ]];
?>
{include file='public/search' /}

<div class="row tuchu">
    <div class="col-md-12 ">
        {include file='public/batch' /}
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
                <th> CVI ID</th>
<!--                <th>编程语言</th>-->
                <th>漏洞类型</th>
                <th>VulFile Path/Title</th>
                <th>来源</th>
                <th>危险等级</th>
                <th>状态</th>
                <th style="width: 100px">操作</th>
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
                    <td><?php echo $projectArr[$value['scan_project_id']]['project_name'] ?></td>
                    <td><?php echo $value['cvi_id'] ?></td>
<!--                    <td>--><?php //echo $value['language'] ?><!--</td>-->
                    <td><?php echo $value['result_type'] ?></td>
                    <td><?php echo $value['vulfile_path'] ?></td>
                    <td><?php echo $value['source_code'] ?></td>
                    <td><?php echo $dengjiArr[$value['is_active']] ?></td>
                    <td><select class="changCheckStatus form-select" data-id="<?php echo $value['id'] ?>">
                            <option value="0" <?php echo $value['check_status'] == 0 ? 'selected' : ''; ?> >未审核</option>
                            <option value="1" <?php echo $value['check_status'] == 1 ? 'selected' : ''; ?> >有效漏洞
                            </option>
                            <option value="2" <?php echo $value['check_status'] == 2 ? 'selected' : ''; ?> >无效漏洞
                            </option>
                        </select>
                    </td>
                    <td>
                        <a href="<?php echo url('code/kunlun_details', ['id' => $value['id']]) ?>"
                           class="btn btn-sm btn-outline-secondary">查看漏洞</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

<input type="hidden" id="to_examine_url" value="<?php echo url('to_examine/kunlun') ?>">
{include file='public/to_examine' /}
{include file='public/fenye' /}
{include file='public/footer' /}