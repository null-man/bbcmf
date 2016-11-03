/**
 * Created by AC on 16/05/05.
 */
//----------------------------------
// 事件[页面加载完成]
//----------------------------------
$(document).ready(function() {
    /////////////////////////////////
    // ###我的结单统计+月结单光荣榜
    /////////////////////////////////
    // 绑定事件[鼠标进入]
    $('.pige_tab_heard li').mouseenter(function() {
        // 当前元素
        var $element = $(this)

        // 移除样式
        $element.each(function(i) {
            $element.removeClass("active")
        });
        // 添加样式
        $element.addClass("active")

        // 获得目标动作
        var action = $element.attr("id")
        // 判断目标动作
        if (action == 'my_rank') {
            // 我的结单统计
            $("#my_rank_tab").show()
        } else {
            // 月结单光荣榜
            $("#all_rank_tab").show()
        }
    });
    // 绑定事件[鼠标离开]
    $('.pige_tab_heard li').mouseleave(function() {
        // 当前元素
        var $element = $(this)

        // 获得目标动作
        var action = $element.attr("id")
        // 判断目标动作
        if (action == 'my_rank') {
            // 我的结单统计
            $("#my_rank_tab").hide()
        } else {
            // 月结单光荣榜
            $("#all_rank_tab").hide()
        }
        
        // 移除样式
        $element.removeClass("active")
    });


    /////////////////////////////////
    // ###我的结单统计
    /////////////////////////////////
    // 绑定事件[点击]
    $('#my_rank').bind('click', function() {
        window.open(window.Think.APP + '/Home/Task/my_tasks/filter/all/time_range/3');
    })


    /////////////////////////////////
    // ###异步请求任务数据
    /////////////////////////////////
    ajax_index_data();
})

