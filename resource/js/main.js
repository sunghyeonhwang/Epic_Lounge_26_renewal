
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



$(function(){
    $main_2 = $(".main_sec2");
    $main_2_list = $main_2.find(".list");

    $main_2_list.slick({
        infinite: true,
        slidesToShow:4,
        slidesToScroll: 1,
        arrows: true,
        autoplay:false,
        autoplaySpeed: 3000,
        variableWidth: false,
        prevArrow : $main_2.find(".prev"),
        nextArrow : $main_2.find('.next'),
        responsive: [
           {
			  breakpoint: 1024,
			  settings: {
				slidesToShow: 3,
				arrows: false
			  }
			},
           {
			  breakpoint:800,
			  settings: {
				slidesToShow: 2,
				arrows: false
			  }
			},
           {
			  breakpoint:480,
			  settings: {
        variableWidth: true
			  }
			}
        ]
    });
});


$(document).ready(function () {
	$('body').addClass('body_active');
});


$(document).ready(function () {
// Hide Header on on scroll down
var didScroll;
var lastScrollTop = 0;
var delta = 5;
var navbarHeight = $('#bt_quick').outerHeight();

$(window).scroll(function(event){
    didScroll = true;
});

setInterval(function() {
    if (didScroll) {
        hasScrolled();
        didScroll = false;
    }
}, 450);

function hasScrolled() {
    var st = $(this).scrollTop();
    
    // Make sure they scroll more than delta
    if(Math.abs(lastScrollTop - st) <= delta)
        return;
    
    // If they scrolled down and are past the navbar, add class .nav-up.
    // This is necessary so you never see what is "behind" the navbar.
    if (st > lastScrollTop && st > navbarHeight){
        // Scroll Down
        $('#bt_quick').removeClass('nav-down').addClass('nav-up');
    } else {
        // Scroll Up
        if(st + $(window).height() < $(document).height()) {
            $('#bt_quick').removeClass('nav-up').addClass('nav-down');
        }
    }
    
    lastScrollTop = st;
}

});



//language
jQuery(function($){
	
	$(".tab_btn_event .btn_1").click(function(){
		$(this).addClass('on');
		$(".off_line").addClass('on');
		$(".tab_btn_event .btn_2").removeClass('on');
		$(".on_line").removeClass('on');return false;
	});
	
	$(".tab_btn_event .btn_2").click(function(){
		$(this).addClass('on');
		$(".on_line").addClass('on');
		$(".tab_btn_event .btn_1").removeClass('on');
		$(".off_line").removeClass('on');return false;
	});

});