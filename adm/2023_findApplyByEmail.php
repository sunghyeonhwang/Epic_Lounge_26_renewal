<?
include_once "../common.php";
echo $mode = $_POST['mode'];
$mode2 = $_GET['mode2'];

if ($mode == 'write') {

    $apply_user_name = str_replace("'","\'",$_POST['apply_user_name']);
    $apply_user_email = str_replace("'","\'",$_POST['inputEmail']);
    $apply_user_phone = str_replace("'","\'",preg_replace("/[^0-9]/u", "", $_POST['apply_user_phone']));
    $apply_user_job = str_replace("'","\'",$_POST['apply_user_job']);
    $apply_user_company = str_replace("'","\'",$_POST['apply_user_company']);
    $apply_user_depart = str_replace("'","\'",$_POST['apply_user_depart']);
    $apply_user_grade = str_replace("'","\'",$_POST['apply_user_grade']);
    $apply_sector = str_replace("'","\'",$_POST['apply_sector']);
    $apply_user_ex1 = str_replace("'","\'",$_POST['apply_user_ex1']);
    $apply_user_ex2 = str_replace("'","\'",$_POST['apply_user_ex2']);
    $apply_user_ex3 = str_replace("'","\'",$_POST['apply_user_ex3']);
    $apply_user_ex4 = str_replace("'","\'",$_POST['apply_user_ex4']);
    $apply_user_email2 = str_replace("'","\'",$_POST['inputEmail2']);
    $apply_product_name = str_replace("'","\'",$_POST['apply_product_name']);
    $apply_user_event_agree = str_replace("'","\'",$_POST['apply_user_event_agree']);
    if($apply_user_event_agree == 'on'){
        $apply_user_event_agree = 1;
    }
    $apply_password = md5(str_replace("'","\'",$_POST['inputEmail']));

    $mysqli = new mysqli("localhost","unrealengine","Good121930!@","unrealengine");

    $sql = "select count(*) from cb_unreal_2023_event_apply where apply_user_email = '$apply_user_email' limit 1";
    $result = $mysqli -> query($sql);
    $obj = $result -> fetch_array();
    if($obj[0]['count']){
        echo '<script type="text/javascript">';
        echo " alert('이미 등록되어 있는 메일입니다.'); history.go(-1);";
        echo '</script>';
        exit();
    };

    $sql = "select count(*) from cb_unreal_2023_event_apply where apply_user_phone = '$apply_user_phone' limit 1";
    $result = $mysqli -> query($sql);
    $obj = $result -> fetch_array();
    if($obj[0]['count']){
        echo '<script type="text/javascript">';
        echo " alert('이미 등록되어 있는 연락처입니다.'); history.go(-1);";
        echo '</script>';
        exit();
    };

    $_apply_ci = $_SESSION["CI"];
    $_apply_di = $_SESSION["DI"];

    $sql = "INSERT INTO cb_unreal_2023_event_apply (
      apply_user_name, 
      apply_user_email,
      apply_user_phone,
      apply_user_job, 
      apply_user_company, 
      apply_user_depart, 
      apply_user_grade, 
      apply_sector, 
      apply_user_ex1, 
      apply_user_ex2, 
      apply_user_ex3, 
      apply_user_ex4, 
      apply_user_email2, 
      apply_product_name, 
      apply_user_event_agree,
      apply_password,
      apply_ci,
      apply_di
    ) VALUES(
      '$apply_user_name', 
      '$apply_user_email',
      '$apply_user_phone',
      '$apply_user_job', 
      '$apply_user_company', 
      '$apply_user_depart', 
      '$apply_user_grade', 
      '$apply_sector', 
      '$apply_user_ex1', 
      '$apply_user_ex2', 
      '$apply_user_ex3', 
      '$apply_user_ex4', 
      '$apply_user_email2', 
      '$apply_product_name', 
      '$apply_user_event_agree',
      '$apply_password',
      '$_apply_ci',
      '$_apply_di'
             
    )";
    $result = $mysqli -> query($sql);
    // $insert_id = $mysqli->insert_id;
    $mysqli -> close();

//문자 발송 시작
    if ($apply_user_ex1 == 'on') {
        $day = '29일';
    } else if ($apply_user_ex2 == 'on') {
        $day = '29일';
    } else if ($apply_user_ex3 == 'on') {
        $day = '29일';
    } else if ($apply_user_ex4 == 'on') {
        $day = '29일';
    }
    $ch = curl_init();
    $title = "시작해요 UEFN 2023";
    $message = '[$NAME]님, <시작해요 UEFN 2023>에 등록되었습니다.
