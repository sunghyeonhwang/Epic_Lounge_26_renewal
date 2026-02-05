<?php 
header('Content-type: application/json');
include_once('../common.php');

	$keyword = $_REQUEST["keyword"];
	$where = " and display_yn='Y' ";
	$where .= " and (title like '%{$keyword}%' or contents like '%{$keyword}%')";
	
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
?>{"data":[{"id":"-1"}<?
    for ($j=0; $row_list=sql_fetch_array($result2); $j++) {
		$link = "";
		if($row_list["site_url"]){
			$link = $row_list["site_url"];
		}else{
			$link = "./news_view.php?idx=".$row_list["rsc_bbs_idx"];
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
		$contents = strip_tags($row_list["contents"]);
		$contents = str_replace("\r","",$contents);
		$contents = str_replace("\n","",$contents);
		$contents = str_replace("'","",$contents);
		$contents = str_replace('"',"",$contents);
		$contents = str_replace('  ',"",$contents);
		$contents = str_replace('  ',"",$contents);
		$contents = str_replace('  ',"",$contents);
		$contents = str_replace('  ',"",$contents);
		$contents = substr($contents,1,50);
?>,{
            "id" : <?=$j+1?>,
            "link" : "<?=$link?>",
            "blank" : "<?=$row_list["site_url"] ? "_blank":""?>",
            "title" : "<?=str_replace($keyword,'<em class=\"sch_wr\">'.$keyword.'</em>',$row_list["title"])?>",
            "content" : "<?=$contents?>",
            "reg_date" : "<?=$reg_date?>",
            "category" : "<?=$row_list["category"]?>"
        }
<?
	}
?>]}