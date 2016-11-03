var TimeFn = null;

// 绑定事件[文档加载]
$(document).ready(function () {
    // 绑定事件[文件拖放]
    Dropzone.options.myAwesomeDropzone = {
        // 自动进行队列处理
        autoProcessQueue:       false,
        // 是否允许一次提交多个文件
        uploadMultiple:         true,
        // 平行上传数量
        parallelUploads:        100,
        // 最多文件数量
        maxFiles:               100,
        // 最大文件大小(单位:MB)
        maxFilesize:            50,
        // 文件大小溢出文本
        dictFileTooBig:         "文件超过50M",
        // 开启文件移除链接
        addRemoveLinks:         true,
        // 文件移除链接文本
        dictRemoveFile:         "移除",

        // Dropzone settings
        init: function () {
            var myDropzone = this;
            this.element.querySelector("button[type='submit']").addEventListener("click", function(e) {
                e.preventDefault();
                var symbol = checkFolder();
                if (symbol === false) {
                    return;
                }
                e.stopPropagation();
                myDropzone.processQueue();
            });

            // 绑定事件[发送]
            this.on("sendingmultiple", function() {});
            // 绑定事件[成功]
            this.on("successmultiple", function(files, response){
                $(".dz-success-mark span").show();
                location.reload(true);
            });
            // 绑定事件[错误]
            this.on("errormultiple", function(files, response) {
                $(".dz-error-mark span").show();
            });

            // 绑定事件[成功]
            /*
            this.on("success", function(file) {
                console.log("File " + file.name + "uploaded");
            });
            */
            // 绑定事件[移除]
            /*
            this.on("removedfile", function(file) {
                console.log("File " + file.name + "removed");
            });
            */
        }
    };

    // 绑定事件[全选]
    $('#check_all').click(function() {
        // 选中状态
        var checked  = $('#check_all').is(":checked");
        // 员工ID
        var staff_id = $("#staff_id").val();
       
        // 选中/反选自己创建的文件
        $("input[name='file_ids[]']").each(function() {
            // 创建人ID
            var creator_id = $(this).attr("rel")
            // 判断文件是否为当前用户创建
            if (creator_id == staff_id) {
                $(this).prop("checked", checked);
            }
        });
    });

    // 绑定事件[复选框改变]
    $("input[name='file_ids[]']").change(function() {
        var total_count     = $("input[name='file_ids[]']").length
        var checked_count   = $("input[name='file_ids[]']:checked").length

        if (total_count != checked_count) {
            $('#check_all').prop("checked", false);
        } else if (total_count == checked_count) {
            $('#check_all').prop("checked", true);
        }
    });

    // 绑定事件[删除所有]
    $('.delFileAll').click(function(){
        layer.config({
            extend: 'extend/layer.ext.js'
        });
        layer.ready(function(){
            layer.confirm(
                '确定删除选中文件？', 
                { 
                    title: '删除文件', 
                }, 
                function(index) {
                    if (!del_files()) { 
                        layer.msg('请选中要删除项！')
                    }
                    layer.close(index);
                }
            );
        });
    });

    // 绑定事件[删除]
    $('.delFile').click(function() {
        var obj = this;
        layer.config({
            extend: 'extend/layer.ext.js'
        });
        layer.ready(function() {
            layer.confirm(
                '确定删除文件？', 
                {
                    title:'删除文件',
                }, 
                function(index) {
                    del_file(obj)
                    layer.close(index);
                }
            );
        });
    });

    // 绑定事件[文档-鼠标进入+鼠标离开]
    $('.document').mouseenter(function(){
        $(this).children(".document-tail").show()
    }).mouseleave(function(){
        $(this).children(".document-tail").hide()
    });

    // 绑定事件[上传]
    $('.upload-Locate').click(function(){
        // 滚动可见区域
        if ($(".document").length > 0) {
            $("html,body").animate({scrollTop:$(".document").last().offset().top-100},500);
        }
        // 模拟点击上传文件
        $("#my-awesome-dropzone").click();
    });
});


