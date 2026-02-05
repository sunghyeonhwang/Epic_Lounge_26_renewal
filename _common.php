<?php
include_once('./common.php');

// 커뮤니티 사용여부
if(defined('G5_COMMUNITY_USE') && G5_COMMUNITY_USE === false) {
    if (!defined('G5_USE_SHOP') || !G5_USE_SHOP)
        die('<p>쇼핑몰 설치 후 이용해 주십시오.</p>');

    define('_SHOP_', true);
}
// ******* 이벤트에 따라 변경될 값들 **************
define('EVENT_TITLE', '러셀과 함께하는 시작해요 언리얼');    // 여러 문구에 들어가는 title
define('MEVENT_TITLE', '언리얼 엔진 5 얼리 액세스 프리미어');    // 문자용에 들어가는 title
// ******************************************
