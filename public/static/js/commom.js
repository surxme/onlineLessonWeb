function verifyinput() {
    var ok = true;
    var tips = '';
    $('.require').each(function(){
        var $this = $(this);
        if($this.val() == '' && ok){
            ok = false;
            layer.msg( $this.data('name')+'不能为空');
        }
    });

    $('.req_checkbox').each(function(){
        var $this = $(this);
        if($this.find('input[type="checkbox"]:checked').length <=0 && ok){
            ok = false;
            Tip( $this.data('name')+'不能为空');
        }
    });

    return ok;
}

var file_upload_manager = {
    upload:'{:url("admin/upload")}',
    //普通上传，有显示预览功能
    normalUpload: function(up_url,attach_data,dom_id,preview_dom_id,hidden_input_id){
        layui.use('upload', function() {
            var $ = layui.jquery
                , upload = layui.upload;

            //普通图片上传
            var uploadInst = upload.render({
                elem: dom_id
                ,url: up_url
                ,before: function(obj){
                    //预读本地文件示例，不支持ie8
                    obj.preview(function(index, file, result){
                        $(preview_dom_id).attr('src', result); //图片链接（base64）
                    });
                }
                ,done: function(res){
                    //如果上传失败
                    if(res.is_ok != 'ok'){
                        return layer.msg('上传失败');
                    }else{
                        $(hidden_input_id).val(res.file_url);
                        layer.msg('上传成功');
                    }
                }
                ,error: function(){
                    //演示失败状态，并实现重传
                    var demoText = $('#demoText');
                    demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
                    demoText.find('.demo-reload').on('click', function(){
                        uploadInst.upload();
                    });
                }
            });
        });
    },
    croppersUpload: function(up_url,attach_data,dom_id,callback){
        layui.config({
            base: 'cropper/' //layui自定义layui组件目录
        }).use(['form','croppers'], function () {
            var $ = layui.jquery
                ,croppers = layui.croppers
                ,layer= layui.layer;

            //创建一个头像上传组件
            croppers.render({
                elem: dom_id
                ,saveW:150     //保存宽度
                ,saveH:150
                ,mark:1/1    //选取比例
                ,area:'600px'  //弹窗宽度
                ,url: up_url  //图片上传接口返回和（layui 的upload 模块）返回的JOSN一样
                ,done: function(res,index){
                    callback(res,index);
                }
            });
        });
    },
    //拖拽上传
    dragUpload: function(up_url,attach_data,dom_id){
        layui.use('upload', function() {
            var $ = layui.jquery
                , upload = layui.upload;

            //普通图片上传
            var uploadInst = upload.render({
                elem: '#test10'
                ,url: '{:url("admin/upload")}'
                ,done: function(res){
                    //如果上传失败
                    if(res.code > 0){
                        return layer.msg('上传失败');
                    }else{
                        console.log(res.file_url);
                        dom_id.find('#poster').val(res.file_url);
                        layer.msg('上传成功');
                    }
                }
            });
        });
    },
};

var ajax_request = {
    post: function(url,data,callback,errcallback){
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (res_data) {
                if(callback){
                    callback(res_data);
                }
            },
            error: function (){
                if(errcallback){
                    errcallback();
                }
            }
        });
    },
};

function refreshDom(url,data,refresh_dom_id,beforeRefresh,afterRefresh){
    layui.use(['element'], function(){
        var layer = layui.layer;

        ajax_request.post(url,data,function(res_data){
            if(beforeRefresh){
                beforeRefresh();
            }
            var new_dom = $(res_data).find(refresh_dom_id);
            if(new_dom.length <= 0){
                layer.msg('刷新错误');
                return;
            }

            $(refresh_dom_id).html(new_dom.html());
            if(afterRefresh){
                afterRefresh();
            }
        });
    });
};

var layeditManager = {
    builder: function (dom_id,url) {
        var  index ,layedit;
        layui.use('layedit', function(){
            layedit = layui.layedit;
            layedit.set({
                uploadImage: {
                    url: url //接口url
                }
            });
            index = layedit.build(dom_id,{height:200}); //建立编辑器
        });
        return {'index':index,'layedit':layedit};
    }
};

function rePositionLayuiIframe(title) {
    layui.use('layer', function () {
        var layer = layui.layer;

        layer.open({
            title: title
            , type:1
            , content: '处理中'
            ,time:100
        });
    });
}

var layui_iframe = {
    editBuilder:function (title,open_url,btn1_callback,area) {
        rePositionLayuiIframe(title);
        layui.use(['layer'], function () {
            var layer = layui.layer;

            layer.open({
                type: 2
                , title: title
                , area: area?area:['800px', '600px']
                , shade: 0.3//弹出后背景的阴影
                , offset: 'auto'
                , content: open_url
                , btn: ['提交','取消']
                , btnAlign:'c'
                , yes: function (index,layero) {
                    btn1_callback(index,layero);
                }
                , btn2:function (index, layero){
                    layer.close(index);//关闭当前
                }
                , zIndex: layer.zIndex //重点1
                , success: function (index,layero) {
                    // layer.setTop(layero); //重点2
                }
            });
        });
    },
    delConfirm : function (callback) {
        layui.use("layer", function () {
            var layer = layui.layer;  //layer初始化
            layer.confirm('确认删除',{btn:['确认','取消']},
                // function () {
                    callback()
                );
        });
    },
    alertConfirm : function (tips,callback) {
        layui.use("layer", function () {
        var layer = layui.layer;  //layer初始化
            layer.confirm(tips,{btn:['确认','取消']},
                function (index) {
                    callback(index)
                }
            );
        });
    },
    viewBuilder : function (title,url) {
        layui.use("layer", function () {
            var layer = layui.layer;  //layer初始化
            layer.open({
                type: 2,
                title: title,
                area: ['800px', '600px'],
                anim: 2,
                content: url, //iframe的url，no代表不显示滚动条
            });
        })
    }
};
var showphotos ={
    init : function (dom_id){
        layui.use(['layer'], function () {
            var layer = layui.layer;
            layer.photos({
                photos: dom_id
                , anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                , tab: function(pic, layero){
                }
            });
        })
    }
};

