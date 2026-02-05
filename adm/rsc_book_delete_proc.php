<?php
$sub_menu = "600600";
include_once('./_common.php');

if ($is_admin != 'super') die('최고관리자만 접근 가능합니다.');
$sql = " delete from v3_rsc_book_bbs where rsc_bbs_idx = '{$_GET[rsc_bbs_idx]}'";
	sql_query($sql);
?>
<script>
location.href = "./rsc_book_mng_list.php";
</script>