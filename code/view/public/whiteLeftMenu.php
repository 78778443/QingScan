<style>
    #leftMenu li {
        padding-right: 5px;
        margin-bottom: 5px;
        text-align: left;
    }
</style>

<!-- 左侧菜单栏内容 -->
<div class="position-sticky" id="leftMenu">
        <div style="height:32px;"></div>
    <ul class="nav flex-column">
        <li style="background-color: #f0ad4e;">
            <a class="btn btn-sm btn-outline-secondary" href="/code/index.html">
                项目管理 </a>
        </li>
        <li>
            <a class="btn btn-sm btn-outline-secondary" href="/code/fortify/index.html">
                Fortify </a>
        </li>
        <li>
            <a class="btn btn-sm btn-outline-secondary" href="/code/semgrep/index.html">
                SemGrep </a>
        </li>
        <li>
            <a class="btn btn-sm btn-outline-secondary" href="/code/murphysec/index.html">
                成份分析 </a>
        </li>

        <li>
            <a class="btn btn-sm btn-outline-secondary" href="/code/code_webshell/index.html">
                WebShell </a>
        </li>
    </ul>
</div>
<script type="text/javascript">
    // 获取当前屏幕高度
    var screenHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

    // 设置要修改高度的DIV的ID，这里假设该DIV的ID为"myDiv"
    var div = document.getElementById("leftMenu");
    screenHeight -= 56;
    // 设置DIV的高度为当前屏幕高度
    div.style.height = screenHeight + "px";
</script>
<script type="text/javascript">
    $("#codeaudit").addClass("nav-active");
</script>