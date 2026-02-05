<?php

include_once('./_common.php');
include_once $_SERVER['DOCUMENT_ROOT'].'/v3/unrealfest2025/phpqrcode/qrlib.php';
$documentRoot = $_SERVER['DOCUMENT_ROOT'];

//24022
//27876

$sql = "SELECT * 
          FROM cb_unreal_2025_event2_apply 
         WHERE pay_complete = 'Y' 
           AND apply_pay_status = 10 and apply_temp_yn = 'N' ";

$result = sql_query($sql);
echo __FILE__;
echo "<br />";
for ($i = 1; $row = sql_fetch_array($result); $i++) {
    $apply_no = $row['apply_no'];
    $file_path = "/unrealengine/www/v3/unrealfest2025/qrdata/" . $apply_no . ".jpg";

    // 파일이 존재하지 않는 경우만 출력
    if (!file_exists($file_path)) {
        echo $file_path.":::".$apply_no . " : 파일 없음<br>";

        $apply_password = $row['apply_password'];
        $sOrignText = $apply_password;
        QRcode::png($sOrignText,$documentRoot.'/v3/unrealfest2025/qrdata/'.$apply_no.".png", 0, 7, 2);
        $imagePath =  $documentRoot.'/v3/unrealfest2025/qrdata/'.$apply_no.'.png';
        $imagePath2 =  $documentRoot.'/v3/unrealfest2025/qrdata/'.$apply_no.'.jpg';
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
    }
}

?>