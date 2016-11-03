/**
	 * [onload 页面初始化]
	 * @return {[type]} [description]
	 */

window.onload=function(){
	//获取设备号-getDsn
    // if(isIDevice){
    //  //js调用oc方法
    //  BridgeOc(function(bridge){
    //  bridge.callHandler('getDsn','',function(response){
    //          dsn=response;
    //      });
    //  });
    // }else if(isAndroid){
    //  //js调用android方法
    //  dsn=activity.getDsn();
    // }
    
    //请求数据接口
    jsonAjax("./month.json","get","","json",showData);
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
        init();
    },1000);
    return;
}
/**
 * [init 初始化操作]
 * @return {[type]} [description]
 */
var init=function() {
        var arrAnimation=[];//动画操作数组
        var percentinit=(1/$("section").length)*100;
        setCss('.progress .progressbar span','width',percentinit+"%");//初始化进度条百分比

        // 基于准备好的dom，初始化echarts实例   
        var myPieChart = echarts.init(document.getElementById('pie-chart'));
        var myLineChart=echarts.init(document.getElementById('line-chart'));
        window.onresize = myPieChart.resize;
        window.onresize = myLineChart.resize;
        adaptScreen();
       
        var mySwiper = new Swiper ('.swiper-container', {//实例化swiper配置项
        direction: 'vertical',
        onInit:function(swiper){
            initpageOne('month');//初始化首页动画
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
        },
        onTransitionEnd:function(swiper){
            swiperAnimate();
            //progerssbar
            var slideDir=parseInt(swiper.activeIndex)-parseInt(swiper.previousIndex),
                index=swiper.activeIndex;
            progressbar(index,slideDir,percentinit);
            //动画控制
            switch(index){
                case 0:
                    initpageOne('month');
                break;
                case 1:
                arrAnimation=[
                {"selector":".month .page2 .title","addClass":"fadeInDown","removeClass":"hide"},
                {"selector":".month .page2 .logo","addClass":"rotateIn","removeClass":"hide"},
                {"selector":".month .page2 .more","addClass":"bounceInUp","removeClass":"hide"},
                {"selector":".month .page2 .ain","addClass":"","removeClass":"hide"}
                ];
                _add_removeClassArr(arrAnimation);
                break;
                case 2:
                arrAnimation=[
                {"selector":".month .page3 .title","addClass":"fadeInDown","removeClass":"hide"},
                {"selector":".month .page3 .desc_ani","addClass":"flipInX","removeClass":"hide"},
                {"selector":".month .page3 .ain","addClass":"","removeClass":"hide"}
                ];
                _add_removeClassArr(arrAnimation);
                setTimeout(function(){
                    _addClass('.month .page3 .applist','bounce');
                },1000);
                break;
                case 3:
                arrAnimation=[
                    {"selector":".month .page4 .appicon,.month .page4 .appname","addClass":"fadeInUpBig","removeClass":"hide"},
                    {"selector":".month .page4 .rank1","addClass":"bounceInDown","removeClass":"hide"},
                    {"selector":".month .page4 .rank2","addClass":"bounceInDown2","removeClass":"hide"},
                    {"selector":".month .page4 .rank3","addClass":"bounceInDown3","removeClass":"hide"},
                    {"selector":".month .page4 .ain","addClass":"","removeClass":"hide"}
                ];
                _add_removeClassArr(arrAnimation);

                break;
                case 4:
                arrAnimation=[
                    {"selector":".month .page5 .title","addClass":"fadeInDown","removeClass":"hide"},
                    {"selector":".month .page5 .block","addClass":"bounceInDown","removeClass":"hide"},
                    {"selector":".month .page5 .train","addClass":"train","removeClass":"hide"},
                    {"selector":".month .page5 .ain","addClass":"","removeClass":"hide"}
                ];
                _add_removeClassArr(arrAnimation);
                break;
                case 5:
                arrAnimation=[
                    {"selector":".month .page6 .title","addClass":"fadeInDown","removeClass":"hide"},
                    {"selector":".month .page6 .ain","addClass":"","removeClass":"hide"}
                ];
                _add_removeClassArr(arrAnimation);

                setTimeout(function(){
                    _addClass('.month .page6 .appitem,.month .page6 .apptag','bounce');
                },1000);

                break;
                case 6:
                arrAnimation=[
                    {"selector":".month .page7 .rocket","addClass":"bounceOutUp","removeClass":"hide"},
                    {"selector":".month .page7 .icon","addClass":"bounceInLeft","removeClass":"hide"},
                    {"selector":".month .page7 .babybus","addClass":"bounceInDown","removeClass":"hide"},
                    {"selector":".month .page7 .title","addClass":"tada","removeClass":"hide"},
                    {"selector":".month .page7 .ain","addClass":"","removeClass":"hide"}
                ];
                _add_removeClassArr(arrAnimation);
                _addClass('.month .page7 .qborder','flash');
                setTimeout(function(){
                    removeEle('.month .page7 .rocket');
                },1000);
                break;
            }
            
            //图表绘制
            if(index==1){
                    // 使用刚指定的配置项和数据显示图表
                    myPieChart.setOption(pie_option);
                    $('canvas').css("width","100%").css("height","100%");
                    $('canvas').parent("div").css("width","100%").css("height","100%");
                }else if(index==4){
                
                myLineChart.setOption(option);
                $('#line-chart canvas').css("width","100%").css("height","100%");
                $('#line-chart canvas').parent("div").css("width","100%").css("height","100%");
              }
            }
        });
        $('#next').click(function(){
            var index=mySwiper.activeIndex;
            mySwiper.slideTo(index+1,1000,true);
        });
    }

    
    
    //监听下拉列表选择-月
    $('.month .page1 .block_r select').change(function(){
        var val=$(this).val();
        var txt=$(this).find("option:selected").text();
        $('.month .page1 .block_r span').text(txt);
         //请求数据
         // jsonAjax("./month.json","get","","json",showData);
    });

    //监听下拉列表选择-年龄段
    $('.month select[name=select-age]').change(function(){
        var val=$(this).val();
        var txt=$(this).find("option:selected").text();
        $('.month .select-item').text(txt);
        //请求数据
        // jsonAjax("./month.json","get","","json",showData);
    });
   
   /**
    * [showDate 数据展示]
    * @return {[type]} [description]
    */
   function showData(data) {
        var dataList=data.data;
        /*page2-学习分类分布*/
        configPic(dataList.category);
        /*page3-行为兴趣分析*/
        var recommend_desc=dataList.recommend.desc;
        $('.month .page3 .desc').text(recommend_desc);
        var recommend_appIcon=[],
            recommend_appname=[],
            recommend_appurl=[];
        for(var i=0;i<dataList.recommend.app.length;i++){
            recommend_appIcon[i]=dataList.recommend.app[i].img;
            recommend_appname[i]=dataList.recommend.app[i].name;
            recommend_appurl[i]=dataList.recommend.app[i].url;
        }
        $('.month .page3 .recommend .appurl1').attr('href',recommend_appurl[0]);
        $('.month .page3 .recommend .appurl2').attr('href',recommend_appurl[1]);
        $('.month .page3 .recommend .appurl3').attr('href',recommend_appurl[2]);
        $('.month .page3 .recommend .appicon1').attr('src',recommend_appIcon[0]);
        $('.month .page3 .recommend .appicon2').attr('src',recommend_appIcon[1]);
        $('.month .page3 .recommend .appicon3').attr('src',recommend_appIcon[2]);
        $('.month .page3 .recommend .appname1').text(recommend_appname[0]);
        $('.month .page3 .recommend .appname2').text(recommend_appname[1]);
        $('.month .page3 .recommend .appname3').text(recommend_appname[2]);
        /*page4-本月流行排行*/
        var appIcon_rank=[];
            appName_rank=[];
        for(var i=0;i<dataList.rank.length;i++){
            appIcon_rank[i]=dataList.rank[i].img;
            appName_rank[i]=dataList.rank[i].name;
        }
        if(dataList.rank.length>0){
            $('.page4 .rank2_appicon').attr('src',appIcon_rank[1]);
            $('.page4 .rank2_appname').text(appName_rank[1]);
            $('.page4 .rank1_appicon').attr('src',appIcon_rank[0]);
            $('.page4 .rank1_appname').text(appName_rank[0]);
            $('.page4 .rank3_appicon').attr('src',appIcon_rank[2]);
            $('.page4 .rank3_appname').text(appName_rank[2]);

        }
        /*上月学习时间分布*/
        configLine(dataList.time);
        /*page6-本月宝宝最爱*/
        $('.month .page6 .edit_area .top_num').text(dataList.like.length);
        var appIcon_like=[],
            appName_like=[];
        for(var i=0;i<dataList.like.length;i++){
            appIcon_like[i]=dataList.like[i].img;
            appName_like[i]=dataList.like[i].name;
        }
        switch (dataList.like.length){
            case 1:
            $('.month .page6 .edit_area .appitem:eq(0) .appicon').attr('src',appIcon_like[0]);
            $('.month .page6 .edit_area .appitem:eq(0) .appname').text(appName_like[0]);
            $('.month .page6 .edit_area .appitem:gt(0),.month .page6 .edit_area .apptag:gt(0)').hide();
            break;
            case 2:
            $('.month .page6 .edit_area .appitem:eq(0) .appicon').attr('src',appIcon_like[0]);
            $('.month .page6 .edit_area .appitem:eq(0) .appname').text(appName_like[0]);
            $('.month .page6 .edit_area .appitem:eq(1) .appicon').attr('src',appIcon_like[1]);
            $('.month .page6 .edit_area .appitem:eq(1) .appname').text(appName_like[1]);
            $('.month .page6 .edit_area .appitem:gt(1),.month .page6 .edit_area .apptag:gt(1)').hide();
            break;
            case 3:
            $('.month .page6 .edit_area .appitem:eq(0) .appicon').attr('src',appIcon_like[0]);
            $('.month .page6 .edit_area .appitem:eq(0) .appname').text(appName_like[0]);
            $('.month .page6 .edit_area .appitem:eq(1) .appicon').attr('src',appIcon_like[1]);
            $('.month .page6 .edit_area .appitem:eq(1) .appname').text(appName_like[1]);
            $('.month .page6 .edit_area .appitem:eq(2) .appicon').attr('src',appIcon_like[2]);
            $('.month .page6 .edit_area .appitem:eq(2) .appname').text(appName_like[2]);
            break;
            default:
            break;
        }
   }
