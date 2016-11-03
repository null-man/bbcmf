//----------------------------------
// 文档加载
//----------------------------------
// 绑定事件[文档加载]
$(document).ready(function() {
    // 渲染附件个数
    renderAnnexCount()
});

// 渲染附件个数
function renderAnnexCount() {
    var annex_count = $('#task_see_file_view>.sm-icon').length
    $('#annex_count').text(annex_count)
    $('#annex_count_title')[annex_count > 0 ? 'show' : 'hide']()
}





//----------------------------------
// 任务添加
//----------------------------------
var task_add_file = {
    // HTML页面元素
    fileInput:      $("#task_add_file").get(0),
    // HTML请求地址
    url:            $("#add_form").attr("action"),
    // HTML响应按钮
    upButton:       $("#add_task_upload_annex").get(0),
    // HTML表单ID
    formId:         "add_form",
    // 回调函数[过滤文件]
    filter:         function(files) {
        //////////////////////////////////////
        // 意图：验证文件大小是否超过范围
        //////////////////////////////////////
        var arrFiles = [];
        for (var i = 0, file; file = files[i]; i++) {
            if (file.size >= 50 * 1024 * 1024) {
                $.Prompt('文件"' + file.name + '"超过50M');
            } else {
                arrFiles.push(file);
            }
        }
        return arrFiles;
    },
    // 回调函数[选中文件]
    onSelect:       function(files) {
        //////////////////////////////////////
        // 意图：验证文件大小是否超过范围
        //////////////////////////////////////
        //       html: HTML代码
        //          i: 文件索引
        // file_names: 文件名称集合
        var html = '', i = 0, file_names = [];

        // 方法[添加图片(循环)]
        var funAppendImage = function() {
            // 获得文件(根据索引进行循环获得)
            var file = files[i];

            // 如果文件有效
            if (file) {
                //////////////////
                // 添加中..
                //////////////////
                // 创建文件读取对象
                var reader = new FileReader()
                // 绑定文件读取函数
                reader.onload = function(e) {
                    // 获得文件类型
                    file_type = suffix_name_type(file.name);

                    // 添加HTML
                    html += 
                        "<div class='sm-icon m-t-s15'>" +
                            "<a href='#;' class='pull-left'>";
                    // 根据文件类型进行添加
                    if (file_type != "img") {
                        // 非图片
                        html += "<div class='" + file_type + "'></div>";
                    } else {
                        // 是图片
                        html += "<img src='" + e.target.result + "'/>";
                    }

                    // 添加HTML
                    html += 
                            "</a>" +
                            "<div class='media-body p-l-s10'>" +
                                "<span class='blue m-r-s10'>" + file.name + "<span class='honeycomb-status-gray'>&nbsp;(";
                    // 判断文件大小
                    if ((file.size / 1024) < 1024) {
                        html += "" + parseInt(file.size / 1024) + "&nbsp;KB";
                    } else {
                        html += "" + parseInt(file.size / 1024 / 1024) + "&nbsp;MB";
                    }

                    // 添加HTML
                    html += 
                                ")</span></span>" +
                                "<div class='forum-sub-title p-s0'>" +
                                    "<a class='m-r-sm' onclick='remove_file(this)' data-index='"+ i +"'>删除</a>" +
                                "</div>" +
                            "</div>" +
                        "</div>";

                    // 添加到文件名称集合
                    file_names.push(file.name);
                    // 索引下移
                    i++;
                    // 继续添加文件
                    funAppendImage();
                }
                // 读取文件
                reader.readAsDataURL(file);
            } else {
                //////////////////
                // 已完成..
                //////////////////
                if (html) {
                    // 更新HTML
                    $("#task_file_view").html(html);
                    // 渲染附件个数
                    renderAnnexCount();
                }
            }
        };
        funAppendImage();
    }
};










