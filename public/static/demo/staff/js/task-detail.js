//----------------------------------
// 文档加载
//----------------------------------
// 绑定事件[文档加载]
$(document).ready(function() {
    // 绑定事件[失去焦点][回复内容]
    $("#comment_info").blur(function() {
        // 当前元素
        var $this = $(this)

        // 判断是否没有进行输入，若未输入，添加警告样式
        if ($.trim($this.val()) == "") {
            $this.addClass("warning-form");
        } else {
            $this.removeClass("warning-form");
        }
    })

    // 调整右侧区域显示高度
    if (true) {
        // 获得内容区域高度
        var minHeight = $("#detail_left").height()
        // 设置右侧区域高度
        $("#detail_right").css("min-height", minHeight)
    }

    // 设置任务内容高度
    if (true) {
        // 任务内容元素
        var $info = $('#info')
        // 如果元素存在，则设置rows属性调整行高
        if ($info.length > 0) {
            $info.attr('rows', text_rows($info.val()))
        }
    }
});










//----------------------------------
// 任务事件
//----------------------------------
// 定时器句柄[短时提交判断]
var fnSubmit = null;
// 处理提交[添加]
function do_update(obj) {
    // ###定义变量
    var $name, $info, $receiver;


    // ###赋值变量
    // 赋值变量[任务名称+任务描述]
    if (obj == 1) {
        // 修改任务
        $name = $("#name");
        $info = $("#info");
    } else if (obj == 3) {
        // 指派任务
        $name = $("#assign_name");
        $info = $("#assign_info");
    } else {
        // 复制任务
        $name = $("#copy_name");
        $info = $("#copy_info");
    }
    // 赋值变量[接单人]
    $receiver = $("select[name='receiver']");


    // ###状态还原
    // 移除警告样式[任务名称]
    $name.removeClass("warning-form");
    // 移除警告样式[任务描述]
    $info.removeClass("warning-form");


    // ###验证有效性
    // 验证[任务名称]
    if ($name.length > 0 && $.trim($name.val()) == "") {
        $name.addClass("warning-form");
        return
    }
    // 验证[任务描述]
    if ($info.length > 0 && $.trim($info.val()) == "") {
        $info.addClass("warning-form");
        return
    }
    // 验证[接单人]
    if ($receiver.length > 0 && $receiver.val() == "") {
        $.Prompt("请选择接单人", "warn");
        return
    }


    // ###追加数据
    // 追加数据[完成时间]
    date_correct();


    // ###提交数据
    // 清除定时器
    clearTimeout(fnSubmit);
    // 创建定时器 + 提交请求
    fnSubmit = setTimeout(function() {
        // 序列化页面数据
        var data = $('#see_form').serialize();
        // 追加属性[动作]
        data.action = 'update'
        // 追加属性[xsrf]
        data._xsrf  = getCookie("_xsrf");


        // 提交请求
        $.ajax({
            // 提交类型
            type:       "POST",
            // 提交网址
            url:        Think.APP + "/Home/Task/validate.html",
            // 提交数据
            data:       data,
            // 响应数据类型
            dataType:   "json",
            // 回调函数[提交前]
            beforeSend: function() {
                // 显示Loading
                showLoading("提交中");
            },
            // 回调函数[错误]
            error: function(result) {
                // 显示Loading
                $.Prompt("Connection error !", "fail");
            },
            // 回调函数[成功]
            success: function(result) {
                // 判断处理是否成功
                if (result.status == '0') {
                    // == 错误 ==
                    $.Prompt(result.message, "warn");
                } else {
                    // == 成功 ==
                    // 显示Loading
                    showLoading("提交中")
                    // 提交附件
                    $("#upload_annex").click();
                }
            }
        });
    }, 300);
}

