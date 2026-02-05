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


$qstr = "apply_user_job=".$apply_user_job."&apply_sector=".$apply_sector."&apply_user_ticket=".$apply_user_ticket;



sql_query("update cb_unreal_2023_event_apply set apply_user_company='".$_POST["apply_user_company"]."', apply_user_depart='".$_POST["apply_user_depart"]."', apply_user_grade='".$_POST["apply_user_grade"]."' where apply_no='".$_POST["apply_no"]."'");



?>
<script>
    location.href= "2023_event_list_repair.php?<?php echo $qstr?>&page=<?php echo $page?>&sst=<?php echo $sst?>&sod=<?php echo $sod?>&sfl=<?php echo $sfl?>&stx=<?php echo $stx?>";
</script>