function deptTreeBuilder(container_dom_id,tree_data,click_callback,is_open_all) {
    layui.use(['form'], function () {
        var form = layui.form;

        var xtree = new layuiXtree({
            elem: container_dom_id   //(必填) 放置xtree的容器id，不要带#号
            , form: form     //(必填) layui 的 from
            , data: tree_data
            , isopen: false  //加载完毕后的展开状态，默认值：true
            , icon:{}
            , click: function (data) {  //节点选中状态改变事件监听，全选框有自己的监听事件
                if(click_callback) {
                    var checked_ids = xtree.GetChecked();
                    var ids = [];
                    $.each(checked_ids,function () {
                        ids.push($(this).val());
                    });
                    click_callback(ids.join(','));
                }
            }
        });
    });
}
//没有选择框的部门树
function noCheckboxTreeBuilder(dom_id,json_data,callback) {

    layui.use(['tree', 'layer'], function () {
        var layer = layui.layer
            , $ = layui.jquery;

        layui.tree({
            elem: dom_id //指定元素
            , click: function (item) {
                var sub = [];
                var childIds = this.getChildIds(item,sub);
                callback(item,childIds);
            }
            ,getChildIds:function (obj,sub) {

                sub.push(obj.id);
                if( obj.children.length > 0 ){
                    $.each( obj.children, function(k, v){
                        sub.push( v.id );
                        this.getChildIds( v );
                    });
                }
                return sub;
            }
            ,skin:'img'
            , nodes: json_data
        });
    });
}


var tree_without_checkbox = {
    noCheckboxTreeBuilder:function(dom_id,json_data,callback) {

        layui.use(['tree', 'layer'], function () {
            var layer = layui.layer
                , $ = layui.jquery;

            layui.tree({
                elem: dom_id //指定元素
                , click: function (item) {
                    var sub = [];
                    sub.push(item.id);
                    var childIds = tree_without_checkbox.getChildIds(item,sub);
                    callback(item,childIds);
                }
                ,skin:'img'
                , nodes: json_data
            });
        });
    },
    getChildIds:function (obj,sub) {
        // sub.push(obj.id);
        if( obj.children.length > 0 ){
            $.each( obj.children, function(k, v){
                sub.push( v.id );
                if(v.children.length > 0){
                    tree_without_checkbox.getChildIds(v,sub);
                }
            });
        }
        return sub;
    }
};
//表单序列化为对象
function serializeObject(dom) {
    var o = {};
    $.each(dom.serializeArray(), function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
}

//列表数据导出
var export_import_manager = {
    /**
     * 功能同下 由layui自带的导出，不支持字段自动排序
     * @param get_data_url
     * @param file_name
     * @param colums_title
     * @param sequence
     */
    ajaxDefaultExport:function (get_data_url,file_name) {
        layui.use([ 'table'], function() {
            var table=layui.table;
            $.ajax({
                url: get_data_url,
                dataType: 'json',
                success: function(data) {
                    table.exportFile(data.title, data.list, file_name+'.csv'); //默认导出 csv，也可以为：xls
                }
            });
        });
    },
    /**
     * 需要请求返回json参数 课自动排序
     * @param get_data_url 获取导出数据的url
     * @param file_name 导出文件名
     * @param colums_title 导出表格的表头信息 {name: '教师名',sex: '性别', dept_name: '部门' ,bir: '生日', email: '邮箱'}
     * @param sequence 导出表格的字段顺序 ['name','sex','dept_name','bir','email']
     */
    ajaxExport:function (get_data_url,file_name,colums_title,sequence) {
        layui.use(['jquery', 'excel', 'layer'], function() {
            var $ = layui.jquery;
            var excel = layui.excel;
            $.ajax({
                url: get_data_url,
                dataType: 'json',
                success: function(data) {
                    var data = data.list;
                    // 1. 数组头部新增表头
                    data.unshift(colums_title);
                    // 2. 如果需要调整顺序，请执行梳理函数
                    data = excel.filterExportData(data, sequence);
                    // 3. 执行导出函数，系统会弹出弹框
                    excel.exportExcel({
                        sheet1: data
                    }, file_name+'.xlsx', 'xlsx');
                }
            });
        });
    },
    /**
     * 直接传json参数
     * @param data
     * @param file_name
     * @param column_title
     */
    normalExport:function (data,file_name,column_title) {
        layui.use(['jquery', 'excel', 'layer'], function() {
            var $ = layui.jquery;
            var excel = layui.excel;

            data.unshift(column_title);
            excel.exportExcel({
                sheet1: data
            }, file_name+'.xlsx', 'xlsx');
        });
    }
};