//----------------------------------
// 任务查看
//----------------------------------
var task_see_file = {
    // HTML页面元素
    fileInput:      $("#task_see_file").get(0),
    // HTML请求地址
    url:            $("#see_form").attr("action"),
    // HTML响应按钮
    upButton:       $("#upload_annex").get(0),
    // HTML表单ID
    formId:         "see_form",
    // 回调函数[过滤文件]
    filter:         function(files) {
        //////////////////////////////////////
        // 意图：验证文件大小是否超过范围
        //////////////////////////////////////
        var arrFiles = [];
        for (var i = 0, file; file = files[i]; i++) {
            if (file.size >= 50 * 1024 * 1024) {
                $.Prompt('文件"' + file.name + '"超过50M');
            } else {
                arrFiles.push(file);
            }
        }
        return arrFiles;
    },
    // 回调函数[选中文件]
    onSelect:       function(files) {
        //////////////////////////////////////
        // 意图：验证文件大小是否超过范围
        //////////////////////////////////////
        //       html: HTML代码
        //          i: 文件索引
        // file_names: 文件名称集合
        var html = '', i = 0, file_names = [];

        // 方法[添加图片(循环)]
        var funAppendImage = function() {
            // 获得文件(根据索引进行循环获得)
            var file = files[i];

            // 如果文件有效
            if (file) {
                //////////////////
                // 添加中..
                //////////////////
                // 创建文件读取对象
                var reader = new FileReader();
                // 绑定文件读取函数
                reader.onload = function(e) {
                    // 获得文件类型
                    file_type = suffix_name_type(file.name);
                    // 添加HTML
                    html += 
                        "<div class='m-b-s15 sm-icon' >" +
                            "<a href='#;' class='pull-left'>";
                    // 根据文件类型进行添加
                    if (file_type != "img") {
                        // 非图片
                        html += "<div class='" + file_type + "'></div>";
                    } else {
                        // 是图片
                        html += "<img src='" + e.target.result + "'/>";
                    }

                    // 添加HTML
                    html += 
                            "</a>" +
                            "<div class='media-body p-l-s10'>" +
                                "<span class='blue m-r-s10'>" + file.name + "<span class='honeycomb-status-gray'>&nbsp;(";
                    // 判断文件大小
                    if ((file.size / 1024) < 1024) {
                        html += "" + parseInt(file.size / 1024) + "&nbsp;KB";
                    } else {
                        html += "" + parseInt(file.size / 1024 / 1024) + "&nbsp;MB";
                    }

                    // 添加HTML
                    html += 
                                ")</span></span>" +
                                "<div class='forum-sub-title p-s0'>" +
                                    "<a class='m-r-sm' onclick='remove_file(this)' data-index='"+ i +"'>删除</a>" +
                                "</div>" +
                            "</div>" +
                        "</div>";

                    // 添加到文件名称集合
                    file_names.push(file.name);
                    // 索引下移
                    i++;
                    // 继续添加文件
                    funAppendImage();
                }
                // 读取文件
                reader.readAsDataURL(file);
            } else {
                //////////////////
                // 已完成..
                //////////////////
                if (html) {
                    // 获得原添加时的附件内容
                    var html0 = ''
                    $("#task_see_file_view .annex0").each(function(){
                        html0 += $(this).prop("outerHTML")
                    })
                    // 更新HTML
                    $("#task_see_file_view").html(html0 + html);
                    // 渲染附件个数
                    renderAnnexCount();
                }
            }
        };
        funAppendImage();
    }
};










