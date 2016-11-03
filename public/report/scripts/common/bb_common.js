var isIDevice = /(iPhone|iPod|iPad)/i.test(navigator.userAgent),
	isiPad = /(iPad)/i.test(navigator.userAgent),
 	isAndroid = !isIDevice && (/android/gi).test(navigator.userAgent),
	dsn=undefined;//设备号



/**
 * [jsonAjax ajax封装]
 * @param  {[type]} url       [请求url]
 * @param  {[type]} type      [请求方式-get/post]
 * @param  {[type]} data      [请求参数]
 * @param  {[type]} dataType  [参数的数据类型]
 * @param  {[type]} successfn [请求成功调用函数]
 * @param  {[type]} loadingfn [发送请求前调用函数]
 * @return {[type]}           [响应数据]
 */

function jsonAjax(url, type, data, dataType, successfn, loadingfn) {
	data = (data == null || data == "" || typeof(data) == "undefined") ? "" : data;
	$.ajax({
		type: type,
		url: url,
		data: data,
		async: true,
		dataType: dataType,
		success: successfn,
		beforeSend: loadingfn,
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(textStatus);
			console.log(errorThrown);
		},
		complete:function(XMLHttpRequest,status){
			
		}
	});
}

/**
 * [BridgeOc js->OC桥接]
 * @param {Function} callback [description]
 */
function BridgeOc(callback) {
if (window.WebViewJavascriptBridge) {
    callback(WebViewJavascriptBridge);
} else {
  document.addEventListener('WebViewJavascriptBridgeReady',function() {
    callback(WebViewJavascriptBridge);
  },false);
}
}
//调用iOS桥接初始化
BridgeOc(function(bridge) {
  bridge.init(function(message, responseCallback) {
  var data = {
   'js请求响应': '初始化'
  };
  responseCallback(data);
  });
});
//这个方法用于js接收oc的callHandler，handler用一个key标记，可以注册多个handler
BridgeOc(function(bridge) {
  bridge.registerHandler(function(responseCallback) {
 	responseCallback(responseData);
 });
});
/**
 * [removeEle 移除一个元素]
 * @param  {[type]} selector [选择器名称]
 * @return {[type]}          [description]
 */
function removeEle(selector){
	$(selector).remove();
}
/**
 * [_addClass 添加类]
 * @param {[type]} selector [选择器名称]
 * @param {[type]} value    [添加的类名]
 */
function _addClass(selector,value){
	$(selector).addClass(value);
}
/**
 * [_removeClass 删除类]
 * @param  {[type]} selector [选择器名称]
 * @param  {[type]} value    [删除的类名]
 * @return {[type]}          [description]
 */
function _removeClass(selector,value) {
	$(selector).removeClass(value);
}
/**
 * [_add_removeClass 为某一元素添加一个类并删除一个类]
 * @param {[type]} selector  [选择器名称]
 * @param {[type]} addval    [添加的类名]
 * @param {[type]} removeval [删除的类名]
 */
function _add_removeClass(selector,addval,removeval){
	$(selector).addClass(addval).removeClass(removeval);
}
/**
 * [_add_removeClassArr 为某一元素数组添加一个类并删除一个类]
 * @param {[type]} arr [数组名称]
 */
function _add_removeClassArr(arr) {
	for(i in arr) {
		$(arr[i].selector).addClass(arr[i].addClass).removeClass(arr[i].removeClass);
	}
}

/**
 * [setCss 设置css]
 * @param {[type]} selector [选择器名称]
 * @param {[type]} attr     [属性名]
 * @param {[type]} value    [属性值]
 */
function setCss(selector,attr,value) {
	$(selector).css(attr,value);
}


function swiperAnimate() {
    clearSwiperAnimate();
}
function clearSwiperAnimate() {
    $('.ain').addClass('hide');
}
/**
 * [progressbar 进度条]
 * @param  {[type]} index       [当前swiper的索引号]
 * @param  {[type]} slideDir    [滚动方向]
 * @param  {[type]} percentinit [初始化的百分比]
 * @return {[type]}             [description]
 */
