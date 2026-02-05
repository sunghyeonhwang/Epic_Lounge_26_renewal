<?php 
header('Content-type: application/json');
include_once('../common.php');

	$where = " and display_yn='Y' ";
	$sc_cate_product = $_REQUEST["cate_product"];
	$sc_cate_subject = $_REQUEST["cate_subject"];
	$sc_cate_industry = $_REQUEST["cate_industry"];
	$sc_cate_difficult = $_REQUEST["cate_difficult"];
	foreach($sc_cate_product as $sc1){
		$where .= " and cate_product like '%{$sc1}%'";		
	}
	foreach($sc_cate_industry as $sc1){
		$where .= " and cate_industry like '%{$sc1}%'";		
	}
	foreach($sc_cate_subject as $sc1){
		$where .= " and cate_subject like '%{$sc1}%'";		
	}
	foreach($sc_cate_difficult as $sc1){
		$where .= " and cate_difficult like '%{$sc1}%'";		
	}

	$where .= " and title like '%" .$_REQUEST["keyword"]."%' ";

	$page = $_GET["page"];
	if(empty($page)) $page = 1;

	$pageunit = $_GET["pageunit"];
	if(empty($pageunit)) $pageunit  = 9999;

	$page_limit = $pageunit * $page;
	$start = ($page-1) * $pageunit;
	
	$sql_list_cnt = 
		" select count(*) cnt from v3_rsc_review_bbs 
		  where 1=1 " 
		  . $where . " ";
	$cnt= sql_fetch($sql_list_cnt);

	$sql_list = 
		" select * from v3_rsc_review_bbs 
		  where 1=1 " 
		  . $where . "
		  order by ordr desc, rsc_bbs_idx desc ";
	$sql_list .= " limit {$start}, ".$pageunit;
	$result2 = sql_query($sql_list);
	$total_page  = ceil($cnt["cnt"] / $pageunit);  // 전체 페이지 계산
	$qstr = "pageunit=".$pageunit."&sc_text=".$_GET["sc_text"];
?>{"data":[<?
    for ($j=0; $row_list=sql_fetch_array($result2); $j++) {
	$bimg_str = "";
	if($row_list["thumb_img_url"]){
		$bimg_str = str_replace(" ","%20",$row_list["thumb_img_url"]);
	}else{
		
		$bimg_str = "";
		$bimg = G5_DATA_PATH."/rsc/{$row_list['thumb_img']}";
		if (file_exists($bimg)  && $row_list['thumb_img'] ) {
			$size = @getimagesize($bimg);
			if($size[0] && $size[0] > 750)
				$width = 750;
			else
				$width = $size[0];

			$bimg_str = G5_DATA_URL.'/rsc/'.$row_list['thumb_img'];
		}
		if (!$bimg_str) {
			$bimg_str = "/v3/resource/images/sub/no_img.jpg";
		}

	}
		$link = "";
		$target = "";
		if($row_list["site_url"]){
			$link = $row_list["site_url"];
			$target = "_blank";
		}else{
			$link = "./replay_view.php?idx=".$row_list["rsc_bbs_idx"];
		}
		$title = $row_list["title"];
		//$title = strip_tags($title);
		$title = str_replace("\r","",$title);
		$title = str_replace("\n","",$title);
		$title = str_replace("'","",$title);
		$title = str_replace('"',"",$title);
		$title = str_replace('  ',"",$title);
		$title = str_replace('  ',"",$title);
		$title = str_replace('  ',"",$title);
		$title = str_replace('  ',"",$title);
?>
	   {
            "id" : <?=$j+1?>,
            "link" : "<?=$link?>",
            "image" : "<?=$bimg_str?>",
            "target" : "<?=$target?>",
            "title" : "<?=$title?>",
            "content" : "<?=$row_list["site_url"]?>",
            "category" : [<?=$row_list["cate_industry"] ? "\"".$row_list["cate_industry"]."\",":""?>
						<?=$row_list["cate_product"] ? "\"".$row_list["cate_product"]."\",":""?>
						<?=$row_list["cate_subject"] ? "\"".$row_list["cate_subject"]."\",":""?>
						<?=$row_list["cate_difficult"] ? "\"".$row_list["cate_difficult"]."\",":""?>""
						]
        }<?=$j < $cnt["cnt"]-1 ? ",":""?>
<?
	}
?>]}