//----------------------------------
// 任务结单
//----------------------------------
// 完成任务文件上传预览
var task_check_file = {
    // HTML页面元素
    fileInput:      $("#task_check_file").get(0),
    // HTML请求地址
    url:            $("#check_form").attr("action"),
    // HTML响应按钮
    upButton:       $("#check_task_upload_annex").get(0),
    // HTML表单ID
    formId:         "check_form",
    // 回调函数[过滤文件]
    filter:         function(files) {
        //////////////////////////////////////
        // 意图：验证文件大小是否超过范围
        //////////////////////////////////////
        var arrFiles = [];
        for (var i = 0, file; file = files[i]; i++) {
            if (file.size >= 50 * 1024 * 1024) {
                $.Prompt('文件"'+ file.name +'"超过50M');
            } else {
                arrFiles.push(file);
            }
        }
        return arrFiles;
    },
    // 回调函数[选中文件]
    onSelect:       function(files) {
        //////////////////////////////////////
        // 意图：验证文件大小是否超过范围
        //////////////////////////////////////
        //       html: HTML代码
        //          i: 文件索引
        // file_names: 文件名称集合
        var html = '', i = 0, file_names = [];

        // 方法[添加图片(循环)]
        var funAppendImage = function() {
            // 获得文件(根据索引进行循环获得)
            var file = files[i];

            // 如果文件有效
            if (file) {
                //////////////////
                // 添加中..
                //////////////////
                // 创建文件读取对象
                var reader = new FileReader();
                // 绑定文件读取函数
                reader.onload = function(e) {
                    // 获得文件类型
                    file_type = suffix_name_type(file.name);

                    // 添加HTML
                    html += "<div class='m-t-s15 sm-icon'>" +
                        "<a href='#;' class='pull-left'>";
                    // 根据文件类型进行添加
                    if (file_type!="img") {
                        // 非图片
                        html += "<div class='" + file_type + "'></div>";
                    } else {
                        // 是图片
                        html += "<img src='" + e.target.result + "'/>";
                    }

                    // 添加HTML
                    html += "</a>" +
                        "<div class='media-body p-l-s10'>" +
                        "<span class='blue m-r-s10'>" + file.name + "<span class='honeycomb-status-gray'>&nbsp;(";
                    // 判断文件大小
                    if ((file.size / 1024) < 1024) {
                        html += "" + parseInt(file.size / 1024) + "&nbsp;KB";
                    }else{
                        html += "" + parseInt(file.size / 1024 / 1024) + "&nbsp;MB";
                    }

                    // 添加HTML
                    html += 
                                ")</span></span>" +
                                "<div class='forum-sub-title p-s0'>" +
                                    "<a class='m-r-sm' onclick='remove_file(this)' data-index='"+ i +"'>删除</a>" +
                                "</div>" +
                            "</div>" +
                        "</div>";

                    // 添加到文件名称集合
                    file_names.push(file.name);
                    // 索引下移
                    i++;
                    // 继续添加文件
                    funAppendImage();
                };
                // 读取文件
                reader.readAsDataURL(file);
            } else {
                //////////////////
                // 已完成..
                //////////////////
                if (html) {
                    // 获得原添加时的附件内容
                    var html0 = ''
                    $("#task_check_file_view .annex1").each(function(){
                        html0 += $(this).prop("outerHTML")
                    })
                    // 更新HTML
                    $("#task_check_file_view").html(html0 + html);
                }
            }
        };
        funAppendImage();
    },
};


//定时任务附件
var timer_task_file = {
    fileInput: $("#timer_task_file").get(0),
    url:$("#timer_task").attr("action"),
    upButton: $("#timer_task_upload_annex").get(0),
    formId:$("#timer_task"),
    filter: function(files) {
        var arrFiles = [];
        for (var i = 0, file; file = files[i]; i++) {
            if (file.size >= 52428800) {
                $.Prompt('文件"'+ file.name +'"超过50M');
            }else{
                arrFiles.push(file);
            }
        }
        return arrFiles;
    },
    onSelect: function(files) {
        var html = '', i = 0, file_names = [];
        var funAppendImage = function() {
            var file = files[i];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    file_type=suffix_name_type(file.name);
                    html += "<div class='m-t-s15 sm-icon'>" +
                        "<a href='#;' class='pull-left'>";
                    if (file_type!="img") {
                        html += "<div class='" + file_type + "'></div>";
                    } else {
                        html += "<img  src='" + e.target.result + "'/>";
                    }
                    html += "</a>" +
                        "<div class='media-body p-l-s10'>" +
                        "<span class='blue m-r-s10'>" + file.name + "<span class='honeycomb-status-gray'>&nbsp;(";
                    if((file.size/1024)<1024){
                        html +=""+parseInt(file.size/1024)+"&nbsp;KB";
                    }else{
                        html +=""+parseInt(file.size/1048576)+"&nbsp;MB";
                    }
                    html += ")</span></span>" +
                        "<div class='forum-sub-title p-s0'>" +
                        "<a class='m-r-sm' onclick='remove_file(this)' data-index='"+ i +"'>删除</a>" +
                        "</div>" +
                        "</div>" +
                        "</div>";
                    file_names.push(file.name);
                    i++;
                    funAppendImage();
                };
                reader.readAsDataURL(file);
            } else {
                if(html){
                    $("#timer_file_view").html(html);
                }
            }
        };
        funAppendImage();
    }
};


