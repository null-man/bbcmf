//----------------------------------
// 任务清单条目
//----------------------------------
$(document).ready(function() {
    $('.task-code').mouseenter(function() {
        $(this).parent().children(".task-code").each(function(i) {
            $(this).children(".task-add-ons").removeClass("on")
        });
        $(this).children(".task-add-ons").addClass("on")
    });

    // $.get(Think.APP + '/Home/Notice/exists', function(result) {
    //     if (result.status == '1' && result.exists) {
    //         $('.top-bar em.notice-tg').show();
    //     }
    // }, 'JSON')
    //document.onreadystatechange = subSomething;//当页面加载状态改变的时候执行这个方法.
});








//----------------------------------
// 文件上传
//----------------------------------
var fileupload = {
    fileInput:      null,                   // HTML file控件
    dragDrop:       null,					// 拖拽敏感区域
    upButton:       null,					// 提交按钮
    url:            "",						// ajax地址
    fileFilter:     [],					    // 过滤后的文件数组
    filter:         function(files) {		// 选择文件组的过滤方法
        return files;
    },
    onSelect:       function() {},		    // 文件选择后
    onDelete:       function() {},		    // 文件删除后
    onDragOver:     function() {},		    // 文件拖拽到敏感区域时
    onDragLeave:    function() {},          // 文件离开到敏感区域时
    onProgress:     function() {},		    // 文件上传进度
    onSuccess:      function() {},		    // 文件上传成功时
    onFailure:      function() {},		    // 文件上传失败时
    onComplete:     function() {},		    // 文件全部上传完毕时



    /* 开发参数和内置方法分界线 */
    // 文件拖放
    funDragHover: function(e) {
        e.stopPropagation();
        e.preventDefault();
        this[e.type === "dragover"? "onDragOver": "onDragLeave"].call(e.target);
        return this;
    },
    // 获取选择文件，file控件或拖放
    funGetFiles: function(e) {
        // 取消鼠标经过样式
        this.funDragHover(e);

        // 获取文件列表对象
        var files = e.target.files || e.dataTransfer.files;
        // 继续添加文件
        this.fileFilter = this.fileFilter.concat(this.filter(files));
        //this.fileFilter = this.filter(files);
        this.funDealFiles();
        return this;
    },
    // 选中文件的处理与回调
    funDealFiles: function() {
        for (var i = 0, file; file = this.fileFilter[i]; i++) {
            // 增加唯一索引值
            file.index = i;
        }
        // 执行选择回调
        this.onSelect(this.fileFilter);
        return this;
    },
    // 删除对应的文件
    funDeleteFile: function(fileDelete_id) {
        var arrFile = [];
        for (var i = 0, file; file = this.fileFilter[i]; i++) {
            if (i != fileDelete_id) {
                arrFile.push(file);
            } else {
                this.onDelete();
            }
        }
        this.fileFilter = arrFile;
        return this;
    },
    // 文件上传
    funUploadFile: function() {
        // 存储备份
        var self = this;
        // 日志输出
        console.log(self)

        // 盗链网站判断
        if (location.host.indexOf("sitepointstatic") >= 0) {
            // 非站点服务器上运行
            return;
        }

        // 构建表单数据对象(用于手工提交file类型数据)
        var file_data = new FormData();
        // 添加文件
        for (var i = 0, file; file = this.fileFilter[i]; i++) {
            file_data.append("uploadedfile[]", file);
        }
        // 添加基础表单字段
        var formStr = $('#' + self.formId).serializeArray();
        $.each(formStr, function(i, field) {
            file_data.append(field.name, field.value);
        });


        // 字符编码处理
        // formStr = decodeURIComponent(formStr.replace(/\+/g, '%20'),true);
        // var formArr=formStr.split("&");
        // for(var i in formArr) {
        //     var k_v=formArr[i].split("=")
        //     file_data.append(k_v[0],k_v[1]);
        // }


        // 提交请求
        $.ajax({
            // 请求类型
            type:           "POST",
            // 请求URL
            url:            self.url,
            // 提交数据
            data:           file_data,
            // 处理数据(必须false才会避开jQuery对formdata的默认处理)
            processData:    false,
            // 文档类型(必须false才会自动加上正确的Content-Type)
            contentType:    false,
            // 响应数据类型
            dataType:       "json",
            // 回调函数[提交前]
            beforeSend:     function() {
                // 显示Loading
                showLoading("提交中...")
            },
            // 回调函数[错误]
            error:          function(result) {
                $.Prompt("有异常哟！赶紧联系程序猿~", "fail");
            },
            // 回调函数[成功]
            success:        function(result) {
                if (result.status == '0') {
                    $.Prompt(result.message);
                } else {
                    // 调试输出
                    /*
                    for (key in result) {
                        alert(key + ':' + result[key])
                    }
                    */
                    if (typeof(result.redirect) !== 'undefined' && result.redirect !== '') {
                        window.location.href = result.redirect;
                    } else {
                        location.reload();
                    }
                }
            }
        })
    },

    init: function() {
        var self = this;
        /*if (this.dragDrop) {
         this.dragDrop.addEventListener("dragover", function(e) { self.funDragHover(e); }, false);
         this.dragDrop.addEventListener("dragleave", function(e) { self.funDragHover(e); }, false);
         this.dragDrop.addEventListener("drop", function(e) { self.funGetFiles(e); }, false);
         }*/

        // 文件选择控件选择
        if (this.fileInput) {
            this.fileInput.addEventListener("change", function(e) { self.funGetFiles(e); }, false);
        }
        // 上传按钮提交
        if (this.upButton) {
            this.upButton.addEventListener("click", function(e) { self.funUploadFile(e); }, false);
        }
    }
};