// 异步刷新项目用户
function ajax_project_staffs($project) {
    // ###操作数据
    // 项目ID
    var project_id  = $project.val();
    // 区域
    var $region     = $("#see_form");


    // ###验证数据
    // 判断项目ID
    if (project_id === '') {
        return
    }


    // ###提交请求
    $.ajax({
        // 提交类型
        type:       "POST",
        // 提交网址
        url:        Think.APP + "/Home/Project/ajax_project_staffs/id/" + project_id + ".html",
        // 提交数据
        data:       { _xsrf: getCookie("_xsrf") },
        // 响应数据类型
        dataType:   "json",
        // 回调函数[提交前]
        beforeSend: function() {
            // 显示Loading
            showLoading("提交中");
        },
        // 回调函数[错误]
        error: function(result) {
            // 显示Loading
            $.Prompt("Connection error !", "fail");
        },
        // 回调函数[成功]
        success: function(result) {
            // 判断处理是否成功
            if (result.status == '0') {
                // == 错误 ==
                $.Prompt(result.message, "warn");
            } else {
                // == 成功 ==
                // 隐藏Loading
                endLoading()
                // 提交附件
                var data = result.data
                var html = "<option>搜索名字/工号</option>"
                $.each(data, function(index, item) {
                    var id   = item['id'];
                    var name = item['name'];
                    var no   = item['no'];
                    html += "<option value="+id+">"+name+"("+no+")</option>"
                })

                // 更新接单人
                $("select[name='receiver']", $region).html(html)
                $("select[name='receiver']", $region).trigger('chosen:updated')
                // 更新抄送人
                // $("select[name='cc_staffs[]']", $region).html(html)
                // $("select[name='cc_staffs[]']", $region).trigger('chosen:updated')
            }
        }
    });
}










//----------------------------------
// 评论事件
//----------------------------------
var fnComment
// 提交数据[评论]
function submit_comment(action) {
    // 区域
    var $region     = $("#see_form")
    // 评论内容
    var info        = $("#comment_info", $region).val();
    // 任务ID
    var task_id     = $("input[name='id']", $region).val();
    // 评论给
    var receiver    = $("input[name='comment_receiver']", $region).val();
    // 父评论ID
    var parent_id   = $("input[name='comment_parent_id']", $region).val();
    // 评论回复标签
    var reply_title = $("input[name='comment_reply_title']", $region).val();


    // FIXME: AC - RAW-JS
    /*
    if (reply_to !== "" && info !== "") {
        if (info.match(receiver) != null) {
            info = info.replace(receiver, "")
        } else {
            $("input[name=reply_to]").val("")
            $("input[name=parent_id]").val("0");
        }
    }
    */

    // 验证[评论内容]
    $("#comment_info").removeClass("warning-form");
    if (info === "") {
        $("#comment_info").addClass("warning-form");
        $.Prompt("请填写评论内容!", "warn");
        return
    }
    // 验证父评论ID 
    if (!info.startsWith(reply_title)) {
        parent_id = 0;
    }



    // ###提交数据
    // 清除定时器
    clearTimeout(fnComment);
    // 创建定时器 + 提交请求
    fnComment = setTimeout(function() {
        // 结构化页面数据
        var data = {
            "task_id":      task_id,
            "receiver":     receiver,
            "info":         info,
            "parent_id":    parent_id,
            "_xsrf":        getCookie("_xsrf")
        };
        // 提交请求
        $.ajax({
            // 提交类型
            type:       "POST",
            // 提交网址
            url:        Think.APP + "/Home/Taskcomment/save.html",
            // 提交数据
            data:       data,
            // 响应数据类型
            dataType:   "json",
            // 回调函数[提交前]
            beforeSend: function() {
                // 显示Loading
                showLoading("提交中");
            },
            // 回调函数[错误]
            error: function(result) {
                // 显示Loading
                $.Prompt("Connection error !", "fail");
            },
            // 回调函数[成功]
            success: function(result) {
                // 判断处理是否成功
                if (result.status == '0') {
                    // == 错误 ==
                    $.Prompt(result.message, "warn");
                } else {
                    // == 成功 ==
                    $.Prompt(result.message, "succ");
                    // 隐藏Loading
                    endLoading()
                    // 重新载入页面
                    location.reload(true)
                }
            }
        });
    }, 300);
    return true
}

// 回复子评论
function reply_comment(parent_id, staff_name, floor_no){
    var title = "回复 " + floor_no + "# " + staff_name + " ：";
    $("input[name='comment_parent_id']").val(parent_id);
    $("input[name='comment_reply_title']").val(title);
    $("#comment_info").val(title);
    $("#comment_info").focus();
}










