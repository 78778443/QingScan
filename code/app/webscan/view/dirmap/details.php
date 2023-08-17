{include file='public/head' /}
<style>
    body {
        margin: 0;
        color: rgba(0, 0, 0, .65);
        font-size: 14px;
        font-family: -apple-system, BlinkMacSystemFont, Segoe UI, PingFang SC, Hiragino Sans GB, Microsoft YaHei, Helvetica Neue, Helvetica, Arial, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol;
        font-variant: tabular-nums;
        line-height: 1.5;
        background-color: #fff;
        -webkit-font-feature-settings: "tnum";
        font-feature-settings: "tnum";
    }

    .ant-card {
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        color: rgba(0, 0, 0, .65);
        font-size: 14px;
        font-variant: tabular-nums;
        line-height: 1.5;
        list-style: none;
        -webkit-font-feature-settings: "tnum";
        font-feature-settings: "tnum";
        position: relative;
        background: #fff;
        border-radius: 2px;
        -webkit-transition: all .3s;
        transition: all .3s;
    }

    .ant-card-head {
        min-height: 48px;
        margin-bottom: -1px;
        padding: 0 24px;
        color: rgba(0, 0, 0, .85);
        font-weight: 500;
        font-size: 16px;
        background: transparent;
        border-bottom: 1px solid #e8e8e8;
        border-radius: 2px 2px 0 0;
        zoom: 1;
    }

    .ant-card {
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        color: rgba(0, 0, 0, .65);
        font-size: 14px;
        font-variant: tabular-nums;
        line-height: 1.5;
        list-style: none;
        -webkit-font-feature-settings: "tnum";
        font-feature-settings: "tnum";
        position: relative;
        background: #fff;
        border-radius: 2px;
        -webkit-transition: all .3s;
        transition: all .3s;
    }

    .ant-layout {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-flex: 1;
        -ms-flex: auto;
        flex: auto;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        min-height: 0;
        background: #f0f2f5;
    }

    .ant-card-head-wrapper {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
    }

    .ant-card-head-title {
        display: inline-block;
        -webkit-box-flex: 1;
        -ms-flex: 1;
        flex: 1;
        padding: 16px 0;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .ant-card-extra {
        float: right;
        margin-left: auto;
        padding: 16px 0;
        color: rgba(0, 0, 0, .65);
        font-weight: 400;
        font-size: 14px;
    }

    h1, h2, h3, h4, h5, h6 {
        margin-top: 0;
        margin-bottom: .5em;
        color: rgba(0, 0, 0, .85);
        font-weight: 500;
    }

    h3 {
        display: block;
        font-size: 1.17em;
        margin-block-start: 1em;
        margin-block-end: 1em;
        margin-inline-start: 0px;
        margin-inline-end: 0px;
        font-weight: bold;
    }

    .ant-spin-nested-loading {
        position: relative;
    }

    .ant-spin-container {
        position: relative;
        -webkit-transition: opacity .3s;
        transition: opacity .3s;
    }

    .ant-table {
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        color: rgba(0, 0, 0, .65);
        font-size: 14px;
        font-variant: tabular-nums;
        line-height: 1.5;
        list-style: none;
        -webkit-font-feature-settings: "tnum";
        font-feature-settings: "tnum";
        position: relative;
        clear: both;
    }

    .ant-table table {
        width: 100%;
        text-align: left;
        border-radius: 4px 4px 0 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    thead {
        display: table-header-group;
        vertical-align: middle;
        border-color: inherit;
    }

    tr {
        display: table-row;
        vertical-align: inherit;
        border-color: inherit;
    }

    thead > tr > th {
        padding: 16px 16px;
        overflow-wrap: break-word;
    }

    .ant-table-thead > tr > th {
        color: rgba(0, 0, 0, .85);
        font-weight: 500;
        text-align: left;
        background: #fafafa;
        border-bottom: 1px solid #e8e8e8;
        -webkit-transition: background .3s ease;
        transition: background .3s ease;
    }

    .ant-table-thead > tr > th .ant-table-header-column {
        display: inline-block;
        max-width: 100%;
        vertical-align: top;
    }

    .ant-table-thead > tr > th .ant-table-column-sorter {
        display: table-cell;
        vertical-align: middle;
    }

    .ant-table-thead > tr > th .ant-table-column-sorter .ant-table-column-sorter-inner {
        height: 1em;
        margin-top: .35em;
        margin-left: .57142857em;
        color: #bfbfbf;
        line-height: 1em;
        text-align: center;
        -webkit-transition: all .3s;
        transition: all .3s;
    }

    tbody {
        display: table-row-group;
        vertical-align: middle;
        border-color: inherit;
    }

    .ant-table-tbody > tr > td, .ant-table-thead > tr > th {
        padding: 16px 16px;
        overflow-wrap: break-word;
    }

    .ant-table-tbody > tr > td {
        border-bottom: 1px solid #e8e8e8;
        -webkit-transition: background .3s;
        transition: background .3s;
    }

    td {
        display: table-cell;
        vertical-align: inherit;
    }

    .ant-table-tbody > tr > td {
        border-bottom: 1px solid #e8e8e8;
        -webkit-transition: background .3s;
        transition: background .3s;
    }

    .ant-table-tbody > tr > td, .ant-table-thead > tr > th {
        padding: 16px 16px;
        overflow-wrap: break-word;
    }

    .ant-descriptions-bordered .ant-descriptions-view {
        border: 1px solid #e8e8e8;
    }

    .expand-detail :not(.internal-detail) .ant-descriptions-item-content, .expand-detail :not(.internal-detail) .ant-descriptions-item-label {
        border-bottom: 1px solid #e8e8e8;
    }

    .expand-detail :not(.internal-detail) .ant-descriptions-item-label {
        width: 150px;
    }

    .ant-descriptions-bordered.ant-descriptions-middle .ant-descriptions-item-content, .ant-descriptions-bordered.ant-descriptions-middle .ant-descriptions-item-label {
        padding: 12px 24px;
    }

    .ant-descriptions-bordered .ant-descriptions-item-label {
        background-color: #fafafa;
    }

    .expand-detail :not(.internal-detail) pre {
        margin: 8px 0;
    }

    pre {
        margin-top: 0;
        margin-bottom: 1em;
        overflow: auto;
    }
</style>
<main class="ant-layout-content" style="padding: 0px 50px; margin-top: 96px;">
    <div style="margin-bottom: 48px;">
        <div class="ant-card ant-card-bordered" style="width: 100%;">
            <div class="ant-card-head">
                <div class="ant-card-head-wrapper">
                    <div class="ant-card-head-title"><h3>Web Vulnerabilities</h3></div>
                    <div class="ant-card-extra">
                        <div class="pull-right">
                            <input type="hidden" id="to_examine_url" value="<?php echo url('to_examine/xray')?>">
                            {include file='public/to_examine' /}
                            <?php if($info['check_status'] == 0){?>
                                <span class="follow-vul j-follow-vul ">
                                <a href="javascript:;" class="btn btn-sm btn-outline-secondary" onclick="to_examine(<?php echo $info['id']?>)">审核</a>
                            </span>
                            <?php }?>
                            <span class="follow-vul j-follow-vul ">
                                <a href="<?php echo url('xray/index') ?>" class="btn btn-sm btn-outline-secondary">返回列表页</a>
                            </span>
                            <span class="follow-vul j-follow-vul ">
                                <a href="<?php echo url('xray/details', ['id' => $info['upper_id']]) ?>"
                                   class="btn btn-sm btn-outline-secondary">上一页</a>
                            </span>
                            <span class="follow-vul j-follow-vul ">
                                <a href="<?php echo url('xray/details', ['id' => $info['lower_id']]) ?>"
                                   class="btn btn-sm btn-outline-secondary">下一页</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ant-card-body" style="padding: 0px;">
                <div class="ant-table-wrapper">
                    <div class="ant-spin-nested-loading">
                        <div class="ant-spin-container">
                            <div class="ant-table ant-table-scroll-position-left ant-table-default">
                                <div class="ant-table-content"><!---->
                                    <div class="ant-table-body">
                                        <table class="">
                                            <thead class="ant-table-thead">
                                            <tr>
                                                <th key="rc-table-expand-icon-cell" title="" rowspan="1"
                                                    class="ant-table-expand-icon-th"></th>
                                                <th key="id" class="ant-table-row-cell-break-word"><span
                                                            class="ant-table-header-column"><div><span
                                                                    class="ant-table-column-title">ID</span></div></span>
                                                </th>
                                                <th key="target"
                                                    class="ant-table-column-has-actions ant-table-column-has-sorters">
                                                    <span class="ant-table-header-column"><div
                                                                class="ant-table-column-sorters"><span
                                                                    class="ant-table-column-title">Target</span>
                                                            </div></span>
                                                </th>
                                                <th key="plugin"
                                                    class="filter-column ant-table-column-has-actions ant-table-column-has-filters ant-table-column-has-sorters">
                                                    <span class="ant-table-header-column"><div
                                                                class="ant-table-column-sorters"><span
                                                                    class="ant-table-column-title">PluginName / VulnType</span>

                                                        </div></span></th>
                                                <th key="create_time"
                                                    class="ant-table-column-has-actions ant-table-column-has-sorters ant-table-row-cell-last">
                                                    <span class="ant-table-header-column"><div
                                                                class="ant-table-column-sorters"><span
                                                                    class="ant-table-column-title">CreateTime</span></div></span>
                                                </th>
                                                <th key="create_time"
                                                    class="ant-table-column-has-actions ant-table-column-has-sorters ant-table-row-cell-last">
                                                    <span class="ant-table-header-column"><div
                                                                class="ant-table-column-sorters"><span
                                                                    class="ant-table-column-title">审核状态</span></div></span>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody class="ant-table-tbody">
                                            <tr class="ant-table-row ant-table-row-level-0" data-row-key="0">
                                                <td class="ant-table-row-expand-icon-cell">
                                                    <div role="button" tabindex="0" aria-label="Expand row"
                                                         class="ant-table-row-expand-icon ant-table-row-collapsed"></div>
                                                </td>
                                                <td class="ant-table-row-cell-break-word"><?php echo $info['id'] ?></td>
                                                <td class="ant-table-column-has-actions ant-table-column-has-sorters"><a
                                                            href="<?php echo $info['detail']['addr'] ?>"
                                                            style="display: inline-block; max-width: 50vw;">
                                                        <?php echo $info['detail']['addr'] ?>
                                                    </a></td>
                                                <td class="ant-table-column-has-actions ant-table-column-has-filters ant-table-column-has-sorters filter-column">
                                                    <?php echo $info['plugin'] ?>
                                                </td>
                                                <td class="ant-table-column-has-actions ant-table-column-has-sorters">
                                                    <?php echo date('Y-m-d H:i:s', substr($info['create_time'], 0, 10)) ?>
                                                </td>
                                                <td class="ant-table-column-has-actions ant-table-column-has-sorters">
                                                    <select  class="changCheckStatus form-select"  data-id="<?php echo $info['id'] ?>">
                                                        <option value="0" <?php echo $info['check_status'] == 0 ? 'selected' : ''; ?> >未审核</option>
                                                        <option value="1" <?php echo $info['check_status'] == 1 ? 'selected' : ''; ?> >有效漏洞</option>
                                                        <option value="2" <?php echo $info['check_status'] == 2 ? 'selected' : ''; ?> >无效漏洞</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr data-row-key="0-extra-row"
                                                class="ant-table-expanded-row ant-table-expanded-row-level-1" style="">
                                                <td class=""></td>
                                                <td colspan="4">
                                                    <div style="margin: 0px;">
                                                        <div class="expand-detail ant-descriptions ant-descriptions-middle ant-descriptions-bordered">
                                                            <div class="ant-descriptions-view">
                                                                <table>
                                                                    <tbody>
                                                                    <tr class="ant-descriptions-row">
                                                                        <th class="ant-descriptions-item-label ant-descriptions-item-colon">
                                                                            URL
                                                                        </th>
                                                                        <td colspan="1"
                                                                            class="ant-descriptions-item-content"><a
                                                                                    href="<?php echo $info['detail']['addr'] ?>"
                                                                                    target="_blank"><?php echo $info['detail']['addr'] ?></a>
                                                                        </td>
                                                                    </tr>
                                                                    <tr class="ant-descriptions-row">
                                                                        <th class="ant-descriptions-item-label ant-descriptions-item-colon">
                                                                            Request1
                                                                        </th>
                                                                        <td colspan="1"
                                                                            class="ant-descriptions-item-content">
                                                                            <pre><?php echo trim($info['detail']['snapshot'][0][0]) ?> </pre>
                                                                        </td>
                                                                    </tr>
                                                                    <tr class="ant-descriptions-row">
                                                                        <th class="ant-descriptions-item-label ant-descriptions-item-colon">
                                                                            Response1
                                                                        </th>
                                                                        <td colspan="1"
                                                                            class="ant-descriptions-item-content">
                                                                            <pre style="max-height: 600px; max-width: 100%;"><?php echo trim($info['detail']['snapshot'][0][0]) ?> </pre>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

{include file='public/to_examine' /}
{include file='public/footer' /}
