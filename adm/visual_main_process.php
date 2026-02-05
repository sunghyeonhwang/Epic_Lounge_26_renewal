<?php
include_once('./_common.php');

// JSON 응답 헤더
header('Content-Type: application/json; charset=utf-8');

// 권한 체크
if (!isset($member['mb_id']) || $member['mb_id'] == '') {
    die(json_encode(['success' => false, 'message' => '로그인이 필요합니다.']));
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

// 새 슬라이드 추가
if ($action == 'add_slide') {
    try {
        // 기존 데이터 순서 한 칸씩 뒤로 밀기
        sql_query('UPDATE v3_visual_main SET vm_order = vm_order + 1');

        // 현재 시간
        $now = date('Y-m-d H:i:s');

        $sql = "INSERT INTO v3_visual_main SET
                    vm_bg_type = 'video',
                    vm_bg_url = '',
                    vm_title_img = '',
                    vm_title_text = '',
                    vm_btn_text = '',
                    vm_link_url = '',
                    vm_display = 1,
                    vm_order = 1,
                    vm_duration = 5000,
                    vm_reg_dt = '{$now}'";

        $result = sql_query($sql, false);

        if ($result) {
            $new_id = sql_insert_id();
            echo json_encode([
                'success' => true,
                'message' => '슬라이드가 추가되었습니다.',
                'vm_id' => $new_id
            ]);
        } else {
            // MySQL 에러 정보
            global $g5;
            if (function_exists('mysqli_error') && isset($g5['connect_db'])) {
                $error_msg = mysqli_error($g5['connect_db']);
                $error_no = mysqli_errno($g5['connect_db']);
            } else {
                $error_msg = 'SQL 실행 실패';
                $error_no = 0;
            }

            error_log('Visual Main Add Error #' . $error_no . ': ' . $error_msg);

            echo json_encode([
                'success' => false,
                'message' => '슬라이드 추가 중 오류가 발생했습니다. (Error #' . $error_no . ': ' . $error_msg . ')'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => '오류: ' . $e->getMessage()
        ]);
    }
}
else {
    echo json_encode([
        'success' => false,
        'message' => '잘못된 요청입니다.'
    ]);
}
?>