//----------------------------------
// 附件[废弃]
//----------------------------------
// 异步删除附件
function del_annex(obj, annex_id, type){
    // ###操作数据
    // 任务ID
    var task_id = $("#id").val();


    // ###提交请求
    $.ajax({
        // 请求类型
        type:           "POST",
        // 请求URL
        url:            Think.APP + "/Home/Taskannex/delete.html",
        // 提交数据
        data:           { id: annex_id, task_id: task_id, _xsrf: getCookie("_xsrf") },
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
                $.Prompt("附件删除成功", "succ");
                if (type == 2) {
                    $(obj).parents(".sm-icon").remove()
                } else {
                    $(obj).parents('.annex').remove();
                }
            }
        }
    })
}










//----------------------------------
// 工时记录
//----------------------------------
// 工时记录-验证
function checkTimerecord(element) {
    var record      = $(element).parents("li").prev("li.man-hour-item");
    var $work_time  = record.find("input[name='work_time[]']");
    var $cost_time  = record.find("input[name='cost_time[]']");
    var $left_time  = record.find("input[name='left_time[]']");
    
    $work_time.removeClass("warning-form");
    $cost_time.removeClass("warning-form");
    $left_time.removeClass("warning-form");
    
    if ($.trim($work_time.val()) == "") {
        $work_time.addClass("warning-form");
        $.Prompt("请选择日期", "warn");
        return false;
    }
    if ($.trim($cost_time.val()) == "" || parseInt($cost_time.val()) == 0) {
        $cost_time.addClass("warning-form");
        $.Prompt("请填写消耗时间(消耗时间必须大于0)", "warn");
        return false;
    }
    /*
    if ($.trim($left_time.val()) == "") {
        $left_time.addClass("warning-form");
        $.Prompt("请填写剩余时间", "warn");
        return false;
    }
    */

    return true;
}

// 工时记录-提交
function submitTimerecord() {
    // ###验证数据
    if (!checkTimerecord($(".add-record-plus"))) {
        return;
    }

    // ###提交数据
    var save_undone = parseInt($(".man-hours-save").length);
    if (save_undone > 0) {
        layer.confirm(
            '你还有未保存的记录，确定提交工时记录？', 
            { title: "确定提交" }, 
            function(index) {
                layer.close(index);
                doSubmitTimerecord();
            }
        )
    } else {
        doSubmitTimerecord();
    }
}

// 工时记录-提交请求
function doSubmitTimerecord() {
    // ###操作数据
    // 任务ID
    var task_id = $("input[name='task_id']").val();


    // ###提交请求
    $.ajax({
        // 请求类型
        type:           "POST",
        // 请求URL
        url:            Think.APP + "/Home/Tasktimerecord/save_all.html",
        // 提交数据
        data:           $('#timeRecordForm').serialize(),
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
                time_record(task_id);
            }
        }
    })
}


var old_work_time
var old_cost_time
var old_left_time
var old_remark
var old_operate
// 工时记录-编辑
function editTimerecord(element) {
    // ###操作数据
    // 记录ID
    var record_id   = $(element).attr('data-id');
    // 发送时间
    var send_time   = $("input[name='send_time']").val();

    // 工时时间
    old_work_time       = $(element).parents('div.recorded-item').find('div.work_time').html();
    var work_time       = $(element).parents('div.recorded-item').find('div.work_time').attr('data-value');
    var work_time_html  = '<input type="text" name="work_time[]" onclick="WdatePicker({minDate:\''+send_time+'\',dateFmt:\'yyyy-MM-dd\',maxDate:\'%y-%M-%d\'})" class="Wdate form-control" value="' + work_time + '">';
    // 消耗工时
    old_cost_time       = $(element).parents('div.recorded-item').find('div.cost_time').html();
    var cost_time       = $(element).parents('div.recorded-item').find('div.cost_time').attr('data-value');
    var cost_time_html  = '<input type="text" name="cost_time[]" class="man-hours form-control honeycomb-bg-llgray inline" value="' + cost_time + '" ' +
        'onkeyup="this.value=this.value.replace(/[^\\d]/g,\'\')" onafterpaste="this.value=this.value.replace(/[^\\d]/g,\'\')">\n' +
        '<span class="darkgray inline m-l-s10">小时</span>';
    // 剩余工时
    old_left_time       = $(element).parents('div.recorded-item').find('div.left_time').html();
    var left_time       = $(element).parents('div.recorded-item').find('div.left_time').attr('data-value');
    var left_time_html  = '<input type="text" name="left_time[]" class="man-hours form-control honeycomb-bg-llgray inline" value="' + left_time + '" ' +
        'onkeyup="this.value=this.value.replace(/[^\\d]/g,\'\')" onafterpaste="this.value=this.value.replace(/[^\\d]/g,\'\')">\n' +
        '<span class="darkgray inline m-l-s10">小时</span>';
    // 备注
    old_remark          = $(element).parents('div.recorded-item').find('div.remark').html();
    var remark          = $(element).parents('div.recorded-item').find('div.remark').attr('data-value');
    var remark_html     = '<input type="text" name="remark[]" class="form-control honeycomb-bg-llgray" value="' + remark + '">';
    // 操作
    old_operate         = $(element).parent('div').html();
    var operation       = '<span class="man-hours-save cursor" data-id="' + record_id + '" onclick="updateTimerecord(this)">保存</span>\n' +
        '<span class="gray cursor m-l-s5" data-id="' + record_id + '" onclick="cancelTimerecord(this)">取消</span>';


    // ###更新显示
    $(element).parents('div.recorded-item').find('div.work_time').html(work_time_html);
    $(element).parents('div.recorded-item').find('div.cost_time').html(cost_time_html);
    $(element).parents('div.recorded-item').find('div.left_time').html(left_time_html);
    $(element).parents('div.recorded-item').find('div.remark').html(remark_html);
    $(element).parent('div').html(operation);
}

