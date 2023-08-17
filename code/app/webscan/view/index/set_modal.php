<div class="modal fade setModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form method="post" action="/index.php?s=app/_add">

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">爬虫规则设置</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
<!--                        <div class="col-md-4">-->
<!--                            <h3>请求头配置</h3>-->
<!--                            <div class="checkbox">-->
<!--                                <label>-->
<!--                                    <input type="checkbox" name="enable-image-display"> 启用图片显示-->
<!--                                </label>-->
<!--                            </div>-->
<!--                            <div class="mb-3">-->
<!--                                <label>页面加载完毕后的等待时间</label>-->
<!--                                <input type="text" name="load-wait" class="form-control"-->
<!--                                       placeholder="页面加载完毕后的等待时间，单位秒，网速不佳时可尝试调大该值" required>-->
<!--                            </div>-->
<!--                            <div class="mb-3">-->
<!--                                <label>启动chrome的路径</label>-->
<!--                                <input type="text" name="exec-path" class="form-control" placeholder="启动chrome的路径"-->
<!--                                       required>-->
<!--                            </div>-->
<!--                            <div class="checkbox">-->
<!--                                <label>-->
<!--                                    <input type="checkbox" name="enable-image-display"> 禁用无头模式-->
<!--                                </label>-->
<!--                            </div>-->
<!--                        </div>-->
                        <div class="col-md-4">
                            <h3>请求头配置</h3>
                            <div class="mb-3">
                                <label>请求user-agent配置</label>
                                <input type="text" name="request-config[user-agent]" class="form-control"
                                       placeholder="请求user-agent配置" required>
                            </div>
                            <div class="mb-3">
                                <label>请求header配置
                                    <a class="btn btn-xs btn-outline-info add-copy-dom" href="#">+</a>
                                </label>
                                <div class="copy-dom">
                                    <input type="text" name="request-config[headers][key][]" class="form-control"
                                           placeholder="key" required>
                                    <input type="text" name="request-config[headers][value][]" class="form-control"
                                           placeholder="value" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>请求cookie配置
                                    <a class="btn btn-xs btn-outline-info add-copy-dom" href="#">+</a>
                                </label>
                                <div class="copy-dom">
                                    <input type="text" name="request-config[cookies][key][]" class="form-control"
                                           placeholder="key" required>
                                    <input type="text" name="request-config[cookies][value][]" class="form-control"
                                           placeholder="value" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h3>爬取URL限制</h3>
                            <div class="mb-3">
                                <label>不允许的文件后缀
                                    <a class="btn btn-xs btn-outline-info add-copy-dom" href="#">+</a>
                                </label>
                                <div class="copy-dom">
                                    <input type="text" name="restrictions-on-urls[disallowed-suffix][]"
                                           class="form-control"
                                           placeholder="不允许的文件后缀">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>不允许的URL关键字
                                    <a class="btn btn-xs btn-outline-info add-copy-dom" href="#">+</a>
                                </label>
                                <div class="copy-dom">
                                    <input type="text"
                                           name="restrictions-on-urls[disallowed-keywords-in-path-and-query][]"
                                           class="form-control" placeholder="不允许的URL关键字">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>不允许的域名
                                    <a class="btn btn-xs btn-outline-info add-copy-dom" href="#">+</a>
                                </label>
                                <div class="copy-dom">
                                    <input type="text" name="restrictions-on-urls[disallowed-domain][]"
                                           class="form-control"
                                           placeholder="不允许的域名">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>不允许的URL（正则）
                                    <a class="btn btn-xs btn-outline-info add-copy-dom" href="#">+</a>
                                </label>
                                <div class="copy-dom">
                                    <input type="text" name="restrictions-on-urls[disallowed-urls][]"
                                           class="form-control"
                                           placeholder="不允许的URL（正则）">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>允许的URL（正则）
                                    <a class="btn btn-xs btn-outline-info add-copy-dom" href="#">+</a>
                                </label>
                                <div class="copy-dom">
                                    <input type="text" name="restrictions-on-urls[allowed-urls][]" class="form-control"
                                           placeholder="允许的URL（正则）">
                                </div>
                            </div>
                        </div>
<!--                    </div>-->
<!--                <hr>-->
<!--                <div class="row">-->
                        <div class="col-md-4">
                            <h3>请求行为限制</h3>
                            <div class="mb-3">
                                <label>最大页面并发（不大于10）</label>
                                <input type="text" name="restrictions-on-requests[max-concurrent]" max="10"
                                       class="form-control" placeholder="最大页面并发（不大于10）" required>
                            </div>
                            <div class="mb-3">
                                <label>最大页面深度限制</label>
                                <input type="text" name="restrictions-on-requests[max-depth]" max="10"
                                       class="form-control"
                                       placeholder="最大页面深度限制" required>
                            </div>
                            <div class="mb-3">
                                <label>一个页面中最大点击深度限制</label>
                                <input type="text" name="restrictions-on-requests[max-click-depth]" class="form-control"
                                       placeholder="一个页面中最大点击深度限制" required>
                            </div>
                            <div class="mb-3">
                                <label>最多爬取的页面数量限制</label>
                                <input type="text" name="restrictions-on-requests[max-count-of-page]"
                                       class="form-control"
                                       placeholder="最多爬取的页面数量限制" required>
                            </div>
                            <div class="mb-3">
                                <label>单个页面中最大点击或事件触发次数(不大于10000)</label>
                                <input type="text" name="restrictions-on-requests[max-click-or-event-trigger]"
                                       max="10000"
                                       class="form-control" placeholder="单个页面中最大点击或事件触发次数(不大于10000)" required>
                            </div>
                            <div class="mb-3">
                                <label>点击间隔，单位毫秒</label>
                                <input type="text" name="restrictions-on-requests[click-or-event-interval]"
                                       class="form-control" placeholder="点击间隔，单位毫秒" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="submit" class="btn btn-sm btn-outline-secondary">提交</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(".add-copy-dom").click(function () {
        // this.html("11");
        // alert($(this).next().prop("outerHTML"))
        // alert($(this).parent().next().prop("outerHTML"))
        $(this).parent().next().parent().append($(this).parent().next().prop("outerHTML"))
    });
</script>