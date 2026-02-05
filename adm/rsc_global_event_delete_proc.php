<?php
$sub_menu = "600990";
include_once('./_common.php');

if ($is_admin != 'super') die('최고관리자만 접근 가능합니다.');
$sql = " delete from v3_rsc_global_event_bbs where rsc_bbs_idx = '{$_GET[rsc_bbs_idx]}'";
	sql_query($sql);
?>
<script>
location.href = "./rsc_global_event_mng_list.php";
</script>