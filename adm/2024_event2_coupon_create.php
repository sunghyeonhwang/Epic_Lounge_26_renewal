<?php
$sub_menu = "700620";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');


// 폼 제출 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $coupon_count = $_POST['coupon_count'];
    $discount_rate = $_POST['discount_rate'];
    $coupon_name = $_POST['coupon_name'];
    $coupon_type = $_POST['coupon_type'];

    for ($i = 0; $i < $coupon_count; $i++) {
        $coupon_serial = md5(uniqid(rand(), true)); // 고유한 쿠폰 일련번호 생성
        $user_name = ""; // 임시 사용자 이름
        $user_email = "";
        $user_id = ""; // 임의의 사용자 ID

        $sql = "INSERT INTO cb_unreal_2024_event2_coupon (coupon_serial,coupon_name, user_name, user_email, discount_rate, user_id,coupon_type)
                VALUES ('$coupon_serial','$coupon_name', '$user_name', '$user_email', '$discount_rate', '$user_id', '$coupon_type')";
        sql_query($sql);
    }

    echo "<script>alert('쿠폰이 성공적으로 생성되었습니다!');location.href='2024_event2_coupon_list.php';</script>";
}