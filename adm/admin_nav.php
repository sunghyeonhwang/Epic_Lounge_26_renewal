<?php
if (!defined('_GNUBOARD_')) exit;

// 전역 변수 설정
global $menu, $amenu;

// 메뉴 초기화 (기존 G5 로직으로 인해 누락되는 경우 방지)
if (!isset($amenu) || !is_array($amenu)) $amenu = array();
if (!isset($menu) || !is_array($menu)) $menu = array();

// --- 100: 환경설정 ---
$amenu['100'] = 'admin.menu100.php';
$menu['menu100'] = array (
    array('100000', '환경설정', G5_ADMIN_URL.'/config_form.php',   'config'),
    array('100100', '기본환경설정', G5_ADMIN_URL.'/config_form.php',   'cf_basic'),
    array('100200', '관리권한설정', G5_ADMIN_URL.'/auth_list.php',     'cf_auth'),
    array('100280', '테마설정', G5_ADMIN_URL.'/theme.php',     'cf_theme', 1),
    array('100290', '메뉴설정', G5_ADMIN_URL.'/menu_list.php',     'cf_menu', 1),
    array('100300', '메일 테스트', G5_ADMIN_URL.'/sendmail_test.php', 'cf_mailtest'),
    array('100310', '팝업레이어관리', G5_ADMIN_URL.'/newwinlist.php', 'scf_poplayer'),
    array('100800', '세션파일 일괄삭제',G5_ADMIN_URL.'/session_file_delete.php', 'cf_session', 1),
    array('100900', '캐시파일 일괄삭제',G5_ADMIN_URL.'/cache_file_delete.php',   'cf_cache', 1),
    array('100910', '캡챠파일 일괄삭제',G5_ADMIN_URL.'/captcha_file_delete.php',   'cf_captcha', 1),
    array('100920', '썸네일파일 일괄삭제',G5_ADMIN_URL.'/thumbnail_file_delete.php',   'cf_thumbnail', 1),
    array('100500', 'phpinfo()',        G5_ADMIN_URL.'/phpinfo.php',       'cf_phpinfo'),
    array('100410', 'DB업그레이드', G5_ADMIN_URL.'/dbupgrade.php', 'db_upgrade'),
    array('100400', '부가서비스', G5_ADMIN_URL.'/service.php', 'cf_service')
);

// --- 200: 회원관리 ---
$amenu['200'] = 'admin.menu200.php';
$menu['menu200'] = array (
    array('200000', '회원관리', G5_ADMIN_URL.'/member_list.php', 'member'),
    array('200100', '회원관리', G5_ADMIN_URL.'/member_list.php', 'mb_list'),
    array('200300', '회원메일발송', G5_ADMIN_URL.'/mail_list.php', 'mb_mail'),
    array('200800', '접속자집계', G5_ADMIN_URL.'/visit_list.php', 'mb_visit', 1),
    array('200810', '접속자검색', G5_ADMIN_URL.'/visit_search.php', 'mb_search', 1),
    array('200820', '접속자로그삭제', G5_ADMIN_URL.'/visit_delete.php', 'mb_delete', 1),
    array('200200', '포인트관리', G5_ADMIN_URL.'/point_list.php', 'mb_point'),
    array('200900', '투표관리', G5_ADMIN_URL.'/poll_list.php', 'mb_poll')
);

// --- 300: 게시판관리 ---
$amenu['300'] = 'admin.menu300.php';
$menu['menu300'] = array (
    array('300000', '게시판관리', ''.G5_ADMIN_URL.'/board_list.php', 'board'),
    array('300100', '게시판관리', ''.G5_ADMIN_URL.'/board_list.php', 'bbs_board'),
    array('300200', '게시판그룹관리', ''.G5_ADMIN_URL.'/boardgroup_list.php', 'bbs_group'),
    array('300500', '1:1문의설정', ''.G5_ADMIN_URL.'/qa_config.php', 'qa'),
    array('300820', '글,댓글 현황', G5_ADMIN_URL.'/write_count.php', 'scf_write_count')
);

