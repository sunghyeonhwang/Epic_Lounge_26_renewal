<?php
include_once('./_common.php');

if ($is_admin != 'super') die('최고관리자만 접근 가능합니다.');

$mode = $_REQUEST["mode"];


$idx = $_REQUEST["idx"];
$move = $_REQUEST["move"];

$now = sql_fetch(" select * from v3_rsc_".$mode."_bbs where rsc_bbs_idx = '{$idx}'");

$ordr = $now["ordr"];
$type = $now["rsc_type"];



if($move == "up"){
    $next = sql_fetch(" select min(ordr) as next from v3_rsc_".$mode."_bbs where ordr > '{$now[ordr]}' ");
    if($next && $next[next] != 0){
        sql_query("update v3_rsc_".$mode."_bbs set ordr = '{$now[ordr]}' where ordr='{$next[next]}' ");
        sql_query("update v3_rsc_".$mode."_bbs set ordr = '{$next[next]}' where rsc_bbs_idx='{$idx}' ");
    }
}else if($move == "down"){
    $prev = sql_fetch(" select max(ordr) as prev from v3_rsc_".$mode."_bbs where ordr < '{$now[ordr]}' ");
    if($prev && $prev[prev] != 0){
        sql_query("update v3_rsc_".$mode."_bbs set ordr = '{$now[ordr]}' where ordr='{$prev[prev]}' ");
        sql_query("update v3_rsc_".$mode."_bbs set ordr = '{$prev[prev]}' where rsc_bbs_idx='{$idx}' ");
    }

}




?>