/**
 * [configPic 配置饼图]
 * @return {[type]} [description]
 */
   function configPic(data) {
    var pie_datas=[];
    for(var i=0;i<data.length;i++){
        var pie_data={};
        pie_data["name"]=data[i].name+data[i].value+"%";
        pie_data["value"]=data[i].value;
        pie_datas.push(pie_data);
    }
    
    // 指定图表的配置项和数据
    pie_option = {
        baseOption:{
            tooltip: {
            show:false,
            formatter: "{a} <br/>{b}: {c} ({d}%)"
        },
        legend: {
            orient: 'vertical',
            x: 'left',
            // data:['直达','营销广告','搜索引擎','邮件营销','联盟广告','视频广告','百度','谷歌','必应','其他']
        },
        series: [
            {
                name:'访问来源',
                type:'pie',
                radius: ['60%', '80%'],

                data:pie_datas,
                label: {
                    normal: {
                        textStyle: {
                         fontSize:50,
                         color:'#fff'
                        }
                    }
                },
                labelLine:{
                    normal:{
                        lineStyle:{
                            width:3,
                            color:'#fff'
                        },
                        smooth:0,
                        length:20,
                        length2:20,
                    }
                }
            }
        ],
        color:['#F7E577','#ff6c6c', '#f89448', '#91d6f3', '#c09ded','#f790bf']
        }
    };
    return pie_option;
   }
