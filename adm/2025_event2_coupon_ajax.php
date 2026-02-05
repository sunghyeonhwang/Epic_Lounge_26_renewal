<?php
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = array('result' => false, 'msg' => '');

    $mode = isset($_POST['mode']) ? sql_real_escape_string($_POST['mode']) : '';
    $coupon_key = isset($_POST['coupon_key']) ? sql_real_escape_string($_POST['coupon_key']) : '';

    if (empty($coupon_key)) {
        $response['msg'] = '필수 입력값이 누락되었습니다.';
    } else if ($mode == 'update') {
        $user_email = isset($_POST['user_email']) ? sql_real_escape_string($_POST['user_email']) : '';
        $user_phone = isset($_POST['user_phone']) ? sql_real_escape_string($_POST['user_phone']) : '';
        $user_name = isset($_POST['user_name']) ? sql_real_escape_string($_POST['user_name']) : '';

        if (empty($user_email) || empty($user_phone)) {
            $response['msg'] = '필수 입력값이 누락되었습니다.';
        } else {
            $sql = "UPDATE cb_unreal_2025_event2_coupon 
                    SET user_email = '$user_email', 
                        user_phone = '$user_phone'
                    WHERE coupon_key = '$coupon_key'";

            if (sql_query($sql)) {
                $response['result'] = true;
            } else {
                $response['msg'] = '쿠폰 정보 수정 중 오류가 발생했습니다.';
            }
        }
    }else if ($mode == 'lock_update') {
        $user_email = isset($_POST['user_email']) ? sql_real_escape_string($_POST['user_email']) : '';
        $user_phone = isset($_POST['user_phone']) ? sql_real_escape_string($_POST['user_phone']) : '';
        $user_name = isset($_POST['user_name']) ? sql_real_escape_string($_POST['user_name']) : '';

        if (empty($user_email) || empty($user_phone)) {
            $response['msg'] = '필수 입력값이 누락되었습니다.';
        } else {
            $sql = "UPDATE cb_unreal_2025_event2_coupon 
                    SET user_email = '$user_email', 
                        user_phone = '$user_phone'
                    WHERE coupon_key = '$coupon_key'";

            if (sql_query($sql)) {
                $response['result'] = true;
            } else {
                $response['msg'] = '쿠폰 정보 수정 중 오류가 발생했습니다.';
            }


            $index = $_POST['coupon_key'];
            $temp_data = sql_fetch("select * from cb_unreal_2025_event2_apply where apply_coupon_no = '$coupon_key' and apply_pay_status = 10 and apply_temp_yn = 'N' and pay_complete='Y' limit 1");
            $old_email = $temp_data["apply_user_email"];
            $old_phone = $temp_data["apply_user_phone"];
            $old_name = $temp_data["apply_user_name"];
            $apply_no = $temp_data["apply_no"];
            $new_email = $user_email;
            $new_phone = $user_phone;
            $new_name = $user_name;


            sql_query("INSERT INTO `unrealengine`.`cb_unreal_2025_event2_coupon_log` (`apply_no`,`old_email`, `old_phone`, `old_name`, `new_email`, `new_phone`, `new_name`, `regdate`) VALUES ('$apply_no','$old_email', '$old_phone', '$old_name', '$new_email', '$new_phone', '$new_name', now());");
            $apply_password = md5(str_replace("'","\'",$user_email));

            $sql = "UPDATE cb_unreal_2025_event2_apply 
                    SET apply_user_email = '$user_email', 
                        apply_user_phone = '$user_phone',
                        apply_password = '$apply_password'
                    WHERE apply_coupon_no = '$coupon_key' and apply_pay_status = 10 and apply_temp_yn = 'N' and pay_complete='Y' ";

            if (sql_query($sql)) {
                $response['result'] = true;
            } else {
                $response['msg'] = '쿠폰 정보 수정 중 오류가 발생했습니다.';
            }
        }
    } else if ($mode == 'lock_update_qrsend') {
        include_once $_SERVER['DOCUMENT_ROOT'].'/v3/unrealfest2025/phpqrcode/qrlib.php';
        $documentRoot = $_SERVER['DOCUMENT_ROOT'];
        $index = $_POST['coupon_key'];
        $temp_data = sql_fetch("select * from cb_unreal_2025_event2_apply where apply_coupon_no = '$coupon_key' and apply_pay_status = 10 and apply_temp_yn = 'N' and pay_complete='Y' limit 1");
        $apply_track = $temp_data["apply_track"];
        $apply_track_array = explode(",", $apply_track);
        $apply_product_code = $temp_data["apply_product_code"];
        $apply_user_name = $temp_data["apply_user_name"];
        $apply_user_phone = $temp_data["apply_user_phone"];
        $apply_password = md5(str_replace("'","\'",$temp_data['apply_user_email']));
        $sOrignText = $apply_password;
        QRcode::png($sOrignText,$documentRoot.'/v3/unrealfest2025/qrdata/'.$temp_data["apply_no"].".png", 0, 7, 2);
        $imagePath =  $documentRoot.'/v3/unrealfest2025/qrdata/'.$temp_data["apply_no"].'.png';
        $imagePath2 =  $documentRoot.'/v3/unrealfest2025/qrdata/'.$temp_data["apply_no"].'.jpg';
        if (file_exists($imagePath)) {
            $pngImage = imagecreatefrompng($imagePath);
            if ($pngImage) {
                $pngWidth = imagesx($pngImage);
                $pngHeight = imagesy($pngImage);
                $jpgImage = imagecreatetruecolor($pngWidth, $pngHeight);
                imagecopy($jpgImage, $pngImage, 0, 0, 0, 0, $pngWidth, $pngHeight);
                imagejpeg($jpgImage, $imagePath2, 100); // 90 is the quality (0-100)
                imagedestroy($pngImage);
                imagedestroy($jpgImage);
            }
        }
//sha256($temp_data["apply_no"]);

        $call_date = " 8월 25일(월)~26일(화) 오전 9시";
        if($apply_product_code == "STD_28" || $apply_product_code == "NORMAL_25") {
            $call_date = "8월 25일(월) 오전 9시";
        }else if($apply_product_code == "STD_29" || $apply_product_code == "NORMAL_26") {
            $call_date = "8월 26일(화) 오전 9시";
        }

        $ch = curl_init();
        $title = "<언리얼 페스트 서울 2025> 등록 확인";
        $message = "<언리얼 페스트 서울 2025> 오프라인 등록이 완료되었습니다. \n행사장 내 셀프 체크인 기기에서 QR코드를 스캔한 후 간편하게 입장하세요.\n\n일시: ".$call_date."\n장소: 코엑스 그랜드볼룸\n\n\n등록 취소는 얼리버드 판매 기간 내에만 가능한 점 착오 없으시길 바라며, 자세한 내용은 FAQ를 참고하세요.\n\n https://bit.ly/ufs25\n\n- 언리얼 페스트 사무국";

        $sender = "023263701";                    //필수입력
        $username = "griff16";                //필수입력
        $key = "BaIpwA1FNBOYszC";           //필수입력
        $receiver = '{"name":"'.$apply_user_name.'","mobile":"'.$apply_user_phone.'"}';
        $receiver = '['.$receiver.']';
        $sms_type = 'NORMAL'; // NORMAL - 즉시발송 / ONETIME - 1회예약 / WEEKLY - 매주정기예약 / MONTHLY - 매월정기예약
        $start_reserve_time = date('Y-m-d H:i:s'); //  발송하고자 하는 시간(시,분단위까지만 가능) (동일한 예약 시간으로는 200회 이상 API 호출을 할 수 없습니다.)
        $end_reserve_time = date('Y-m-d H:i:s'); //  발송이 끝나는 시간 1회 예약일 경우 $start_reserve_time = $end_reserve_time
        $remained_count = 1;
        $message = str_replace(' ', ' ', $message);  //유니코드 공백문자 치환
        $file = array(); // 배열 초기화
        $file[] = array('attc' => 'https://epiclounge.co.kr/v3/unrealfest2025/qrdata/'. $temp_data["apply_no"].".jpg");
        $attaches = json_encode($file);
        $postvars = '"title":"'.$title.'"';
        $postvars = $postvars.', "message":"'.$message.'"';
        $postvars = $postvars.', "sender":"'.$sender.'"';
        $postvars = $postvars.', "username":"'.$username.'"';
        $postvars = $postvars.', "receiver":'.$receiver.'';
        $postvars = $postvars.', "key":"'.$key.'"';
        $postvars = $postvars.', "attaches":'.$attaches;                      //첨부파일이 있는 경우 주석해제 바랍니다.

        $postvars = '{'.$postvars.'}';      //JSON 데이터
        $url = "https://directsend.co.kr/index.php/api_v2/sms_change_word";         //URL
        $headers = array("cache-control: no-cache","content-type: application/json; charset=utf-8");
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $postvars);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
        curl_setopt($ch,CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        if(curl_errno($ch)){
            //echo 'Curl error: ' . curl_error($ch);
        }else{
            //print_r($response);
        }
        curl_close ($ch);

//문자 발송 종료

        $response = array('result' => false, 'msg' => '');
        $response['result'] = true;

    } else if ($mode == 'delete') {
        $sql = "DELETE FROM cb_unreal_2025_event2_coupon WHERE coupon_key = '$coupon_key'";

        if (sql_query($sql)) {
            $response['result'] = true;
        } else {
            $response['msg'] = '쿠폰 삭제 중 오류가 발생했습니다.';
        }
    } else {
        $response['msg'] = '잘못된 요청입니다.';
    }
}

echo json_encode($response);
exit;

