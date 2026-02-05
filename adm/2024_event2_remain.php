<?php
$sub_menu = "700620";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

$sql_common = " from cb_unreal_2024_event2_apply a ";
$sql_search = " where (apply_pay_status = 10 or apply_pay_status = 1) ";

if ($stx) {
    $sql_search .= " and ( ";
    if ($sfl) {
        $sql_search .= " ($sfl like '%$stx%') ";
    }
    $sql_search .= " ) ";
}



if ($strack) {
    $sql_search .= "and apply_track = '$strack'";
}

if ($apply_user_job) {
    $sql_search .= "and apply_user_job = '$apply_user_job'";
}

if ($apply_sector) {
    $sql_search .= "and apply_sector = '$apply_sector'";
}

if ($start_date) {
    $sql_search .= "and apply_reg_datetime > '$start_date'";
}
if ($end_date) {
    $sql_search .= "and apply_reg_datetime < '$end_date 23:59:59'";
}
if($sc_md=="free"){
    $sql_search .= " and free_yn = 'Y' ";
    $qstr .= '&amp;sc_md=' . urlencode($sc_md);
}else if($sc_md=="pay"){
    $sql_search .= " and free_yn = 'N' ";
    $qstr .= '&amp;sc_md=' . urlencode($sc_md);
}else if($sc_md=="group"){
    $sql_search .= " and team_yn='Y' ";
    $qstr .= '&amp;sc_md=' . urlencode($sc_md);

}else if($sc_md=="fr"){
    $sql_search .= " and apply_user_job = '외부등록' ";
    $qstr .= '&amp;sc_md=' . urlencode($sc_md);

}
// sfl apply_user_job apply_sector apply_user_ticket
//$qstr .= "&amp;sfl=".
//sst=&sod=&sfl=apply_user_name&stx=&page=4
$qstr .= "&apply_track&=".$_REQUEST["apply_track"]."&apply_user_job=".$_REQUEST["apply_user_job"]."&apply_sector=".$_REQUEST["apply_sector"]."&apply_user_ticket=".$_REQUEST["apply_user_ticket"];

$sst  = "a.apply_no";
$sod = "desc";
$sql_order = " order by $sst $sod ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

if (!$rows) {
    $rows = 51;
}
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);


$sql_all_cnt = " select count(*) as cnt {$sql_common} where (apply_pay_status = 10 or apply_pay_status = 1)  {$sql_order} ";
$row_all_cnt = sql_fetch($sql_all_cnt);


$sql_free_cnt = " select count(*) as cnt {$sql_common} where (apply_pay_status = 10 or apply_pay_status = 1) and free_yn='Y' ";
$row_free_cnt = sql_fetch($sql_free_cnt);

$sql_pay_cnt = " select count(*) as cnt {$sql_common} where (apply_pay_status = 10 or apply_pay_status = 1) and free_yn='N' ";
$row_pay_cnt = sql_fetch($sql_pay_cnt);

$sql_refund_cnt = " select count(*) as cnt {$sql_common} where IFNULL(refund_date,'') <> '' ";
$row_refund_cnt = sql_fetch($sql_refund_cnt);


$sql_wait_cnt = " select count(*) as cnt from cb_unreal_2024_event2_apply_inquire ";
$row_wait_cnt = sql_fetch($sql_wait_cnt);



$sql_group_cnt = " select count(*) as cnt {$sql_common} where (apply_pay_status = 10 or apply_pay_status = 1)  and team_yn='Y' {$sql_order} ";
$row_group_cnt = sql_fetch($sql_group_cnt);

$sql_fr_cnt = " select count(*) as cnt {$sql_common} where apply_user_job = '외부등록' ";
$row_fr_cnt = sql_fetch($sql_fr_cnt);



$g5['title'] = '이벤트 잔여석';
include_once('./admin.head.php');

$colspan = 12;
?>

    <link rel="stylesheet" type="text/css" href="https://epiclounge.co.kr/cib/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="https://epiclounge.co.kr/cib/assets/css/bootstrap-theme.min.css" />
    <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="https://epiclounge.co.kr/cib/assets/css/datepicker3.css" />
    <link rel="stylesheet" type="text/css" href="https://epiclounge.co.kr/cib/views/admin/basic/css/style.css" />
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/earlyaccess/nanumgothic.css" />

    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script type="text/javascript" src="https://epiclounge.co.kr/cib/assets/js/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="https://epiclounge.co.kr/cib/assets/js/bootstrap-datepicker.kr.js"></script>

    <script type="text/javascript" src="https://epiclounge.co.kr/cib/assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://epiclounge.co.kr/cib/assets/js/jquery.validate.min.js"></script>
    <script type="text/javascript" src="https://epiclounge.co.kr/cib/assets/js/jquery.validate.extension.js"></script>
    <script type="text/javascript" src="https://epiclounge.co.kr/cib/assets/js/common.js"></script>
    <style>
        h1
        {
            font-weight: bold;    font-size: 1.5em; font-family: 'Malgun Gothic',"맑은 고딕",AppleGothic,Dotum,"돋움", sans-serif;
        }
        body table td,body table th
        {
            font-size: 12px;
        }
    </style>