// 异步请求任务数据
function ajax_index_data() {
    /////////////////////////////////
    // ###设置页面高度
    /////////////////////////////////
    // 页面内容高度
    var h = document.body.clientHeight
    // 设置页面最小高度
    $("#page-wrapper").css("minHeight", (h-80) + "px");



    /////////////////////////////////
    // ###异步请求
    /////////////////////////////////
    $.ajax({
        // 提交类型
        type:       "GET",
        // 提交网址
        url:        window.Think.APP + '/Home/Task/index_data',
        // 响应数据类型
        dataType:   "json",
        // 回调函数[错误]
        error:      function(result) {
            // 显示Loading
            $.Prompt("Connection error !", "fail");
        },
        // 回调函数[成功]
        success:    function(result, status) {
            // 判断处理是否成功
            if (result.status == '0') {
                // == 错误 ==
                $.Prompt(result.message, "warn");
            } else {
                // == 成功 ==
                // 结果数据
                var data = result.data;

                // 填充数据[待办事务]
                if (data.todo_tasks.page.total > 0) {
                    var idTitle = "#idx_todo";
                    var idTab   = "#tab-1"
                    var title   = "待办事务";
                    var total   = data.todo_tasks.page.total
                    var list    = data.todo_tasks.list
                    var index   = 0

                    $(idTitle).html(title + "(" + total + ")");
                    $(idTab).attr("data-count", total);
                    parse_tasks(list, $(idTab), index);
                }
                // 填充数据[今日任务]
                if (data.today_tasks.page.total > 0) {
                    var idTitle = "#idx_today";
                    var idTab   = "#tab-2"
                    var title   = "今日任务";
                    var total   = data.today_tasks.page.total
                    var list    = data.today_tasks.list
                    var index   = 1

                    $(idTitle).html(title + "(" + total + ")");
                    $(idTab).attr("data-count", total);
                    parse_tasks(list, $(idTab), index);
                }
                // 填充数据[本周任务]
                if (data.week_tasks.page.total > 0) {
                    var idTitle = "#idx_week";
                    var idTab   = "#tab-3"
                    var title   = "本周任务";
                    var total   = data.week_tasks.page.total
                    var list    = data.week_tasks.list
                    var index   = 2

                    $(idTitle).html(title + "(" + total + ")");
                    $(idTab).attr("data-count", total);
                    parse_tasks(list, $(idTab), index);
                }
                // 填充数据[进行中]
                if (data.ing_tasks.page.total > 0){
                    var idTitle = "#idx_ing";
                    var idTab   = "#tab-4"
                    var title   = "进行中";
                    var total   = data.ing_tasks.page.total
                    var list    = data.ing_tasks.list
                    var index   = 3

                    $(idTitle).html(title + "(" + total + ")");
                    $(idTab).attr("data-count", total);
                    parse_tasks(list, $(idTab), index);
                }
                // 填充数据[我下的单]
                if (data.create_tasks.page.total > 0){
                    var idTitle = "#idx_create";
                    var idTab   = "#tab-5"
                    var title   = "我下的单";
                    var total   = data.create_tasks.page.total
                    var list    = data.create_tasks.list
                    var index   = 4

                    $(idTitle).html(title + "(" + total + ")");
                    $(idTab).attr("data-count", total);
                    parse_tasks(list, $(idTab), index);
                }
                // 填充数据[其他]
                if (data.other_tasks.page.total > 0) {
                    var idTitle = "#idx_other";
                    var idTab   = "#tab-6"
                    var title   = "其他";
                    var total   = data.other_tasks.page.total
                    var list    = data.other_tasks.list
                    var index   = 5

                    $(idTitle).html(title + "(" + total + ")");
                    $(idTab).attr("data-count", total);
                    parse_tasks(list, $(idTab), index);
                }
            }
        },
        // 回调函数[完成]
        complete:   function() {
            /////////////////////////////////
            // ###选项卡
            /////////////////////////////////
            // 遍历选项卡任务内容块
            $("div.idx-task-list").each(function(i) {
                // 存在数据进行更新
                if ($(this).attr("data-count") > 0) {
                    // 标签卡
                    $("ul.idx-tabs-heard li").removeClass("active");
                    $("ul.idx-tabs-heard li").eq(i).addClass("active");
                    // 任务内容
                    $("div.idx-task-list").removeClass("active");
                    $("div.idx-task-list").eq(i).addClass("active");
                    return false
                }
            });

            /////////////////////////////////
            // ###任务项
            /////////////////////////////////
            // 绑定事件[鼠标进入]
            $('.task-code').mouseenter(function() {
                // 移除所有选中样式
                $(this).parent().children(".task-code").each(function(i) {
                    $(this).children(".task-add-ons").removeClass("on")
                })
                // 添加当前项选中样式
                $(this).children(".task-add-ons").addClass("on")
            })
        }
    });
}

