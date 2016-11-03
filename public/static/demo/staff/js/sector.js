$(document).ready(function() {
    var pie_val = {
        'new': $("#new_count").val(),
        'ing': $("#ing_count").val(),
        'pige': $("#pige_count").val(),
        'done': $("#done_count").val(),
        'all': $("#all_count").val(),
        'overdue': $("#overdue_count").val()
    };
    var elm = {
        'pie-main': 'dep-pie-main',
        'pie_done_pro': 'dep_pie_finish_pro',
        'pie_overdue_pro': 'dep_pie_overdue_pro'
    };
    task_pie(pie_val, elm)
})


function task_pie(val,elm){
    var index_pie_main,index_pie_finish_pro,index_pie_overdue_pro;
    if(val['all']==0){
        index_pie_main = [{value: 1,color: "#d9d9d9",highlight: "#d1d1d1"}];
        index_pie_finish_pro = [{value: 1,color: "#d9d9d9",highlight: "#d1d1d1"}];
        index_pie_overdue_pro = [{value: 1,color: "#d9d9d9",highlight: "#d1d1d1"}];
    }else {
        index_pie_main = [{value: val['new'], color: "#44a2de", highlight: "#2697d9", label: "待确认"},
            {value: val['ing'], color: "#47c54a", highlight: "#3bba3f", label: "进行中"},
            {value: val['pige'], color: "#b2d32f", highlight: "#9dbd28", label: "待结单"},
            {value: val['done'], color: "#d9d9d9", highlight: "#d1d1d1", label: "已完成"}];

        index_pie_finish_pro = [{value:val['all']-val['done'],color:"#d9d9d9",highlight: "#d1d1d1"},
            { value:val['done'] ,color: "#f79d01", highlight: "#e49101"}];

        index_pie_overdue_pro = [{value: val['all']-val['overdue'],color: "#d9d9d9",highlight: "#d1d1d1"},
            {value: val['overdue'],color:"#e1384e",highlight: "#d5203b"}];
    }


    new Chart(document.getElementById(elm["pie-main"]).getContext("2d")).Doughnut(index_pie_main, {responsive: true});
    new Chart(document.getElementById(elm["pie_done_pro"]).getContext("2d")).Doughnut(index_pie_finish_pro, {responsive: true});
    new Chart(document.getElementById(elm["pie_overdue_pro"]).getContext("2d")).Doughnut(index_pie_overdue_pro, {responsive: true});

}

function dep_op(obj){
    val= $("#pie_panel").css('display')
    if(val=="none" || val==undefined){
        $("#pie_panel").css('display',"block");
        $(obj).removeClass("pie-bnt-bg");
        $(obj).html("<em class='up'></em>");
    }else{
        $("#pie_panel").css('display',"none");
        $(obj).addClass("pie-bnt-bg");
        $(obj).html("查看图表统计<em class='down'></em>");
    }
}