<?php
$sql_track = "select * from 2024_event_ticket";
$result_track = sql_query($sql_track);

?>

 
   <span style="color: #FF0000; font-weight: 800; font-size: 2rem">절대 ! 기등록 갯수 아래로 설정하지 마세요 ! 문제 생길 수 있습니다.(잔여는 최소가 0 입니다.) </span>
   <br  /> <br  />
    <section id="anc_bo_basic">
        <div class="tbl_frm01 tbl_wrap">
            <style>
                .btn_table_re td {border:1px solid #ddd;padding:10px;}
                .btn_table_re th {border:1px solid #ddd;padding:10px;}
            </style>
            <table class="btn_table_re" style="width:1400px;">
                <colgroup>
                    <col style="width:10%">
                    <col style="width:12%;">
                    <col style="width:10%;">
                    <col style="width:12%;">
                    <col style="width:10%;">
                    <col style="width:10%;">
                    <col style="width:15%;">
                    <col style="width:10%;">
                    <col style="width:10%;">
                </colgroup>
                <thead class="">
                    
                <tr >
                    <th style="text-align: right;">트랙명</th>
                    <th style="text-align: right;" colspan="2">28일</th>
                    <th >카운트</th>
                    <th >현재설정값</th>
                    <th style="text-align: right;" colspan="2">29일</th>
                    <th >카운트</th>
                    <th >현재설정값</th>
                </tr>
                </thead>
                <tbody >
                <?
                while($row_track = sql_fetch_array($result_track)){

                    $track = $row_track["name"];
                    $result_cnt = 0;

                    $sql = "select count(*) as cnt from cb_unreal_2024_event2_apply where apply_pay_status <> 0 and apply_temp_yn = 'N'   and apply_product_code like 'NORMAL%' and apply_track = '".$track."' and (apply_product_code like '%28' or apply_product_code like '%ALL')";
                    $track_nor_28 = sql_fetch($sql);

                    $sql = "select count(*) as cnt from cb_unreal_2024_event2_apply where apply_pay_status <> 0 and apply_temp_yn = 'N'   and apply_product_code like 'STD%' and apply_track = '".$track."' and (apply_product_code like '%28' or apply_product_code like '%ALL')";
                    $track_std_28 = sql_fetch($sql);


                    $sql = "select count(*) as cnt from cb_unreal_2024_event2_apply where apply_pay_status <> 0 and apply_temp_yn = 'N'   and apply_product_code like 'NORMAL%' and apply_track = '".$track."' and (apply_product_code like '%29' or apply_product_code like '%ALL')";
                    $track_nor_29 = sql_fetch($sql);

                    $sql = "select count(*) as cnt from cb_unreal_2024_event2_apply where apply_pay_status <> 0 and apply_temp_yn = 'N'   and apply_product_code like 'STD%' and apply_track = '".$track."' and (apply_product_code like '%29' or apply_product_code like '%ALL')";
                    $track_std_29 = sql_fetch($sql);


                    ?>
                    <form action="2024_event2_remain_proc.php" method="post">
                        <input type="hidden" name="name" value="<?=$row_track["name"]?>">
                        <tr class="last">
                            <td style="text-align: right;" ><?=$row_track["name"]?></td>
                            <td style="text-align: right;"><?=$track_nor_28["cnt"] + $track_std_28["cnt"]?>(일반:<?=$track_nor_28["cnt"]?>,학생:<?=$track_std_28["cnt"]?>)/<?=$row_track["date1"]?></td>
                            <td>잔여 : <?=$row_track["date1"] - ($track_nor_28["cnt"] + $track_std_28["cnt"])?></td>

                            <td style="background-color: #CCECFF"><input type="text" name="date1" value="<?=$row_track["date1"]?>" id="stx" class=" frm_input" >
                                <input type="submit" class="btn btn_02" value="변경하기" /></td>
                            <td ><?=$row_track["date1"]?></td>

                            <td style="text-align: right;"><?=$track_nor_29["cnt"] + $track_std_29["cnt"]?>(일반:<?=$track_nor_29["cnt"]?>,학생:<?=$track_std_29["cnt"]?>)/<?=$row_track["date2"]?></td>
                            <td>잔여 : <?=$row_track["date2"] - ($track_nor_29["cnt"] + $track_std_29["cnt"])?></td>

                            <td style="background-color: #CCECFF"><input type="text" name="date2" value="<?=$row_track["date2"]?>" id="stx" class=" frm_input" >
                                <input type="submit" class="btn btn_02" value="변경하기" /></td>
                            <td ><?=$row_track["date2"]?></td>
                        </tr>
                    </form>
                    <?
                }

                ?>
                </tbody>
            </table>
        </div>
    </section>


<?php
include_once('./admin.tail.php');