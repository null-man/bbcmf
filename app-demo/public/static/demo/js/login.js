$(document).ready(function() {


	/**
	 * 默认高亮
	 */
	function autohigh(selchoose,addclass){
		$(selchoose).eq(0).addClass(addclass);
	}
	

	/**
 	 * input获得焦点时边框加蓝边
 	 */
 	function focusblue(focusblue){
 		$(focusblue).focus(function(){
			$(this).parent().css({"box-shadow":"0px 0px 10px 1px #b9d3ed","border":"1px solid #00a0e9","transition":"0.3s"});
			$(this).siblings('i').css("background-position-x","0px");
		});
		$(focusblue).focusout(function(){
			$(this).parent().css({"box-shadow":"0px 0px 0px 0px #00a0e9","border":"1px solid #eee"});
			$(this).siblings('i').css("background-position-x","-15px");
		});
 	}


 	/**
	 * login页面
	 */
	focusblue('.login div form div input[type!=checkbox]');



	/**
	 * 表单验证
	 * @param  {[type]}
	 * @return {[type]}
	 */
	$(".demoform").Validform({
		tiptype:3,
		btnSubmit:"#login",
		showAllError:false,
		ajaxPost:false,
		beforeSubmit:function(curform){
			var fromData=$("form").serialize();
			$.post('',fromData, function(data){
				console.log(data);
    			if(data.status==1){
    			    //成功则跳转到相应的url
    			    window.location="http://"+window.location.host+data.info
    				
    			}else if(data.status==0){
    				$('.msg_content').text(data.info);
    				console.log("登录失败");
    				// $(this).resetForm();//提交失败后重置表单
    			}
			},"json");
			return false;
		}
	})
	
})