//任务修改文件上传预览
var up_timer_task_file = {
    fileInput: $("#up_timer_task_file").get(0),
    url:$("#up_timer_task").attr("action"),
    upButton: $("#up_timer_task_upload_annex").get(0),
    formId:$("#up_timer_task"),
    filter: function(files) {
        var arrFiles = [];
        for (var i = 0, file; file = files[i]; i++) {
            if (file.size >= 52428800) {
                $.Prompt('文件"'+ file.name +'"超过50M');
            }else{
                arrFiles.push(file);
            }
        }
        return arrFiles;
    },
    onSelect: function(files) {
        var html = '', i = 0, file_names = [];
        var funAppendImage = function() {
            var file = files[i];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    file_type=suffix_name_type(file.name);
                    html += "<div class='m-b-s10 sm-icon' >" +
                        "<a href='#;' class='pull-left'>";
                    if (file_type!="img") {
                        html += "<div class='" + file_type + "'></div>";
                    } else {
                        html += "<img  src='" + e.target.result + "'/>";
                    }
                    html += "</a>" +
                        "<div class='media-body p-l-s10'>" +
                        "<span class='blue m-r-s10'>" + file.name + "<span class='honeycomb-status-gray'>&nbsp;(";
                    if((file.size/1024)<1024){
                        html +=""+parseInt(file.size/1024)+"&nbsp;KB";
                    }else{
                        html +=""+parseInt(file.size/1048576)+"&nbsp;MB";
                    }
                    html += ")</span></span>" +
                        "<div class='forum-sub-title p-s0'>" +
                        "<a class='m-r-sm' onclick='remove_file(this)' data-index='"+ i +"'>删除</a>" +
                        "</div>" +
                        "</div>" +
                        "</div>";
                    file_names.push(file.name);
                    i++;
                    funAppendImage();
                }
                reader.readAsDataURL(file);
            } else {
                if(html){
                    $("#timer_upd_file_view").html(html);
                }
            }
        };
        funAppendImage();
    }
};

// 移除文件[现添加]
function remove_file(element) {
    // 删除附件
    fileupload.funDeleteFile(parseInt($(element).attr("data-index")));
    // 移除元素
    $(element).parent().parent().parent().remove();
    // 渲染附件个数
    renderAnnexCount()
}

// 移除文件[原添加]
function remove_file_db(element, field_name) {
    // 元素
    var $element    = $(element)
    // 元素[父]
    var $parent     = $element.parents('.' + field_name + ':first')
    // 表单
    var $form       = $element.parents('form:first')
    // ID
    var id          = $element.attr('data-id')

    // 添加隐藏字段[删除元素ID]
    $form.append('<input type="hidden" name="' + field_name + '_delete_ids[]" value="' + id + '"/>')
    // 移除元素
    $parent.remove();
    // 渲染附件个数
    renderAnnexCount()
}

// 清空附件
function clear_annex(element) {
    $('#' + element).empty();
    $('#' + element).parent("div").find(".upload-input").val("");
}

