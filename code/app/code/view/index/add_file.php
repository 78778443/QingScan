{include file='public/head' /}
<div class="row" style="height: 50px"></div>
<div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6 tuchu">
                <h1>添加白盒项目</h1>
                <form method="post" action="<?php echo url('code/add_file')?>" enctype="multipart/form-data">
                    <!--<div class="mb-3">
                        <label class="form-label">项目名称(项目名称和压缩包目录名称必须一致)</label>
                        <input type="text" name="name" class="form-control" placeholder="请输入项目名称" required>
                    </div>-->
                    <div class="mb-3">
                        <label class="form-label">项目类型</label>
                        <select name="project_type" class="form-select form-select-sm" aria-label="Default select example" required>
                            <option value="1">PHP项目</option>
                            <option value="2">JAVA项目</option>
                            <option value="3">Python项目</option>
                            <option value="4">Golang项目</option>
                            <option value="5">APP项目</option>
                            <option value="6">其他</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">项目文件压缩包zip格式(小于100M)</label>
                        <input type="file" class="form-control form-control" name="file" accept=".zip" required/>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">需要调用的工具</label>
                        <div class="checkbox">
                            <?php
                                foreach ($tools_list as $k=>$v) {
                            ?>
                            <label>
                                <input type="checkbox" name="tools[]" value="<?php echo $k;?>"><?php echo $v;?>
                            </label>
                            <?php }?>
                        </div>
                    </div>
                    <div class="row" style="height: 10px"></div>
                    <a href="<?php echo url('code/index')?>" class="btn btn-sm btn-outline-secondary">返回</a>
                    <button type="submit" class="btn btn-sm btn-outline-secondary">提交</button>
                </form>
            </div>
            <div class="col-md-3"></div>
</div>
<div class="row" style="height: 50px"></div>
{include file='public/footer' /}