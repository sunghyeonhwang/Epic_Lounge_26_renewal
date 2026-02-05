<?php
include_once "./_common.php";

// 권한 체크
if (!$member['mb_id']) {
    alert('로그인이 필요합니다.', G5_BBS_URL . '/login.php');
    exit;
}

$mode = clean_xss_tags($_POST['mode']);
$mode2 = clean_xss_tags($_GET['mode2']);

if ($mode == 'write') {
    // 추후 신규 등록 기능이 필요한 경우 구현
    
} else if ($mode == 'modify') {
    // 추후 수정 기능이 필요한 경우 구현
    
} else if ($mode2 == 'del') {
    // 삭제 처리
    $apply_no = (int)$_GET['no'];
    
    if (!$apply_no) {
        alert('잘못된 요청입니다.');
        exit;
    }
    
    try {
        // 안전한 삭제 쿼리
        $sql = "DELETE FROM cb_unreal_2026_speaker_apply WHERE id = $apply_no";
        sql_query($sql);
        
        alert('삭제되었습니다.', '/v3/adm/2026_event_speaker.php');
        
    } catch (Exception $e) {
        error_log('Speaker Delete Error: ' . $e->getMessage());
        alert('삭제 중 오류가 발생했습니다.');
    }
    
    exit;
}
?>
