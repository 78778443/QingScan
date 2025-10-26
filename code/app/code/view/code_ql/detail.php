{include file='public/head' /}

<style>
    a {
        text-decoration: none;
    }

    li {
        list-style: none;
    }

    .highlight {
        background-color: yellow;
    }

    .code-container {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        max-height: 380px;
    }

    pre {
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .line-number {
        counter-increment: line;
    }

    .line-number::before {
        content: counter(line);
        display: inline-block;
        width: 2em;
        margin-right: 0.5em;
        text-align: right;
        color: #888;
    }

    .hrefBack{
        background-color: #eee;
    }

</style>
<!-- Prism.js CSS 和 JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.28.0/themes/prism.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.28.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.28.0/components/prism-python.min.js"></script>

<div class="row">
    <div class="col-1">
        {include file='Common/nav' /}
    </div>

    <div class="col-11 tuchu">
        <div class="row">
            <div class="col-12">
                <h5 class="mb-4" style="color: #aaa;">
                    <span>{$info['ruleId']}</span>
                    <span style="font-size: 13px; ">({$info['create_time']})</span>
                </h5>


            </div>
            <div class="col-2">
                <span style="color: #aaa;font-size: 14px;">代码位置</span>
                <ul class="list-group">
                    <?php foreach ($info['locations'] as $result) { ?>
                        <li class="list-group-item">
                            <a href="#" style="font-size: 12px;" class="click_a"
                               onclick="showFile('<?php echo $result['file']; ?>', <?php echo $result['start_line']; ?>, <?php echo $result['start_column']; ?>, <?php echo $result['end_column']; ?>); return false;">
                                <?php echo basename($result['file']); ?>: <?php echo $result['start_line']; ?>,
                                <?php echo $result['start_column']; ?>-<?php echo $result['end_column']; ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>

                <br>
                <span style="color: #aaa;font-size: 14px;">数据流转</span>
                <div class="accordion" id="accordionExample">
                    <?php foreach ($info['codeFlows'] as $k => $result) { ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button style="font-size: 13px;color: #999;padding: 8px 10px;" class="accordion-button collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne{$k}" aria-expanded="false"
                                        aria-controls="collapseOne{$k}"
                                >
                                    第 {$k+1} 条路径
                                </button>


                            </h2>
                            <div id="collapseOne{$k}" class="accordion-collapse collapse "
                                 data-bs-parent="#accordionExample">
                                <div>
                                    <ul class="list-group">
                                        <?php foreach ($result['threadFlows'] as $item) { ?>
                                            <?php foreach ($item['locations'] as $val) { ?>
                                                <?php foreach ($val['location'] as $v) { ?>
                                                    <li class="list-group-item">
                                                        <a href="#" style="font-size: 12px;" class="click_a"
                                                           onclick="showFile('<?php echo $v['file']; ?>', <?php echo $v['start_line']; ?>, <?php echo $v['start_column']; ?>, <?php echo $v['end_column']; ?>); return false;">
                                                            <?php echo basename($v['file']); ?>
                                                            : <?php echo $v['start_line']; ?>,
                                                            <?php echo $v['start_column']; ?>
                                                            -<?php echo $v['end_column']; ?>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?>
                                    </ul>

                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <br>

            </div>
            <div class="col-10">
                <span style="color: #aaa;font-size: 14px;">代码详情</span>
                <div id="code-container" class="code-container">
                    <pre id="code" class="line-number"></pre>
                </div>
                <br>

                <div class="row">
                    <div class="col-6">
                        <span style="color: #aaa;font-size: 14px;">漏洞说明</span>
                        <div class="code-container" style="font-size: 12px;color: #999;">
                            <?php echo $info['prompt'] ?>
                        </div>
                    </div>
                    <div class="col-6">
                        <span style="color: #aaa;font-size: 14px;">AI分析</span>
                        <div class="code-container" style="font-size: 12px;color: #999;">
                            <div id="markdown-content"></div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<script src="/static/js/marked.min.js"></script>
<script>

    $(".codeql_index").addClass("nav_li_hover");

    function b64DecodeUnicode(encodedData) {
        return decodeURIComponent(atob(encodedData).split('').map(function (c) {
            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
        }).join(''));
    }

    const markdownContent = b64DecodeUnicode('<?php echo base64_encode($info['ai_message']) ?>');
    document.getElementById("markdown-content").innerHTML = marked.parse(markdownContent);

    $(".click_a").click(function () {
        $('.hrefBack').removeClass('hrefBack');

        $(this).closest('li').addClass('hrefBack');
    })

    const showFile = (filePath, startLine, startColumn, endColumn) => {




        fetch(`/admin/Codeql/readFile.html?file=` + filePath, {headers: {'Content-Type': 'application/json'}})
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                const decodedContent = b64DecodeUnicode(data.content);
                const lines = decodedContent.split('\n');
                let codeHtml = '';
                lines.forEach((line, index) => {
                    const lineNumber = index + 1;
                    let lineHtml = Prism.highlight(line, Prism.languages.python, 'python');
                    if (lineNumber === startLine) {
                        lineHtml = line.slice(0, startColumn - 1) +
                            '<span class="highlight">' +
                            line.slice(startColumn - 1, endColumn) +
                            '</span>' +
                            line.slice(endColumn);
                    }
                    codeHtml += '<div class="line-number">' + lineHtml + '</div>\n';
                });
                const codeContainer = document.getElementById('code');
                codeContainer.innerHTML = '<pre><code class="language-python">' + codeHtml + '</code></pre>';

                // 滚动到高亮行
                const highlightedElement = document.querySelector('.highlight');
                if (highlightedElement) {
                    highlightedElement.scrollIntoView({behavior: 'smooth', block: 'center'});
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    };
</script>