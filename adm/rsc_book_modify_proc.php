<?php
$sub_menu = "600600";
include_once('./_common.php');
include_once ('../lib/shop.lib.php');
if ($is_admin != 'super') die('최고관리자만 접근 가능합니다.');
$cate_industry = implode('|',$_POST[cate_industry]);
$cate_product = implode('|',$_POST[cate_product]);
$cate_subject = implode('|',$_POST[cate_subject]);
$cate_subcate = implode('|',$_POST[cate_subcate]);
$cate_version = implode('|',$_POST[cate_version]);
	
	$sql = " update v3_rsc_book_bbs set 
				title = '{$_POST[title]}',
				contents = '{$_POST[contents]}',
				category = '{$_POST[category]}',
				cate_industry = '{$cate_industry}',
				cate_product = '{$cate_product}',
				cate_subject = '{$cate_subject}',
				cate_subcate = '{$cate_subcate}',
				cate_version = '{$cate_version}',
				cate_difficult = '{$_POST[cate_difficult]}',
				display_yn = '{$display_yn}',
				site_url = '{$_POST[site_url]}',
				thumb_img_url = '{$_POST[thumb_img_url]}',
				updt_id = '{$_SESSION[ss_mb_id]}',
				updt_date = now()
			where rsc_bbs_idx = {$_POST[rsc_bbs_idx]}
 ";

	sql_query($sql);




	@mkdir(G5_DATA_PATH."/rsc", G5_DIR_PERMISSION);
	@chmod(G5_DATA_PATH."/rsc", G5_DIR_PERMISSION);

	$thumb_img      = $_FILES['thumb_img']['tmp_name'];
	$thumb_img_name = $_FILES['thumb_img']['name'];

	$top_bbs_img      = $_FILES['top_bbs_img']['tmp_name'];
	$top_bbs_img_name = $_FILES['top_bbs_img']['name'];


	//파일이 이미지인지 체크합니다.
	if( $thumb_img || $thumb_img_name ){

		if( !preg_match('/\.(gif|jpe?g|bmp|png)$/i', $thumb_img_name) ){
			alert("이미지 파일만 업로드 할수 있습니다.");
		}

		$timg = @getimagesize($thumb_img);
		if ($timg['2'] < 1 || $timg['2'] > 16){
			alert("이미지 파일만 업로드 할수 있습니다.");
		}

	}	
	$thumb_img = time()."_".rand(1000,9999);

    if ($_FILES['thumb_img']['name']) {
		upload_file($_FILES['thumb_img']['tmp_name'], $thumb_img, G5_DATA_PATH."/rsc");
		$sql = " update v3_rsc_book_bbs 
				 set thumb_img = '{$thumb_img}' 
				 where rsc_bbs_idx = {$_POST[rsc_bbs_idx]} ";
		sql_query($sql);
	}

	
	//파일이 이미지인지 체크합니다.
	if( $top_bbs_img || $top_bbs_img_name ){

		if( !preg_match('/\.(gif|jpe?g|bmp|png)$/i', $top_bbs_img_name) ){
			alert("이미지 파일만 업로드 할수 있습니다.");
		}

		$timg = @getimagesize($top_bbs_img);
		if ($timg['2'] < 1 || $timg['2'] > 16){
			alert("이미지 파일만 업로드 할수 있습니다.");
		}

	}	
	$top_bbs_img = time()."_".rand(1000,9999);

    if ($_FILES['top_bbs_img']['name']) {
		upload_file($_FILES['top_bbs_img']['tmp_name'], $top_bbs_img, G5_DATA_PATH."/rsc");
		$sql = " update v3_rsc_book_bbs 
				 set top_bbs_img = '{$top_bbs_img}' 
				 where rsc_bbs_idx = {$_POST[rsc_bbs_idx]} ";
		sql_query($sql);
	}
?>
<script>
location.href = "./rsc_book_modify.php?rsc_bbs_idx=<?=$_POST["rsc_bbs_idx"]?>";
</script>