//----------------------------------
// 输入框事件监听
//----------------------------------
// 输入框监听事件
var warning = function(elem) {
    // 根据文本内容是否为空, 设置或取消警告样式
    $.trim($(elem).val()) == "" && $(elem).addClass("warning-form") || $(elem).removeClass("warning-form");
};
// 绑定文本框事件[blur]
$("input[name='name']").blur(function() {
    warning(this);
});
// 绑定文本域事件[blur]
$("textarea[name='info']").blur(function() {
    warning(this);
});








//----------------------------------
// 完成时间回调函数
//----------------------------------
// 回调函数[完成时间]
function fnPickedAdd(obj) {
    var result = $dp.cal.getP('y') + "-" + $dp.cal.getP('M') + "-" + $dp.cal.getP('d');
    $("#add_ok_date").val(result)
}

// 回调函数[完成时间]
function fnPickedSee() {
    var result = $dp.cal.getP('y') + "-" + $dp.cal.getP('M') + "-" + $dp.cal.getP('d');
    $("#see_ok_date").val(result)
    $("#see_ok_date_text").removeClass("honeycomb-status-red")
}

// 回调函数[延迟时间]
function fnPickedDelay(obj) {
    var result = $dp.cal.getP('y') + "-" + $dp.cal.getP('M') + "-" + $dp.cal.getP('d');
    $("#delay_day").val(result)
}








//----------------------------------
// 动态获取任务
//----------------------------------
function get_tasks(obj) {
    if (obj == undefined) {
        // 起始时间
        if ($.trim($('#begin').val()) == '') {
            $.Prompt("请选择开始时间", "warn");
            $('#begin').focus();
            return;
        }
        // 结束时间
        if ($.trim($('#end').val()) == '') {
            $.Prompt("请选择结束时间", "warn");
            $('#end').focus();
            return;
        }
    }
    $('.form-inline').submit();
}








//----------------------------------
// _xsrf验证
//----------------------------------
function getCookie(name) {
    var r = document.cookie.match("\\b" + name + "=([^;]*)\\b");
    return r ? r[1] : undefined;
}








//----------------------------------
// 模态窗口动态加载
//----------------------------------
// 重置模态内容
function modal_init() {
    $('#password').empty();
    $('#add-task').empty();
    $('#add-cp-task').empty();
    $('#stop-task').empty()
    $('#cancel-stop-task').empty()
    $('#refuse-task').empty()
    $('#time-record').empty()
    $('#delay-apply-task').empty()
    $('#delay-update-task').empty()
    $('#refuse-delay-task').empty()
    $('#refer-apply-task').empty()
    $('#refer-update-task').empty()
    $('#assign-task').empty()
    $('#check-task').empty()
    $('#check-update-task').empty()
    $('#pige-task').empty()
    $('#redo-task').empty()
    $('#timer-task-info').empty()
    $('#timer-task-edit').empty()
}

