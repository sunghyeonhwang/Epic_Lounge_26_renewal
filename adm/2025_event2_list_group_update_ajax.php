<?php
include_once "../common.php";

$apply_no = $_POST["apply_no"];
$apply_product_code = $_POST["apply_product_code"];

$apply_product_name = "";
$apply_user_ex2 = $_POST["apply_user_ex2"];
if($apply_product_code == "NORMAL_ALL"){
    $apply_product_name = "양일권";
}else if($apply_product_code == "NORMAL_25"){
    $apply_product_name = "28일권";
}else if($apply_product_code == "NORMAL_26"){
    $apply_product_name = "29일권";
}

sql_query("update cb_unreal_2025_event2_apply set apply_product_code='".$apply_product_code."',apply_product_name='".$apply_product_name."',apply_user_ex2 = '".$apply_user_ex2."' where apply_no='".$apply_no."'");


?>