// --- 400: 쇼핑몰관리 ---
if (defined('G5_USE_SHOP') && G5_USE_SHOP) {
    $amenu['400'] = 'admin.menu400.shop_1of2.php';
    $menu['menu400'] = array (
        array('400000', '쇼핑몰관리', G5_ADMIN_URL.'/shop_admin/', 'shop_config'),
        array('400010', '쇼핑몰현황', G5_ADMIN_URL.'/shop_admin/', 'shop_index'),
        array('400100', '쇼핑몰설정', G5_ADMIN_URL.'/shop_admin/configform.php', 'scf_config'),
        array('400400', '주문내역', G5_ADMIN_URL.'/shop_admin/orderlist.php', 'scf_order', 1),
        array('400440', '개인결제관리', G5_ADMIN_URL.'/shop_admin/personalpaylist.php', 'scf_personalpay', 1),
        array('400200', '분류관리', G5_ADMIN_URL.'/shop_admin/categorylist.php', 'scf_cate'),
        array('400300', '상품관리', G5_ADMIN_URL.'/shop_admin/itemlist.php', 'scf_item'),
        array('400660', '상품문의', G5_ADMIN_URL.'/shop_admin/itemqalist.php', 'scf_item_qna'),
        array('400650', '사용후기', G5_ADMIN_URL.'/shop_admin/itemuselist.php', 'scf_ps'),
        array('400620', '상품재고관리', G5_ADMIN_URL.'/shop_admin/itemstocklist.php', 'scf_item_stock'),
        array('400610', '상품유형관리', G5_ADMIN_URL.'/shop_admin/itemtypelist.php', 'scf_item_type'),
        array('400500', '상품옵션재고관리', G5_ADMIN_URL.'/shop_admin/optionstocklist.php', 'scf_item_option'),
        array('400800', '쿠폰관리', G5_ADMIN_URL.'/shop_admin/couponlist.php', 'scf_coupon'),
        array('400810', '쿠폰존관리', G5_ADMIN_URL.'/shop_admin/couponzonelist.php', 'scf_coupon_zone'),
        array('400750', '추가배송비관리', G5_ADMIN_URL.'/shop_admin/sendcostlist.php', 'scf_sendcost', 1),
        array('400410', '미완료주문', G5_ADMIN_URL.'/shop_admin/inorderlist.php', 'scf_inorder', 1),
    );

    // --- 500: 쇼핑몰현황/기타 ---
    $amenu['500'] = 'admin.menu500.shop_2of2.php';
    $menu['menu500'] = array (
        array('500000', '쇼핑몰현황/기타', G5_ADMIN_URL.'/shop_admin/itemsellrank.php', 'shop_stats'),
        array('500110', '매출현황', G5_ADMIN_URL.'/shop_admin/sale1.php', 'sst_order_stats'),
        array('500100', '상품판매순위', G5_ADMIN_URL.'/shop_admin/itemsellrank.php', 'sst_rank'),
        array('500120', '주문내역출력', G5_ADMIN_URL.'/shop_admin/orderprint.php', 'sst_print_order', 1),
        array('500400', '재입고SMS알림', G5_ADMIN_URL.'/shop_admin/itemstocksms.php', 'sst_stock_sms', 1),
        array('500300', '이벤트관리', G5_ADMIN_URL.'/shop_admin/itemevent.php', 'scf_event'),
        array('500310', '이벤트일괄처리', G5_ADMIN_URL.'/shop_admin/itemeventlist.php', 'scf_event_mng'),
        array('500500', '배너관리', G5_ADMIN_URL.'/shop_admin/bannerlist.php', 'scf_banner', 1),
        array('500140', '보관함현황', G5_ADMIN_URL.'/shop_admin/wishlist.php', 'sst_wish'),
        array('500210', '가격비교사이트', G5_ADMIN_URL.'/shop_admin/price.php', 'sst_compare', 1)
    );
}

