<?php
$sub_menu = '600950';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$where = ' where ';
$sql_search = '';

$g5['title'] = '메인 비쥬얼 관리';
include_once (G5_ADMIN_PATH.'/admin.head.php');
$sql_common = " from v3_main_banner ";
$sql_common .= $sql_search;

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

?>

<div class="local_ov01 local_ov">
    <span class="btn_ov01"><span class="ov_txt"> <?php echo ($sql_search) ? '검색' : '등록'; ?>된 배너 </span><span class="ov_num"> <?php echo $total_count; ?>개</span></span>


</div>

<div class="btn_fixed_top">
    <a href="./rsc_main_banner_form.php" class="btn_01 btn">배너추가</a>
</div>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col"style="width:50px;">No.</th>
        <th scope="col">배경이미지</th>
        <th scope="col">텍스트</th>
        <th scope="col">내용</th>
        <th scope="col">링크주소</th>
        <th scope="col" style="width:150px;">등록일</th>
        <th scope="col" style="width:50px;">사용</th>
        <th scope="col" style="width:150px;">관리</th>
    </tr>
    </thead>
    <tbody>

    <?php
    $sql = " select * from v3_main_banner $sql_search
          order by bn_order, bn_id desc
          limit $from_record, $rows  ";
    $result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
    ?>

    <tr>
		<td><?php echo $row['bn_id']; ?></td>
		<td><img src="/v3/data/main_banner/<?php echo $row['bn_id']; ?>" width="200"/></td>
		<td><?php echo $row['bn_txt_top']; ?></td>
		<td><?php echo $row['bn_txt_bot_color_btn']; ?></td>
		<td><?php echo $row['bn_url']; ?></td>
		<td><?php echo $row['bn_date']; ?></td>
		<td><?php echo $row['bn_use_at']; ?></td>
			
		<td>
            <a href="./rsc_main_banner_form.php?bn_id=<?=$row["bn_id"]?>&w=u" class="btn btn_03">수정</a>
            <a href="./rsc_main_banner_mng_proc.php?w=d&amp;bn_id=<?php echo $row['bn_id']; ?>" onclick="return delete_confirm(this);" class="btn btn_02">삭제</a>
        </td>
    </tr>

    <?php
    }
    if ($i == 0) {
    echo '<tr><td colspan="8" class="empty_table">자료가 없습니다.</td></tr>';
    }
    ?>
    </tbody>
    </table>

</div>


<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<script>
jQuery(function($) {
    $(".sbn_img_view").on("click", function() {
        $(this).closest(".td_img_view").find(".sbn_image").slideToggle();
    });
});
function delete_frm(idx){
	if(confirm("정말로 삭제 하시겠습니까? 삭제한 데이터는 복원이 불가능합니다.")){
	}
}
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
