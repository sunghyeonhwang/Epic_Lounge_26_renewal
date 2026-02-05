<?php
ini_set("display_errors","1");
ini_set("display_error","1");
$sub_menu = '600900';
include_once('./_common.php');
include_once ('../lib/shop.lib.php');


check_demo();

if ($W == 'd')
    auth_check($auth[$sub_menu], "d");
else
    auth_check($auth[$sub_menu], "w");

check_admin_token();

@mkdir(G5_DATA_PATH."/main_news", G5_DIR_PERMISSION);
@chmod(G5_DATA_PATH."/main_news", G5_DIR_PERMISSION);

$bn_bimg      = $_FILES['bn_bimg']['tmp_name'];
$bn_bimg_name = $_FILES['bn_bimg']['name'];
$bn_simg      = $_FILES['bn_simg']['tmp_name'];
$bn_simg_name = $_FILES['bn_simg']['name'];

$bn_id = (int) $bn_id;

$RData = sql_fetch("select * from v3_main_banner_news where bn_id = '{$bn_id}'");

$bn_url = clean_xss_tags($bn_url);
$bn_alt = function_exists('clean_xss_attributes') ? clean_xss_attributes(strip_tags($bn_alt)) : strip_tags($bn_alt);

if ($bn_id=="")
{
    if (!$bn_bimg_name) alert('배너 이미지를 업로드 하세요.');

    sql_query(" alter table v3_main_banner_news auto_increment=1 ");

    $sql = " insert into v3_main_banner_news
	                set bn_bimg        = '$bn_bimg_name',
	                bn_simg        = '$bn_simg_name',
                    bn_info        = '$bn_info',
                    bn_title        = '$bn_title',
                    bn_tag     = '$bn_tag',
                    bn_url   = '$bn_url',
                    bn_use_at     = '$bn_use_at',
                    bn_hit        = '0',
                    bn_date        = now(),
                    bn_order      = '$bn_order' ";
					
    sql_query($sql);

    $bn_id = sql_insert_id();
}
else if ($w=="u")
{
	if(empty($bn_bimg_name)){
		$bn_bimg_name = $RData["bn_bimg"];
	}
	if(empty($bn_simg_name)){
		$bn_simg_name = $RData["bn_simg"];
	}

		$sql = " update v3_main_banner_news
				  set	
						bn_bimg        = '$bn_bimg_name',
						bn_simg        = '$bn_simg_name',
						bn_info        = '$bn_info',
						bn_title        = '$bn_title',
						bn_tag     = '$bn_tag',
						bn_url   = '$bn_url',
						bn_use_at     = '$bn_use_at',
						bn_order      = '$bn_order'
				  where bn_id = '$bn_id' ";

    sql_query($sql);
}
else if ($w=="d")
{
    @unlink(G5_DATA_PATH."/main_news/$bn_id");
    @unlink(G5_DATA_PATH."/main_news/$bn_id_2");

    $sql = " delete from v3_main_banner_news where bn_id = $bn_id ";
    $result = sql_query($sql);
}


if ($w == "" || $w == "u")
{
    if ($_FILES['bn_bimg']['name']) upload_file($_FILES['bn_bimg']['tmp_name'], $bn_id, G5_DATA_PATH."/main_news");
    if ($_FILES['bn_simg']['name']) upload_file($_FILES['bn_simg']['tmp_name'], $bn_id."_2", G5_DATA_PATH."/main_news");

    goto_url("./rsc_main_news_form.php?w=u&amp;bn_id=$bn_id");
} else {
    goto_url("./rsc_main_news_mng.php");
}
?>