// --- 600: 리소스 관리 ---
$amenu['600'] = 'admin.menu600.php';
$menu["menu600"] = array (
    array('600000', '리소스 관리', ''.G5_ADMIN_URL.'/rsc_review_list.php', 'rsc_review_list'),
    array('600000', '사이트메인 관리', ''.G5_ADMIN_URL.'/rsc_main_banner_mng.php', 'rsc_main_banner_mng', 1),
    array('600950', '메인 비쥬얼관리', ''.G5_ADMIN_URL.'/rsc_main_banner_mng.php', 'rsc_main_banner_mng'),
    array('600960', '메인 새소식관리', ''.G5_ADMIN_URL.'/rsc_main_news_mng.php', 'rsc_main_news_mng'),
    array('600970', '메인 리소스관리', ''.G5_ADMIN_URL.'/rsc_main_res_mng.php', 'rsc_main_res_mng'),
    array('600000', '새소식', ''.G5_ADMIN_URL.'/rsc_news_list.php', 'rsc_news_list', 1),
    array('600700', '새소식 관리', ''.G5_ADMIN_URL.'/rsc_news_list.php', 'rsc_news_list'),
    array('600000', '이벤트 관리', ''.G5_ADMIN_URL.'/rsc_event_list.php', 'rsc_event_list', 1),
    array('600980', '이벤트 관리', ''.G5_ADMIN_URL.'/rsc_event_list.php', 'rsc_event_list'),
    array('600990', '글로벌 이벤트 관리', ''.G5_ADMIN_URL.'/rsc_global_event_list.php', 'rsc_global_event_list'),
    array('600000', '리소스 배너관리', ''.G5_ADMIN_URL.'/rsc_banner_mng.php', 'rsc_banner_mng', 1),
    array('600900', '리소스 배너관리', ''.G5_ADMIN_URL.'/rsc_banner_mng.php', 'rsc_banner_mng'),
    array('600000', '다시보기', ''.G5_ADMIN_URL.'/rsc_review_mng.php', 'rsc_review_mng', 1),
    array('600100', '리소스 카테고리 관리', ''.G5_ADMIN_URL.'/rsc_review_mng.php', 'rsc_review_mng'),
    array('600200', '리소스 관리', ''.G5_ADMIN_URL.'/rsc_review_list.php', 'rsc_review_list'),
    array('600000', '무료콘텐츠', ''.G5_ADMIN_URL.'/rsc_free_mng.php', 'rsc_free_mng', 1),
    array('600300', '리소스 카테고리 관리', ''.G5_ADMIN_URL.'/rsc_free_mng.php', 'rsc_free_mng'),
    array('600400', '리소스 관리', ''.G5_ADMIN_URL.'/rsc_free_list.php', 'rsc_free_list'),
    array('600000', '백서', ''.G5_ADMIN_URL.'/rsc_book_mng.php', 'rsc_book_mng', 1),
    array('600500', '리소스 카테고리 관리', ''.G5_ADMIN_URL.'/rsc_book_mng.php', 'rsc_book_mng'),
    array('600600', '리소스 관리', ''.G5_ADMIN_URL.'/rsc_book_list.php', 'rsc_book_list'),
);

