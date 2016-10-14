$(function () {
	$("#index-content .tab-pane").height($("body").height()-160);
	// moment.locale("zh-cn")
	// $("#moment").text(moment("2016-07-09 00:30", "YYYY-MM-DD hh:mm").startOf('hour').fromNow());
});

//关闭当前标签
function close_page(){
	var $menu=$("#index-menu>li[class != 'pull-right']");
	var $thisMenu=$("#index-menu>li.active");
	var $thisContent=$("#index-content>.tab-pane.active");
	var thisIndex=$("#index-menu>li.active").index();
	
	if ($thisMenu.find("a").attr("href") != "#tab_home" && $thisMenu.find("a").text().trim() != "首页") {
		$menu.eq(thisIndex-3).find("a").click();
		$thisMenu.remove();
		$thisContent.remove();
		if ($menu.length == 2) {
			$("#closeMenu").remove();
		}
	}
	
}

function refresh_page(){
	$(".tab-content>.tab-pane.active>iframe").attr("src",$(".tab-content>.tab-pane.active>iframe").attr("src"))
}


/**
 * [openapp 打开新页面]
 * @param  {[string]}  	url 		[打开的链接]
 * @param  {[int]} 		appid		[链接的id]
 * @param  {[string]} 	appname 	[对应的名称]
 * @param  {[bool]} 	refresh 	[是否刷新]
 */
function openapp(url, appid, appname, refresh) {
	
	var contentHeight=$("#index-content .tab-pane").height();
	var $menu=$("#index-menu");
	var $content=$("#index-content");
	
	//添加标签关闭按钮
    if ($("#index-menu>li[id!='closeMenu']").length < 3) {
    	$menu.append("<li id='closeMenu' class='pull-right'><a href='javascript:close_page();' class='text-muted'><i class='fa fa-times'></i></a></li>")
    }
	//添加新页面
    var $app = $("#index-menu>li>a[href='#appid"+appid+"']");
    if ($app.length == 0) {
    	var menuHtml="<li class=''><a href='#appid"+appid+"' data-toggle='tab' aria-expanded='false'>"+appname+"</a></li>"
    	$menu.append(menuHtml);
    	
    	var contentHtml="<div class='tab-pane' style='height:"+contentHeight+"px' id='appid"+appid+"'>"
						+	"<iframe src='"+url+"' frameborder='0' id='appiframe-0' class='appiframe'></iframe>"
                  		+"</div>";
    	$content.append(contentHtml);
    	
		var $app = $("#index-menu>li>a[href='#appid"+appid+"']");
		$app.click()
    }else{
    	$app.click()
    }
    
    
}

