// 绑定事件[讨论名称]
$("input[name='name']").blur(function() {
    warning(this);
});
// 绑定事件[讨论介绍]
$("textarea[name='info']").blur(function() {
    warning(this);
});

// 提交验证
function submit_check(obj) {
    ///////////////
    // 验证数据
    ///////////////
    // 验证字段[讨论名称]
    if ($.trim($(obj).find("input[name='name']").val()) == '') {
        $.Prompt("讨论标题必须填写");
        $(obj).find("input[name='name']").addClass("warning-form");
        return false;
    } else {
        $(obj).find("input[name='name']").removeClass("warning-form");
    }
    if ($.trim($(obj).find("input[name='name']").val()).length > 32 || $.trim($(obj).find("input[name='name']").val()).length < 3) {
        $.Prompt("讨论标题长度过短（3-32个字符）");
        $(obj).find("input[name='name']").focus();
        return false
    }

    // 验证字段[讨论介绍]
    if ($.trim($(obj).find("textarea[name='info']").val()) == '') {
        $.Prompt("讨论内容必须填写");
        $(obj).find("textarea[name='info']").addClass("warning-form");
        return false;
    } else {
        $(obj).find("textarea[name='info']").removeClass("warning-form");
    }
    if ($.trim($(obj).find("textarea[name='info']").val()).length < 10) {
        $.Prompt("讨论内容长度过短（少于10个字符）");
        $(obj).find("textarea[name='info']").focus();
        return false
    }



    ///////////////
    // 加工数据
    ///////////////
    // 获得所有标签ID，并存储至临时集合[ids]中
    var ids = [];
    $("select[name='tags'] option").each(function(){
        $option = $(this);
        ids.push($option.attr("value"))
    })

    // 筛选已选择的标签ID，并存储到临时集合[ids_selected]中
    var ids_selected = [];
    $(".chosen-choices a.search-choice-close").each(function(){
        var index = $(this).attr("data-option-array-index") * 1;
        ids_selected.push(ids[index])
    })

    // 设置页面隐藏字段[tags_selected]
    $("[name='tags_selected']").val(ids_selected.join("|"))

    // 验证成功
    return true;
}

// 回复意见反馈
function topic_reply() {
    if ($.trim($("textarea[name='info']").val()) == '') {
        $.Prompt("评论内容不能为空", "warn");
        $("textarea[name='info']").addClass("warning-form");
        return false;
    } else {
        $("textarea[name='info']").removeClass("warning-form");
    }
    return true;
}