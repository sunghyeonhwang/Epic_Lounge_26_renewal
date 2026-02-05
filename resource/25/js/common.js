//상단메뉴
function web_menu(a){
	var top1menu = $("#lnb .top1menu"),
		depth1 = $("#lnb .top1menu > li"),
		depth1_t = $("#lnb .top1menu li.depth1 > .depth1_ti"),
		depth2 = $(".depth2 > li");

	top1menu.find(" > li > div").addClass('top2m');
	depth1_t.bind({
		mouseenter:function(){
			$(this).parents('.depth1').addClass('on').find('div.top2m').stop().slideDown(300);
		},
		focusin:function(){
			depth1.removeClass('on').find('div.top2m').stop().slideUp(100);
			$(this).parents('.depth1').addClass('on').find('div.top2m').stop().show(300);
		}
	});

	depth1.find('ul').focusin(function(){
		$(this).parents('.depth1').addClass('on');
	});

	depth1.mouseleave(function(){
		$(this).removeClass('on').find('div.top2m').stop().slideUp(100);
	});

	top1menu.find('li:last-child .top2m li:last-child a').focusout(function(){
		$('#lnb .depth1.on').removeClass('on');
		$(this).parents('li.depth1').removeClass('on').find('.top2m').stop().slideUp(100);
	});
	
	depth2.bind({
		mouseenter:function(){$(this).addClass('on');},
		focusin:function(){
			depth2.find('li').removeClass('on');
			$(this).addClass('on');
		},
		focusout:function(){$(this).siblings('ul li:last-child()').removeClass('on');}
	});

	depth2.mouseleave(function(){
		$(this).removeClass('on');
	});

	depth1.each(function(index){
		$(this).addClass("depth1_"+index);
	});

	top1menu.find('> li:last-child').addClass('part_info');
	top1menu.find('> li:last-child ul li').each(function(index){
		$(this).addClass('part_icon'+index);
	});
	//2017-05-25異붽�
	//$('.part_info div ')
};

function mobile_menu(a){
	var depth1 = $(".top1menu"),
		dep1_length = depth1.find(" > li").size(),
		depLast_length = depth1.find(" > li:nth-child("+dep1_length+")  li").size();

	depth1.find(" > li > div").addClass('top2m');
	depth1.off();
	depth1.find(" > li > a").off();
	depth1.find(" ul > li a").off();


	depth1.find(" > li > a").siblings().each(function(index){
		if(!$(this).hasClass("load_actvie")){
			$(this).slideUp();
		}
	});

	$(".top1menu .top2m, .top1menu .top2m div").css("height","auto");
	$(".top1menu .top2m, .top1menu .top2m div.menu_bg2").removeClass("menu_bg2").addClass("menu_bg");
	$(".top1menu .top2m, .top1menu .top2m").removeClass("top2m2");
	depth1.find(" >  li > a").on('click',function(event){
		var depth2_has=$(this).siblings("div").size();
		if(depth2_has==0){

		}else{
			event.preventDefault ();
			var m_open=$(this).hasClass('active');
			if(m_open==true){
				$(this).siblings().slideUp();
				$(this).removeClass('active');
			}else{
				depth1.find(" > li > div ").stop().slideUp();
				depth1.find(" a ").removeClass('active');
				$(this).siblings().slideDown();
				$(this).addClass('active')
			}

		}
	});

	depth1.find(" ul > li a ").on('click',function(event){
		var depth3_has=$(this).siblings("ul").size();
		if(depth3_has>0){
			event.preventDefault();
		}
		var m_open=$(this).hasClass('active');
		if(m_open==true){
			$(this).siblings().slideUp();
			$(this).removeClass('active');
		}else{
			depth1.find(" ul ul").stop().slideUp();
			depth1.find(" ul a").removeClass('active');
			$(this).siblings().slideDown();
			$(this).addClass('active')
		}
	});
};

$(document).ready(function(){
	var lnb = $('#lnb'),
		
		m_nav_open = $('.lnb_m_nav'),
		m_nav_close = $('.mask, .lnb_close button'),
		mask = $( '.mask' ),
		lnb_close = $( '.lnb_close' ),
		bodyFrame = $( 'body, html' ),
		m_nav_display=false,
		gnb_navi = $('.gnb_navi');
		link_set = $('.link_set');

	m_nav_open.click(function() {
		var h = $(window).height();
		lnb.animate(  { right : 0 },  500);
		gnb_navi.animate(  { right : 0 },  500);
		link_set.animate(  { right : 0 },  500);
		lnb_close.animate(  { right : 280 },  500);
		bodyFrame.css("overflow",'hidden');
		bodyFrame.addClass('openM');
		m_nav_open.fadeOut(500);
		mask.show();
	});
	m_nav_close.click(function() {
		lnb.animate(  { right : -280 },  500);
		gnb_navi.animate(  { right : -280 },  500);
		link_set.animate(  { right : -280 },  500);
		lnb_close.animate(  { right : -48 },  500);
		bodyFrame.css('overflow', '' );
		bodyFrame.removeClass('openM');
		m_nav_open.delay(300).fadeIn(0);
		mask.hide();
	});
});


