//----------------------------------
// 常量
//----------------------------------
// 验证[制作明细最小长度]
var VALIDATE_INFO_MINLENGTH = 6



//----------------------------------
// 公共方法
//----------------------------------
// 添加子任务，动态显示
var subTaskHtml = function($textArea, addHtml, del, is_modal) {
    // 项目内容
    var text = $textArea.val();

    // 判断项目内容有效性
    if ($.trim(text) != "") {
        // 所有输入区域集
        var $lis = $textArea.parent().parent().children()

        // 计算ID
        var id
        if (is_modal == 'true') {
            id = parseInt($lis.length - 1) <= 0 ? 1 : (parseInt($lis.length) - 1);
        } else {
            id = parseInt($lis.length - 1) <= 0 ? 1 : (parseInt($lis.length));
        }

        // 计算 
        var t
        if (is_modal == 'true') {
            t = '<li ondblclick ="editTaskItem(this)" ' + addHtml['modal'] + ' class="hand border n-b-b">' +
                    '<span class="badge badge-square">' + id + '</span>' +
                    '<span class="m-l-s15 cursor">' + text + '</span>' + 
                    addHtml['input'] +
                    '<a class="pull-right a-gray" href="javascript:;" onclick="' + del + '(this,' + is_modal + ')">删除</a>' +
                '</li>';
        } else {
            t = '<li ondblclick ="editTaskItem(this)" ' + addHtml['modal'] + ' class="hand">' +
                    '<span class="badge badge-square">' + id + '</span>' +
                    '<span class="m-l-s15 cursor">' + text + '</span>' + 
                    addHtml['input'] + 
                    '<a class="pull-right a-gray" href="javascript:;" onclick="delTaskItem(this)">删除</a>' +
                '</li>';
        }

        // 添加新的项目输入区域
        $textArea.parent().before(t);


        // ###更新项目ID
        // 项目ID
        var items
        if (is_modal == 'true') {
            items = stringToArray($("#add_items").val());
        } else {
            items = stringToArray($("input[name='items']").val());
        }
        items.push(text);
        items = arrayToString(items);
        if (is_modal == 'true') {
            $("#add_items").val(items)
        } else {
            $("input[name='items']").val(items)
        }
    }

    // 隐藏模板区域
    $('.taskItemInputM').hide();
    $('.taskItemInput').hide();

    // 重置文本框状态
    $textArea.val("");
    $textArea.off("blur");
};

// 添加任务制作明细(modal='true'为模态窗口)
function addTaskItem(obj) {
    // 模态标识
    var is_modal = $(obj).attr('modal');

    // 判断是否模态
    if (is_modal == 'true') {
        // ###模态窗口
        var textAreaObj = $("textarea[name='subTaskTextM']");
        $('.taskItemInputM').show();
        textAreaObj.focus();
        textAreaObj.on('blur', function() {
            if ($.trim(textAreaObj.val()).length == 0) {
                $('.taskItemInputM').hide();
                return false
            }
            if ($.trim(textAreaObj.val()).length < VALIDATE_INFO_MINLENGTH) {
                textAreaObj.focus()
                $.Prompt("制作明细不能小于" + VALIDATE_INFO_MINLENGTH + "个字");
                return false;
            }
            var addHtml={"modal":"modal='true'","input":''};
            var del = "delSubTaskHtml";
            subTaskHtml(textAreaObj,addHtml,del,is_modal)
        });
    } else {
        // ###非模态窗口
        // 输入框
        var $textArea = $("textarea[name='subTaskText']");

        // 显示输入区域
        $('.taskItemInput').show();
        // 隐藏空白提示
        $("#item_none").hide()

        // 设置焦点[输入框]
        $textArea.focus();
        // 绑定事件[失去焦点][输入框]
        $textArea.on('blur', function() {
            // 判断无输入
            if ($.trim($textArea.val()).length === 0) {
                // 隐藏输入区域
                $('.taskItemInput').hide();

                // 所有输入区域集
                $lis = $textArea.parent().parent().children()
                // 判断是否存在有效输入区域
                if ($lis.length === 0) {
                    // 无则显示空白提示
                    $("#item_none").show()
                }
                return false
            }

            // 判断非法输入
            if ($.trim($textArea.val()).length < VALIDATE_INFO_MINLENGTH) {
                // 设置焦点[输入区域]
                $textArea.focus()
                // 消息提示
                $.Prompt("制作明细不能小于" + VALIDATE_INFO_MINLENGTH + "个字");
                return false;
            }

            // 异步提交任务制作明细
            subTaskAjax($textArea)
        });
    }
}