// 工时记录-删除
function deleteTimerecord(element){
    //1新增 2修改(保存) 3删除
    layer.confirm('确定要删除此记录？',{title:"删除工时记录"},function(index){
        layer.close(index);

        // ###操作数据
        // 记录ID
        var record_id   = $(element).attr('data-id');
        // 任务ID
        var task_id     = $("input[name='task_id']").val();


        // ###提交请求
        // 提交数据
        var data = {
            "id":           record_id,
            "_xsrf":        getCookie("_xsrf")
        };
        // 发起请求
        $.ajax({
            // 请求类型
            type:           "POST",
            // 请求URL
            url:            Think.APP + "/Home/Tasktimerecord/delete.html",
            // 提交数据
            data:           data,
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

                    $(element).parents("div.recorded-item").remove();
                    time_record(task_id);
                }
            }
        })
    });

}

// 工时记录-编辑[保存]
function updateTimerecord(element) {
    // ###操作数据
    // 记录ID
    var record_id   = $(element).attr('data-id');
    // 工时时间
    var work_time   = $(element).parents('div.recorded-item').find("input[name='work_time[]']").val();
    // 消耗工时
    var cost_time   = $(element).parents('div.recorded-item').find("input[name='cost_time[]']").val();
    // 剩余工时
    var left_time   = $(element).parents('div.recorded-item').find("input[name='left_time[]']").val();
    // 备注
    var remark      = $(element).parents('div.recorded-item').find("input[name='remark[]']").val();

    // ###提交请求
    // 提交数据
    var data = {
        "id":           record_id,
        "work_time":    work_time,
        "cost_time":    cost_time,
        "left_time":    left_time,
        "remark":       remark,
        "_xsrf":        getCookie("_xsrf")
    };
    // 发起请求
    $.ajax({
        // 请求类型
        type:           "POST",
        // 请求URL
        url:            Think.APP + "/Home/Tasktimerecord/update.html",
        // 提交数据
        data:           data,
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

                var $work_time = $(element).parents('div.recorded-item').find('div.work_time');
                $work_time.attr('data-value', work_time);
                $work_time.html(work_time);

                var $cost_time = $(element).parents('div.recorded-item').find('div.cost_time');
                $cost_time.attr('data-value', cost_time);
                $cost_time.html(cost_time + '小时');

                var $left_time = $(element).parents('div.recorded-item').find('div.left_time');
                $left_time.attr('data-value', left_time);
                $left_time.html(left_time + '小时');

                var $remark = $(element).parents('div.recorded-item').find('div.remark');
                $remark.attr('data-value', remark);
                $remark.html(remark);

                $(element).parent('div').html(old_operate);
                time_record(task_id);
            }
        }
    })
}

// 工时记录-编辑[取消]
function cancelTimerecord(obj){
    $(obj).parents('div.recorded-item').find('div.work_time').html(old_work_time);
    $(obj).parents('div.recorded-item').find('div.cost_time').html(old_cost_time);
    $(obj).parents('div.recorded-item').find('div.left_time').html(old_left_time);
    $(obj).parents('div.recorded-item').find('div.remark').html(old_remark);
    $(obj).parent('div').html(old_operate);
}