//language
jQuery(function($){
	
	$("#right_bar .btn_1").click(function(){
		$("#call_popbox").addClass('on');
		$("#bl_box").addClass('on');
		$("#kaka_popbox").removeClass('on');return false;
	});
	$("#right_bar .btn_2").click(function(){
		$("#kaka_popbox").addClass('on');
		$("#bl_box").addClass('on');
		$("#call_popbox").removeClass('on');return false;
	});
	$(".close_btn").click(function(){
		$("#call_popbox").removeClass('on');
		$("#bl_box").removeClass('on');
		$("#kaka_popbox").removeClass('on');return false;
	});




	$(".site_link div.layer").fadeOut("fast");
	$(".site_link h3 button.open").click(function(){
		$(".site_link div.layer").fadeOut("fast");
		$(this).parent().addClass("on_btn");
		$(this).parent().next("div.layer").fadeIn("fast");return false;
	});
	$(".site_link .close").click(function(){
		$(this).parent().removeClass("on_btn");
		$(this).parent().fadeOut("fast");return false;
	});
	
	$('.sitelink_cont .layer li:last-child a').on('focusout',function(){
		$(this).parents('.layer').slideUp(300);
	});
});

$(function () {
	$(window).on({
		load: function () {
			if ($(window).width() > 1000) {
				web_menu();
		//		web_lang();
			}
			else {
				mobile_menu();
		//		mobile_lang();
			}
			header_search();
		},
		resize: function () {
			if ($(window).width() > 1000) {
				web_menu();
		//		web_lang();
			}
			else {
				mobile_menu();
		//		mobile_lang();
			}
			header_search();
		}
	});
});


(function($) {
	'use strict';
	
	$(function() {
	
		$('.tab_layout').each(function(){
			var tab_layout = $(this),
			$tab = tab_layout.find('> .tab_button > ul > li'),
			$tabContent = tab_layout.find(' > .tab_content');
			
			
			$(window).on("load", function(){
				if(!tab_layout.is('[data-roadtab]')){
					tab_layout.attr('data-roadtab','1');
				}

				var num = tab_layout.attr('data-roadtab');

				var $tab_view = tab_layout.find(' > .tab_content.tab'+num);
				$tab.eq(num-1).addClass('active');
				$tab_view.addClass('active');
			});

			$tab.bind("click",function(event){
				var this_eq = $tab.index( $(this) );
				$tab.removeClass('active');
				$tab.eq(this_eq).addClass('active');
				$tabContent.removeClass('active');
				tab_layout.find('.tab_content.tab'+(this_eq+1)).addClass('active');
				event.preventDefault();
			});
		});



	});
})(jQuery);
$('.tab_layout').each(function(){
	var tab_layout = $(this),
		$tab = tab_layout.find('> .tab_button > ul > li'),
		$tabContent = tab_layout.find(' > .tab_content'),
		$thisTitle = tab_layout.find('h3');


	$(window).on("load", function(){
		if(!tab_layout.is('[data-roadtab]')){
			tab_layout.attr('data-roadtab','1');
		}

		var num = tab_layout.attr('data-roadtab');

		var $tab_view = tab_layout.find(' > .tab_content.tab'+num);
		$tab.eq(num-1).addClass('active');
		$tab_view.addClass('active');
	});

	$tab.bind("click",function(event){
		var this_eq = $tab.index( $(this) ),
			this_text = $tab.eq(this_eq).children().text();
		$tab.removeClass('active').removeAttr('title');
		$tab.eq(this_eq).addClass('active').attr('title', '열시');
		$tabContent.removeClass('active');
		tab_layout.find('.tab_content.tab'+(this_eq+1)).addClass('active');
		event.preventDefault();
		$thisTitle.text(this_text);
	});
});


$(document).ready(function(){
	//이미지 롤오버
	 $(".overimg").mouseover(function (){
		var file = $(this).attr('src').split('/');
		var filename = file[file.length-1];
		var path = '';
		for(i=0 ; i < file.length-1 ; i++){
		 path = ( i == 0 )?path + file[i]:path + '/' + file[i];
		}
		$(this).attr('src',path+'/'+filename.replace('_off.','_on.'));

	 }).mouseout(function(){
		var file = $(this).attr('src').split('/');
		var filename = file[file.length-1];
		var path = '';
		for(i=0 ; i < file.length-1 ; i++){
		 path = ( i == 0 )?path + file[i]:path + '/' + file[i];
		}
		$(this).attr('src',path+'/'+filename.replace('_on.','_off.'));
	 });
});

