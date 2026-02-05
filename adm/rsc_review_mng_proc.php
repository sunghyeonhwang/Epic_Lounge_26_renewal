<?php
$sub_menu = "600100";
include_once('./_common.php');

if ($is_admin != 'super') die('최고관리자만 접근 가능합니다.');

$mode = $_REQUEST["mode"];
if($mode == "delete"){
	$sql = " delete from v3_rsc_review_category where rsc_idx = '{$_REQUEST[rsc_idx]}'";
	sql_query($sql);
?>
<script>
location.href = "./rsc_review_mng.php";
</script>
<?

}else if($mode == "update"){
	$sql = " update v3_rsc_review_category set rsc_name = '{$_REQUEST[rsc_name]}',updt_id='{$_SESSION[ss_mb_id]}',updt_date=now() where rsc_idx = '{$_REQUEST[rsc_idx]}'";
	sql_query($sql);
?>
<script>
location.href = "./rsc_review_mng.php";
</script>
<?

}else if($mode == "insert"){
	

	$sort = sql_fetch(" select IFNULL(max(sort),1) as sort from v3_rsc_review_category");

	$sql = " insert into v3_rsc_review_category set 
				rsc_type = '{$_REQUEST[rsc_type]}',
				rsc_name = '{$_REQUEST[rsc_name]}',
				reg_id = '{$_SESSION[ss_mb_id]}',
				reg_date = now(),
				sort = '{$sort[sort]}'
 ";
	sql_query($sql);

?>
<script>
location.href = "./rsc_review_mng.php";
</script>
<?

}else if($mode == "sort"){
	$idx = $_REQUEST["idx"];
	$move = $_REQUEST["move"];

	$now = sql_fetch(" select * from v3_rsc_review_category where rsc_idx = '{$idx}'");
	
	$sort = $now["sort"];
	$type = $now["rsc_type"];

	
	
	if($move == "up"){
		$prev = sql_fetch(" select max(sort) as prev from v3_rsc_review_category where sort < '{$now[sort]}' and rsc_type = '{$type}'");
		if($prev){
			sql_query("update v3_rsc_review_category set sort = '{$now[sort]}' where sort='{$prev[prev]}' ");
			sql_query("update v3_rsc_review_category set sort = '{$prev[prev]}' where rsc_idx='{$idx}' ");
		}
	}else if($move == "down"){
		
		$next = sql_fetch(" select min(sort) as next from v3_rsc_review_category where sort > '{$now[sort]}' and rsc_type = '{$type}' ");
		if($next){

			sql_query("update v3_rsc_review_category set sort = '{$now[sort]}' where sort='{$next[next]}' ");
			sql_query("update v3_rsc_review_category set sort = '{$next[next]}' where rsc_idx='{$idx}' ");
		}
	}


}


?>