// 修改密码
function password() {
    modal_init()
    $('#password').load(Think.APP + "/Home/Login/password.html");
    $('#password').modal({backdrop: 'static', keyboard: false});
}

// 任务下单
function add_task(project_id) {
    var args
    if (typeof project_id !== 'undefined' && project_id > 0) {
        args = "/project_id/" + project_id
    } else {
        args = ""
    }

    modal_init()
    $('#add-task').load(Think.APP + "/Home/Task/add" + args + ".html");
    $('#add-task').modal({backdrop: 'static', keyboard: false});
}
// 任务复制下单
function add_cp_task(id) {
    modal_init()
    $('#add-cp-task').load(Think.APP + "/Home/Task/copy_add/id/" + id + ".html");
    $('#add-cp-task').modal({backdrop: 'static', keyboard: false});
}

// 任务终止
function stop_task(id) {
    modal_init()
    $('#stop-task').load(Think.APP + "/Home/Task/stop/id/" + id + ".html");
}
// 任务取消终止
function cancel_stop_task(id) {
    modal_init()
    $('#cancel-stop-task').load(Think.APP + "/Home/Task/cancel_stop/id/" + id + ".html");
}

// 接受任务
function accept_task(id) {
    showLoading("接受中");
    location.href = Think.APP + "/Home/Task/do_accept/id/" + id + ".html";
}
// 拒绝任务
function refuse_task(id) {
    modal_init()
    $('#refuse-task').load(Think.APP + "/Home/Task/refuse/id/" + id + ".html");
}

// 记录工时
function time_record(id) {
    modal_init()
    $('#time-record').load(Think.APP + "/Home/Tasktimerecord/edit/task_id/" + id + ".html");
    $('#time-record').modal({backdrop: 'static', keyboard: false});
    $("#time-record").off("hidden.bs.modal").on("hidden.bs.modal", function() {
        if (window.parent) {
            window.parent.location.reload()
        }
    })
}
// 查看工时
function time_sheet(id) {
    modal_init()
    $('#time-record').load(Think.APP + "/Home/Tasktimerecord/see/task_id/" + id + ".html");
    $('#time-record').modal({backdrop: 'static', keyboard: false});
}

// 延单申请
function delay_apply_task(id) {
    modal_init()
    $('#delay-apply-task').load(Think.APP + "/Home/Task/delay_apply/id/" + id + ".html");
}
// 延单修改
function delay_update_task(delay_id) {
    modal_init()
    $('#delay-update-task').load(Think.APP + "/Home/Task/delay_update/delay_id/" + delay_id + ".html");
}
// 延单拒绝
function refuse_delay_task(delay_id) {
    modal_init()
    $('#refuse-delay-task').load(Think.APP + "/Home/Task/delay_refuse/delay_id/" + delay_id + ".html", function(result) {
        result = eval('(' + result + ')')
        if (result.status == '0') {
            $.Prompt(result.message, "warn");
            $('#refuse-delay-task').modal('hide')
        }
    });
}
// 延单取消
function delay_cancel_task(delay_id) {
    // ###同步请求
    // location.href = Think.APP + "/Home/Task/do_delay_cancel/delay_id/" + delay_id + ".html";

    // ###异步请求
    $.ajax({
        type:   "GET",
        url:    Think.APP + "/Home/Task/do_delay_cancel/delay_id/" + delay_id + "/ajax/1.html",
        dataType: "json",
        beforeSend: function() {
            showLoading("提交中")
        },
        error: function(result) {
            $.Prompt("有异常哟！赶紧联系程序猿~", "fail");
        },
        success: function(result) {
            if (result.status == '0') {
                $.Prompt(result.message);
            } else {
                $.Prompt(result.message);
                location.reload();
            }
        }
    });
}