function progressbar(index,slideDir,percentinit){
	var progressbarWidth=parseInt($('.progressbar>span').width()),
		progressWidth=parseInt($('.progress').width()),
		percentCur=(progressbarWidth/progressWidth)*100;
	if(slideDir>0){//向下滑动
		setCss('.progressbar>span','width',(percentCur+percentinit)+"%");
	}else if(slideDir<0){//向下滑动
		setCss('.progressbar>span','width',(percentCur-percentinit)+"%");
	}
	if(index==($("section").length-1)){
		$("#next").hide();
		setCss('.progressbar>span','width',100+"%");
	}else {
		$("#next").show();
	}

}
/**
 * [initpageOne 开场动画]
 * @return {[type]} [description]
 */
function initpageOne(param){
	switch (param){
		case 'week':
			_removeClass('.week .page1 .cloud_r span','hide');
			_removeClass('.week .page1 .cloud_r','mc');
			_add_removeClass('.week .page1 .block,.week .page1 .cloud_l,.week .page1 .cloud_r','bounceInDown','hide');
	  		setCss('.week .page1 .avatar','z-index',1);
			
			setTimeout(function(){
		    	_add_removeClass('.week .page1 .avatar','bounceInUp','hide');
		    	_add_removeClass('.week .page1 .cloud_r','mc','hide');

		  	},1000);
		  	setTimeout(function(){
		  		arrAnimation=[{"selector":".week .page1 .ain","addClass:":"","removeClass":"hide"}];
		  		_add_removeClassArr(arrAnimation);
		  		setCss('.week .page1 .avatar','z-index',2);
		  	},1900);
	  	break;
	  	case 'month':
	  		_removeClass('.month .page1 .block_l','mc');
	  		_add_removeClass('.month .page1 .block_l,.month .page1 .block_r,.month .page1 .block_c','bounceInDown','hide');

            setTimeout(function(){
                _add_removeClass('.month .page1 .block_l','mc','hide');
                arrAnimation=[{"selector":".month .page1 .wave,.month .page1 .2016","addClass":"bounceInDown","removeClass":"hide"}];
                _add_removeClassArr(arrAnimation);
            },1000);

	  	break;
	  	default:
	  	break;
	}
}

/**
 * [adaptScreen 屏幕尺寸适配]
 * @type {[type]}
 */
function adaptScreen() {
	var	scaleW=window.innerWidth/2208;
		scaleH=window.innerHeight/1242;
		resizes = document.querySelectorAll('.resize');
	for (var j=0; j<resizes.length; j++) {
	   resizes[j].style.width=parseInt(resizes[j].style.width)*scaleW+'px';
	   resizes[j].style.height=parseInt(resizes[j].style.height)*scaleH+'px';
	   resizes[j].style.fontSize=parseInt(resizes[j].style.fontSize)*scaleH+'px';
	   resizes[j].style.lineHeight=parseInt(resizes[j].style.lineHeight)*scaleH+'px';
	   resizes[j].style.top=parseInt(resizes[j].style.top)*scaleH+'px';
	   resizes[j].style.left=parseInt(resizes[j].style.left)*scaleW+'px'; 
  }
}
/**
 * [timeTrans 时间转换]
 * 示例:70分钟->01:10:00;60分钟->00:60:00
 * @return {[type]} []
 */
