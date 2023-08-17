{include file='public/head' /}

<link rel="stylesheet" href="/static/plugins/zTree/css/zTreeStyle.css" type="text/css">
<div class="row tuchu">
    <div class="col-md-12">
        <legend>配置权限</legend>
        <div class="col-md-9">
            <form class="">
                <ul id="treeDemo" class="ztree">

                </ul>
                <div class="layui-form-item text-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="submit">提交</button>
                    <button class="btn btn-sm btn-outline-secondary" type="button" onclick="window.history.back()">返回</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript" src="/static/plugins/zTree/js/jquery.ztree.core.min.js"></script>
<script type="text/javascript" src="/static/plugins/zTree/js/jquery.ztree.excheck.min.js"></script>
<script type="text/javascript">
    var setting = {
        check: {enable: true},
        view: {showLine: false, showIcon: false, dblClickExpand: false},
        data: {
            simpleData: {enable: true, pIdKey: 'pid', idKey: 'auth_rule_id'},
            key: {name: 'title'}
        }
    };

    var zNodes = <?php echo json_encode($data)?>;
    console.log(zNodes)


    function setCheck() {
        var zTree = $.fn.zTree.getZTreeObj("treeDemo");
        zTree.setting.check.chkboxType = {"Y": "ps", "N": "ps"};
    }

    $.fn.zTree.init($("#treeDemo"), setting, zNodes);
    setCheck();

    $('#submit').click(function(){
        // 提交到方法 默认为本身
        var treeObj = $.fn.zTree.getZTreeObj("treeDemo"),
            nodes = treeObj.getCheckedNodes(true),
            v = "";
        for (var i = 0; i < nodes.length; i++) {
            v += nodes[i].auth_rule_id + ",";
        }
        var auth_group_id = <?php echo $auth_group_id?>;
        $.ajax({
            type: 'post',
            url: "<?php echo url('auth/authGroupSetaccess')?>",
            data: {'rules': v, 'auth_group_id': auth_group_id},
            dataType: 'json',
            success: function (res) {
                alert(res.msg)
                if (res.code == 0) {
                    location.reload();
                }
            }
        });
    })
</script>

{include file='public/footer' /}