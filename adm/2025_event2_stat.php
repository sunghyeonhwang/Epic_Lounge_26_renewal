<?php
$sub_menu = "700720";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

$sql_common = " from cb_unreal_2025_event2_apply a ";
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


$sql_wait_cnt = " select count(*) as cnt from cb_unreal_2025_event2_apply_inquire ";
$row_wait_cnt = sql_fetch($sql_wait_cnt);



$sql_group_cnt = " select count(*) as cnt {$sql_common} where (apply_pay_status = 10 or apply_pay_status = 1)  and team_yn='Y' {$sql_order} ";
$row_group_cnt = sql_fetch($sql_group_cnt);

$sql_fr_cnt = " select count(*) as cnt {$sql_common} where apply_user_job = '외부등록' ";
$row_fr_cnt = sql_fetch($sql_fr_cnt);



$g5['title'] = '이벤트 통계';
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
$sql_track = "select * from 2025_event_ticket";
$result_track = sql_query($sql_track);

?>


    <section id="anc_bo_basic">
        <div class="tbl_frm01 tbl_wrap">
            <style>
                .btn_table_re td {border:1px solid #ddd;padding:10px;}
                .btn_table_re th {border:1px solid #ddd;padding:10px;}
            </style>
            <table class="btn_table_re" style="width:1400px;">
                <thead class="">

                <tr >
                    <th style="text-align: right;">조회조건</th>
                    <th style="" >구분</th>
                    <th style="" >카운트</th>
                    <th style="" >구분</th>
                    <th style="" >카운트</th>
                    <th style="" >구분</th>
                    <th style="" >카운트</th>
                    <th style="" >합계</th>
                </tr>
                </thead>
                <tbody >
                <?
                //NORMAL_25
                $result_normal = sql_query("select apply_product_code,count(apply_product_code) as cnt from cb_unreal_2025_event2_apply where (apply_pay_status = 10 or apply_pay_status = 1) and apply_product_code like 'NORMAL%'  and free_yn='N' and apply_temp_yn = 'N' and (apply_coupon_no = 0 or apply_coupon_no is null) group by apply_product_code   order by apply_product_code   desc");
                echo "<tr>";
                echo "<td >순수구매자</td>";
                $total = 0;
                while($row_track = sql_fetch_array($result_normal)){
                    $total += $row_track["cnt"];
                    $type = $row_track["apply_product_code"];
                    switch ($type) {
                        case "NORMAL_25":
                            $type="25일권";
                            break;
                        case "NORMAL_26":
                            $type="26일권";
                            break;
                        case "NORMAL_ALL":
                            $type="양일권";
                            break;
                    }
                    echo "<td>".$type."</td>";
                    echo "<td>".$row_track["cnt"]."</td>";
                }
                echo "<td>".$total."</td>";
                echo "</tr>";
                ?>


                <?
                //NORMAL_25
                $result_normal = sql_query("select apply_product_code,count(apply_product_code) as cnt from cb_unreal_2025_event2_apply where (apply_pay_status = 10 or apply_pay_status = 1) and apply_product_code like 'NORMAL%'   and free_yn='N' and apply_temp_yn = 'N' and (apply_coupon_no >= 1 and apply_coupon_no < 10000 ) group by apply_product_code   order by apply_product_code   desc");
                echo "<tr class='bg-info bg bg1'>";
                echo "<td >단체</td>";
                $total = 0;
                while($row_track = sql_fetch_array($result_normal)){
                    $total += $row_track["cnt"];
                    $type = $row_track["apply_product_code"];
                    switch ($type) {
                        case "NORMAL_25":
                            $type="25일권";
                            break;
                        case "NORMAL_26":
                            $type="26일권";
                            break;
                        case "NORMAL_ALL":
                            $type="양일권";
                            break;
                    }
                    echo "<td>".$type."</td>";
                    echo "<td>".$row_track["cnt"]."</td>";
                }
                echo "<td>".$total."</td>";
                echo "</tr>";
                ?>



                <?
                //NORMAL_25
                $result_normal = sql_query("select apply_product_code,count(apply_product_code) as cnt from cb_unreal_2025_event2_apply where (apply_pay_status = 10 or apply_pay_status = 1) and apply_product_code like 'NORMAL%'  and free_yn='N' and apply_temp_yn = 'N' and (apply_coupon_no >= 10000 ) group by apply_product_code   order by apply_product_code   desc");
                echo "<tr>";
                echo "<td >스폰서</td>";
                $total = 0;
                while($row_track = sql_fetch_array($result_normal)){
                    $total += $row_track["cnt"];
                    $type = $row_track["apply_product_code"];
                    switch ($type) {
                        case "NORMAL_25":
                            $type="25일권";
                            break;
                        case "NORMAL_26":
                            $type="26일권";
                            break;
                        case "NORMAL_ALL":
                            $type="양일권";
                            break;
                    }
                    echo "<td >".$type."</td>";
                    echo "<td colspan='5'>".$row_track["cnt"]."</td>";
                }
                echo "<td >".$total."</td>";
                echo "</tr>";
                ?>
                </tbody>
            </table>
        </div>
    </section>


    <table class="btn_table_re" style="width:1400px;">
        <thead class="">
        <tr>
            <th colspan="4">일반신청</th>
        </tr>
        <tr >
            <th style="text-align: right;">번호</th>
            <th style="" >취소자</th>
            <th style="" >이름</th>
            <th style="" >환불일</th>
        </tr>
        </thead>
        <tbody >
    <?php
    $result_normal = sql_query("select apply_user_email,max(apply_user_name) as apply_user_name,max(refund_date) as rdate from cb_unreal_2025_event2_apply 
                                                           where  apply_product_code like 'NORMAL%'  
                                                             and free_yn='N' and apply_temp_yn = 'N' 
                                                             and (apply_coupon_no = 0 or apply_coupon_no is null )
                                                           and apply_user_email like 'del:%'
                                                           and apply_user_name NOT IN ('김대수','박경덕','유선희','최진영','박도은','황성현','송수용','오승훈')
                                                           group by apply_user_email   order by refund_date   asc");

    $loopI = 0;
    while($row_track = sql_fetch_array($result_normal)){
        $loopI += 1;
        echo "<tr>";
        echo "<td >".$loopI."</td>";
        echo "<td >".$row_track["apply_user_email"]."</td>";
        echo "<td >".$row_track["apply_user_name"]."</td>";
        echo "<td >".$row_track["rdate"]."</td>";
        echo "</tr>";
    }
    ?>
        </tbody>
</table>

    <br />
    <br />
    <br />
    <table class="btn_table_re" style="width:1400px;">
        <thead class="">
        <tr>
            <th colspan="4">단체신청</th>
        </tr>
        <tr >
            <th style="text-align: right;">번호</th>
            <th style="" >취소자</th>
            <th style="" >이름</th>
            <th style="" >등록일</th>
        </tr>
        </thead>
        <tbody >
        <?php
        $result_normal = sql_query("select apply_user_email,max(apply_user_name) as apply_user_name,max(apply_reg_datetime) as rdate from cb_unreal_2025_event2_apply 
                                                           where  apply_product_code like 'NORMAL%'  
                                                             and free_yn='N' and apply_temp_yn = 'N' 
                                                             and (apply_coupon_no < 10000 and apply_coupon_no > 0 )
                                                           and apply_user_email like 'del:%'
                                                           and apply_user_name NOT IN ('김대수','박경덕','유선희','최진영','박도은','황성현','송수용','오승훈')
                                                           group by apply_user_email   order by refund_date   asc");

        $loopI = 0;
        while($row_track = sql_fetch_array($result_normal)){
            $loopI += 1;
            echo "<tr>";
            echo "<td >".$loopI."</td>";
            echo "<td >".$row_track["apply_user_email"]."</td>";
            echo "<td >".$row_track["apply_user_name"]."</td>";
            echo "<td >".$row_track["rdate"]."</td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
<?php
include_once('./admin.tail.php');