// --- 700: 이벤트관리 ---
$amenu['700'] = 'admin.menu700.php';
$menu["menu700"] = array (
    array('700000', '이벤트 관리', 'rsc_event_list', 1),
    array('700000', 'UE57', ''.G5_ADMIN_URL.'/rsc_event_list.php', 'rsc_event_list', 1),
    array('700100', '신청 목록', ''.G5_ADMIN_URL.'/2025tw_event_list2.php', 'rsc_event_list'),
    array('700300', '라이브 접속 리스트', ''.G5_ADMIN_URL.'/2025tw_live_list602.php', '2023_live_list'),
    array('700000', '시작해요 25', ''.G5_ADMIN_URL.'/rsc_event_list.php', 'rsc_event_list', 1),
    array('700100', '신청 목록', ''.G5_ADMIN_URL.'/2025tw_event_list.php', 'rsc_event_list'),
    array('700300', '라이브 접속 리스트', ''.G5_ADMIN_URL.'/2025tw_live_list60.php', '2023_live_list'),
    array('700400', '문의내역', ''.G5_ADMIN_URL.'/2025tw_event_inquery_list.php', '2023_event_inquery_list'),
    array('700700', 'UE FEST 25', ''.G5_ADMIN_URL.'/2024_event2_list.php', '2024_event2_list', 1),
    array('700710', '신청목록', ''.G5_ADMIN_URL.'/2025_event2_list.php', '2025_event2_list'),
    array('700720', '이벤트 잔여석', ''.G5_ADMIN_URL.'/2025_event2_remain.php', '2025_event2_remain'),
    array('700730', '쿠폰목록', ''.G5_ADMIN_URL.'/2025_event2_coupon_list.php', '2025_event2_coupon_list'),
    array('700740', '단체신청', ''.G5_ADMIN_URL.'/2025_event2_coupon_team_list.php', '2025_event2_coupon_team_list'),
    array('700750', '스폰서쿠폰목록', ''.G5_ADMIN_URL.'/2025_event2_coupon_sp_list.php', '2025_event2_coupon_sp_list'),
    array('700760', '라이브접속목록', ''.G5_ADMIN_URL.'/2025_live_list.php', '2025_live_list'),
    array('700707', '스피커', ''.G5_ADMIN_URL.'/2025_event_speaker.php', '2025_event_speaker'),
    array('700600', '챌린지 25', ''.G5_ADMIN_URL.'/event_list3.php', 'event_list3', 1),
    array('700610', '이벤트 목록', ''.G5_ADMIN_URL.'/event_list3.php', 'event_list3'),
    array('700600', 'UE FEST 24', ''.G5_ADMIN_URL.'/2024_event2_list.php', '2024_event2_list', 1),
    array('700610', '이벤트 목록', ''.G5_ADMIN_URL.'/2024_event2_list.php', '2024_event2_list'),
    array('700620', '이벤트 잔여석', ''.G5_ADMIN_URL.'/2024_event2_remain.php', '2024_event2_remain'),
    array('700630', '쿠폰목록', ''.G5_ADMIN_URL.'/2024_event2_coupon_list.php', '2024_event2_coupon_list'),
    array('700500', 'UE FEST 24 스피커', ''.G5_ADMIN_URL.'/rsc_2024_event_list.php', 'rsc_2024_event_list', 1),
    array('700520', '이벤트 목록', ''.G5_ADMIN_URL.'/2024_event_list.php', 'rsc_2024_event_list'),
    array('700800', '언리얼챌린지', ''.G5_ADMIN_URL.'/event_list2.php', 'event_list2', 1),
    array('700810', '24 언리얼챌린지', ''.G5_ADMIN_URL.'/event_list2.php', 'event_list2'),
    array('700820', '25 언리얼챌린지', ''.G5_ADMIN_URL.'/event_list3.php', 'event_list3'),
    array('700600', '챌린지 24', ''.G5_ADMIN_URL.'/event_list2.php', 'event_list2', 1),
    array('700610', '이벤트 목록', ''.G5_ADMIN_URL.'/event_list2.php', 'event_list2'),
);

// --- 900: 주소변환기 ---
$amenu['900'] = 'admin.menu900.php';
$menu["menu900"] = array (
    array('900000', 'SERVICE', G5_ADMIN_URL.'/service_main.php', 'service'),
    array('900100', '주소변환기', G5_ADMIN_URL.'/service_main.php', 'service_info')
);

// 정렬
@ksort($amenu);
?>
