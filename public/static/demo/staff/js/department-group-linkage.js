(function($){
    $(".ajaxWebDepartments").each(function(){
        // ###操作数据
        var $this       = $(this);
        var $department = $("[name='department_id']", $this);
        var $group      = $("[name='group_id']", $this);
        var ajaxURL     = $this.attr("url");


        // ###重置数据
        $department.empty();
        $group.empty();


        // ###异步请求
        $.get(ajaxURL, 'json').done(function(data) {
            // ###结果数据
            var data    = data.data;
            var length  = data.length;

            // ###处理数据
            // 填充部门
            for (var i = 0; i < length; i++) {
                if (data[i].checked) {
                    $department.append("<option value='" + data[i].id + "' selected>" + data[i].name + "</option>")
                } else {
                    $department.append("<option value='" + data[i].id + "'>" + data[i].name + "</option>")
                }
            }

            // 填充组别
            if ($department.val() == "") {
                $group.append("<option value=''>" + data[0].child + "</option>")
            } else {
                for (var x in data) {
                    if (data[x].id == $department.val()) {
                        var groupData   = data[x].child;
                        var groupLength = data[x].child.length;
                        if (groupLength == 0) {
                            $group.append("<option value=''>请选择组别</option>")
                        } else {
                            if (groupData.checked) {
                                $group.append("<option value=''>请选择组别</option>")
                                for (var i = 0; i < groupLength; i++){
                                    $group.append("<option value='" + groupData[i].id + "'>" + groupData[i].name + "</option>")
                                }
                            } else {
                                for (var i = 0; i < groupLength; i++){
                                    if (groupData[i].checked) {
                                        $group.append("<option value='" + groupData[i].id + "' selected>" + groupData[i].name + "</option>")
                                    } else {
                                        $group.append("<option value='" + groupData[i].id + "'>" + groupData[i].name + "</option>")
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $department.unbind('change').change(function() {
                // ###重置数据
                $group.empty();
                
                // ###处理数据
                if ($department.val() == "") {
                    $group.append("<option value=''>" + data[0].child + "</option>")
                } else {
                    for (var x in data) {
                        if (data[x].id == $department.val()) {
                            var groupData   = data[x].child;
                            var groupLength = data[x].child.length;
                            if (groupLength == 0) {
                                $group.append("<option value=''>请选择组别</option>")
                            } else {
                                for (var i = 0; i < groupLength; i++) {
                                    $group.append("<option value='" + groupData[i].id + "'>" + groupData[i].name + "</option>")
                                }
                            }
                        }
                    }
                }
            })
        }).error(function(){
            console.log("下拉框数据加载出错！");
        })
    })
})(jQuery);