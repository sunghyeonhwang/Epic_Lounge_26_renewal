<?php
include_once('./_common.php');


$track = $_POST["name"];
$date1 = $_POST["date1"];
$date2 = $_POST["date1"];

sql_query("update 2025_event_ticket set date1='".$date1."',date2='".$date2."' where name='".$track."'");
alert("변경되었습니다.");