// 双击变成输入框输入更改
function showElement(element) {
    // 目录创建人
    var creator     = $(element).attr("creator");
    // 当前操作人
    var staff_id    = $("#staff_id").val();

    // 判断当前操作人是否拥有修改权限
    if (creator != staff_id) {
        $.Prompt("你没有修改该目录的权限", "fail");
        return
    }


    // 重置调度器
    clearTimeout(TimeFn);

    // 记录文件数
    var file_count = $.trim($(element).find("t").text())
    // 去除元素[数量]
    $(element).find("t").remove()


    // 原来元素HTML
    var oldHTML = element.innerHTML;
    // 原来元素TEXT
    var oldTEXT = element.innerText;


    // 清空元素内容
    element.innerHTML = '';



    // 1.创建新元素[内容文本框]
    // 意在将文本转换为输入框
    var newObj = document.createElement("input");
    newObj.type = 'text';
    newObj.className = 'form-control-edit';
    newObj.setAttribute("value", oldTEXT);
    element.appendChild(newObj);



    // 2.创建新元素[操作组]
    var opFolder = document.createElement("span")
    opFolder.className='op-folder';
    element.appendChild(opFolder)

    // 创建新元素[操作-保存]
    var save = document.createElement("em")
    save.className = 'folder-save';
    opFolder.appendChild(save)
    // 创建新元素[操作-取消]
    var remove = document.createElement("em")
    remove.className='folder-remove';
    opFolder.appendChild(remove)


    // 3.设置焦点[内容文本框]
    newObj.focus();
    // 绑定事件[文档]
    $(document).click(function(event) {
        // 如果对象为[操作-保存]
        if ($(event.target).is("em.folder-save") == true) {
            //--------------
            // ###保存
            //--------------
            // 验证内容长度
            if (newObj.value.length < 2 || newObj.value.length > 10) {
                $.Prompt("目录名2-10个字之间哦~");
                $(newObj).focus();
                return false;
            }

            // 0.重置显示HTML
            element.innerHTML = oldHTML;
            // 1.更新显示HTML中的内容
            if (newObj.value) {
                // 更新结果内容
                $(element).find('span').text(newObj.value + file_count);

                // 项目ID
                var project_id  = $("input[name='project_id']").val();
                // 目录ID
                var dir_id      = $(element).attr('dir');
                // 提交数据
                var data        = {
                    "id":           dir_id, 
                    "name":         newObj.value, 
                    "project_id":   project_id,
                    "act":          "update",
                    "_xsrf":        getCookie("_xsrf")
                };

                // ###提交请求
                $.ajax({
                    // 请求类型
                    type:       "POST", 
                    // 请求URL
                    url:        Think.APP + "/Home/Project/sou_dir.html", 
                    // 提交数据
                    data:       data,
                    // 响应数据类型
                    dataType:   "json",
                    // 回调函数[错误]
                    error:      function(result) {
                        $.Prompt("有异常哟！赶紧联系程序猿~", "fail");
                    },
                    // 回调函数[成功]
                    success:    function(result) {
                        if (result.status == '0') {
                            element.innerHTML = oldHTML
                            $.Prompt(result.message, "fail");
                        } else {
                            $(element).parent('li').remove()
                            location.reload(true);
                        }
                    }
                });
            } else {
                $(element).find('span').text(oldTEXT);
                location.reload(true);
            }

            return false;
        } else if ($(event.target).is("em.folder-remove") == true) {
            //--------------
            // ###删除
            //--------------
            layer.ready(function() {
                layer.confirm('确定删除目录？', {
                    title: '删除目录',
                }, function (index) {
                    // 项目ID
                    var project_id  = $("input[name='project_id']").val();
                    // 目录ID
                    var dir_id      = $(element).attr('dir');
                    // 提交数据
                    var data        = {
                        "id":           dir_id, 
                        "project_id":   project_id,
                        "act":          "delete",
                        "_xsrf":        getCookie("_xsrf")
                    };

                    // ###提交请求
                    $.ajax({
                        // 请求类型
                        type:       "POST", 
                        // 请求URL
                        url:        Think.APP + "/Home/Project/delete_dir.html", 
                        // 提交数据
                        data:       data,
                        // 响应数据类型
                        dataType:   "json",
                        // 回调函数[错误]
                        error:      function(result) {
                            $.Prompt("有异常哟！赶紧联系程序猿~", "fail");
                        },
                        // 回调函数[成功]
                        success: function(result) {
                            if (result.status == '0') {
                                element.innerHTML = oldHTML
                                $.Prompt(result.message, "fail");
                            } else {
                                $(element).remove()
                            }
                        }
                    });
                    layer.close(index);
                });
            });
        } else {
            //--------------
            // ###取消编辑
            //--------------
            if ($(event.target).is("input.form-control-edit") == false) {
                element.innerHTML = oldHTML;
                if ($(event.target).is("a.layui-layer-btn0") == true) {
                    setTimeout(function(){
                        location.reload(true);
                    }, 1000)
                } else {
                    location.reload(true);
                }
            }
        }
    })
};

// 查看目录
function view_dir(project_id, dir_id) {
    clearTimeout(TimeFn);
    TimeFn = setTimeout(function(){
        window.location.href = Think.APP + "/Home/Project/docs/id/" + project_id + "/dir_id/" + dir_id + ".html";
    }, 300);
};

