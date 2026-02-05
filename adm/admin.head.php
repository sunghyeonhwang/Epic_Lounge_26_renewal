<?php
if (!defined('_GNUBOARD_'))
    exit;

$g5_debug['php']['begin_time'] = $begin_time = get_microtime();

$files = glob(G5_ADMIN_PATH . '/css/admin_extend_*');
if (is_array($files)) {
    foreach ((array) $files as $k => $css_file) {
        $fileinfo = pathinfo($css_file);
        $ext = $fileinfo['extension'];

        if ($ext !== 'css')
            continue;

        $css_file = str_replace(G5_ADMIN_PATH, G5_ADMIN_URL, $css_file);
        add_stylesheet('<link rel="stylesheet" href="' . $css_file . '">', $k);
    }
}
add_stylesheet('<link rel="stylesheet" href="' . G5_ADMIN_URL . '/css/admin_modern_sidebar.css">', 100);

include_once (G5_PATH . '/head.sub.php');

function print_menu1($key, $no = '')
{
    global $menu;

    $str = print_menu2($key, $no);

    return $str;
}

function print_menu2($key, $no = '')
{
    global $menu, $auth_menu, $is_admin, $auth, $g5, $sub_menu;

    $str = '<ul>';
    for ($i = 1; $i < count($menu[$key]); $i++) {
        if (!isset($menu[$key][$i])) {
            continue;
        }

        if ($is_admin != 'super' && (!array_key_exists($menu[$key][$i][0], $auth) || !strstr($auth[$menu[$key][$i][0]], 'r')))
            continue;

        $gnb_grp_div = $gnb_grp_style = '';

        if (isset($menu[$key][$i][4])) {
            if (($menu[$key][$i][4] == 1 && $gnb_grp_style == false) || ($menu[$key][$i][4] != 1 && $gnb_grp_style == true))
                $gnb_grp_div = 'gnb_grp_div';

            if ($menu[$key][$i][4] == 1)
                $gnb_grp_style = 'gnb_grp_style';
        }

        $current_class = '';

        if ($menu[$key][$i][0] == $sub_menu) {
            $current_class = ' on';
        }

        $str .= '<li data-menu="' . $menu[$key][$i][0] . '"><a href="' . $menu[$key][$i][2] . '" class="gnb_2da ' . $gnb_grp_style . ' ' . $gnb_grp_div . $current_class . '">' . $menu[$key][$i][1] . '</a></li>';

        $auth_menu[$menu[$key][$i][0]] = $menu[$key][$i][1];
    }
    $str .= '</ul>';

    return $str;
}

$adm_menu_cookie = array(
    'container' => '',
    'gnb' => '',
    'btn_gnb' => '',
);

if (!empty($_COOKIE['g5_admin_btn_gnb'])) {
    $adm_menu_cookie['container'] = 'container-small';
    $adm_menu_cookie['gnb'] = 'gnb_small';
    $adm_menu_cookie['btn_gnb'] = 'btn_gnb_open';
}
?>

<script>
var tempX = 0;
var tempY = 0;

function imageview(id, w, h)
{

    menu(id);

    var el_id = document.getElementById(id);

    //submenu = eval(name+".style");
    submenu = el_id.style;
    submenu.left = tempX - ( w + 11 );
    submenu.top  = tempY - ( h / 2 );

    selectBoxVisible();

    if (el_id.style.display != 'none')
        selectBoxHidden(id);
}
</script>

<div id="to_content"><a href="#container">본문 바로가기</a></div>

