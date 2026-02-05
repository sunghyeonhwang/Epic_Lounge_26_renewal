<?php 
header('Content-type: application/json');
include_once('../common.php');

	$where = " and display_yn='Y' ";
	if($_REQUEST[category])
	$where .= " and category = '{$_REQUEST[category]}'";
	$page = $_GET["page"];
	if(empty($page)) $page = 1;

	$pageunit = $_GET["pageunit"];
	if(empty($pageunit)) $pageunit  = 9999;

	$page_limit = $pageunit * $page;
	$start = ($page-1) * $pageunit;
	
	$sql_list_cnt = 
		" select count(*) cnt from v3_rsc_news_bbs 
		  where 1=1 " 
		  . $where . " ";
	$cnt= sql_fetch($sql_list_cnt);

	$sql_list = 
		" select * from v3_rsc_news_bbs 
		  where 1=1 " 
		  . $where . "
		  order by ordr desc, rsc_bbs_idx desc ";
	$result2 = sql_query($sql_list);
?>{"data":[<?
    for ($j=0; $row_list=sql_fetch_array($result2); $j++) {
		$link = "";
		if($row_list["site_url"]){
			$link = $row_list["site_url"];
		}else{
			$link = "./news_view.php?idx=".$row_list["rsc_bbs_idx"];
		}

		
		if($row_list["thumb_img_url"]){
			$bimg_str = $row_list["thumb_img_url"];
		}else{
			
			$bimg_str = "";
			$bimg = G5_DATA_PATH."/news/{$row_list['thumb_img']}";
			if (file_exists($bimg)  && $row_list['thumb_img'] ) {
				$size = @getimagesize($bimg);
				if($size[0] && $size[0] > 750)
					$width = 750;
				else
					$width = $size[0];

				$bimg_str = G5_DATA_URL.'/news/'.$row_list['thumb_img'];
			}
			if (!$bimg_str) {
				$bimg_str = "/v3/resource/images/sub/no_img.jpg";
			}

		}
		$reg_date = $row_list["reg_date"];
		$reg_date_time = strtotime( $reg_date );
		$reg_date_time = time() - $reg_date_time;
		if($reg_date_time < 60){
			$reg_date = "방금전";
		}else if($reg_date_time < 60 * 60){
			$reg_diff = $reg_date_time / (60);
			$reg_date = round($reg_diff) . "분전";
		}else if($reg_date_time < 24 * 60 * 60){
			$reg_diff = $reg_date_time / (60 * 60);
			$reg_date = round($reg_diff) . "시간전";
		}else if($reg_date_time < 7 * 24 * 60 * 60){
			$reg_diff = $reg_date_time / (24 *  60 * 60);
			$reg_date = round($reg_diff) . "일전";
		}else if($reg_date_time < 30 * 24 * 60 * 60){
			$reg_diff = $reg_date_time / (7 * 24 *  60 * 60);
			$reg_date = round($reg_diff) . "주전";
		}else if($reg_date_time <  365 * 24 * 60 * 60){
			$reg_diff = $reg_date_time / (30 * 24 *  60 * 60);
			$reg_date = round($reg_diff) . "달전";
		}else if($reg_date_time >  365 * 24 * 60 * 60){
			$reg_diff = $reg_date_time / (365 * 24 *  60 * 60);
			$reg_date = round($reg_diff) . "년전";
		}
?>
	   {
            "id" : <?=$j+1?>,
            "link" : "<?=$link?>",
            "blank" : "<?=$row_list["site_url"] ? "_blank":""?>",
            "image" : "<?=str_replace(" ","%20",$bimg_str)?>",
            "title" : "<?=$row_list["title"]?>",
            "content" : "<?=$row_list["site_url"]?>",
            "category" : "<?=$row_list["category"]?>",
            "news_time" : "<?=$reg_date?>"
        }<?=$j < $cnt["cnt"]-1 ? ",":""?>
<?
	}
?>]}