/**
 * [conficLine 折线图配置]
 * @param  {[type]} data [接口数据]
 * @return {[type]}      [返回配置项]
 */
   function configLine(data){
    var date=new Date(),
        year=date.getFullYear(),
        month=date.getMonth(),
        dateEnd=DayNumOfMonth(year,month);
    var line_datas=[];
    for(var i=0;i<data.length;i++){
        var line_data={};
        line_data["value"]=data[i];
        line_data["itemStyle"]={"normal":{"opacity":1}}
        line_datas.push(line_data);
        line_datas[0].itemStyle.normal.opacity=0;
    }
    option = {
        grid        : {
        left        : '3%',
        right       : '4%',
        bottom      : '3%',
        containLabel: true
        },
        xAxis : [
            {
                
                type         : 'category',
                boundaryGap  : false,
                data         : ['','5','10','15','20','25',dateEnd],
                name         :'/日期',
                nameTextStyle:{
                color        :'#000',
                fontSize     :22
                },
                nameGap:0,
                splitLine:false,
                axisLine :{
                lineStyle:{
                color    :'#78b9fb',
                width    :2
                    }
                },
                axisLabel:{
                    textStyle:{
                        color:'#3e3a39',
                        fontSize:20
                    }
                },
                axisTick:{
                show    :false
                }
            }
        ],
        yAxis : [
            {
                type : 'value',
                splitLine:false,
                data : ['0','20','40','60','80','100','110'],
                name:'/分钟',
                nameTextStyle:{
                    color:'#000',
                    fontSize:22
                },
                nameGap:20,
                axisLine:{
                    lineStyle:{
                        color:'#78b9fb',
                        width:2
                    }
                },
                axisLabel:{
                    textStyle:{
                        color:'#3e3a39',
                        fontSize:20
                    }
                },
                axisTick:{
                    show:false
                }
                
            }
        ],
        series : [
            {
                name:'',
                type:'line',
                stack: '总量',
                symbol:'circle',
                symbolSize:8,
                itemStyle:{
                  normal:{
                      color:'#45c7fa'
                  }  
                },
                label: {
                    normal: {
                        show: true,
                        position: 'top',
                        textStyle:{
                            color:'#45c7fa',
                            fontSize:22
                        }
                    }
                },
                lineStyle:{
                    normal:{
                        color:'#45c7fa'
                    }
                },
                areaStyle: {normal: {
                    color:'#a2e3fc',
                    
                }},
                data:line_datas
            }
        ]
    };
    return option;
}