<header id="hd">
    <h1><?php echo $config['cf_title'] ?></h1>
    <div id="hd_top">
        <?php /* <button type="button" id="btn_gnb" class="btn_gnb_close <?php echo $adm_menu_cookie['btn_gnb'];?>">메뉴</button> */ ?>
       <div id="logo"><a href="<?php echo G5_ADMIN_URL; ?>" class="text_logo">EPIC LOUNGE 관리</a></div>

        <div id="tnb">
            <ul>
                <?php  /* if (defined('G5_USE_SHOP') && G5_USE_SHOP) { ?>
                 <li class="tnb_li"><a href="<?php echo G5_SHOP_URL ?>/" class="tnb_shop" target="_blank" title="쇼핑몰 바로가기">쇼핑몰 바로가기</a></li>
                 <?php } ?>
                 <li class="tnb_li"><a href="<?php echo G5_URL ?>/" class="tnb_community" target="_blank" title="커뮤니티 바로가기">커뮤니티 바로가기</a></li>
                 <li class="tnb_li"><a href="<?php echo G5_ADMIN_URL ?>/service.php" class="tnb_service">부가서비스</a></li> */
                ?>
                <li class="tnb_li"><a href="<?php echo G5_BBS_URL ?>/logout.php?url=<?php echo urlencode('/v3/adm') ?>" class="tnb_mb_btn" style="color:#fff; font-weight:700; text-decoration:none;">로그아웃</a>
                </li>
            </ul>
        </div>
    </div>
    <nav id="gnb" class="gnb_large <?php echo $adm_menu_cookie['gnb']; ?>">
        <h2>관리자 주메뉴</h2>
        <ul class="gnb_ul">
            <?php
            $jj = 1;
            foreach ($amenu as $key => $value) {
                $href1 = $href2 = '';

                if (isset($menu['menu' . $key][0][2]) && $menu['menu' . $key][0][2]) {
                    $href1 = '<a href="' . $menu['menu' . $key][0][2] . '" class="gnb_1da">';
                    $href2 = '</a>';
                } else {
                    continue;
                }

                $current_class = '';
                if (isset($sub_menu) && (substr($sub_menu, 0, 3) == substr($menu['menu' . $key][0][0], 0, 3)))
                    $current_class = ' on';

                $button_title = $menu['menu' . $key][0][1];

                // SVG 아이콘 정의
                $menu_icon = '';
                switch ($key) {
                    case '100':  // 환경설정
                        $menu_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>';
                        break;
                    case '200':  // 회원관리
                        $menu_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>';
                        break;
                    case '300':  // 게시판관리
                        $menu_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 8h10"/><path d="M7 12h10"/><path d="M7 16h10"/></svg>';
                        break;
                    case '400':  // 쇼핑몰관리
                        $menu_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>';
                        break;
                    case '500':  // 쇼핑몰현황
                        $menu_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>';
                        break;
                    case '600':  // 시스템/리소스
                        $menu_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>';
                        break;
                    case '700':  // 이벤트관리
                        $menu_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="m9 16 2 2 4-4"/></svg>';
                        break;
                    case '900':  // SERVICE
                        $menu_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecapround="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>';
                        break;
                    default:
                        $menu_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>';
                }
                ?>
            <li class="gnb_li<?php echo $current_class; ?>">
                <button type="button" class="btn_op menu-<?php echo $key; ?> menu-order-<?php echo $jj; ?>" title="<?php echo $button_title; ?>">
                    <span class="menu_icon"><?php echo $menu_icon; ?></span>
                    <span class="menu_text" style="display:none"><?php echo $button_title; ?></span>
                </button>
                <div class="gnb_oparea_wr">
                    <div class="gnb_oparea">
                        <h3><?php echo $menu['menu' . $key][0][1]; ?></h3>
                        <?php echo print_menu1('menu' . $key, 1); ?>
                    </div>
                </div>
            </li>
            <?php
                $jj++;
            }  // end foreach
            ?>
        </ul>
    </nav>