2023년 6월 15일부터 6월 29일까지 3주간 매주 목요일, 오후 2시부터 등록된 메일과 카카오톡 문자로 발송되는 URL로 접속하거나 에픽 라운지에 방문하셔서 시청하실 수 있습니다.
사전준비 사항을 반드시 확인하세요!
 - 에픽 라운지 사무국';             //필수입력
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
    $postvars = '"title":"'.$title.'"';
    $postvars = $postvars.', "message":"'.$message.'"';
    $postvars = $postvars.', "sender":"'.$sender.'"';
    $postvars = $postvars.', "username":"'.$username.'"';
    $postvars = $postvars.', "receiver":'.$receiver.'';
    $postvars = $postvars.', "key":"'.$key.'"';
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

    echo '<script type="text/javascript">';
    echo "alert('시작해요 UEFN 2023 등록이 완료되었습니다. \\n2023년 6월 15일부터 6월 29일까지 3주간 매주 목요일, 오후 2시부터\\n 등록된 메일과 카카오톡 문자로 \\n발송되는 URL로 접속하거나 에픽 라운지에 방문하셔서 시청하실 수 있습니다!'); location.replace('https://epiclounge.co.kr/start_uefn2023.php');";
    echo '</script>';
    exit();

} else if ($mode == 'modify') {

    $apply_no = str_replace("'","\'",$_POST['apply_no']);
    $apply_user_name = str_replace("'","\'",$_POST['apply_user_name']);
    $apply_user_email = str_replace("'","\'",$_POST['inputEmail']);
    $apply_user_phone = str_replace("'","\'",preg_replace("/[^0-9]/u", "", $_POST['apply_user_phone']));
    $apply_user_job = str_replace("'","\'",$_POST['apply_user_job']);
    if ($apply_user_job == '직장인') {
        $apply_user_company = str_replace("'","\'",$_POST['apply_user_company1']);
        $apply_user_depart = str_replace("'","\'",$_POST['apply_user_depart1']);
        $apply_user_grade = str_replace("'","\'",$_POST['apply_user_grade1']);
    } else if ($apply_user_job == '학생') {
        $apply_user_company = str_replace("'","\'",$_POST['apply_user_company2']);
        $apply_user_depart = str_replace("'","\'",$_POST['apply_user_depart2']);
        $apply_user_grade = str_replace("'","\'",$_POST['apply_user_grade2']);
    } else if ($apply_user_job == '교육자/교육기관') {
        $apply_user_company = str_replace("'","\'",$_POST['apply_user_company3']);
        $apply_user_depart = str_replace("'","\'",$_POST['apply_user_depart3']);
        $apply_user_grade = str_replace("'","\'",$_POST['apply_user_grade3']);
    } else if ($apply_user_job == '인디/프리랜서') {
        $apply_user_company = str_replace("'","\'",$_POST['apply_user_company4']);
        $apply_user_depart = str_replace("'","\'",$_POST['apply_user_depart4']);
        $apply_user_grade = str_replace("'","\'",$_POST['apply_user_grade4']);
    } else if ($apply_user_job == '기타') {
        $apply_user_company = str_replace("'","\'",$_POST['apply_user_company5']);
        $apply_user_depart = str_replace("'","\'",$_POST['apply_user_depart5']);
        $apply_user_grade = str_replace("'","\'",$_POST['apply_user_grade5']);
    }
    $apply_sector = str_replace("'","\'",$_POST['apply_sector']);
    $apply_user_ex1 = str_replace("'","\'",$_POST['apply_user_ex1']);
    $apply_user_ex2 = str_replace("'","\'",$_POST['apply_user_ex2']);
    $apply_user_ex3 = str_replace("'","\'",$_POST['apply_user_ex3']);
    $apply_user_ex4 = str_replace("'","\'",$_POST['apply_user_ex4']);
    $apply_user_email2 = str_replace("'","\'",$_POST['inputEmail2']);
    $apply_product_name = str_replace("'","\'",$_POST['apply_product_name']);
    $apply_user_event_agree = str_replace("'","\'",$_POST['apply_user_event_agree']);
    if($apply_user_event_agree == 'on'){
        $apply_user_event_agree = 1;
    }
    $apply_password = md5(str_replace("'","\'",$_POST['inputEmail']));

    $mysqli = new mysqli("localhost","unrealengine","Good121930!@","unrealengine");


    $sql = "select count(*) from cb_unreal_2023_event_apply where apply_user_email = '$apply_user_email' and apply_no != '$apply_no' limit 1";
    $result = $mysqli -> query($sql);
    $obj = $result -> fetch_array();
    if($obj[0]['count']){
        echo '<script type="text/javascript">';
        echo " alert('이미 등록되어 있는 메일입니다.'); history.go(-1);";
        echo '</script>';
        exit();
    };

    $sql = "select count(*) from cb_unreal_2023_event_apply where apply_user_phone = '$apply_user_phone' and apply_no != '$apply_no' limit 1";
    $result = $mysqli -> query($sql);
    $obj = $result -> fetch_array();
    if($obj[0]['count']){
        echo '<script type="text/javascript">';
        echo " alert('이미 등록되어 있는 연락처입니다.'); history.go(-1);";
        echo '</script>';
        exit();
    };


    $sql = "UPDATE cb_unreal_2023_event_apply SET
    apply_user_name='$apply_user_name', 
    apply_user_email='$apply_user_email',
    apply_user_phone='$apply_user_phone',
    apply_user_job='$apply_user_job', 
    apply_user_company='$apply_user_company', 
    apply_user_depart='$apply_user_depart', 
    apply_user_grade='$apply_user_grade', 
    apply_sector='$apply_sector', 
    apply_user_ex1='$apply_user_ex1', 
    apply_user_ex2='$apply_user_ex2', 
    apply_user_ex3='$apply_user_ex3', 
    apply_user_ex4='$apply_user_ex4', 
    apply_user_email2='$apply_user_email2', 
    apply_product_name='$apply_product_name', 
    apply_user_event_agree='$apply_user_event_agree',
    apply_password='$apply_password'
  WHERE apply_no = '$apply_no'";
    $result = $mysqli -> query($sql);
    $mysqli -> close();
    echo '<script type="text/javascript">';
    echo "location.replace('application_modify_complete.html?num=$apply_password');";
    echo '</script>';
    exit();

} else if ($mode2 == 'del') {

    $apply_no = $_GET['no'];
    $mysqli = new mysqli("localhost","unrealengine","Good121930!@","unrealengine");


    $sql = "select * from cb_unreal_2023_event_apply where apply_no = '$apply_no' limit 1";

    $result = $mysqli -> query($sql);
    $obj = $result -> fetch_array();
    $mail =  'del:'.$obj['apply_user_email'];
    $apply_user_phone =  'del:'.$obj['apply_user_phone'];
    $apply_password =  'del:'.$obj['apply_password'];
    $apply_ci =  'del:'.$obj['apply_ci'];
    $apply_di =  'del:'.$obj['apply_di'];

    $sql2 = "UPDATE cb_unreal_2023_event_apply SET
  apply_user_email = '$mail',
  apply_user_phone = '$apply_user_phone',
  apply_password = '$apply_password',
  apply_ci = '$apply_ci',
  apply_di = '$apply_di',
  apply_pay_status = 0
  WHERE apply_no = '$apply_no'";
    $result = $mysqli -> query($sql2);


    $sql3 = "delete from cb_unreal_2023_event_apply_inquire where apply_user_email = '".$obj['apply_user_email']."'";
    $result = $mysqli -> query($sql3);

    $mysqli -> close();
    echo '<script type="text/javascript">';
    echo "location.replace('/v3/adm/2023_event_list.php');";
    echo '</script>';
    exit();

} else if ($mode2 == 'del2') {

    $apply_no = $_GET['no'];

    $sql = "select * from cb_unreal_2023_event_apply where apply_no = '$apply_no' limit 1";
    $obj = sql_fetch($sql);



    header("Content-Type: text/html; charset=utf-8");
    if(!empty($obj["mpay_tid"])){ //모바일결제
        //step1. 요청을 위한 파라미터 설정
        $key         = "nf2Vszdaxij1qXsm";
        $type        = "Refund";
        $paymethod   = $obj["mpay_type"];
        $timestamp   = date("YmdHis");
        $clientIp    = $_SERVER['REMOTE_ADDR'];
        $mid         = "MOIepiclou";
        $tid         = $obj["mpay_tid"];
        $msg         = "회원요청에 의한 환불처리";
    }else {//PC결제
        //step1. 요청을 위한 파라미터 설정
        $key         = "nf2Vszdaxij1qXsm";
        $type        = "Refund";
        $paymethod   = $obj["pay_paymethod"];
        $timestamp   = date("YmdHis");
        $clientIp    = $_SERVER['REMOTE_ADDR'];
        $mid         = "MOIepiclou";
        $tid         = $obj["pay_tid"];
        $msg         = "회원요청에 의한 환불처리";
    }

    // INIAPIKey + type + paymethod + timestamp + clientIp + mid + tid
    $hashData = hash("sha512",(string)$key.(string)$type.(string)$paymethod.(string)$timestamp.(string)$clientIp.(string)$mid.(string)$tid); // hash 암호화


    //step2. key=value 로 post 요청
    $data = array(
        'type' => $type,
        'paymethod' => $paymethod,
        'timestamp' => $timestamp,
        'clientIp' => $clientIp,
        'mid' => $mid,
        'tid' => $tid,
        'msg' => $msg,
        'hashData'=> $hashData
    );


    $url = "https://iniapi.inicis.com/api/v1/refund";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=utf-8'));
    curl_setopt($ch, CURLOPT_POST, 1);

    $response = curl_exec($ch);
    curl_close($ch);

    //step3. 요청 결과

    $resultMap = json_decode($response, true);

    sql_query("insert into 2023_event_log(log_text,rdate) values('".str_replace("'","`",$response)."',now())");

    if($resultMap["resultCode"] == "01"){

        echo '<script type="text/javascript">';
        echo "alert('".$resultMap["resultMsg"]."');";
        echo "alert('환불에 실패하였습니다.'); location.replace('/v3/adm/2023_event_list.php');";
        echo '</script>';
        exit();
    }else{

        $mail =  'del:'.$obj['apply_user_email'];
        $apply_user_phone =  'del:'.$obj['apply_user_phone'];
        $apply_password =  'del:'.$obj['apply_password'];
        $apply_ci =  'del:'.$obj['apply_ci'];
        $apply_di =  'del:'.$obj['apply_di'];

        $sql2 = "UPDATE cb_unreal_2023_event_apply SET
  apply_user_email = '$mail',
  apply_user_phone = '$apply_user_phone',
  apply_password = '$apply_password',
  apply_ci = '$apply_ci',
  apply_di = '$apply_di',
  apply_pay_status = 0,
  refund_msg = '".$response."',
  refund_time = '".$resultMap["cancelTime"]."',
  refund_date = '".$resultMap["cancelDate"]."'

  WHERE apply_no = '$apply_no'";
        sql_query($sql2);
        echo '<script type="text/javascript">';
        echo "alert('환불이 완료되었습니다.'); location.replace('/v3/adm/2023_event_list.php');";
        echo '</script>';
        exit();
    }

} else if ($mode == 'inquire') {
    $apply_user_name = str_replace("'","\'",$_POST['apply_user_name']);
    $apply_user_email = str_replace("'","\'",$_POST['inputEmail']);
    $apply_user_phone = str_replace("'","\'",preg_replace("/[^0-9]/u", "", $_POST['apply_user_phone']));
    $apply_user_company = str_replace("'","\'",$_POST['apply_user_company']);
    $apply_user_depart = str_replace("'","\'",$_POST['apply_user_depart']);
    $apply_user_grade = str_replace("'","\'",$_POST['apply_user_grade']);
    $apply_content = str_replace("'","\'",$_POST['apply_content']);

    $mysqli = new mysqli("localhost","unrealengine","Good121930!@","unrealengine");
    $sql = "INSERT INTO cb_unreal_2023_event_apply_inquire(
    apply_user_name, 
    apply_user_email,
    apply_user_phone,
    apply_user_company, 
    apply_user_depart, 
    apply_user_grade, 
    apply_content
  ) VALUES(
    '$apply_user_name', 
    '$apply_user_email',
    '$apply_user_phone',
    '$apply_user_company', 
    '$apply_user_depart', 
    '$apply_user_grade', 
    '$apply_content'
  )";
    $result = $mysqli -> query($sql);
    $mysqli -> close();
    echo '<script type="text/javascript">';
    echo "alert('등록 되었습니다.'); history.go(-1);";
    echo '</script>';
}

?>