//----------------------------------
// 异步提交
//----------------------------------
// [页面展示]提交
var subTaskAjax = function($textArea) {
    // ###操作数据
    // 任务ID
    var task_id     = $("#id").val();
    // 项目ID
    var item_id     = $textArea.children("input[name='item_id']").val();
    // 创建人
    var staff_id    = $("[name='sender']").val();
    // 项目内容
    var info        = $textArea.val();


    // ###格式化变量
    // 项目ID
    item_id     = $.trim(item_id) == "" ? 0 : item_id;


    // ###提交请求
    // 提交数据
    var data    = {
        "item_id"   : item_id,
        "task_id"   : task_id,
        "staff_id"  : staff_id,
        "info"      : info,
        "_xsrf"     : getCookie("_xsrf")
    };
    // 提交请求
    $.ajax({
        // 请求类型
        type:       "POST",
        // 请求URL
        url:        Think.APP + "/Home/Taskitem/save.html",
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
            // 判断处理是否成功
            if (result.status == '0') {
                // == 错误 ==
                $.Prompt(result.message);
            } else {
                // == 成功 ==
                var addHtml = {
                    "modal": '',
                    "input": '<input type="hidden" name="item_id" value="' + result.id + '"/>'
                };
                subTaskHtml($textArea, addHtml, "del", "false");
                $.Prompt("任务制作明细添加成功", "succ");
            }
        }
    });
};

// [页面展示]修改
var editTaskItemHtml = function(obj, $textArea, old_info, is_modal) {
    // 项目内容
    var info = $textArea.val();

    // 验证有效性
    if ($.trim(info).length >= VALIDATE_INFO_MINLENGTH) {
        // == 成功 ==
        // 更新项目内容[显示区域]
        $(obj).find('span.m-l-s15').text(info);
        
        // 更新项目内容[隐藏区域]
        var items = null
        if (is_modal == "true") {
            items = $("#add_items").val();
            items = items.replace(old_info, info);
            $("#add_items").val(items);
        } else {
            items = $("input[name='items']").val();
            items = items.replace(old_info, info);
            $("input[name='items']").val(items)
        }
        
        // 隐藏模板区域
        $('.taskItemInputM').hide();
        $('.taskItemInput').hide();


        // 重置文本框状态
        $textArea.val("");
        $textArea.off("blur");
    } else {
        // == 错误 ==
        $textArea.focus()
        $.Prompt("制作明细不能小于" + VALIDATE_INFO_MINLENGTH + "个字");
    }
}

// [页面展示]删除
var delSubTaskHtml = function(obj, is_modal) {
    // LI结点
    var $li     = $(obj).parent();
    // 项目内容
    var info    = $li.find("span.m-l-s15").text()

    // 更新项目内容
    var items   = null;
    if (is_modal == 'true') {
        items = $("#add_items").val();
        items = items.replace("," + info, "");
        $("#add_items").val(items);
    } else {
        items = $("input[name='items']").val();
        items = items.replace("," + info, "");
        $("input[name='items']").val(items);
    }
    
    // 移除LI结点
    $li.remove();
    // 判断是否已经无项目, 则显示空白提示
    if ($("#subTast li.hand").length === 0) {
        $("#item_none").show()
    }

    // 阻止事件冒泡
    // event.stopPropagation();
    return false;
}