function timeTrans(timeOri){
	var timeTrans=0,
    	timeTrans_h,
    	timeTrans_m;
    if(timeOri<0){
    	timeOri=0;
	} 	
	if(timeOri>60){
		timeTrans_h=parseInt(timeOri/60);
		timeTrans_m=timeOri%60;
		if(timeTrans_h<10){
			timeTrans_h="0"+timeTrans_h;
		}
		if(timeTrans_m<10){
			timeTrans_m="0"+timeTrans_m;
		}
		timeTrans=timeTrans_h+":"+timeTrans_m+":"+"00";
	}else {
		if(timeOri<10){
			timeTrans="00:0"+timeOri+":00";
		}else {
			timeTrans="00:"+timeOri+":00";
		}
	}
	return timeTrans;
}
/**
 * [DayNumOfMonth 获取上月天数]
 * @param {[type]} year  [当前年份]
 * @param {[type]} month [上月月份]
 */
function DayNumOfMonth(year,month){
	var  day = new Date(year,month,0);
	var daycount = day.getDate();
	return daycount;
}
/**
 * [getMonthWeek  获取当前月有几周]
 * @return {[type]} [select-option]
 */

function getMonthWeek() {
	var today = new Date(),
		last = new Date(today.getFullYear(),today.getMonth()+1,0),//获取当前月最后一天时间
		y = last.getYear(),
		m = last.getMonth()+1,
		d = last.getDate();

 	var date = new Date(y,parseInt(m),d),
 		w = date.getDay(),
 		e = date.getDate();
 	var weekCount=Math.ceil((e+6-w)/7),
 		html=undefined,
 		num=undefined;
 	for(var i=0;i<weekCount;i++){
 		switch (i){
 			case 0:num="一周";break;
 			case 1:num="二周";break;
 			case 2:num="三周";break;
 			case 3:num="四周";break;
 			case 4:num="五周";break;
 			case 5:num="六周";break;
 			default:break;
 		}
 		html+="<option value='"+i+"'>";
 		html+=date.getMonth()+"月第"+num;
 		html+="</option>";
 	}
 	$("select[name='sWeek']").empty().append(html);
}
/**
 * [注册点击app下载事件] 
 * @param  {[type]} ){} [description]
 * @return {[type]}     [description]
 */
$(".download").each(function(){
	$(this).unbind('click').click(function(){
		if(isIDevice){
			var downData={}
		}else if(isAndroid){
			var downData={}
		}
		allOpenApp(downData);
	});
});
/**
 * [allOpenApp 下载app]
 * @param  {[type]} param [下载app所需参数]
 * @return {[type]}       [description]
 */
function allOpenApp(param){

}
/**
 * [注册点击分享事件]
 * @param  {[type]} )[description]
 * @return {[type]}  [description]
 */
$('.btn-share').each(function(){
	$(this).unbind("click").click(function(){
		var target=$(this).find("img").attr('data-tag');
		console.log(target);
		openShare(target);
	});
});

/**
 * [openShare 外部分享]
 * @param [分享参数] 
 * @return {[type]} [description]
 */
function openShare(param){
	if(isIDevice) {
		//js调用oc方法
		// BridgeOc(function(bridge){
		// bridge.callHandler('','',function(response){
		// 		dsn=response;
		// 	});
		// });
	}else if(isAndroid){
		//js调用android方法
		// dsn=activity.;
	}
}

/**
 * [setImgArr :设置需要本地缓存的图片数组]
 * 注意：图片按数组顺序存入localStroage,
 * 		 使用name="cacheswitch"属性作为标识
 * [getImgArr :将缓存图片取出，放入数组中]		 
 * @type {Array}
 */

var setImgArr = [],
	getImgArr = [];
/**
 * [getImgSrc 获取页面中有标识的图片的src,添加到本地缓存数组中,并从本地缓存中取出赋值给img src]
 * 
 */
getImgSrc();
function getImgSrc() {
	$('img').each(function() {
		var attr=$(this).attr("name");
		if(attr=="cacheImg") {
			setImgArr.push($(this).attr("src"));
		}
	});

	for(i in setImgArr) {
		setImg(setImgArr[i]);//设置图片缓存
		getImg(setImgArr[i]);//获取图片缓存
	}
	
	// var attrCache=document.getElementsByName("cacheImg");
	// for(var i=0;i<attrCache.length;i++) {
	// 	attrCache[i].src=getImgArr[i];
	// }
}

