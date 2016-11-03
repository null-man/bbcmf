/***
 * Prompt提示语插件
 * 编写时间：2013年4月8号
 * version:Prompt.1.0.js
 * author:小宇<i@windyland.com>
 ***/
(function($){
	$.extend({
		PromptBox:{
			defaults : {
				name  :	"t"+ new Date().getTime(),
				content :"操作成功",			//弹出层的内容(text文本、容器ID名称、URL地址、Iframe的地址)
				width : 300,					//弹出层的宽度
				height : 80,
				time:3000,						//设置自动关闭时间，设置为0表示不自动关闭
				type:"warn",                       //弹框类型(warn fail succ)
				bg:true
			},
			timer:{
				stc:null,
				clear:function(){
					if(this.st)clearTimeout(this.st);
					if(this.stc)clearTimeout(this.stc);
				}
			},
			config:function(def){
				this.defaults = $.extend(this.defaults,def);
			},
			created:false,
			create : function(op){
				this.created=true;
				var ops = $.extend({},this.defaults,op);
				this.element = $("<div id='fb"+ops.name+"'></div><div class='prompt_box-"+ops.type+"' id='"+ops.name+"'><div class='content'></div></div>");
				$("body").prepend(this.element);
				this.blank = $("#fb"+ops.name);						//遮罩层对象
				this.content = $("#"+ops.name+" .content");		//弹出层内容对象
				this.dialog = $("#"+ops.name+"");					//弹出层对象
				/*if ($.browser.msie && ($.browser.version == "6.0") && !$.support.style) {//判断IE6
				 this.blank.css({height:$(document).height(),width:$(document).width()});
				 }*/
			},
			show:function(op){
				this.dialog.show();
				var ops = $.extend({},this.defaults,op);
				this.content.css({width:ops.width});
				this.content.html("<span class='"+ops.type+"-icon'></span><span>"+ops.content+"</span>");
				var Ds = {
					width:this.content.outerWidth(true),
					height:this.content.outerHeight(true)
				};
				if(ops.bg){
					this.blank.show();
					this.blank.animate({opacity:"0.5"},"normal");
				}else{
					this.blank.hide();
					this.blank.css({opacity:"0"});
				}
				this.dialog.css({
					width:Ds.width,
					height:Ds.height,
					left:(($(document).width())/2-(parseInt(Ds.width)/2)-5)+"px",
					//top:(($(window).height()-parseInt(Ds.height))/2+$(document).scrollTop())+"px"
				});
				if ($.isNumeric(ops.time)&&ops.time>0){//自动关闭
					this.timer.clear();
					this.timer.stc = setTimeout(function (){
						var DB = $.PromptBox;
						DB.close();
					},ops.time);
				}
			},
			close:function(){
				var DB = $.PromptBox;
				DB.blank.animate({opacity:"0.0"},
					"normal",
					function(){
						DB.blank.hide();
						DB.dialog.hide();
					});
				DB.timer.clear();
			}
		},
		Prompt:function(con,tp,t,ops){
			if(ops==undefined){
				if($.trim(tp)!=""){
					ops=$.PromptBox.defaults.type=tp;
				}
			}else{
				ops.type=tp
			}
			if(!$.PromptBox.created){$.PromptBox.create(ops);}
			if($.isPlainObject(con)){
				if(con.close){
					$.PromptBox.close();
				}else{
					$.PromptBox.config(con);
				}
				return true;
			}

			ops = $.extend({},$.PromptBox.defaults,ops,{time:t});
			ops.content = con||ops.content;
			$.PromptBox.show(ops);
			endLoading()
		}
	})
})(jQuery);