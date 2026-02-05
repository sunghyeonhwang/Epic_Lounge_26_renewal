
"use strict";

try {
	this.mode = '';

	//제이쿼리가 있으면
	this.jQuery = this.jQuery || undefined;

	//제이쿼리가 있으면
	if(jQuery) {
		//$ 중복방지
		(function($) {
			$(function() {
				//side메뉴
				$('.side_menu').menu({
					cut : {},
					event : 'click',
					namespace : 'side'
				});

				$('.tab_menu').not($('.prettyprint').children()).each(function() {
					var li_length = $(this).children('ul').find('li').length;
					$(this).addClass('divide'+li_length);
				});

				
	
			
				

				
			});
		})(jQuery);
	}
}catch(e) {
	console.error(e);
}




(function($) {
	'use strict';
	
	$(function() {
	

		$('.snsbox .sns_btn').on('click', function(){
					var $this = $(this),
						$snsbox = $this.parent('.snsbox'),
						$layer = $this.siblings('.layer'),
						OnOff = $snsbox.is('.active');
					if(!OnOff){
						//$snsbox.addClass('active');
						$this.attr('title', 'sns 공유 닫기').text('sns 공유 닫기');
						$layer.animate({
							width: "show"
						}, 250, function(){
							//$snsbox.addClass('active');
						});
						$snsbox.addClass('active');
					} else{
						$snsbox.removeClass('active');
						$this.attr('title', 'sns 공유 열기').text('sns 공유 열기');
						$layer.animate({
							width: "hide"
						}, 250);
					};
				});
				$('.snsbox .sns_close').on('click', function(){
					var $this = $(this),
						$snsbox = $this.parents('.snsbox'),
						$layer = $this.parent('.layer'),
						$sns_btn = $layer.siblings('.sns_btn');
					$snsbox.removeClass('active');
					$layer.animate({
						width: "hide"
					}, 250);
					$sns_btn.attr('title', 'sns 공유 열기').text('sns 공유 열기').focus();
				});

	});
})(jQuery);


var naverSns = function(title, url) {
	if(title == ""){
		title = $('.btn-naver').attr("data-title");
	}
	window.open("http://blog.naver.com/openapi/share?url="+encodeURIComponent(url)+"&title="+encodeURIComponent(title), "sns", "width=500,height=600,scrollbars=yes,toolbar=no,menubar=no");
}

var facebookSns = function(title, url) {
	if(title == ""){
		title = $('.btn-facebook').attr("data-title");
	}
	window.open("https://www.facebook.com/sharer/sharer.php?u="+encodeURIComponent(url), "sns", "width=500,height=300,scrollbars=yes,toolbar=no,menubar=no");
}


$(document).ready(function(){
   $(".step_type_1").each(function(){
   var nodes=$(this).children();
      var max_h=0;
      nodes.each(function(){
         var h = parseInt($(this).css("height"));
         if(max_h<h){ max_h = h; }
      });
      nodes.each(function(){
         $(this).css({height:max_h});
      });
   });
});

jQuery(function($){
	$("body").addClass("active");
});

jQuery(function($){
     var lastScroll = 0;
     $(window).scroll(function(event){
          var scroll = $(this).scrollTop();
          if (scroll > 100){
          //이벤트를 적용시킬 스크롤 높이
               $("body").addClass("scroll");
          }
          else {
               $("body").removeClass("scroll");
          }
          lastScroll = scroll;
     });
});



jQuery(function($){
	$(".acc_toggle").click(function(){
		if($(this).parent(".part").hasClass("close")){

			$(this).next('.part_con').slideDown("");
			$(this).parent('.part').removeClass("close");
			console.log("#1");
		}else{

			$(this).next('.part_con').slideUp("");
			$(this).parent('.part').addClass("close");
			console.log("#2");
		}

	});

});

