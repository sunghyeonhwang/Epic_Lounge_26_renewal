<?php
$menu['menu700'] = array(
    array('700000', '이벤트 관리', G5_URL . '/contents/event_list.php?category=%EC%BB%A4%EB%AE%A4%EB%8B%88%ED%8B%B0%20%EC%9D%B2%EB%B2%A4%ED%8A%B8', 'rsc_event_list', 1),
    // 언리얼챌린지 (맨 위로 이동)
    array('700800', '언리얼챌린지', '' . G5_ADMIN_URL . '/event_list2.php', 'event_list2', 1),
    array('700820', '25 언리얼챌린지', '' . G5_ADMIN_URL . '/event_list3.php', 'event_list3'),
    array('700810', '24 언리얼챌린지', '' . G5_ADMIN_URL . '/event_list2.php', 'event_list2'),
    // UE57 메뉴 (주석처리)

    /*
     * array('700100', 'UE57', ''.G5_ADMIN_URL.'/rsc_event_list.php?sub_menu=700100', 'rsc_event_list', 1),
     * array('700110', '신청 목록', ''.G5_ADMIN_URL.'/2025tw_event_list2.php', 'rsc_event_list'),
     * array('700120', '라이브 접속 리스트', ''.G5_ADMIN_URL.'/2025tw_live_list602.php', '2023_live_list'),
     */
    array('700200', '시작해요 25', '' . G5_ADMIN_URL . '/rsc_event_list.php?sub_menu=700200', 'rsc_event_list', 1),
    array('700210', '신청 목록', '' . G5_ADMIN_URL . '/2025tw_event_list.php', 'rsc_event_list'),
    array('700220', '라이브 접속 리스트', '' . G5_ADMIN_URL . '/2025tw_live_list60.php', '2023_live_list'),
    array('700230', '문의내역', '' . G5_ADMIN_URL . '/2025tw_event_inquery_list.php', '2023_event_inquery_list'),
    
    // UE FEST 26
    array('700300', 'UE FEST 26', '' . G5_ADMIN_URL . '/2026_event_speaker.php', '2026_event_speaker', 1),
    array('700310', '스피커 신청', '' . G5_ADMIN_URL . '/2026_event_speaker.php', '2026_event_speaker'),
    
    // UE FEST 25
    array('700700', 'UE FEST 25', '' . G5_ADMIN_URL . '/2024_event2_list.php', '2024_event2_list', 1),
    array('700710', '신청목록', '' . G5_ADMIN_URL . '/2025_event2_list.php', '2025_event2_list'),
    array('700720', '이벤트 잔여석', '' . G5_ADMIN_URL . '/2025_event2_remain.php', '2025_event2_remain'),
    array('700730', '쿠폰목록', '' . G5_ADMIN_URL . '/2025_event2_coupon_list.php', '2025_event2_coupon_list'),
    array('700740', '단체신청', '' . G5_ADMIN_URL . '/2025_event2_coupon_team_list.php', '2025_event2_coupon_team_list'),
    array('700750', '스폰서쿠폰목록', '' . G5_ADMIN_URL . '/2025_event2_coupon_sp_list.php', '2025_event2_coupon_sp_list'),
    array('700760', '라이브접속목록', '' . G5_ADMIN_URL . '/2025_live_list.php', '2025_live_list'),
    array('700707', '스피커', '' . G5_ADMIN_URL . '/2025_event_speaker.php', '2025_event_speaker'),
    // 챌린지 25 메뉴 (주석처리)
    // array('700400', '챌린지 25', ''.G5_ADMIN_URL.'/event_list3.php', 'event_list3', 1),
    // array('700410', '이벤트 목록', ''.G5_ADMIN_URL.'/event_list3.php', 'event_list3'),
    array('700600', 'UE FEST 24', '' . G5_ADMIN_URL . '/2024_event2_list.php', '2024_event2_list', 1),
    array('700610', '이벤트 목록', '' . G5_ADMIN_URL . '/2024_event2_list.php', '2024_event2_list'),
    array('700620', '이벤트 잔여석', '' . G5_ADMIN_URL . '/2024_event2_remain.php', '2024_event2_remain'),
    array('700630', '쿠폰목록', '' . G5_ADMIN_URL . '/2024_event2_coupon_list.php', '2024_event2_coupon_list'),
    array('700500', 'UE FEST 24 스피커', '' . G5_ADMIN_URL . '/rsc_2024_event_list.php', 'rsc_2024_event_list', 1),
    array('700520', '이벤트 목록', '' . G5_ADMIN_URL . '/2024_event_list.php', 'rsc_2024_event_list'),
    // 챌린지 24 (주석처리됨)
    // array('700900', '챌린지 24', ''.G5_ADMIN_URL.'/event_list2.php', 'event_list2', 1),
    // array('700910', '이벤트 목록', ''.G5_ADMIN_URL.'/event_list2.php', 'event_list2'),
);
?>