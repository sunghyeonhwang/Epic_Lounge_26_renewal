<?php 
header('Content-type: application/json');
include_once('../common.php');

	$keyword = $_REQUEST["keyword"];
	$where = " and display_yn='Y' ";
	$where .= " and (title like '%{$keyword}%' or contents like '%{$keyword}%')";

	$sql_list = 
		" select * from v3_rsc_review_bbs 
		  where 1=1 " 
		  . $where . "
		  order by ordr desc, rsc_bbs_idx desc ";
	$result2 = sql_query($sql_list);
?>{"data":[{"id":"-1"}<?
    for ($j=0; $row_list=sql_fetch_array($result2); $j++) {
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
            "link" : "./replay_view.php?idx=<?=$row_list["rsc_bbs_idx"]?>",
            "title" : "<?=str_replace($keyword,'<em class=\"sch_wr\">'.$keyword.'</em>',$row_list["title"])?>",
            "content" : "<?=$contents?>",
            "reg_date" : "<?=substr($row_list["reg_date"],0,10)?>",
            "category" : [<?=$row_list["cate_industry"] ? "\"".$row_list["cate_industry"]."\",":""?>
						<?=$row_list["cate_product"] ? "\"".$row_list["cate_product"]."\",":""?>
						<?=$row_list["cate_subject"] ? "\"".$row_list["cate_subject"]."\",":""?>
						<?=$row_list["cate_difficult"] ? "\"".$row_list["cate_difficult"]."\",":""?>""
						]
        }
<?
	}

	$sql_list = 
		" select * from v3_rsc_free_bbs 
		  where 1=1 " 
		  . $where . "
		  order by ordr desc, rsc_bbs_idx desc ";
	$result2 = sql_query($sql_list);


    for ($j=0; $row_list=sql_fetch_array($result2); $j++) {
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
            "link" : "./free_view.php?idx=<?=$row_list["rsc_bbs_idx"]?>",
            "title" : "<?=str_replace($keyword,'<em class=\"sch_wr\">'.$keyword.'</em>',$row_list["title"])?>",
            "content" : "<?=$contents?>",
            "reg_date" : "<?=substr($row_list["reg_date"],0,10)?>",
            "category" : [<?=$row_list["cate_industry"] ? "\"".$row_list["cate_industry"]."\",":""?>
						<?=$row_list["cate_engine"] ? "\"".$row_list["cate_engine"]."\",":""?>
						<?=$row_list["category"] ? "\"".$row_list["category"]."\",":""?>""
						]
        }
<?
	}

	$sql_list = 
		" select * from v3_rsc_book_bbs 
		  where 1=1 " 
		  . $where . "
		  order by ordr desc, rsc_bbs_idx desc ";
	$result2 = sql_query($sql_list);


    for ($j=0; $row_list=sql_fetch_array($result2); $j++) {
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
            "link" : "./book_view.php?idx=<?=$row_list["rsc_bbs_idx"]?>",
            "title" : "<?=str_replace($keyword,'<em class=\"sch_wr\">'.$keyword.'</em>',$row_list["title"])?>",
            "content" : "<?=$contents?>",
            "reg_date" : "<?=substr($row_list["reg_date"],0,10)?>",
            "category" : [<?=$row_list["cate_industry"] ? "\"".$row_list["cate_industry"]."\",":""?>""
						]
        }
<?
	}
?>]}