</div>
<footer class="footer navbar-fixed-bottom">
    <div class=" footer-bottom">
        <ul class="list-inline text-center">
            <li style="color:red;">QingScan 产品仅授权你在遵守《<a
                        href="https://baike.baidu.com/item/%E4%B8%AD%E5%8D%8E%E4%BA%BA%E6%B0%91%E5%85%B1%E5%92%8C%E5%9B%BD%E7%BD%91%E7%BB%9C%E5%AE%89%E5%85%A8%E6%B3%95"
                        target="_blank">中华人民共和国网络安全法</a>》前提下使用，请不要擅自对未获得授权的目标进行安全测试！！！
            </li>
        </ul>
    </div>
</footer>
<script>
    function quanxuan(obj) {
        var child = $('.table').find('.ids');
        child.each(function (index, item) {
            if (obj.checked) {
                item.checked = true
            } else {
                item.checked = false
            }
        })
    }
</script>
</body>
</html>