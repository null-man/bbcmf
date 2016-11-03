//----------------------------------
// 绑定事件
//----------------------------------
// 绑定文本框事件[blur][任务名称]
$("#add_name").blur(function(){warning(this);});
// 绑定文本域事件[blur][任务描述]
$("#add_info").blur(function(){warning(this);});










//----------------------------------
// 任务事件
//----------------------------------
// 定时器句柄[短时提交判断]
var fnSubmit = null;
// 处理提交[保存]
function do_save(obj) {
    // ###定义变量
    var $name, $info, $receiver;


    // ###赋值变量
    // 赋值变量[任务名称+任务描述]
    if (obj == 1) {
        // 添加任务
        $name = $("#add_name");
        $info = $("#add_info");
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
    $receiver  = $("select[name='receiver']");
    $receivers = $("select[name='receivers[]']");


    // ###状态还原
    // 移除警告样式[任务名称]
    $name.removeClass("warning-form");
    // 移除警告样式[任务描述]
    $info.removeClass("warning-form");


    // ###验证有效性
    // 验证[任务名称]
    if ($.trim($name.val()) == "") {
        $.Prompt("请填写任务名称", "warn");
        $name.addClass("warning-form");
        return
    }
    // 验证[任务描述]
    if ($.trim($info.val()) == "") {
        $.Prompt("请填写任务描述", "warn");
        $info.addClass("warning-form");
        return
    }
    // 验证[接单人]
    if ($receiver.length > 0) {
        if ($receiver.val() == "") {
            $.Prompt("请选择接单人!", "warn");
            return
        }
    } else if ($receivers.length > 0) {
        if ($receivers.val() == "") {
            $.Prompt("请选择接单人!!", "warn");
            return
        }
    }
    // 验证[附件]
    if ($('#task_file_view .sm-icon').length > 15) {
        $.Prompt("上传的附件数量不能超过15个", "warn");
        return
    };


    // ###追加数据
    // 追加数据[完成时间]
    date_correct();


    // ###提交请求
    // 清除定时器
    clearTimeout(fnSubmit);
    // 创建定时器 + 提交请求
    fnSubmit = setTimeout(function() {
        // 序列化页面数据
        var data = $('#add_form').serialize();
        // 追加属性[动作]
        data.action = 'save'
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
                    $("#add_task_upload_annex").click();
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
    var $region     = $("#add_form");


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
// 工具方法
//----------------------------------
// 校正日期
function date_correct() {
    // 结果日期
    var result;

    // 区域
    var $region     = $("#add_form")
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