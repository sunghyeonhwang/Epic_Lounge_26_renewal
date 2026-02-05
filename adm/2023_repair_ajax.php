<?php
include_once "../common.php";


$sst = $_GET["sst"];
$sod = $_GET["sod"];
$sfl = $_GET["sfl"];
$stx = $_GET["stx"];
$page = $_GET["page"];
$apply_user_job = $_GET["apply_user_job"];
$apply_sector = $_GET["apply_sector"];
$apply_user_ticket = $_GET["apply_user_ticket"];
$repair_memo = $_POST["repair_memo"];



$qstr = "apply_user_job=".$apply_user_job."&apply_sector=".$apply_sector."&apply_user_ticket=".$apply_user_ticket;



sql_query("update cb_unreal_2023_event_apply set repair_memo='".$repair_memo."',apply_user_company='".$_POST["apply_user_company"]."', apply_user_depart='".$_POST["apply_user_depart"]."', apply_user_grade='".$_POST["apply_user_grade"]."' where apply_no='".$_POST["apply_no"]."'");



$row = sql_fetch("select * from cb_unreal_2023_event_apply where apply_no='".$_POST["apply_no"]."'");

?>[학교: <?php echo $row["apply_user_company"]?>]
[학과: <?php echo $row["apply_user_depart"]?>]
[학년: <?php echo $row["apply_user_grade"]?>]
[메모: <?php echo $row["repair_memo"]?>]