// 工时添加记录
function addTimerecord(element) {
    // ###验证数据
    if (!checkTimerecord(element)) {
        return;
    }
    
    // 项目数量
    var count       = $('.man-hour-item').length;
    // 发送时间
    var send_time   = $("input[name='send_time']").val();

    // 验证项目数量
    if (count > 5) {
        $.Prompt("一次至多添加5条记录", "warn");
        return;
    }

    var content = 
        '<li class="border top-un-border man-hour-item">\n' +
            '<div class="row p-b-s10 p-t-s5">\n' +
                '<div class="col-sm-2">\n' +
                    '<div><input type="text" name="work_time[]" onclick="WdatePicker({minDate:\''+send_time+'\',dateFmt:\'yyyy-MM-dd\',maxDate:\'%y-%M-%d\'})" class="Wdate form-control"></div>\n' +
                '</div>\n' +
                '<div class="col-sm-2">\n' +
                    '<div>\n' +
                        '<input type="text" name="cost_time[]" class="man-hours form-control honeycomb-bg-llgray inline" onkeyup="this.value=this.value.replace(/[^\\d]/g,\'\')" onafterpaste="this.value=this.value.replace(/[^\\d]/g,\'\')">\n' +
                        '<span class="darkgray inline"><span class="m-l-s10">小时</span></span>\n' +
                    '</div>\n' +
                '</div>\n' +
                '<div class="col-sm-2">\n' +
                    '<div>\n' +
                        '<input type="text" name="left_time[]" class="man-hours form-control honeycomb-bg-llgray inline" onkeyup="this.value=this.value.replace(/[^\\d]/g,\'\')" onafterpaste="this.value=this.value.replace(/[^\\d]/g,\'\')">\n' +
                        '<span class="darkgray inline"><span class="m-l-s10">小时</span></span></div>\n' +
                    '</div>\n' +
                '<div class="col-sm-6">\n' +
                    '<em class="ding-line man-hours-mark-line"></em>\n' +
                    '<div><input type="text" name="remark[]" class="form-control honeycomb-bg-llgray"></div>\n' +
                '</div>\n' +
            '</div>\n' +
        '</li>\n';
    $('#modal-add-hours').before(content);
}










//----------------------------------
// 工具方法
//----------------------------------
// 获得文本行数
function text_rows(text) {
    if (text == "" || text == null || text == undefined) {
        return 0;
    }
    return text.split("\n").length;
}

// 动态设置优先级下拉框颜色
function priority_color(element) {
    $(element).removeClass();
    var color = $('option:selected', element).attr('class');
    $(element).addClass('make-span ' + color);
}

// 校正日期
function date_correct() {
    // 结果日期
    var result;

    // 区域
    var $region     = $("#see_form")
    // 完成时间-日期
    var ok_date     = $("input[name='ok_date']", $region).val();
    // 完成时间-小时
    var ok_hour     = $("#ok_hour", $region).val();
    // 完成时间-分钟
    var ok_minute   = $("#ok_minute", $region).val();

    // 格式化小时
    if (ok_hour * 1 < 10) {
        ok_hour = '0' + ok_hour;
    }
    // 格式化分钟
    if (ok_minute * 1 < 10) {
        ok_minute = '0' + ok_minute;
    }

    // 构建结果日期
    result = ok_date + " " + ok_hour + ":" + ok_minute + ":00";
    // 填充页面元素
    $("input[name='ok_time']", $region).val(result);
}

// 打开新窗口
function open_window(url) {
    if (url == undefined || $.trim(url) === "" || url === null) {
        return
    }

    // 弹出窗口的宽度
    var iWidth  = 980;
    // 弹出窗口的高度
    var iHeight = 800;
    // 获得窗口的垂直位置
    var iTop    = (window.screen.availHeight - 30 - iHeight) / 2;
    // 获得窗口的水平位置
    var iLeft   = (window.screen.availWidth  - 10 - iWidth) / 2;

    // 打开窗口
    // window.open(url, "_blank", "height=" + iHeight + ", width=" + iWidth + ", top=" + iTop + ", left=" + iLeft, toolbar='no', status='no', menubar='no', resizable='yes', scrollbars='yes');
    window.open(url, "_blank", "height=" + iHeight + ", width=" + iWidth + ", top=" + iTop + ", left=" + iLeft + ', toolbar=0, status=0, menubar=0, resizable=1, scrollbars=1');
}