/**
 * [setImg 设置本地图片缓存]
 * @return {[type]} [description]
 */
function setImg(key) {
	var img=document.createElement('img');
	img.addEventListener('load',function(){
		//创建一个canvas
		var imgCanvas=document.createElement("canvas"),
			imgContext=imgCanvas.getContext("2d");
		//确保canvas元素的大小和图片尺寸一致
		imgCanvas.width=this.width;
		imgCanvas.height=this.height;
		//渲染图片到canvas中，使用canvas的drawImage()方法
		imgContext.drawImage(this,0,0,this.width,this.height);
		//用canvas的dataUrl的形式取出图片，imgAsDataURL是一个base64字符串
		var imgAsDataURL=imgCanvas.toDataURL("image/png");
		//保存到本地存储中
		//使用try-catch()查看是否支持localstorage
		try {
			localStorage.setItem(key,imgAsDataURL);//将取出的图片存放到localStorage
		}catch(e) {
			console.log("Storage failed:"+e);//存储失败
		}
	},false);
	img.src=key;
}
/**
 * [getImg 从本地缓存获取图片并且渲染]
 * @param  {[type]} key [localStorage中的键名称]
 * @return {[type]}     [存储值-base64的字符串]
 */

function getImg(key) {
	if(localStorage.getItem(key)==null){
		return false;
	}else {
		if(window.location.pathname.indexOf('/month')>0) {
			if(localStorage.getItem(key)==null||localStorage.getItem(key)==undefined||localStorage.length==0){
				return false;
			}
		}else {
			var srcStr = localStorage.getItem(key);//从localStorage中取出图片
			getImgArr.push(srcStr);
			var attrCache=document.getElementsByName("cacheImg");
			for(var i=0;i<attrCache.length;i++) {
				attrCache[i].src=getImgArr[i];
			}
		}
	}
}

function removeImg(key) {
	localStorage.removeItem(key);
}



(function(){
/**
 * [filePreLoad 预加载资源图片封装]
 * @param  {[type]} obj [description]
 * @return {[type]}     [description]
 * 调用方法：
 	preload({
		files : [],
		progress : function(precent, currentImg) {
			具体代码
		},
		complete : function() {
			具体代码
		}
 	})
 */
	function filePreLoad(obj) {
		this.files = obj.files;
		this.progress = obj.progress;
		this.complete = obj.complete;
		// 当前加载数量为0
		this.current = 0;
		// 容器设置
		this.box = document.createElement('div');
		this.box.style.cssText = 'overflow:hidden; position: absolute; left: -9999px; top: 0; width: 1px; height: 1px;';
		document.body.appendChild(this.box);
		this.getFiles();
	}

	// 获取每一个图片
	filePreLoad.prototype.getFiles = function() {
		var fileArr = [];
		for (var i = 0; i < this.files.length; i++) {
			fileArr[i] = this.files[i];
			this.loadImg(fileArr[i]);
		};
	}

	// 加载图像
	filePreLoad.prototype.loadImg = function(file) {
		var _this = this;
		var newImg = new Image();
		newImg.onload = function(){
			newImg.onload = null;
			_this.loadFtn(newImg);
		}
		newImg.src = file;
	}

	// 执行相关回调
	filePreLoad.prototype.loadFtn = function(currentImg) {
		this.current++;
		this.box.appendChild(currentImg);
		if (this.progress) {
			var precentage = parseInt(this.current / this.files.length * 100);
			this.progress(precentage, currentImg);
		};
		if (this.current == this.files.length) {
			if (this.complete) {
				this.complete();
			};
		};
	}

	function preload(obj) {
		return new filePreLoad(obj);
	}
	
	window.preload = preload;
})();