// 转交申请
function refer_apply_task(id) {
    modal_init()
    $('#refer-apply-task').load(Think.APP + "/Home/Task/refer_apply/id/" + id + ".html");
}
// 转交更新
function refer_update_task(refer_id) {
    modal_init()
    $('#refer-update-task').load(Think.APP + "/Home/Task/refer_update/refer_id/" + refer_id + ".html");
}
// 延单拒绝
function refer_refuse_task(refer_id) {
    $.ajax({
        type:   "GET",
        url:    Think.APP + "/Home/Task/do_refer_refuse/refer_id/" + refer_id + "/ajax/1.html",
        dataType: "json",
        beforeSend: function() {
            showLoading("提交中")
        },
        error: function(result) {
            $.Prompt("有异常哟！赶紧联系程序猿~", "fail");
        },
        success: function(result) {
            if (result.status == '0') {
                $.Prompt(result.message);
            } else {
                $.Prompt(result.message);
                location.reload();
            }
        }
    });
}
// 转交取消
function refer_cancel_task(refer_id) {
    // ###同步请求
    // location.href = Think.APP + "/Home/Task/do_refer_cancel/refer_id/" + refer_id + ".html";

    // ###异步请求
    $.ajax({
        type:   "GET",
        url:    Think.APP + "/Home/Task/do_refer_cancel/refer_id/" + refer_id + "/ajax/1.html",
        dataType: "json",
        beforeSend: function() {
            showLoading("提交中")
        },
        error: function(result) {
            $.Prompt("有异常哟！赶紧联系程序猿~", "fail");
        },
        success: function(result) {
            if (result.status == '0') {
                $.Prompt(result.message);
            } else {
                $.Prompt(result.message);
                location.reload();
            }
        }
    });
}

// 任务分配
function assign_task(id) {
    modal_init()
    $('#assign-task').load(Think.APP + "/Home/Task/portion/id/" + id + ".html");
}

// 任务结单
function check_task(id) {
    modal_init()
    $('#check-task').load(Think.APP + "/Home/Task/check/id/" + id + ".html");
}

// 任务结单修改
function check_update_task(check_id) {
    modal_init()
    $('#check-update-task').load(Think.APP + "/Home/Task/check_update/check_id/" + check_id + ".html");
}
// 任务结单取消
function check_cancel_task(check_id) {
    $.ajax({
        type:   "GET",
        url:    Think.APP + "/Home/Task/do_check_cancel/check_id/" + check_id + "/ajax/1.html",
        dataType: "json",
        beforeSend: function() {
            showLoading("提交中")
        },
        error: function(result) {
            $.Prompt("有异常哟！赶紧联系程序猿~", "fail");
        },
        success: function(result) {
            if (result.status == '0') {
                $.Prompt(result.message);
            } else {
                $.Prompt(result.message);
                location.reload();
            }
        }
    });
}

// 任务结单通过(结单反馈)
// function pige_task(id) {
//     modal_init()
//     $('#pige-task').load(Think.APP + "/Home/Task/pige/id/" + id + ".html");
// }
// // 任务结单拒绝(任务返工)
// function redo_task(id) {
//     modal_init()
//     $('#redo-task').load(Think.APP + "/Home/Task/redo/id/" + id + ".html");
// }

// 任务阅读[是]
function read_task(id) {
    $.ajax({
        type:   "POST",
        url:    Think.APP + "/Home/Task/do_pige.html",
        data:   { id: id, reason: '阅读完成' },
        // dataType: "json",
        beforeSend: function() {
            showLoading("阅读中")
        },
        error: function(result) {
            $.Prompt("有异常哟！赶紧联系程序猿~", "fail");
        },
        success: function(result) {
            location.reload();
        }
    });
}
// 任务阅读[否]
function unread_task(id) {
    $.Prompt("加油！每日保持阅读量，将来就不可限量~", "warn");
}


/*定时任务信息*/
function timer_task_info(task_id) {
    modal_init()
    $('#timer-task-info').load("/timer-task-info"+task_id)
}

/*编辑定时任务*/
function timer_task_edit(task_id) {
    modal_init()
    $('#timer-task-edit').load("/timer-task-edit"+task_id)
}








//----------------------------------
// 模态窗口理由验证
//----------------------------------
// 检查理由
function reason_check() {
    var $reason = $("textarea[name='reason']");
    $reason.removeClass("warning-form");
    if ($.trim($reason.val()) === "") {
        $reason.addClass("warning-form");
        return false;
    }
    if ($.trim($reason.val()).length < 6 ||
        $.trim($reason.val()).length > 512) {
        $.Prompt("理由内容6-512个字之间哦~", "warn");
        return false
    }
    showLoading("提交中")
    return true
}