//popupzone
(function($){
	$.fn.PopupZone = function(options) {
		var settings = {
			prevBtn : '',
			nextBtn : '',
			playBtn : '',
			waitingTime : ''
		};
		
		$.extend(settings, options);
		settings.areaDiv = this;
		settings.prevBtn = $(settings.prevBtn);
		settings.nextBtn = $(settings.nextBtn);
		settings.playBtn = $(settings.playBtn);
		
		settings.cnt = settings.areaDiv.find('li').length;		
		settings.waitingTime = parseInt(settings.waitingTime);
		settings.nowNum = 0;
		settings.moveFlag = true; 
		settings.moveType;
		settings.setTimeOut;
		var status=true;
		
		function emptySetting() {
			settings.areaDiv.find('.count').html(settings.nowNum+1);
			settings.areaDiv.find('.all').html(settings.cnt);
			settings.areaDiv.find('li').hide();
			//settings.areaDiv.find('img').hide();
		}
		function setRolling(aniFlag) {
			if(!settings.moveFlag){
				if(settings.moveType=="next" || settings.moveType == null){ 
					settings.nowNum++;
					if(settings.nowNum == settings.cnt) settings.nowNum = 0;
				} else if(settings.moveType=="prev") {
					settings.nowNum--;
					if(settings.nowNum < 0) settings.nowNum = (settings.cnt-1);
				}
			}			
			emptySetting();
			if( settings.cnt < 2 ) {
				aniFlag = true;
			}
			
			if(aniFlag) settings.areaDiv.find('li').eq(settings.nowNum).show();
			else settings.areaDiv.find('li').eq(settings.nowNum).fadeIn('normal');
			 // 기본 : aniFlag 설정 없으면 fade 효과 - 조정
			
			aniFlag = false;
			settings.moveFlag = false;
			if(status){
				if( settings.cnt > 1 ) {
					settings.setTimeOut= setTimeout(setRolling , settings.waitingTime);
				}
			}
		}
		function playRolling(){
			if(status){
				console.log('a')
				clearTimeout(settings.setTimeOut);
				settings.playBtn.attr('class',"btn_play").html("팝업 롤링 재생");
				status = false;
			}else{
				console.log('b')
				settings.playBtn.attr('class',"btn_pause").html("팝업 롤링 정지");
				status = true;
				setRolling();
			}
			return false;
		}
		function prevRolling(){
			clearTimeout(settings.setTimeOut);
			settings.moveType = "prev";
			setRolling();
			return false;
		}
		function nextRolling() {
			clearTimeout(settings.setTimeOut);
			settings.moveType = "next";
			setRolling();
			return false;
		}
		setRolling();
		settings.prevBtn.click(prevRolling);
		settings.nextBtn.click(nextRolling);
		settings.playBtn.click(playRolling);
		
	};
})(jQuery);
$(document).ready(function(){
	 $('.popup').PopupZone({
		prevBtn : '.popup_control .btn_prev',
		nextBtn : '.popup_control .btn_next',
		playBtn : '.popup_control .btn_pause',
		waitingTime : '6000'
	});

	$('.popup_small').PopupZone({
		prevBtn : '.popup_control2 .btn_prev',
		nextBtn : '.popup_control2 .btn_next',
		playBtn : '.popup_control2 .btn_pause',
		waitingTime : '6000'
	});

	
});

$(document).ready(function(){
     var lastScroll = 0;
     $(window).scroll(function(event){
          var scroll = $(this).scrollTop();
          if (scroll > 100){
               $("#right_bar").addClass("fixed");
          }
          else {
               $("#right_bar").removeClass("fixed");
          }
          lastScroll = scroll;
     });
 });



// 동영상 체크
function GetIEVersion() {
    var sAgent = window.navigator.userAgent;
    var Idx = sAgent.indexOf("MSIE");

    // If IE, return version number.
    if (Idx > 0)
        return parseInt(sAgent.substring(Idx+ 5, sAgent.indexOf(".", Idx)));

    // If IE 11 then look for Updated user agent string.
    else if (!!navigator.userAgent.match(/Trident\/7\./))
        return 11;

    else
        return 99; //It is not IE
}

function checkIE(ver,seletor) {
    var ver = ver,
        seletor = seletor;

    if (GetIEVersion() <= ver) { //IE브라우전 체크 버전보다 작으면
        $(seletor + " video").remove();
    } else{
        $(seletor + " object").remove();
    }
    console.log('a');
}



$(document).ready(function () {
	$('html').addClass('body_active');
});

