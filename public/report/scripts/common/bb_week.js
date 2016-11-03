/**
	 * [onload 页面初始化]
	 * @return {[type]} [description]
	 */
	
	window.onload=function(){
		//获取设备号-getDsn
		// if(isIDevice){
		// 	//js调用oc方法
		// 	BridgeOc(function(bridge){
		// 	bridge.callHandler('getDsn','',function(response){
		// 			dsn=response;
		// 		});
		// 	});
		// }else if(isAndroid){
		// 	//js调用android方法
		// 	dsn=activity.getDsn();
		// }
		//请求数据接口
		// jsonAjax("./week.json","get","","json",showData);
		jsonAjax(url,"get","","json",showData);
		loading();
	}
	/**
	 * [loading 加载]
	 * @return {[type]} [description]
	 */
	function loading(){
		setTimeout(function(){
			removeEle('.loading');
			$('#swiper').show();
			getMonthWeek();
			adaptScreen();
        	init();
      	},1000);
      	return;
	}
	/**
	 * [init 初始化]
	 * @return {[type]} [description]
	 */
	var init=function(){
		var arrAnimation=[];//动画操作数组
		var percentinit=(1/$("section").length)*100;
		setCss('.progress .progressbar span','width',percentinit+"%");//初始化进度条百分比

		var mySwiper = new Swiper ('.swiper-container', {//实例化swiper配置项
	    direction: 'vertical',
	    onInit:function(swiper) {
	  		initpageOne('week');//初始化开场动画
	    },
		onTransitionEnd:function(swiper){
				swiperAnimate();
		    	//progerssbar
		    	var slideDir=parseInt(swiper.activeIndex)-parseInt(swiper.previousIndex),
		    	 	index=swiper.activeIndex;
		    	progressbar(index,slideDir,percentinit);
		    	//动画控制
		    	switch (index){
		    		case 0:
			     	initpageOne('week');
		    		break;
		    		case 1:
		    		//动画操作
		    		arrAnimation=[
		    		{"selector":".week .page2 .title1","addClass":"fadeInLeft","removeClass":"hide"},
		    		{"selector":".week .page2 .title2","addClass":"fadeInRight","removeClass":"hide"},
		    		{"selector":".week .page2 .avatar,.week .page2 .plane","addClass":"bounceIn","removeClass":"hide"},
		    		{"selector":".week .page2 .phone,.week .page2 .time","addClass":"swing","removeClass":"hide"},
		    		{"selector":".week .page2 .ain","addClass:":"","removeClass":"hide"}
		    		];
		    		_add_removeClassArr(arrAnimation);

		    		break;
		    		case 2:
		    		//动画操作
		    		arrAnimation=[	
		    			{"selector":".week .page3 .title-l,.week .page3 .title-r","addClass":"bounceInDown","removeClass":"hide"},
		    			{"selector":".week .page3 .appitem","addClass":"fadeInDown","removeClass":"hide"},
		    			{"selector":".week .page3 .ain","addClass:":"","removeClass":"hide"}
		    		];
		    		_add_removeClassArr(arrAnimation);
		    		break;
		    		case 3:
		    		//动画操作
		    		arrAnimation=[
		    			{"selector":".week .page4 .appicon,.week .page4 .appname","addClass":"fadeInUpBig","removeClass":"hide"},
		    			{"selector":".week .page4 .rank1","addClass":"bounceInDown","removeClass":"hide"},
		    			{"selector":".week .page4 .rank2","addClass":"bounceInDown2","removeClass":"hide"},
		    			{"selector":".week .page4 .rank3","addClass":"bounceInDown3","removeClass":"hide"},
		    			{"selector":".week .page4 .ain","addClass:":"","removeClass":"hide"}
		    		];
		    		_add_removeClassArr(arrAnimation);
		    		break;
		    		case 4:
		    		arrAnimation=[
		    			{"selector":".week .page5 .rocket","addClass":"bounceOutUp","removeClass":"hide"},
		    			{"selector":".week .page5 .icon","addClass":"bounceInLeft","removeClass":"hide"},
		    			{"selector":".week .page5 .babybus","addClass":"bounceInDown","removeClass":"hide"},
		    			{"selector":".week .page5 .title","addClass":"tada","removeClass":"hide"},
		    			{"selector":".week .page5 .ain","addClass:":"","removeClass":"hide"}
		    		];
		    		_add_removeClassArr(arrAnimation);
		    		_addClass('.qborder','flash');

		    		setTimeout(function(){
		    			removeEle('.rocket');
		    		},1500);
		    		break;
		    	}
		    },
			onSetTransition: function(swiper, speed) {
		        for (var i = 0; i < swiper.slides.length; i++){
		          es = swiper.slides[i].style;
				  es.webkitTransitionDuration = es.MsTransitionDuration = es.msTransitionDuration = es.MozTransitionDuration = es.OTransitionDuration = es.transitionDuration = speed + 'ms';
		        }
	      	},
	      	watchSlidesProgress : true,
			onProgress: function(swiper, progress){
				for (var i = 0; i < swiper.slides.length; i++){
		          var slide = swiper.slides[i],
		           	  progress = slide.progress,
		           	  translate = progress*swiper.height/4;  
				  scale = 1 - Math.min(Math.abs(progress * 0.5), 1);
		          var opacity = 1 - Math.min(Math.abs(progress/2),0.5);
		          slide.style.opacity = opacity;
				  es = slide.style;
				  es.webkitTransform = es.MsTransform = es.msTransform = es.MozTransform = es.OTransform = es.transform = 'translate3d(0,'+translate+'px,-'+translate+'px) scaleY(' + scale + ')';

		        }
			}
		});
		/**
		 * [nextClick 向下按钮点击操作]
		 * @return {Function} [description]
		 */
		$('#next').click(function(){
			var index=mySwiper.activeIndex;
			mySwiper.slideTo(index+1,1000,true);
		});
	}
	
	//监听下拉列表选择-周
    $('.week .page1 select').change(function(){
    	var val=$(this).val();
        var txt=$(this).find("option:selected").text();
        $('.week .page1 .select-item').text(txt);
         //请求数据
        jsonAjax("./week.json","get","","json",showData);
        return false;
    });
    //监听下拉列表选择-年龄段
    $('.week .page4 select').change(function(event){
        var val=$(this).val();
        var txt=$(this).find("option:selected").text();
        $('.week .page4 .btn-age span').text(txt);
        //请求数据
        jsonAjax("./week_age.json","get","","json",showData);
        return false;
    });
    /**
     * [showData 展示数据]
     * @return {[type]} [description]
     */
    function showData(data) {
    	var dataList=data.data;
    	// page2-获取学习时间
    	$('.page2 .edit_area .title1 span').text(dataList.time);
    	$('.page2 .edit_area .title2 span').text(dataList.percent);
    	var timeOri=dataList.time;
    	$('.page2 .edit_area .time').text(timeTrans(timeOri));
    	//page3-宝宝本周最爱
    	var appIcon_like=[],
    		appName_like=[];
    	for(var i=0;i<dataList.like.length;i++){
    		appIcon_like[i]=dataList.like[i].img;
    		appName_like[i]=dataList.like[i].name;
    	}

    	switch (dataList.like.length){
    		case 1:
    		$('.page3 .edit_area .like1 .appicon').attr("src",appIcon_like[0]);
    		$('.page3 .edit_area .like1 .appname').text(appName_like[0]);
    		$('.page3 .edit_area .like2,.page3 .edit_area .like3').hide();
    		break;
    		case 2:
    		$('.page3 .edit_area .like1 .appicon').attr("src",appIcon_like[0]);
    		$('.page3 .edit_area .like1 .appname').text(appName_like[0]);
    		$('.page3 .edit_area .like2 .appicon').attr("src",appIcon_like[1]);
    		$('.page3 .edit_area .like2 .appname').text(appName_like[1]);
    		$('.page3 .edit_area .like3').hide();
    		break;
    		case 3:
    		$('.page3 .edit_area .like1 .appicon').attr("src",appIcon_like[0]);
    		$('.page3 .edit_area .like1 .appname').text(appName_like[0]);
    		$('.page3 .edit_area .like2 .appicon').attr("src",appIcon_like[1]);
    		$('.page3 .edit_area .like2 .appname').text(appName_like[1]);
    		$('.page3 .edit_area .like3 .appicon').attr("src",appIcon_like[2]);
    		$('.page3 .edit_area .like3 .appname').text(appName_like[2]);
    		$('.page3 .edit_area .like2,.page3 .edit_area .like3').show();
    		break;
    		default:
    		break;
    	}
    	//page3-猜您宝宝喜欢
    	var appIcon_guess=[],
    	 	appName_guess=[];
    	for(var i=0;i<dataList.guess.length;i++){
    		appIcon_guess[i]=dataList.guess[i].img;
    		appName_guess[i]=dataList.guess[i].name;
    	}
    	switch (dataList.guess.length){
    		case 1:
    		$('.page3 .edit_area .guess1 .appicon').attr("src",appIcon_guess[0]);
    		$('.page3 .edit_area .guess1 .appname').text(appName_guess[0]);
    		$('.page3 .edit_area .guess2,.page3 .edit_area .guess3').hide();
    		break;
    		case 2:
    		$('.page3 .edit_area .guess1 .appicon').attr("src",appIcon_guess[0]);
    		$('.page3 .edit_area .guess1 .appname').text(appName_guess[0]);
    		$('.page3 .edit_area .guess2 .appicon').attr("src",appIcon_guess[1]);
    		$('.page3 .edit_area .guess2 .appname').text(appName_guess[1]);
    		$('.page3 .edit_area .guess3').hide();
    		break;
    		case 3:
    		$('.page3 .edit_area .guess1 .appicon').attr("src",appIcon_guess[0]);
    		$('.page3 .edit_area .guess1 .appname').text(appName_guess[0]);
    		$('.page3 .edit_area .guess2 .appicon').attr("src",appIcon_guess[1]);
    		$('.page3 .edit_area .guess2 .appname').text(appName_guess[1]);
    		$('.page3 .edit_area .guess3 .appicon').attr("src",appIcon_guess[2]);
    		$('.page3 .edit_area .guess3 .appname').text(appName_guess[2]);
    		$('.page3 .edit_area .guess1,.page3 .edit_area .guess2,.page3 .edit_area .guess3').show();
    		break;
    		default:
    		break;
    	}

    	//page4-本周流行排行
    	var appIcon_rank=[],
    	 	appName_rank=[];
    	for(var i=0;i<dataList.rank.length;i++){
    		appIcon_rank[i]=dataList.rank[i].img;
    		appName_rank[i]=dataList.rank[i].name;
    	}
    	if(dataList.rank.length>0){
    		$('.page4 .rank1_appicon').attr('src',appIcon_rank[0]);
    		$('.page4 .rank1_appname').text(appName_rank[0]);

    		$('.page4 .rank2_appicon').attr('src',appIcon_rank[1]);
    		$('.page4 .rank2_appname').text(appName_rank[1]);
    		
    		$('.page4 .rank3_appicon').attr('src',appIcon_rank[2]);
    		$('.page4 .rank3_appname').text(appName_rank[2]);
    	}
    	// adaptScreen();
    }
		