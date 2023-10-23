</div>
</div>

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

    // Function to get current page path
    function getCurrentPagePath() {
        var url = window.location.href;
        var path = url.match(/\/\/[^\/]*(\/[^?#]*)/);
        if (path && path.length > 1) {
            return path[1].replace(/\.html.*$/, '');
        }
        return '';
    }

    // Function to add 'active' class to A links containing the path
    function addActiveClassToLinks(path) {
        $('#leftMenu a').each(function () {
            if ($(this).attr('href').indexOf(path) !== -1) {
                $(this).addClass('active');
            }
        });
    }

    $(document).ready(function () {
        // 获取所有具有 auto-height-textarea 类的 textarea 元素
        var textareas = document.getElementsByClassName("auto-height-textarea");

        // 为每个 textarea 添加 input 事件监听器
        Array.from(textareas).forEach(function (textarea) {
            textarea.addEventListener("input", function () {
                // 自动调整高度
                this.style.height = "auto";
                this.style.height = this.scrollHeight + "px";
            });

            // 页面加载完毕后，首次触发 input 事件，以便调整初始高度
            textarea.dispatchEvent(new Event("input"));
        });

    });


    // Call the functions
    var currentPagePath = getCurrentPagePath();
    addActiveClassToLinks(currentPagePath);


</script>
</body>
</html>