/**
 * Epic Lounge v4 - App Script
 * common26.js + sub.js 통합 (충돌 제거)
 *
 * 제거 항목:
 * - tab_layout 중복 (common26.js 256-285줄) → IIFE 버전 1개만 유지
 * - lastScroll 전역 변수 중복 → 스코프 내 변수로 통합
 * - $(window).scroll() 중복 등록 → 1개로 합침
 * - GetIEVersion() / checkIE() → IE 지원 종료, 불필요
 * - .overimg 이미지 롤오버 → CSS :hover로 대체
 * - .step_type_1 높이 균일화 → CSS flexbox로 대체
 * - .bind() → .on() 전환
 * - $(document).ready() 산발 → 1개로 통합
 */
(function($) {
  'use strict';

  // ══════════════════════════════════════
  // 헤더 메뉴 (common26.js에서 이식)
  // ══════════════════════════════════════

  function web_menu() {
    var top1menu = $('#lnb .top1menu'),
      depth1 = $('#lnb .top1menu > li'),
      depth1_t = $('#lnb .top1menu li.depth1 > .depth1_ti'),
      depth2 = $('.depth2 > li');

    top1menu.find(' > li > div').addClass('top2m');
    depth1_t.on({
      mouseenter: function() {
        $(this).parents('.depth1').addClass('on').find('div.top2m').stop().fadeIn(150);
      },
      focusin: function() {
        depth1.removeClass('on').find('div.top2m').stop().fadeOut(100);
        $(this).parents('.depth1').addClass('on').find('div.top2m').stop().fadeIn(150);
      }
    });

    depth1.find('ul').on('focusin', function() {
      $(this).parents('.depth1').addClass('on');
    });

    depth1.on('mouseleave', function() {
      $(this).removeClass('on').find('div.top2m').stop().fadeOut(100);
    });

    top1menu.find('li:last-child .top2m li:last-child a').on('focusout', function() {
      $('#lnb .depth1.on').removeClass('on');
      $(this).parents('li.depth1').removeClass('on').find('.top2m').stop().fadeOut(100);
    });

    depth2.on({
      mouseenter: function() { $(this).addClass('on'); },
      focusin: function() {
        depth2.find('li').removeClass('on');
        $(this).addClass('on');
      },
      focusout: function() { $(this).siblings('ul li:last-child()').removeClass('on'); }
    });

    depth2.on('mouseleave', function() {
      $(this).removeClass('on');
    });

    depth1.each(function(index) {
      $(this).addClass('depth1_' + index);
    });

    top1menu.find('> li:last-child').addClass('part_info');
    top1menu.find('> li:last-child ul li').each(function(index) {
      $(this).addClass('part_icon' + index);
    });
  }

  function mobile_menu() {
    var depth1 = $('.top1menu'),
      dep1_length = depth1.find(' > li').length;

    depth1.find(' > li > div').addClass('top2m');
    depth1.off();
    depth1.find(' > li > a').off();
    depth1.find(' ul > li a').off();

    depth1.find(' > li > a').siblings().each(function() {
      if (!$(this).hasClass('load_actvie')) {
        $(this).slideUp();
      }
    });

    $('.top1menu .top2m, .top1menu .top2m div').css('height', 'auto');
    $('.top1menu .top2m, .top1menu .top2m div.menu_bg2').removeClass('menu_bg2').addClass('menu_bg');
    $('.top1menu .top2m, .top1menu .top2m').removeClass('top2m2');

    depth1.find(' > li > a').on('click', function(event) {
      var depth2_has = $(this).siblings('div').length;
      if (depth2_has > 0) {
        event.preventDefault();
        var m_open = $(this).hasClass('active');
        if (m_open) {
          $(this).siblings().slideUp();
          $(this).removeClass('active');
        } else {
          depth1.find(' > li > div').stop().slideUp();
          depth1.find(' a').removeClass('active');
          $(this).siblings().slideDown();
          $(this).addClass('active');
        }
      }
    });

    depth1.find(' ul > li a').on('click', function(event) {
      var depth3_has = $(this).siblings('ul').length;
      if (depth3_has > 0) {
        event.preventDefault();
      }
      var m_open = $(this).hasClass('active');
      if (m_open) {
        $(this).siblings().slideUp();
        $(this).removeClass('active');
      } else {
        depth1.find(' ul ul').stop().slideUp();
        depth1.find(' ul a').removeClass('active');
        $(this).siblings().slideDown();
        $(this).addClass('active');
      }
    });
  }

  // ══════════════════════════════════════
  // 모바일 네비 슬라이드 (common26.js에서 이식)
  // ══════════════════════════════════════

  function initMobileNav() {
    var lnb = $('#lnb'),
      m_nav_open = $('.lnb_m_nav'),
      m_nav_close = $('.mask, .lnb_close button'),
      mask = $('.mask'),
      lnb_close = $('.lnb_close'),
      bodyFrame = $('body, html'),
      gnb_navi = $('.gnb_navi'),
      link_set = $('.link_set');

    m_nav_open.on('click', function() {
      lnb.animate({ right: 0 }, 500);
      gnb_navi.animate({ right: 0 }, 500);
      link_set.animate({ right: 0 }, 500);
      lnb_close.animate({ right: 280 }, 500);
      bodyFrame.css('overflow', 'hidden');
      bodyFrame.addClass('openM');
      m_nav_open.fadeOut(500);
      mask.show();
    });

    m_nav_close.on('click', function() {
      lnb.animate({ right: -280 }, 500);
      gnb_navi.animate({ right: -280 }, 500);
      link_set.animate({ right: -280 }, 500);
      lnb_close.animate({ right: -48 }, 500);
      bodyFrame.css('overflow', '');
      bodyFrame.removeClass('openM');
      m_nav_open.delay(300).fadeIn(0);
      mask.hide();
    });
  }

  // ══════════════════════════════════════
  // 사이트 링크 레이어 (common26.js에서 이식)
  // ══════════════════════════════════════

  function initSiteLink() {
    $('.site_link div.layer').fadeOut('fast');
    $('.site_link h3 button.open').on('click', function() {
      $('.site_link div.layer').fadeOut('fast');
      $(this).parent().addClass('on_btn');
      $(this).parent().next('div.layer').fadeIn('fast');
      return false;
    });
    $('.site_link .close').on('click', function() {
      $(this).parent().removeClass('on_btn');
      $(this).parent().fadeOut('fast');
      return false;
    });
    $('.sitelink_cont .layer li:last-child a').on('focusout', function() {
      $(this).parents('.layer').slideUp(300);
    });
  }

  // ══════════════════════════════════════
  // Right Bar (common26.js에서 이식)
  // ══════════════════════════════════════

  function initRightBar() {
    $('#right_bar .btn_1').on('click', function() {
      $('#call_popbox').addClass('on');
      $('#bl_box').addClass('on');
      $('#kaka_popbox').removeClass('on');
      return false;
    });
    $('#right_bar .btn_2').on('click', function() {
      $('#kaka_popbox').addClass('on');
      $('#bl_box').addClass('on');
      $('#call_popbox').removeClass('on');
      return false;
    });
    $('.close_btn').on('click', function() {
      $('#call_popbox').removeClass('on');
      $('#bl_box').removeClass('on');
      $('#kaka_popbox').removeClass('on');
      return false;
    });
  }

  // ══════════════════════════════════════
  // 탭 레이아웃 — 1개만 유지 (중복 제거)
  // ══════════════════════════════════════

  function initTabLayout() {
    $('.tab_layout').each(function() {
      var tab_layout = $(this),
        $tab = tab_layout.find('> .tab_button > ul > li'),
        $tabContent = tab_layout.find('> .tab_content');

      if (!tab_layout.is('[data-roadtab]')) {
        tab_layout.attr('data-roadtab', '1');
      }

      var num = tab_layout.attr('data-roadtab');
      var $tab_view = tab_layout.find('> .tab_content.tab' + num);
      $tab.eq(num - 1).addClass('active');
      $tab_view.addClass('active');

      $tab.on('click', function(event) {
        var this_eq = $tab.index($(this));
        $tab.removeClass('active');
        $tab.eq(this_eq).addClass('active');
        $tabContent.removeClass('active');
        tab_layout.find('.tab_content.tab' + (this_eq + 1)).addClass('active');
        event.preventDefault();
      });
    });
  }

  // ══════════════════════════════════════
  // SNS 공유 (sub.js에서 이식)
  // ══════════════════════════════════════

  function initSnsShare() {
    $('.snsbox .sns_btn').on('click', function() {
      var $this = $(this),
        $snsbox = $this.parent('.snsbox'),
        $layer = $this.siblings('.layer'),
        isActive = $snsbox.is('.active');

      if (!isActive) {
        $this.attr('title', 'sns 공유 닫기').text('sns 공유 닫기');
        $layer.animate({ width: 'show' }, 250);
        $snsbox.addClass('active');
      } else {
        $snsbox.removeClass('active');
        $this.attr('title', 'sns 공유 열기').text('sns 공유 열기');
        $layer.animate({ width: 'hide' }, 250);
      }
    });

    $('.snsbox .sns_close').on('click', function() {
      var $this = $(this),
        $snsbox = $this.parents('.snsbox'),
        $layer = $this.parent('.layer'),
        $sns_btn = $layer.siblings('.sns_btn');

      $snsbox.removeClass('active');
      $layer.animate({ width: 'hide' }, 250);
      $sns_btn.attr('title', 'sns 공유 열기').text('sns 공유 열기').focus();
    });
  }

  // ══════════════════════════════════════
  // 아코디언 (sub.js에서 이식)
  // ══════════════════════════════════════

  function initAccordion() {
    $('.acc_toggle').on('click', function() {
      var $part = $(this).parent('.part');
      if ($part.hasClass('close')) {
        $(this).next('.part_con').slideDown();
        $part.removeClass('close');
      } else {
        $(this).next('.part_con').slideUp();
        $part.addClass('close');
      }
    });
  }

  // ══════════════════════════════════════
  // 스크롤 감지 — 통합 1개 (중복 제거)
  // lastScroll: 스코프 내 변수 (전역 아님)
  // ══════════════════════════════════════

  function initScrollDetect() {
    var lastScroll = 0;
    $(window).on('scroll', function() {
      var scroll = $(this).scrollTop();
      // common26.js: #right_bar fixed
      if (scroll > 100) {
        $('#right_bar').addClass('fixed');
      } else {
        $('#right_bar').removeClass('fixed');
      }
      // sub.js: body .scroll 클래스
      if (scroll > 100) {
        $('body').addClass('scroll');
      } else {
        $('body').removeClass('scroll');
      }
      lastScroll = scroll;
    });
  }

  // ══════════════════════════════════════
  // 다크모드 테마 토글 (common26.js에서 이식)
  // ══════════════════════════════════════

  function initThemeToggle() {
    var body = $('body');
    var toggleBtn = $('#theme-toggle');
    var currentTheme = localStorage.getItem('theme');

    if (currentTheme === 'dark') {
      body.addClass('dark-theme');
    }

    toggleBtn.on('click', function() {
      body.toggleClass('dark-theme');
      if (body.hasClass('dark-theme')) {
        localStorage.setItem('theme', 'dark');
      } else {
        localStorage.setItem('theme', 'light');
      }
    });
  }

  // ══════════════════════════════════════
  // Side Menu (sub.js에서 이식)
  // ══════════════════════════════════════

  function initSideMenu() {
    if ($.fn.menu) {
      $('.side_menu').menu({
        cut: {},
        event: 'click',
        namespace: 'side'
      });
    }

    $('.tab_menu').not($('.prettyprint').children()).each(function() {
      var li_length = $(this).children('ul').find('li').length;
      $(this).addClass('divide' + li_length);
    });
  }

  // ══════════════════════════════════════
  // PopupZone 플러그인 (common26.js에서 이식)
  // ══════════════════════════════════════

  $.fn.PopupZone = function(options) {
    var settings = {
      prevBtn: '',
      nextBtn: '',
      playBtn: '',
      waitingTime: ''
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
    settings.moveType = null;
    settings.setTimeOut = null;
    var status = true;

    function emptySetting() {
      settings.areaDiv.find('.count').html(settings.nowNum + 1);
      settings.areaDiv.find('.all').html(settings.cnt);
      settings.areaDiv.find('li').hide();
    }

    function setRolling(aniFlag) {
      if (!settings.moveFlag) {
        if (settings.moveType === 'next' || settings.moveType == null) {
          settings.nowNum++;
          if (settings.nowNum === settings.cnt) settings.nowNum = 0;
        } else if (settings.moveType === 'prev') {
          settings.nowNum--;
          if (settings.nowNum < 0) settings.nowNum = (settings.cnt - 1);
        }
      }
      emptySetting();
      if (settings.cnt < 2) {
        aniFlag = true;
      }

      if (aniFlag) settings.areaDiv.find('li').eq(settings.nowNum).show();
      else settings.areaDiv.find('li').eq(settings.nowNum).fadeIn('normal');

      aniFlag = false;
      settings.moveFlag = false;
      if (status) {
        if (settings.cnt > 1) {
          settings.setTimeOut = setTimeout(setRolling, settings.waitingTime);
        }
      }
    }

    function playRolling() {
      if (status) {
        clearTimeout(settings.setTimeOut);
        settings.playBtn.attr('class', 'btn_play').html('팝업 롤링 재생');
        status = false;
      } else {
        settings.playBtn.attr('class', 'btn_pause').html('팝업 롤링 정지');
        status = true;
        setRolling();
      }
      return false;
    }

    function prevRolling() {
      clearTimeout(settings.setTimeOut);
      settings.moveType = 'prev';
      setRolling();
      return false;
    }

    function nextRolling() {
      clearTimeout(settings.setTimeOut);
      settings.moveType = 'next';
      setRolling();
      return false;
    }

    setRolling();
    settings.prevBtn.on('click', prevRolling);
    settings.nextBtn.on('click', nextRolling);
    settings.playBtn.on('click', playRolling);
  };

  // ══════════════════════════════════════
  // v4 전용: 사이드바 필터 AJAX
  // ══════════════════════════════════════

  function initV4Filter() {
    // 사이드바 필터 체크박스 변경 시 AJAX 호출
    $(document).on('change', '.v4-filter-checkbox input[type="checkbox"]', function() {
      var $form = $(this).closest('form');
      var ajaxUrl = $form.data('ajax-url');
      var containerId = $form.data('container');
      if (!ajaxUrl || !containerId) return;

      var formData = $form.serialize();
      $.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: formData + '&page=1',
        dataType: 'json',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        beforeSend: function() {
          $('#' + containerId).addClass('v4-loading');
        },
        success: function(res) {
          if (res && res.html) {
            $('#' + containerId).html(res.html);
          }
          if (res && res.total_count !== undefined) {
            $('.v4-total-count').text(res.total_count);
          }
        },
        complete: function() {
          $('#' + containerId).removeClass('v4-loading');
        }
      });
    });

    // 모바일 필터 토글
    $(document).on('click', '.v4-filter-toggle', function() {
      $(this).closest('.v4-sidebar').toggleClass('v4-sidebar--open');
    });
  }

  // ══════════════════════════════════════
  // v4 전용: 뷰 전환 (갤러리/리스트)
  // ══════════════════════════════════════

  function initV4ViewToggle() {
    $(document).on('click', '.v4-view-toggle button', function() {
      var mode = $(this).data('view');
      var $grid = $(this).closest('.v4-list-content').find('.v4-card-grid');
      $(this).siblings().removeClass('active');
      $(this).addClass('active');
      $grid.removeClass('v4-card-grid--gallery v4-card-grid--list');
      $grid.addClass('v4-card-grid--' + mode);
      localStorage.setItem('v4-view-mode', mode);
    });

    // 저장된 뷰 모드 복원
    var savedMode = localStorage.getItem('v4-view-mode');
    if (savedMode) {
      $('.v4-view-toggle button[data-view="' + savedMode + '"]').trigger('click');
    }
  }

  // ══════════════════════════════════════
  // v4 전용: 더보기 (AJAX 페이지네이션)
  // ══════════════════════════════════════

  function initV4LoadMore() {
    $(document).on('click', '.v4-load-more-btn', function() {
      var $btn = $(this);
      if ($btn.hasClass('v4-load-more--loading')) return;

      var ajaxUrl = $btn.data('ajax-url');
      var page = parseInt($btn.data('page')) + 1;
      var containerId = $btn.data('container');
      var $form = $btn.closest('.v4-list-layout').find('form');
      var formData = $form.length ? $form.serialize() : '';

      $btn.addClass('v4-load-more--loading');

      $.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: formData + '&page=' + page,
        dataType: 'json',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(res) {
          if (res && res.html) {
            $('#' + containerId).append(res.html);
            $btn.data('page', page);
          }
          if (res && res.is_last) {
            $btn.hide();
          }
        },
        complete: function() {
          $btn.removeClass('v4-load-more--loading');
        }
      });
    });
  }

  // ══════════════════════════════════════
  // v4 전용: 검색바
  // ══════════════════════════════════════

  function initV4Search() {
    $(document).on('submit', '.v4-search-form', function(e) {
      var $input = $(this).find('input[name="keyword"]');
      var keyword = $.trim($input.val());
      if (keyword.length === 0) {
        $input.focus();
        e.preventDefault();
        return false;
      }
    });
  }

  // ══════════════════════════════════════
  // 초기화 — 1개의 ready
  // ══════════════════════════════════════

  $(function() {
    // 메뉴 초기화 (반응형)
    function setupMenu() {
      if ($(window).width() > 1000) {
        web_menu();
      } else {
        mobile_menu();
      }
    }
    setupMenu();
    $(window).on('resize', setupMenu);

    // 공통 초기화
    initMobileNav();
    initSiteLink();
    initRightBar();
    initTabLayout();
    initSnsShare();
    initAccordion();
    initScrollDetect();
    initThemeToggle();
    initSideMenu();

    // PopupZone
    if ($('.popup').length) {
      $('.popup').PopupZone({
        prevBtn: '.popup_control .btn_prev',
        nextBtn: '.popup_control .btn_next',
        playBtn: '.popup_control .btn_pause',
        waitingTime: '6000'
      });
    }
    if ($('.popup_small').length) {
      $('.popup_small').PopupZone({
        prevBtn: '.popup_control2 .btn_prev',
        nextBtn: '.popup_control2 .btn_next',
        playBtn: '.popup_control2 .btn_pause',
        waitingTime: '6000'
      });
    }

    // v4 전용 초기화
    initV4Filter();
    initV4ViewToggle();
    initV4LoadMore();
    initV4Search();

    // body 클래스
    $('html').addClass('body_active');
    $('body').addClass('active');
  });

})(jQuery);

// ══════════════════════════════════════
// 전역 함수 (외부 호출용 — SNS 공유)
// ══════════════════════════════════════

function naverSns(title, url) {
  if (title === '') {
    title = $('.btn-naver').attr('data-title');
  }
  window.open(
    'http://blog.naver.com/openapi/share?url=' + encodeURIComponent(url) + '&title=' + encodeURIComponent(title),
    'sns',
    'width=500,height=600,scrollbars=yes,toolbar=no,menubar=no'
  );
}

function facebookSns(title, url) {
  if (title === '') {
    title = $('.btn-facebook').attr('data-title');
  }
  window.open(
    'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url),
    'sns',
    'width=500,height=300,scrollbars=yes,toolbar=no,menubar=no'
  );
}

// 링크 복사
function copyLink(url) {
  if (!url) url = window.location.href;
  if (navigator.clipboard) {
    navigator.clipboard.writeText(url).then(function() {
      alert('링크가 복사되었습니다.');
    });
  } else {
    var textarea = document.createElement('textarea');
    textarea.value = url;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    alert('링크가 복사되었습니다.');
  }
}