</header>
<script>
jQuery(function($){

    var menu_cookie_key = 'g5_admin_btn_gnb';

    $(".tnb_mb_btn").click(function(){
        $(".tnb_mb_area").toggle();
    });

    $("#btn_gnb").click(function(){
        
        var $this = $(this);

        try {
            if( ! $this.hasClass("btn_gnb_open") ){
                set_cookie(menu_cookie_key, 1, 60*60*24*365);
            } else {
                delete_cookie(menu_cookie_key);
            }
        }
        catch(err) {
        }

        $("#container").toggleClass("container-small");
        $("#gnb").toggleClass("gnb_small");
        $this.toggleClass("btn_gnb_open");

    });

    $(".gnb_ul li .btn_op" ).click(function() {
        $(this).parent().addClass("on").siblings().removeClass("on");
    });

    // 서브메뉴 그룹 접기/펴기 기능 (UE57, 시작해요 25 등)
    function init_menu_groups() {
        $(".gnb_2da.gnb_grp_style").each(function() {
            var $headerA = $(this);
            var $headerLi = $headerA.closest('li');
            var $nextItems = $headerLi.nextUntil('li:has(.gnb_grp_style)');
            var menuKeyClass = $headerLi.closest('.gnb_li').find('.btn_op').attr('class');

            if ($nextItems.length > 0) {
                // 헤더 LI 스타일 설정
                $headerLi.addClass('gnb_grp_header').css({
                    "position": "relative",
                    "cursor": "pointer"
                });

                // 현재 상태에 따라 초기 노출 여부 결정
                var hasOn = $nextItems.find("a.on").length > 0 || $headerA.hasClass("on");

                // 특정 메뉴 자동 펼침 처리
                if (menuKeyClass) {
                    // 리소스 관리 (600)는 모두 펼침
                    if (menuKeyClass.indexOf('menu-600') > -1) {
                        hasOn = true;
                    }
                    // 이벤트 관리 (700)는 모두 펼침
                    if (menuKeyClass.indexOf('menu-700') > -1) {
                        hasOn = true;
                    }
                }

                if (hasOn) {
                    $headerLi.addClass('on');
                    $nextItems.show();
                } else {
                    $headerLi.removeClass('on').addClass('collapsed');
                    $nextItems.hide();
                }
            }
        });
    }

    // LI 전체 클릭 이벤트
    $(document).on("click", ".gnb_grp_header", function(e) {
        var $headerLi = $(this);
        var $nextItems = $headerLi.nextUntil('li:has(.gnb_grp_style)');
        
        // a 태그 클릭 시 기본 동작(페이지 이동) 방지
        e.preventDefault();

        if ($nextItems.length > 0) {
            $nextItems.slideToggle(150);
            $headerLi.toggleClass('collapsed');
            
            // 화살표 방향 변경
            var $icon = $headerLi.find('.grp_arrow');
            if ($icon.hasClass('fa-angle-up')) {
                $icon.removeClass('fa-angle-up').addClass('fa-angle-down');
            } else {
                $icon.removeClass('fa-angle-down').addClass('fa-angle-up');
            }
        }
    });

    // 화살표 아이콘 주입
    function inject_arrows() {
        $(".gnb_grp_header").each(function() {
            if ($(this).find('.grp_arrow').length === 0) {
                var isVisible = $(this).next().is(':visible');
                var arrowClass = isVisible ? 'fa-angle-up' : 'fa-angle-down';
                $(this).append('<i class="fa ' + arrowClass + ' grp_arrow" style="position:absolute; right:15px; top:50%; margin-top:-7px; font-size:14px; color:#64748b; pointer-events:none;"></i>');
            }
        });
    }

    // 메뉴 열릴 때 실행
    $(".btn_op").click(function() {
        setTimeout(function() {
            init_menu_groups();
            inject_arrows();
        }, 50);
    });

    // 초기 실행
    init_menu_groups();
    inject_arrows();

});
</script>


<div id="wrapper">

    <div id="container" class="<?php echo $adm_menu_cookie['container']; ?>">

        <h1 id="container_title"><?php echo $g5['title'] ?></h1>
        <div class="container_wr">