// 解析任务
function parse_tasks(tasks, target_id, tab) {
    // 项目HTML
    var item_html = ''
    
    /////////////////////////////////
    // ###生成项目HTML
    /////////////////////////////////
    $.each(tasks, function(idx, item) {
        // ###任务块 开始
        item_html += '<div class="task-code">'
        // ###任务主体 开始
        item_html += '<div class="task-main">'


        // +++链接+完成时间
        var ok_time_html
        if (is_task_overdue(item)) {
            ok_time_html = '<span class="tm task-time-overdue">' + item.ok_time_format + '</span>'
        } else if (is_task_today(item)) {
            ok_time_html = '<span class="tm task-time-today">' + item.ok_time_format + '</span>'
        } else {
            ok_time_html = '<span class="tm">' + item.ok_time_format + '</span>'
        }
        item_html += 
            '<span class="badge badge-square m-r-s15">'+ (idx + 1) +'</span>' +
            '<span class="completion-time m-r-s8">'+ ok_time_html +'</span>' +
            '<a href="' + window.Think.APP + '/Home/Task/see/id/' + item.id + '.html" class="title">';

        // +++优先级
        if (true) {
            var priority_html
            switch (item.priority_name) {
                case "紧急":
                    priority_html = '<span class="m-r-s5 task-priority-urgent">[紧急]</span>';
                    break;
                case "优先":
                    priority_html = '<span class="m-r-s5 task-priority-important">[优先]</span>';
                    break;
                default:
                    priority_html = '<span class="m-r-s5 task-priority-normal">[普通]</span>';
                    break;
            }
            item_html += priority_html
        }

        // +++任务名称
        if (is_task_end(item)) {
            item_html += "<span class='task-state-end'>" + item.name + "</span>";
        } else {
            item_html += "<span>" + item.name + "</span>";
        }
        item_html += "</a><span class='child-span-font-s18'>";
        
        // +++已分配
        if (is_task_assign(item)) {
            item_html += ' <span class="task-state-assign">(已分配)</span>';
        } 
        // +++已延单
        if (is_task_delay(item)) {
            item_html += ' <span class="task-state-delay">(接受延单)</span>';
        }
        // +++逾期
        if (is_task_overdue(item)) {
            item_html += ' <span class="task-state-overdue">(逾期)</span>';
        }

        // +++知照单/进度
        if (is_task_known(item)) {
            item_html += ' <span class="task-kind-known">(知照单)</span>';
        } else if (is_task_pause(item)) {
            item_html += ' <span class="task-state-pause">(已暂停)</span>';
        } else if (is_task_stop(item)) {
            item_html += ' <span class="task-state-stop">(已终止)</span>';
        } else if (is_task_reading(item)) {
            if (!is_task_complete(item)) {
                item_html += ' <span class="task-kind-reading">(阅读单)</span>';
                item_html += ' <span class="m-r-s10 task-state-reading">(待阅读)</span>';
            } else {
                item_html += ' <span class="task-kind-reading>(阅读单)</span>';
            }
        } else {
            var process_html
            switch (item.process_name) {
                case "待确认":
                    process_html = ' <span class="task-process-confirm">(待确认)</span>';
                    break;
                case "进行中":
                    process_html = ' <span class="task-process-ing">(进行中)</span>';
                    break;
                case "待结单":
                    process_html = ' <span class="task-process-check">(待结单)</span>';
                    break;
                default:
                    process_html = ' <span class="task-process-other">(' + item.process_name + ')</span>';
                    break;
            }
            item_html += '<span>' + process_html + '</span>';
        }

        // +++申请分配
        if (is_task_assigning(item)) {
            item_html += ' <span class="task-state-assigning">(分配任务至' + item.assign_receiver_names + ')</span>';
        }
        // +++申请延单
        if (is_task_delaying(item)) {
            item_html += ' <span class="task-state-delaying">(申请延单至' + item.delay_data.delay_time_format + ')</span>';
        }
        // +++申请转交
        if (is_task_refering(item)) {
            item_html += ' <span class="task-state-refering">(申请转交至' + item.refer_data.receiver_name + ')</span>';
        }

        // ###任务主体 结束
        item_html +='</span></span></div>' ;



        // ###任务详情 开始
        // 如果为第一条任务记录, 则默认开启详细信息, 否则关闭
        if (idx == 0) {
            item_html += '<div class="task-add-ons on">';
        } else {
            item_html +=  '<div class="task-add-ons">';
        }

        // +++下单人/分配人
        item_html +='<span class="m-r-s15"><span class="font-s10">' ;
        if (has_parent_task(item)) {
            item_html += '分配人：'
        } else {
            item_html += '下单人：'
        }

        // +++任务详情
        item_html +='</span>'+ item.sender_name +'</span>' +
            '<span class="m-r-s15"><span class="font-s10">下单时间：</span>'+ item.add_time_format +'</span>' +
            '<span class="m-r-s15"><span class="font-s10">接单人：</span>'+ item.receiver_name +'</span>' +
            '<span class="m-r-s15"><span class="font-s10">类型：</span>'+ item.kind_name +'</span>' +
            '<span class="m-r-s15"><span class="font-s10">预计工时：</span>'+ item.worktime +'小时</span>' +
            '<span class="m-r-s15"><span class="font-s10">项目：</span>'+ item.project_name +'</span>';
        
        // +++评论数量
        if (item.has_comment !== '0') {
            item_html += '<span class="m-r-s15"><i class="fa comments-icon"></i> '+ item.comment_count +'条评论</span>';
        }


        // ###任务详情 结束
        // ###任务主体 结束
        item_html += '</div></div>';
    });

    

    /////////////////////////////////
    // ###生成激励HTML
    /////////////////////////////////
    if (item_html !== "") {
        // ###激励详情 开始
        // ###激励主体 开始
        item_html += '<div class="feed-element">' +
            '<div class="media-body text-center">';

        // +++激励内容
        if (tab < 4) {
            var time_range = 0
            if (tab == 1) {
                // ###今日任务
                item_html  = '<div class="tip"><em></em>情人的嘱托（任务）搞不定？要跪键盘的哦（纳入绩效考核）！当日任务需按时完成，预计超时请提前与需求方沟通改单哦。</div>' + item_html;
                time_range = 1
            } else if (tab == 2 || tab == 3) {
                // ###周任务||进行中
                item_html  = '<div class="tip"><em></em>任务须按时完成，预计超时请提前与需求方沟通改单~ ERP逾期扣绩效1分/天/单，2016年3月1日正式生效 ！</div>' + item_html;
                time_range = 2
            }

            item_html += '<a href="' + window.Think.APP + '/Home/Task/my_tasks/time_range/' + time_range + '" class="honeycomb-status-midgray">查看我的任务<i class="fa icon-arrows"></i></a>';
        } else if (tab == 5) {
            item_html += '<a href="' + window.Think.APP + '/Home/Task/my_tasks/filter/all" class="honeycomb-status-midgray">更多相关任务<i class="fa icon-arrows"></i></a>';
        } else {
            item_html  = '<div class="tip"><em></em>不想体验情人的鸽子？那就别让他/她寂寞！下单前请充分沟通，下单后请定时了解进度提醒加快处理哦。</div>' + item_html;
            item_html += '<a href="' + window.Think.APP + '/Home/Task/my_orders" class="honeycomb-status-midgray">查看我下的单<i class="fa icon-arrows"></i></a>';
        }

        // ###激励详情 结束
        // ###激励主体 结束
        item_html +='</div></div>';
    }

    $(target_id).html(item_html);
}

