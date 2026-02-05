/**
 * @author (주)한신정보기술 퍼블리셔팀 권정현
 * @since 2018-08-13
 * @version 1.0.0
 */
try {
	//$ 중첩 방지
	(function($) {
		'use strict';

		/**
		 * @name decodeURIComponent
		 * @param {string} value
		 * @return {string}
		 * @since 2018-07-13
		 */
		function _decodeURIComponent(value) {
			return (typeof value === 'string') ? decodeURIComponent(value.replace(/\+/g, '%20')) : '';
		}

		/**
		 * @name getParam
		 * @param {string} value
		 * @param {string} name
		 * @return {number}
		 * @since 2018-07-13
		 */
		window.getParam = function(value, name) {
			var result = '';
			
			//문자일 때
			if(typeof value === 'string') {
				var param = value.split('?')[1];

				//문자이면서 파라미터가 있을 때
				if(typeof name === 'string' && param) {
					param = param.split('&');

					for(var i = 0, paramLength = param.length; i < paramLength; i++) {
						var paramI = param[i].split('=');
						
						//찾는 값이 있을 때
						if(name === paramI[0]) {
							var valueI = paramI[1];
							
							//값이 있을 때
							if(valueI) {
								do {
									valueI = _decodeURIComponent(valueI);
									result = _decodeURIComponent(valueI);
								}
								
								//i번째 값과 결과가 다를 때
								while(valueI !== result);
							}

							break;
						}
					}
				}
			}

			return result;
		};

		window.cookie = {
			/**
			 * @name cookie.set
			 * @since 2017-01-16
			 * @param {string} name
			 * @param {string} value
			 * @param {number} day
			 * @return {boolean}
			 */
			set : function(name, value, day) {
				var date = new Date(),
					result = false;
				
				//문자일 때
				if(typeof name === 'string' && typeof value === 'string') {
					//숫자가 아닐 때
					if(!(typeof day === 'number' && !isNaN(day) && isFinite(day))) {
						day = -1;
					}

					date.setDate(date.getDate() + day);
					document.cookie = name + '=' + escape(value) + '; expires=' + date.toUTCString() + '; path=/;';

					//쿠키생성 후 확인해서 있으면
					if(this.get(name) === value) {
						result = true;
					}
				}

				return result;
			},

			/**
			 * @name cookie.get
			 * @since 2017-01-16
			 * @param {string} name
			 * @return {string}
			 */
			get : function(name) {
				var cookie = document.cookie.split(';'),
					result = '';
				
				//문자일 때
				if(typeof name === 'string') {
					var valueIndex = name.length + 1;

					for(var i = 0, cookieLength = cookie.length; i < cookieLength; i++) {
						var cookieI = cookie[i];
						
						//첫번째 글자가 공백일 때
						while(cookieI.charAt(0) === ' ') {
							cookieI = cookieI.substring(1);
							break;
						}
						
						//쿠키값이 있을 때
						if(cookieI.indexOf(name) > -1) {
							result = unescape(cookieI.substring(valueIndex, cookieI.length));
							break;
						}
					}
				}

				return result;
			}
		};

		//제이쿼리가 있는지 확인
		if(typeof $ === 'function') {
			$.tag = {
				wdw : $(window),
				dcmt : $(document),
				html : $('html'),
				head : $('head')
			};
			
			$.variable = {};

			$(function() {
				$.tag.body = $('body');
				$.tag.wrapper = $('#wrapper');
				$.tag.header = $('#header');
				$.tag.container = $('#container');
				$.tag.footer = $('#footer');
				$.tag.pathbox = $('.pathbox');

				
				
				//탭메뉴
				$.tag.tabMenu = $.tag.container.find('.tab_menu');
				$.tag.tabWrap = $.tag.tabMenu.children('.tab_wrap');
				$.tag.tabUL = $.tag.tabWrap.children('ul');
				$.tag.tabLI = $.tag.tabUL.children('li');
				$.tag.tabContent = $.tag.tabLI.children('.tab_content');
				$.tag.tabButton = $.tag.tabLI.children('.tab_button');

				$.tag.tabButton.on('click.layout', function(event) {
					var $this = $(this),
						hash = this.hash,
						tagName = this.tagName.toLowerCase();
					
					//해쉬가 있거나 버튼일때
					if(hash || tagName === 'button') {
						//클래스 처리
						$this.parents('li').addClass('active').siblings('li').removeClass('active');
						
						//영역 처리
						$this.parents('.tab_menu').each(function(index, element) {
							var $element = $(element),
								outerHeight = $element.find(' > .tab_wrap > ul > li.active > .tab_content').outerHeight() || '',
								unlock = $element.data('unlock') || '',
								unlockCount = 1;

							if(unlock) {
								unlock = unlock.split(',');
								unlockCount = unlock.length;
							}

							for(var i = 0; i < unlockCount; i++) {
								if($.inArray(unlock[i], $.responsive.settings.nowState) > -1) {
									$element.css('padding-bottom', '');
									break;
								}else{
									$element.css('padding-bottom', outerHeight);
								}
							}
						});
					}
				});

				//해쉬이동
				$.tag.htmlAndBody = $.tag.html.add($.tag.body);
				$.tag.anchor = $('[href^="#"]').filter('a, area');
				$.tag.anchor.on('click.layout', function(event) {
					var $this = $(this),
						hash = this.hash,
						data = $this.data(),
						$hash = $(hash);
					
					//예외가 아닐 때
					if(!data.hasOwnProperty('exception')) {
						//해쉬 요소가 있고 애니메이션이 끝났을때
						if($hash.length && !$.tag.htmlAndBody.is(':animated')) {
							var hasTabindex = $hash.attr('tabindex'),
								offset = $hash.offset(),
								counter = 0;

							$.tag.htmlAndBody.animate({
								scrollTop : offset.top,
								scrollLeft : offset.left
							}, {
								duration : data.duration || 250,
								easing : data.easing || 'easeInOutExpo',
								complete : function() {
									//1이상일때
									if(!counter) {
										//tabindex 속성을 가지고있지 않을때
										if(!hasTabindex) {
											$hash.attr('tabindex', -1);
										}

										$hash.focus();
										location.hash = hash;

										if(!hasTabindex) {
											$hash.removeAttr('tabindex');
										}

										counter++;
									}
									
									//함수일때
									if(typeof data.complete === 'function') {
										data.complete();
									}
								}
							});
						}

						event.preventDefault();
					}
				});

				$('a[target="_blank"]').attr('rel', 'noopener noreferrer');
			});

			$.tag.wdw.on('load.layout', function(event) {
				$.tag.wdw.on('responsive:all.layout', function(event) {
					//탭메뉴
					$.tag.tabActiveLI = $($.tag.tabLI.filter('.active').get().reverse());
					$.tag.tabActiveLI.each(function(index, element) {
						var $element = $(element),
							$tabMenu = $element.parents('.tab_menu'),
							outerHeight = $element.children('.tab_content').outerHeight() || '',
							unlock = $tabMenu.data('unlock') || '',
							unlockCount = 1;

						if(unlock) {
							unlock = unlock.split(',');
							unlockCount = unlock.length;
						}

						for(var i = 0; i < unlockCount; i++) {
							if($.inArray(unlock[i], event.settings.nowState) > -1) {
								$tabMenu.css('padding-bottom', '');
								break;
							}else{
								$tabMenu.css('padding-bottom', outerHeight);
							}
						}
					});
				}).triggerHandler($.Event('responsive:all.layout', {
					settings : $.responsive.settings
				}));
			});
		}else{
			throw '제이쿼리가 없습니다.';
		}
	})(window.jQuery);
}catch(e) {
	console.error(e);
}



