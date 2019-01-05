<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/5
 * Time: 12:35
 */
?>
<div class="layui-form-item layui-form-text">
    <label class="layui-form-label">课程封面</label>
    <div class="layui-input-block">
        <div class="layui-upload-drag" id="test10">
            <i class="layui-icon"></i>
            <p>点击上传，或将文件拖拽到此处</p>
        </div>
    </div>
</div>

<script>
    layui.use('upload', function() {
        var $ = layui.jquery
            , upload = layui.upload;

        upload.render({
            elem: '#test10'
            , url: '/upload/'
            , done: function (res) {
                console.log(res)
            }
        });
    });
</script>