// 判断任务是否分配
function is_task_assign(task) {
    return (parseInt(task.is_assign) === 1)
}
// 判断任务是否正在分配
function is_task_assigning(task) {
    return (parseInt(task.is_assigning) === 1)
}
// 判断任务是否逾期
function is_task_overdue(task) {
    return (parseInt(task.is_overdue) === 1)
}
// 判断任务是否转交
function is_task_refer(task) {
    return (parseInt(task.is_refer) === 1)
}
// 判断任务是否正在转交
function is_task_refering(task) {
    return (parseInt(task.is_refering) === 1)
}
// 判断任务是否延期
function is_task_delay(task) {
    return (parseInt(task.is_delay) === 1)
}
// 判断任务是否正在延期
function is_task_delaying(task) {
    return (parseInt(task.is_delaying) === 1)
}
// 判断任务是否暂停
function is_task_pause(task) {
    return (parseInt(task.is_pause) === 1)
}
// 判断任务是否停止
function is_task_stop(task) {
    return (parseInt(task.is_stop) === 1)
}
// 判断任务是否完成
function is_task_complete(task) {
    return (parseInt(task.is_complete) === 1)
}
// 判断任务是否拒绝
function is_task_refuse(task) {
    return (parseInt(task.is_refuse) === 1)
}
// 判断任务是否结束
function is_task_end(task) {
    return (parseInt(task.is_end) === 1)
}
// 判断任务是否知照单
function is_task_known(task) {
    return (parseInt(task.is_known) === 1)
}
// 判断任务是否阅读单
function is_task_reading(task) {
    return (parseInt(task.is_reading) === 1)
}
// 判断任务是否有父任务
function has_parent_task(task) {
    return (parseInt(task.parent_id) !== 0)
}
// 判断任务是否是今天
function is_task_today(task) {
    return (parseInt(task.is_today) === 1)
}