$(document).ready(function(){
	//이미지 롤오버 
	 $('.overimg').mouseover(function (){
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
'use strict';

try {
	this.mode = '';
	
	//제이쿼리가 있으면
	this.jQuery = this.jQuery || undefined;

	if(jQuery) {
		//$ 중복방지
		(function($) {
			//태그객체
			$.tag = {
				wdw : $(window),
				dcmt : $(document),
				html : $('html')
			};

			$(function() {
				

				//language
				$('.gnb ul li.list.language .tit').on('click', function() {
					var $this = $(this),
						$languagebox = $this.parent('li.list.language'),
						IsActive = $languagebox.is('.active'),
						$Layer = $this.siblings('.layer');
					if(!IsActive){
						$this.attr('title', '언어선택 닫기');
						$languagebox.addClass('active');
						$Layer.slideDown();
					} else{
						$this.attr('title', '언어선택 열기');
						$languagebox.removeClass('active');
						$Layer.slideUp();
					};
				});

			
				// 푸터 바로가기
				$('.footer_site a.tit').on('click',function(event){
					var $target=$(event.target);
					if($target.is('.on')){
						if($target.is('a')){
							$(this).removeClass('on').next('.cont_box').slideUp(300);
						}else{
							$(this).parents('a.tit').removeClass('on').next('.cont_box').slideUp(300);
						};
					}else{
						$('.footer_site a.tit').removeClass('on').next('.cont_box').slideUp(300);
						if($target.is('a')){
							$(this).addClass('on').next('.cont_box').slideDown(300);
						}else{
							$(this).parents('a.tit').addClass('on').next('.cont_box').slideDown(300);
						};			
					};
					return false;
				});
				
				

			});

		
		})(jQuery);
	}
}catch(e) {
	console.error(e);
}
$(document).ready(function() {
    if (7 < $(".banner li").length) {
        var t = null;

        function n() {
            $(".banner ul").stop().animate({
                left: "-=130px"
            }, 0, function() {
                var e = $(".banner ul li:first").clone(!0);
                $(".banner ul li:first").remove(), $(".banner ul").css("left", 0), $(".banner ul").append(e)
            }), t && clearTimeout(t), t = setTimeout(n, 3e3)
        }
        $(document).ready(function() {
            t = setTimeout(n, 3e3), $rightB = $(".banner_controller .banner_next"), $leftB = $(".banner_controller .banner_prev"), $pauseB = $(".banner_controller .banner_ctrl");
            var e = !1;
            $leftB.click(function() {
                return "left", clearTimeout(t), $(".banner ul").stop().animate({
                    left: "0px"
                }, 0, function() {
                    var e = $(".banner ul li:last").clone(!0);
                    $(".banner ul li:last").remove(), $(".banner ul").css("left", "0"), $(".banner ul").prepend(e)
                }), t && clearTimeout(t), t = setTimeout(n, 3e3), !1
            }), $rightB.click(function() {
                return "right", clearTimeout(t), n(), !1
            }), $pauseB.click(function() {
                0 == e ? (clearTimeout(t), $pauseB.addClass("play").text("배너 롤링 재생하기"), e = !0) : (e = !1, $pauseB.removeClass("play").text("배너 롤링 일시정지하기"), t = setTimeout(n, 1500))
            }), $(".banner ul li a").on("mouseover focusin", function() {
                clearTimeout(t)
            }), $(".banner ul li a").on("mouseleave focusout", function() {
                e = !1
            })
        })
    }
});


$(document).ready(function() {
    if (7 < $(".search_text li").length) {
        var t = null;

        function n() {
            $(".search_text ul").stop().animate({
                left: "-=130px"
            }, 0, function() {
                var e = $(".search_text ul li:first").clone(!0);
                $(".search_text ul li:first").remove(), $(".search_text ul").css("left", 0), $(".search_text ul").append(e)
            }), t && clearTimeout(t), t = setTimeout(n, 3e3)
        }
        $(document).ready(function() {
            t = setTimeout(n, 3e3), $rightB = $(".search_text_controller .search_text_next"), $leftB = $(".search_text_controller .search_text_prev"), $pauseB2 = $(".search_text_controller .search_text_ctrl");
            var e = !1;
            $leftB.click(function() {
                return "left", clearTimeout(t), $(".search_text ul").stop().animate({
                    left: "0px"
                }, 0, function() {
                    var e = $(".search_text ul li:last").clone(!0);
                    $(".search_text ul li:last").remove(), $(".search_text ul").css("left", "0"), $(".search_text ul").prepend(e)
                }), t && clearTimeout(t), t = setTimeout(n, 3e3), !1
            }), $rightB.click(function() {
                return "right", clearTimeout(t), n(), !1
            }), $pauseB2.click(function() {
                0 == e ? (clearTimeout(t), $pauseB2.addClass("play").text("검색어 재생하기"), e = !0) : (e = !1, $pauseB2.removeClass("play").text("검색어 일시정지하기"), t = setTimeout(n, 1500))
            }), $(".search_text ul li a").on("mouseover focusin", function() {
                clearTimeout(t)
            }), $(".search_text ul li a").on("mouseleave focusout", function() {
                e = !1
            })
        })
    }
});




	



$(window).resize(function(){
	step_resizer();
});
$(document).ready(function(){
	step_resizer();
});

function step_resizer(){

	$(".step_list").each(function(){
	   var nodes1=$(this).children().children('.cont_box').children('.cont');
		  nodes1.each(function(){
			 $(this).css("height","auto");
		  });
	});
   $(".step_list").each(function(){

   var nodes1=$(this).children().children('.cont_box').children('.cont');
      var max_h=0;
      nodes1.each(function(){
         var h = parseInt($(this).css("height"));
         if(max_h<h){ max_h = h; }
      });
      nodes1.each(function(){
         $(this).css({height:max_h});
      });
   var nodes2=$(this).children().children('.cont_box').children('.tit');
      var max_h=0;
      nodes2.each(function(){
         var h = parseInt($(this).css("height"));
         if(max_h<h){ max_h = h; }
      });
      nodes2.each(function(){
         $(this).css({height:max_h});
      });
   });
}



$(window).resize(function(){
	step_resizer2();
});
$(document).ready(function(){
	step_resizer2();
});

function step_resizer2(){

	$(".cst_450_list").each(function(){
	   var nodes1=$(this).children().children('.title_box');
		  nodes1.each(function(){
			 $(this).css("height","auto");
		  });
	});
   $(".cst_450_list").each(function(){

   var nodes1=$(this).children().children('.title_box');
      var max_h=0;
      nodes1.each(function(){
         var h = parseInt($(this).css("height"));
         if(max_h<h){ max_h = h; }
      });
      nodes1.each(function(){
         $(this).css({height:max_h});
      });
   var nodes2=$(this).children().children('.title_box');
      var max_h=0;
      nodes2.each(function(){
         var h = parseInt($(this).css("height"));
         if(max_h<h){ max_h = h; }
      });
      nodes2.each(function(){
         $(this).css({height:max_h});
      });
   });
}








$(document).ready(function(){
	//이미지 롤오버 
	$('.tab_nav .tab_list').addClass(function () {
		return 'list_' + $(this).children('li').size();
	});
});









(function($) {
    'use strict';
 $(function() {
        //��찓��
        var $window = $(window),
            $container = $('#container'),
            colgroup = $container.colgroup = {},
            $colgroup = colgroup.$element = $container.find('.colgroup'),
            tabMenu = colgroup.tabMenu = {},
            $tabMenu = tabMenu.$element = $colgroup.find('.tab_menu'),
            $tabNav = tabMenu.$contents = $tabMenu.find('.tab_nav'),
            $tabBtn = tabMenu.$btn = $tabMenu.find('.tab_btn'),
            $tabSelect = tabMenu.$select = $tabMenu.find('.tab_select');

        $window.on('screen:wide.layoutSub screen:web.layoutSub', function(event) {
            $tabSelect.removeClass('active');
            $tabNav.css('display', '');
        });

        $tabBtn.on('click.layoutSub', function(event) {
            var $this = $(this);

            $this.closest('.tab_menu').children('.tab_select').removeClass('active').text($this.text());

  
            if(mode === 'mobile') {
                $this.closest('.tab_nav').slideUp(250, 'easeInOutExpo');
            }
        });

        $tabSelect.on('click.layoutSub', function(event) {
            var $this = $(this),
                $tabNav = $this.next('.tab_nav');


            if(!$tabNav.is(':animated')) {
                $tabNav.slideToggle(250, 'easeInOutExpo');
                $this.toggleClass('active');
            }
        });


        $('.tab_menu.scripttab').not($('.prettyprint').children()).each(function(){
            var $this = $(this),
                $tab_select = $this.find('.tab_select'),
                ActiveText = $this.find('.tab_item.active').text();
            $tab_select.text(ActiveText);
        });

		


    });
})(window.jQuery);


(function($) {
	'use strict';
	
	$(function() {
	
		//탭버튼 - 버튼, 콘텐츠 분리
		$('.tab_layout').each(function(){
			var tab_layout = $(this),
			$tab = tab_layout.find('> .tab_button > ul > li'),
			$tabContent = tab_layout.find(' > .tab_content');
			
			
			$(window).on("load", function(){
				//로드 됐을때 보여줄 탭. data-roadtab 속성 없을시 기본값 1
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