// 添加目录
function add_dir(element) {
    // ###操作数据
    // 项目ID
    var project_id = $("input[name='project_id']").val();
    // 模板HTML    
    var html = 
        '<li>' +
            '<input type="text" class="form-control-edit" value="新建目录1"/>' +
            '<span class="op-folder">' +
                '<em class="folder-save"></em>' +
                '<em class="folder-remove"></em>' +
            '</span>' +
        '</li>';

    // 添加新元素
    $(element).parent('li').before(html);
    // 隐藏“添加目录”元素
    $(element).parent('li').hide()


    // ###绑定事件
    // 绑定事件[保存]
    $("em.folder-save").bind("click", function() {
        // 保存元素
        var that = this
        // 目录名称
        var dir_name = $(that).parent().parent().find("input").val()

        // 验证目录名称
        if ($.trim(dir_name).length < 2 || $.trim(dir_name).length > 10) {
            $.Prompt("目录名2-10个字之间哦~");
            return false;
        }


        // ###提交请求
        // 提交数据
        var data = {
            "project_id":   project_id,
            "act":          "save",
            "name":         dir_name,
            "_xsrf":        getCookie("_xsrf")
        };
        // 提交请求
        $.ajax({
            // 请求类型
            type:       "POST", 
            // 请求URL
            url:        Think.APP + "/Home/Project/sou_dir.html", 
            // 提交数据
            data:       data,
            // 响应数据类型
            dataType:   "json",
            // 回调函数[错误]
            error:      function(result) {
                $.Prompt("有异常哟！赶紧联系程序猿~", "fail");
            },
            // 回调函数[成功]
            success: function (result) {
                if (result.status == '0') {
                    $(that).parent().parent().remove()
                    $(element).parent('li').show()
                    $.Prompt(result.message, "fail");
                } else {
                    var html = 
                        '<li ondblclick="showElement(this)" dir="' + result.id + '">' +
                            '<a href="javascript:void(0)" onclick="view_dir(' + result.id + ')" class="folder">' +
                                '<span class="nav-label">' + result.name + '(0)</span>' +
                            '</a>' +
                        '</li>';
                    $(that).parent().parent().remove()
                    $(element).parent('li').before(html);
                    // $("li[dir='" + result.id + "']").dblclick();
                    location.reload(true);
                }
            }
        })

        return false;
    })

    // 绑定事件[删除]
    $("em.folder-remove").bind("click", function() {
        $(this).parent().parent().remove()
        $(element).parent('li').show()
    })
}









//----------------------------------
// 数据验证
//----------------------------------
// 判断是否存在目录
function checkFolder() {
    var folder_id = $("input[name='folder_id']").val();
    if(folder_id == '') {
        $.Prompt("请先创建目录！");
        return false;
    }
}









//----------------------------------
// 文件
//----------------------------------
// 删除文件[批量]
function del_files() {
    // ID集合
    var ids = [];
    // 提取要删除的ID集合
    $("input[name='file_ids[]']:checked").each(function(){
        ids.push($(this).val());
    });

    // ###提交请求
    if (true) {
        // 项目ID
        var project_id  = $("input[name='project_id']").val();
        // 提交数据
        var data        = {
            "ids":          ids.join(','), 
            "project_id":   project_id,
            "act":          "delete_batch",
            "_xsrf":        getCookie("_xsrf")
        };

        // ###提交请求
        $.ajax({
            // 请求类型
            type:       "POST", 
            // 请求URL
            url:        Think.APP + "/Home/Project/delete_files.html", 
            // 提交数据
            data:       data,
            // 响应数据类型
            dataType:   "json",
            // 回调函数[错误]
            error:      function(result) {
                $.Prompt("有异常哟！赶紧联系程序猿~", "fail");
            },
            // 回调函数[成功]
            success:    function(result) {
                if (result.status == '0') {
                    $.Prompt(result.message, "fail");
                } else {
                    $.Prompt(result.message, "succ");
                    setTimeout(function(){ location.reload(true) }, 500)
                }
            }
        })
    }

    return ids.length > 0;
}

// 删除文件[单个]
function del_file(element) {
    // 项目ID
    var project_id  = $("input[name='project_id']").val();
    // 目录ID
    var file_id     = $(element).attr('file_id') || $(element).val();
    // 提交数据
    var data        = {
        "id":           file_id, 
        "project_id":   project_id,
        "act":          "delete",
        "_xsrf":        getCookie("_xsrf")
    };

    // ###提交请求
    $.ajax({
        // 请求类型
        type:       "POST", 
        // 请求URL
        url:        Think.APP + "/Home/Project/delete_file.html", 
        // 提交数据
        data:       data,
        // 响应数据类型
        dataType:   "json",
        // 回调函数[错误]
        error:      function(result) {
            $.Prompt("有异常哟！赶紧联系程序猿~", "fail");
        },
        // 回调函数[成功]
        success:    function(result) {
            if (result.status == '0') {
                $.Prompt(result.message, "fail");
            } else {
                $.Prompt(result.message, "succ");
                setTimeout(function(){ location.reload(true) }, 500)
            }
        }
    })
}
