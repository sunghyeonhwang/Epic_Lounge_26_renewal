<?php
$sub_menu = "600980";
include_once('./_common.php');

if ($is_admin != 'super') die('최고관리자만 접근 가능합니다.');

$idx = $_POST["idx"];
$field = $_POST["field"];
$value = $_POST["value"];
$mode = $_POST["mode"];

if($mode == "delete"){
	$sql = " delete from v3_rsc_event_bbs where rsc_bbs_idx = '{$idx}'";
	sql_query($sql);
}else if($mode == "update"){
	if($field == "category"){
		$sql = " update v3_rsc_event_bbs set category = '{$value}',updt_id='{$_SESSION[ss_mb_id]}',updt_date=now() where rsc_bbs_idx = '{$idx}'";
		sql_query($sql);
	}else if($field == "iti"){
		$sql = " update v3_rsc_event_bbs set cate_industry = '{$value}',updt_id='{$_SESSION[ss_mb_id]}',updt_date=now() where rsc_bbs_idx = '{$idx}'";
		sql_query($sql);
	}else if($field == "product"){
		$sql = " update v3_rsc_event_bbs set cate_product = '{$value}',updt_id='{$_SESSION[ss_mb_id]}',updt_date=now() where rsc_bbs_idx = '{$idx}'";
		sql_query($sql);

	}else if($field == "subject"){
		$sql = " update v3_rsc_event_bbs set cate_subject = '{$value}',updt_id='{$_SESSION[ss_mb_id]}',updt_date=now() where rsc_bbs_idx = '{$idx}'";
		sql_query($sql);

	}else if($field == "diff"){
		$sql = " update v3_rsc_event_bbs set cate_difficult = '{$value}',updt_id='{$_SESSION[ss_mb_id]}',updt_date=now() where rsc_bbs_idx = '{$idx}'";
		sql_query($sql);

	}else if($field == "display"){
		$sql = " update v3_rsc_event_bbs set display_yn = '{$value}',updt_id='{$_SESSION[ss_mb_id]}',updt_date=now() where rsc_bbs_idx = '{$idx}'";
		sql_query($sql);
	}
}else if($mode == "group_delete"){
	foreach($_POST["chk"] as $idx){
		$sql = " delete from v3_rsc_event_bbs where rsc_bbs_idx = '{$idx}'";
		sql_query($sql);		
	}
?><script> location.href= "./rsc_event_list.php";</script>
<?
exit;
}


?>