// [异步提交]编辑
function editTaskItem(obj) {
    // 项目内容
    var info        = $(obj).find('span.m-l-s15').text();
    // 项目内容[旧]
    var old_info    = info;
    // 是否模态
    var is_modal    = $(obj).attr('modal');

    // 显示编辑区域
    if (is_modal == 'true') {
        var $textArea = $("textarea[name='subTaskTextM']");
        $textArea.val(info);
        $('.taskItemInputM').show();
    } else {
        var $textArea = $("textarea[name='subTaskText']");
        $textArea.val(info);
        $('.taskItemInput').show();
    }
    // 设置焦点[输入框]
    $textArea.focus();


    // 绑定事件[失去焦点][输入框]
    $textArea.on('blur', {obj:obj}, function() {
        // 模态窗口
        if (is_modal == 'true') {
            editTaskItemHtml(obj, $textArea, old_info, is_modal)
        } else {
            // ###验证
            // 验证内容长度
            if ($.trim($textArea.val()).length < 6) {
                editTaskItemHtml(obj, $textArea, old_info, is_modal)
                return;
            }


            // ###操作数据
            // 任务ID
            var task_id     = $("#id").val();
            // 执行人ID
            var man_id      = $("#man_id").val();
            // 项目ID
            var item_id     = $(obj).children("input[name='item_id']").val();
            // 创建人
            var staff_id    = $("[name='sender']").val();


            // ###校正数据
            if ($.trim(item_id) === "") {
                item_id = 0;
            }
            

            // ###提交请求
            // 提交数据
            var data = {
                "task_id"   : task_id,
                "item_id"   : item_id,
                "staff_id"  : staff_id,
                "info"      : $textArea.val(),
                "_xsrf"     : getCookie("_xsrf")
            };

            // 提交请求
            $.ajax({
                // 请求类型
                type:       "POST",
                // 请求URL
                url:        Think.APP + "/Home/Taskitem/save.html",
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
                    // 判断处理是否成功
                    if (result.status == '0') {
                        // == 错误 ==
                        $.Prompt(result.message);
                    } else {
                        // == 成功 ==
                        editTaskItemHtml(obj, $textArea, old_info, is_modal)
                        $.Prompt("任务制作明细修改成功", "succ");
                    }
                }
            });
        }
    });
}

// [异步提交]删除
function delTaskItem(obj) {
    // ###操作数据
    // 项目ID
    var item_id     = $(obj).prev("input[name='item_id']").val();
    // 任务ID
    var task_id     = $("#id").val();
    // 创建人
    var staff_id    = $("[name='sender']").val();


    // ###提交请求
    // 提交数据
    var data    = {
        "item_id"   : item_id,
        "task_id"   : task_id,
        "staff_id"  : staff_id,
        "_xsrf"     : getCookie("_xsrf")
    };
    // 提交请求
    $.ajax({
        // 请求类型
        type:       "POST",
        // 请求URL
        url:        Think.APP + "/Home/Taskitem/delete.html",
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
            delSubTaskHtml(obj, "false");
            $.Prompt("任务制作明细删除成功", "succ");
        }
    });

    
    // 阻止事件冒泡
    // event.stopPropagation();
    return false;
}

// [异步提交]完成
function finish_item(obj) {
    var is_checked=$(obj).is(":checked")
    var task_id = $("#id").val();
    var args = {"item_id":$(obj).prev("input[name='item_id']").val(),"act":"un_finish","taskId":task_id};
    if (is_checked==true) {
        args = {"item_id":$(obj).prev("input[name='item_id']").val(),"act":"finish","taskId":task_id};
    }
    args._xsrf = getCookie("_xsrf");
    $.ajax({url: "/task/upd-sub", data: args, dataType: "json", type: "POST",
        success: function(response) {
            if (is_checked==true) {
                $.Prompt("任务制作明细已完成","succ");
            } else {
                $.Prompt("任务制作明细完成已取消","succ");
            }
        }
    });
}