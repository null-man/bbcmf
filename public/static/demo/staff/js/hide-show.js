$(function(){
	// $(".j-hide-show").siblings('.nav').hide();
	// 默认展开有 hs-active 样式的层级
	$(".hs-1.j-hide-show.hs-active:first").siblings('.nav').slideDown();
	$(".hs-2.j-hide-show.hs-active:first").siblings('.nav').slideDown();
	// 绑定点击事件
	$(document).on('click','.j-hide-show', toggleHideShow);
})


var toggleHideShow = function(){
	if($(this).siblings('.nav').is(":hidden")){
		console.log('is hidden');
		$(this).addClass("hs-active");
		$(this).siblings('.nav').slideDown();
		$(this).parent().siblings().find('.hs-active').removeClass("hs-active");
		$(this).parent().siblings().find('.nav').slideUp();
	}else{
		console.log('is visible');
		$(this).removeClass("hs-active");
		$(this).siblings('.nav').slideUp();
	}

	// 针对 无子元素 的 第三层级元素 的选中处理（添加相应的 class）
	if($(this).siblings().size() <= 0){
		$(this).addClass("hs-active");
		$(this).parent().siblings().find('.hs-active').removeClass("hs-active");
	}
}