<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * Admin Navigation Data & Rendering
 * 통합 관리자 메뉴 정의 및 출력 함수
 */

function get_admin_nav_data() {
    global $menu, $amenu;
    
    // G5 기본 메뉴 데이터가 있으면 활용하고, 없으면 하드코딩된 데이터를 보충합니다.
    $nav = array();
    
    // 100: 환경설정
    $nav['100'] = array(
        'title' => '환경설정',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>',
        'url' => G5_ADMIN_URL.'/config_form.php'
    );
    
    // 200: 회원관리
    $nav['200'] = array(
        'title' => '회원관리',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'url' => G5_ADMIN_URL.'/member_list.php'
    );
    
    // 300: 게시판관리
    $nav['300'] = array(
        'title' => '게시판관리',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 8h10"/><path d="M7 12h10"/><path d="M7 16h10"/></svg>',
        'url' => G5_ADMIN_URL.'/board_list.php'
    );

    // 400/500: 쇼핑몰 (사용할 경우만)
    if (defined('G5_USE_SHOP') && G5_USE_SHOP) {
        $nav['400'] = array(
            'title' => '쇼핑몰설정',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>',
            'url' => G5_ADMIN_URL.'/shop_admin/configform.php'
        );
        $nav['500'] = array(
            'title' => '쇼핑몰현황',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>',
            'url' => G5_ADMIN_URL.'/shop_admin/itemsellrank.php'
        );
    }

    // 600: 리소스 관리
    $nav['600'] = array(
        'title' => '리소스 관리',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>',
        'url' => G5_ADMIN_URL.'/rsc_review_list.php'
    );

    // 700: 이벤트관리
    $nav['700'] = array(
        'title' => '이벤트관리',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="m9 16 2 2 4-4"/></svg>',
        'url' => G5_ADMIN_URL.'/rsc_event_list.php'
    );

    // 900: 서비스 (기존 주소변환기 등)
    $nav['900'] = array(
        'title' => 'SERVICE',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>',
        'url' => G5_ADMIN_URL.'/service_main.php'
    );

    return $nav;
}

function render_admin_nav() {
    global $menu, $amenu, $sub_menu;
    
    $nav_data = get_admin_nav_data();
    $jj = 1;
    
    echo '<ul class="gnb_ul">';
    
    foreach ($nav_data as $key => $data) {
        $current_class = "";
        
        // 데이터 누락 방지: admin_nav.php가 로드되지 않았을 경우를 대비해 여기서 다시 체크
        if (!isset($menu['menu'.$key]) || empty($menu['menu'.$key])) {
            // 강제로 다시 로드 시도 (이전 include 실패 대비)
            @include_once(G5_ADMIN_PATH.'/admin_nav.php');
        }

        // 현재 카테고리 활성화 판단
        if (isset($sub_menu) && substr($sub_menu, 0, 3) == $key) {
            $current_class = " on";
        }

        $button_title = $data['title'];
        $menu_icon = $data['icon'];
        $first_url = $data['url'];
        
        // 실제 메뉴 아이템이 있으면 첫 번째 아이템의 URL을 가져옴
        if (isset($menu['menu'.$key][1][2])) {
            $first_url = $menu['menu'.$key][1][2];
        }

        echo '<li class="gnb_li' . $current_class . '">';
        echo '  <button type="button" class="btn_op menu-' . $key . '" title="' . $button_title . '" onclick="location.href=\'' . $first_url . '\'">';
        echo '    <span class="menu_icon">' . $menu_icon . '</span>';
        echo '    <span class="menu_text" style="display:none">' . $button_title . '</span>';
        echo '  </button>';
        echo '  <div class="gnb_oparea_wr">';
        echo '    <div class="gnb_oparea">';
        echo '      <h3>' . $button_title . '</h3>';
        echo        print_menu1('menu' . $key, 1);
        echo '    </div>';
        echo '  </div>';
        echo '</li>';
        
        $jj++;
    }
    
    echo '</ul>';
}
?>
