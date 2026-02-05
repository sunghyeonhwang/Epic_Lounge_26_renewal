<?php
$sub_menu = "600700";
include_once('./_common.php');
include_once ('../lib/shop.lib.php');

$cate_industry = implode('|',$_POST[cate_industry]);
$cate_product = implode('|',$_POST[cate_product]);
$cate_subject = implode('|',$_POST[cate_subject]);
if ($is_admin != 'super') die('최고관리자만 접근 가능합니다.');


	@mkdir(G5_DATA_PATH."/news", G5_DIR_PERMISSION);
	@chmod(G5_DATA_PATH."/news", G5_DIR_PERMISSION);

	$thumb_img      = $_FILES['thumb_img']['tmp_name'];
	$thumb_img_name = $_FILES['thumb_img']['name'];

	$top_bbs_img      = $_FILES['top_bbs_img']['tmp_name'];
	$top_bbs_img_name = $_FILES['top_bbs_img']['name'];

	$doc_file      = $_FILES['doc_file']['tmp_name'];
	$doc_file_name = $_FILES['doc_file']['name'];


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
		upload_file($_FILES['thumb_img']['tmp_name'], $thumb_img, G5_DATA_PATH."/news");
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
		upload_file($_FILES['top_bbs_img']['tmp_name'], $top_bbs_img, G5_DATA_PATH."/news");
	}





	
	$doc_file = time()."_".rand(1000,9999);

    if ($_FILES['doc_file']['name']) {
		upload_file($_FILES['doc_file']['tmp_name'], $doc_file, G5_DATA_PATH."/news");
	}
	$sql = " insert into v3_rsc_news_bbs set 
				title = '{$_POST[title]}',
				contents = '{$_POST[contents]}',
				category = '{$_POST[category]}',
				tag = '{$_POST[tag]}',
				cate_industry = '{$cate_industry}',
				cate_product = '{$cate_product}',
				cate_subject = '{$cate_subject}',
				cate_subcate = '{$cate_subcate}',
				cate_version = '{$cate_version}',
				cate_difficult = '{$_POST[cate_difficult]}',
				site_url = '{$_POST[site_url]}',
				thumb_img_url = '{$_POST[thumb_img_url]}',
				thumb_img = '{$thumb_img}',
				top_bbs_img = '{$top_bbs_img}',
				doc_file = '{$doc_file}',
				display_yn = 'Y',
				reg_id = '{$_SESSION[ss_mb_id]}',
				reg_date = now()
 ";
	sql_query($sql);


    $last_idx = sql_query("SELECT LAST_INSERT_ID() as idx")->fetch_array()['idx'];
    sql_query("UPDATE v3_rsc_news_bbs SET ordr = {$last_idx} WHERE rsc_bbs_idx = {$last_idx}");
?>
<script>
location.href = "./rsc_news_list.php";
</script>