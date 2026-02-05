<?php
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = array('result' => false, 'msg' => '');
    $memo = isset($_POST['memo']) ? sql_real_escape_string($_POST['memo']) : '';
    $gcode = isset($_POST['gcode']) ? sql_real_escape_string($_POST['gcode']) : '';

    if (empty($gcode)) {
        $response['msg'] = '필수 입력값이 누락되었습니다.';
    } else {
        $sql = "UPDATE cb_unreal_2025_event2_coupon 
                    SET creator_memo = '$memo'
                    WHERE creator_gcode = '$gcode'";

        if (sql_query($sql)) {
            $response['result'] = true;
        } else {
            $response['msg'] = '메모 저장 중 오류가 발생했습니다.';
        }
    }
}

echo json_encode($response);
exit;