// 检查理由[延单申请]
function delay_apply_check() {
    var $reason = $("textarea[name='reason']");
    $reason.removeClass("warning-form");
    if ($.trim($reason.val()) === "") {
        $reason.addClass("warning-form");
        return false
    }
    if ($.trim($reason.val()).length < 6 ||
        $.trim($reason.val()).length > 512) {
        $.Prompt("理由内容6-512个字之间哦~", "warn");
        return false
    }

    $.ajax({
        type:   "POST",
        url:    Think.APP + "/Home/Task/delay_apply_check.html",
        data:   $('#delay_apply').serialize(),
        dataType: "json",
        beforeSend: function() {
            showLoading("提交中")
        },
        error: function(result) {
            $.Prompt("有异常哟！赶紧联系程序猿~", "fail");
        },
        success: function(result) {
            if (result.status == '0') {
                $.Prompt(result.message);
            } else {
                $("#delay_apply").submit();
            }
        }
    });
}

// 检查理由[延单修改]
function delay_update_check() {
    var $reason = $("textarea[name=reason]");
    $reason.removeClass("warning-form");
    if ($.trim($reason.val()) === "") {
        $reason.addClass("warning-form");
        return false
    }
    if ($.trim($reason.val()).length < 6 ||
        $.trim($reason.val()).length > 512) {
        $.Prompt("理由内容6-512个字之间哦~", "warn");
        return false
    }

    $.ajax({
        type:   "POST",
        url:    Think.APP + "/Home/Task/delay_apply_check.html",
        data:   $('#delay_update').serialize(),
        dataType: "json",
        beforeSend: function() {
            showLoading("提交中")
        },
        error: function(result) {
            $.Prompt("有异常哟！赶紧联系程序猿~", "fail");
        },
        success: function(result) {
            if (result.status == '0') {
                $.Prompt(result.message);
            } else {
                $("#delay_update").submit();
            }
        }
    });
}

// 检查理由[转交申请]
function refer_reason_check() {
    var $reason = $("textarea[name='reason']");
    $reason.removeClass("warning-form");
    if ($.trim($reason.val()) === "") {
        $reason.addClass("warning-form");
        return false
    }
    if ($.trim($reason.val()).length < 6 ||
        $.trim($reason.val()).length > 512) {
        $.Prompt("理由内容6-512个字之间哦~", "warn");
        return false
    }

    var receiver            = $("select[name='receiver']").val();
    var current_receiver    = $("input[name='current_receiver']").val();
    if (receiver === current_receiver) {
        $.Prompt("转单接收人不能为自己", "warn");
        return false
    }
    showLoading("提交中")
    return true
}

// 检查理由[结单申请]
var isCommitted = false;
function finish_reason_check() {
    var $reason = $("textarea[name='reason']");
    $reason.removeClass("warning-form");
    if ($.trim($reason.val()) === "") {
        $reason.addClass("warning-form");
        return false
    }
    if ($.trim($reason.val()).length < 6 ||
        $.trim($reason.val()).length > 512) {
        $.Prompt("理由内容6-512个字之间哦~", "warn");
        return false
    }

    if (!isCommitted) {
        showLoading("提交中")
        $("#check_task_upload_annex").click();
        isCommitted = true;
    }
}










//----------------------------------
// 工具方法
//----------------------------------
// 判断项目是否存在数组中
function in_array(search, array) {
    for (var i in array) {
        if (array[i] == search) {
            return true;
        }
    }
    return false;
}

// 数组转字符串
// 说明: 以,进行拼接
function arrayToString(array) {
    if (array == undefined) {
        return '';
    }
    return array.join(",");
}

// 字符串转数组
// 说明: 以,进行切割
function stringToArray(str) {
    if (str == undefined) {
        return '';
    }
    return str.split(",");
}

// 判断字符串是否以某个字符串开始
String.prototype.startsWith = function(str) {    
  var reg = new RegExp("^" + str);    
  return reg.test(this);       
} 

// 判断字符串是否以某个字符串结束
String.prototype.endsWith = function(str) {    
  var reg = new RegExp(str + "$");    
  return reg.test(this);       
}

