
/**
 * [readAjax 使用jquery自带的$.ajax()方法请求数据]
 * @param  {[type]} url        [请求数据路径]
 * @paran  {[type]} type       [请求方式-get/post]
 * @param  {[type]} data       [参数]
 * @param  {[type]} datatype   [数据类型]
 * @param  {[type]} successTo  [请求成功回调函数]
 * @return {[type]}            [响应数据]
 */
var readAjax=function(url,type,data,dataType,successfn,loadingfn) {
	data = (data == null || data == "" || data == undefined) ? "" : data ;
	$.ajax({
		type: type,
		url: url,
		data: data,
		async:true,
		dataType: dataType,
		success:successfn,
		beforeSend: loadingfn,
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(textStatus);
			console.log(errorThrown);
		},
		complete:function(XMLHttpRequest,status){
		}
	});
}



/*
 * 最近登录时间差
 */
$('.timeago').text(moment("20160610", "YYYYMMDD").fromNow());



/**
 * [balloon description 确认是否进行某操作]
 * @param  {[type]} word     [确认信息]
 * @return {[type]}          [description]
 */
var balloon_check = function(word,sub){
    $(".fn-alert").remove();
    var prompt_check = '<div class="fn-alert">'
                            +'<div class="alert alert-warning alert-dismissable g-t-center">'
                            +'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'
                            +'<p>'
                            +'<i class="icon fa fa-ban"></i>'+word
                            +'<button class="btn btn-danger btn-sm " id="checkSubmit" type="submit" style="margin-left:5px;margin-right:5px;">'+sub+'</button>'
                            +'<button class="btn btn-default btn-sm" type="button" data-dismiss="alert" aria-hidden="true">取消</button>'
                            +'</p>'
                            +'</div>'
                            +'</div>';
    $(".content-wrapper").prepend(prompt_check);
}



/**
 * [balloon description 成功或失败提示框]
 * @param  {[type]} bal_type [提示框类型]
 * @param  {[type]} fa_icon  [文字信息前的图标]
 * @param  {[type]} word     [显示文字信息]
 * @return {[type]}          [description]
 */
var balloon = function(bal_type,fa_icon,word){
    $(".fn-alert").remove();
    var prompt = '<div class="fn-alert">'
                            +'<div class="alert '+bal_type+' warning alert-dismissable g-t-center">'
                            +'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'
                            +'<div>'
                            +'<i class="icon fa '+fa_icon+'"></i>'
                            +'<span>'+word+'</span>'
                            +'</div>'
                            +'</div>'
                            +'</div>';
    $(".content-wrapper").prepend(prompt);
}