function suffix_name_type(file_name) {
    var suffix = file_name.split('.').pop().toLowerCase();
    if (suffix == "") {
        suffix == "png";
    }
    var imgs    = Array('gif','jpg','jpeg','png')
    var words   = Array('doc','docx')
    var excels  = Array('xls','xlsx')
    var ppts    = Array('ppt')
    var pdfs    = Array('pdf')
    var rars    = Array('zip','rar')

    if (true == in_array(suffix, imgs)) {
        return "img"
    }
    if (true == in_array(suffix, words)) {
        return "word"
    }
    if (true == in_array(suffix, excels)) {
        return "excel"
    }
    if (true == in_array(suffix, ppts)) {
        return "ppt"
    }
    if (true == in_array(suffix, pdfs)) {
        return "pdf"
    }
    if (true == in_array(suffix, rars)) {
        return "rar"
    }
    return "sm-div-icon"
}











//----------------------------------
// 搜索任务
//----------------------------------
function search_task() {
    var keyword = $.trim($("#search_keyword").val());
    if (keyword.length == 0) {
        $.Prompt("请输入搜索关键字", "warn");
        return false;
    }

    location.href = Think.APP + "/Home/Index/search.html?wd=" + encodeURI(keyword)
}
$("#search_keyword").mouseenter(function() {
    $(this).removeClass("search-bg-out");
    $(this).addClass("search-bg-enter");
}).mouseout(function() {
    $(this).removeClass("search-bg-enter");
    $(this).addClass("search-bg-out");
}).keydown(function(e) {
    if (e.which == 13) {
        search_task()
    }
});











//----------------------------------
// 动态加载
//----------------------------------
// 显示Loading
function showLoading(tip) {
    // 显示遮罩
    $("div.mask").show()
    // 显示Loading
    $("div.loading").show()
  
    // 设置提示内容
    if (tip != undefined && $.trim(tip) != "") {
        $("div.loading_tip").html(tip + "...");
    } else {
        $("div.loading_tip").html("加载中...");
    }
}

// 隐藏Loading
function endLoading() {
    // 隐藏遮罩
    $("div.mask").hide()
    // 隐藏Loading
    $("div.loading").hide()
}

// 处理提交状态时的Loading显示
function subSomething() {
    if (document.readyState == "Loading") {
        showLoading();
    } else {
        endLoading();
    }
}











//----------------------------------
// 任务关注
//----------------------------------
function ajax_concern(element, source, id) {
    // ###操作数据
    // 操作模块
    var module  = source + 'concern';
    // 操作模块
    var operate = $(element).attr("rel");


    // ###验证有效性
    // 验证[ID]
    if (isNaN(id)) {
        $.Prompt("关注来源ID不存在!", "warn");
        return
    }


    // ###提交请求
    $.ajax({
        // 请求类型
        type:           "POST",
        // 请求URL
        url:            Think.APP + "/Home/" + module + "/concern.html",
        // 提交数据
        data:           { source: id, operate: operate, _xsrf: getCookie("_xsrf") },
        // 响应数据类型
        dataType:       "json",
        // 回调函数[提交前]
        beforeSend:     function() {
            // 显示Loading
            showLoading("提交中...")
        },
        // 回调函数[错误]
        error:          function(result) {
            $.Prompt("有异常哟！赶紧联系程序猿~", "fail");
        },
        // 回调函数[成功]
        success:        function(result) {
            if (result.status == '0') {
                $.Prompt(result.message);
            } else {
                $.Prompt(result.message, "succ");
                if (source == 'task') {
                    if ($(element).attr("rel") == 'concern') {
                        $(element).html("取消关注")
                        $(element).attr("rel", "unconcern")
                    } else {
                        $(element).html("<span>＋</span>关注")
                        $(element).attr("rel", "concern")
                    }
                } else if (source == 'topic') {
                    if ($(element).attr("rel") == 'concern') {
                        $(element).html("<i class='icon-unconcern'></i>")
                        $(element).attr("rel", "unconcern")
                    } else {
                        $(element).html("<i class='icon-concern'></i>")
                        $(element).attr("rel","concern")
                    }
                }
            }
        }
    })
}