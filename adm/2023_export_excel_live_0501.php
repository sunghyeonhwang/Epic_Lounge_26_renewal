<?php
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');
header( "Content-type: application/vnd.ms-excel; charset=utf8" );
header('Content-Disposition: attachment; filename=live_list' . date('Y_m_d') . '.xls');
header( "Content-Description: PHP4 Generated Data" );
header('Content-Type: text/html; charset=UTF-8');
// header('Content-type: application/vnd.ms-excel');
// header('Content-Disposition: attachment; filename=event_list_' . cdate('Y_m_d') . '.xls');


$sql_common = " from cb_unreal_2023_event_apply_live a ";
$sql_search = " where 1 ";

if ($stx) {
    $sql_search .= " and ( ";
    if ($sfl) {
        $sql_search .= " ($sfl like '%$stx%') ";
    }
    $sql_search .= " ) ";
}

if ($apply_reg_datetime) {
    $sql_search .= "and apply_reg_datetime like '$apply_reg_datetime%'";
}

if ($track == 1) {
    $sql_search .= "and track1 = '$track'";
} else if ($track == 2) {
    $sql_search .= "and track2 = '$track'";
}

if ($export == 'all') {
    $sql_search .= " group by apply_user_email ";
}
if($export == "dual"){
    $sql_common = " from cb_unreal_2023_event_apply_live a,cb_unreal_2023_event_apply b ";
    $sql_search .= "and a.apply_user_email=b.apply_user_email and b.free_yn = 'Y' group by a.apply_user_email";
}

if ($export == 'rand15') {
    $sql_search .= "and down is null";
    $sst  = "rand()";
    $sod = "limit 0, 15";
} else if ($export == 'rand300') {
    $sql_search .= "and down is null";
    $sst  = "rand()";
    $sod = "limit 0, 300";
}  else if ($export == 'all') {

} else {
    $sst  = "a.apply_no";
    $sod = "desc";
}
$sql_order = " order by $sst $sod ";

if($export == "all"){
    $sql = "select count(*) as cnt from (
            select apply_user_email ,count(*) as cnt from cb_unreal_2023_event_apply_live a 
            where 1 
            group by apply_user_email 
            )b, cb_unreal_2023_event_apply_live l where cnt = 4 and l.apply_user_email=b.apply_user_email 
            group by l.apply_user_email ";
}else if($export == "rand300"){
    $sql = "select count(*) as cnt from (
            select apply_user_email ,count(*) as cnt from cb_unreal_2023_event_apply_live a 
            where 1 
            group by apply_user_email 
            )b, cb_unreal_2023_event_apply_live l where cnt = 4 and l.apply_user_email=b.apply_user_email and down is null
            group by l.apply_user_email order by rand() limit 0, 300";

}else if($export == "excel"){
    $sql = "select count(*) as cnt from cb_unreal_2023_event_apply_live a 
            where 1 
            order by a.apply_no desc";

}else if($export == "dual"){
    $sql = "select count(*) cnt from (
                select count(*) as cnt from cb_unreal_2023_event_apply_live a , cb_unreal_2023_event_apply b 
                where 1 and a.apply_user_email=b.apply_user_email and b.free_yn = 'N'
                group by a.apply_user_email
            )cc";

}else{
    $sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
}
$row = sql_fetch($sql);
$total_count = $row['cnt'];
if($total_count == 0){
    echo 'no list';
    exit();
}


if($export == "all"){
    $sql = "select * from (
            select apply_user_email ,count(*) as cnt from cb_unreal_2023_event_apply_live a 
            where 1 
            group by apply_user_email 
            )b, cb_unreal_2023_event_apply_live l , cb_unreal_2023_event_apply e
            where cnt = 4 and l.apply_user_email=b.apply_user_email 
              and e.apply_user_email=b.apply_user_email
            group by l.apply_user_email ";
}else if($export == "rand300"){
    $sql = "select * from (
            select apply_user_email ,count(*) as cnt from cb_unreal_2023_event_apply_live a 
            where 1 
            group by apply_user_email 
            )b, cb_unreal_2023_event_apply_live l   , cb_unreal_2023_event_apply e
         where cnt = 4 and l.apply_user_email=b.apply_user_email and down is null
              and e.apply_user_email=b.apply_user_email
            group by l.apply_user_email order by rand() limit 0, 300";

}else if($export == "excel"){
    $sql = "select * from cb_unreal_2023_event_apply_live a, cb_unreal_2023_event_apply b 
            where 1 and a.apply_user_email=b.apply_user_email 
            order by a.apply_no desc";

}else if($export == "dual"){
    $sql = "select * from cb_unreal_2023_event_apply_live a, cb_unreal_2023_event_apply b 
            where 1 and a.apply_user_email=b.apply_user_email
            group by a.apply_user_email
            order by a.apply_no desc";

    $sql = "
                select * from cb_unreal_2023_event_apply_live a , cb_unreal_2023_event_apply b 
                where 1 and a.apply_user_email=b.apply_user_email and b.free_yn = 'N'
                group by a.apply_user_email ";
}else{
    $sql = " select * {$sql_common} {$sql_search} {$sql_order}";
}
$result = sql_query($sql);
if ($_GET['export'] == 'rand60') {
    $apply_reg_datetime = $_GET['apply_reg_datetime'];
    $track = $_GET['track'];
    $stx = $_GET['stx'];

    $sql_common = "FROM cb_unreal_2023_event_apply_live a WHERE 1";
    if ($apply_reg_datetime) {
        $sql_common .= " AND apply_reg_datetime LIKE '$apply_reg_datetime%'";
    }

    if ($track == 1) {
        $sql_common .= " AND track1 = '1'";
    } else if ($track == 2) {
        $sql_common .= " AND track2 = '2'";
    }

    if ($stx) {
        $sql_common .= " AND (apply_user_name LIKE '%$stx%' OR apply_user_email LIKE '%$stx%' OR apply_user_phone LIKE '%$stx%')";
    }

    // ★ 랜덤 60명 추출 쿼리
    $sql = "SELECT * $sql_common ORDER BY RAND() LIMIT 60";
    $result = sql_query($sql);

    // 이제 $result를 가지고 엑셀 파일 생성
    // (이미 기존 다른 export 케이스처럼 처리하면 됨)

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"live_rand_60_list.xls\"");
    header("Content-Description: PHP Generated Data");

    echo "<table border='1'>";
    echo "<tr><th>이름</th><th>이메일</th><th>연락처</th><th>최초접속</th><th>최종접속</th></tr>";

    while ($row = sql_fetch_array($result)) {
        echo "<tr>";
        echo "<td>{$row['apply_user_name']}</td>";
        echo "<td>{$row['apply_user_email']}</td>";
        echo "<td>{$row['apply_user_phone']}</td>";
        echo "<td>{$row['apply_reg_datetime']}</td>";
        echo "<td>{$row['apply_reg_datetime2']}</td>";
        echo "</tr>";
    }

    echo "</table>";
    exit;
}

?>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption>기간:
        <?php
            if ($apply_reg_datetime) { echo $apply_reg_datetime; } else { echo '전체'; }
            echo ' / ';
        if ($export == 'rand15') { echo '15명 랜덤 리스트'; } else { echo '전체 리스트'; }
        if ($export == 'rand300') { echo '300명 랜덤 리스트'; } else { echo '전체 리스트'; }
            if ($track) { echo ' / 트랙:';echo $track; }
        ?>
    </caption>
    
    <?php
        if ($export == 'rand15' || $export == 'rand300') {
    ?>
        <thead>
            <tr>
                <th scope="col">번호</th>
                <th scope="col">이름</th>
                <th scope="col">이메일</th>
                <th scope="col">연락처</th>
                <th scope="col">접속시간</th>
               <!-- <th scope="col">트랙1</th>
                <th scope="col">트랙2</th>-->
            </tr>
        </thead>
        <tbody>
            <?php
            for ($i=1; $row=sql_fetch_array($result); $i++) {
                //$sql3 = "UPDATE cb_unreal_2023_event_apply_live SET down = '1' WHERE apply_user_email = '".$row['apply_user_email']."'";
                //$result3 = sql_query($sql3);
            ?>
                <tr>
                    <td class="td_chk">
                        <?php echo $i; ?>
                    </td>
                    <td><?=$row['apply_user_name']?></td>
                    <td><?=$row['apply_user_email']?></td>
                    <td style='mso-number-format:\@;'><?=$row['apply_user_phone']?></td>
                    <td><?=$row['apply_reg_datetime']?></td>
                   <!-- <td><?=$row['track1']?></td>
                    <td><?=$row['track2']?></td>-->
                </tr>
            <?php
            }
            ?>
        </tbody>
    <?php } else { ?>
        <thead>
            <tr>
                <th scope="col">번호</th>
                <th scope="col">이름</th>
                <th scope="col">이메일</th>
                <th scope="col">연락처</th>
                <th scope="col">부문</th>
                <th scope="col">회사명</th>
                <th scope="col">부서</th>
                <th scope="col">직무</th>
                <th scope="col">산업/관심분야</th>
                <th scope="col">선택세션(Day1)</th>
                <th scope="col">선택세션(Day2)</th>
                <th scope="col">선택세션(Day3)</th>
                <th scope="col">선택세션(Day4)</th>
                <th scope="col">계정이메일</th>
                <!-- <th scope="col">상품</th> -->
                <th scope="col">수신동의</th>
                <th scope="col">가입일</th>
                <th scope="col">초대코드</th>
                <th scope="col">최초접속시간</th>
                <th scope="col">최종접속시간</th>
            </tr>
        </thead>
        <tbody>
            <?php
            for ($i=1; $row=sql_fetch_array($result); $i++) {
                //$sql2 = "select * from cb_unreal_2023_event_apply where apply_user_email = '".$row['apply_user_email']."' limit 1";
                //$row2 = sql_fetch($sql2);
            ?>
                <tr>
                    <td>
                        <?php echo $i; ?>
                    </td>
                    <td><?=$row['apply_user_name']?></td>
                    <td><?=$row['apply_user_email']?></td>
                    <td style='mso-number-format:\@;'><?=$row['apply_user_phone']?></td>
                    <td><?=$row['apply_user_job']?></td>
                    <td><?=$row['apply_user_company']?></td>
                    <td><?=$row['apply_user_depart']?></td>
                    <td><?=$row['apply_user_grade']?></td>
                    <td><?=$row['apply_sector']?></td>
                    <td>
                        <?
                        if ($row['apply_user_ex1']) {
                            echo '8월 29일 (화) - 공통';
                        }
                        ?>
                    </td>
                    <td>
                        <?
                        if ($row['apply_user_ex2']) {
                            echo '8월 30일 (수) - 게임';
                        }
                        ?>
                    </td>
                    <td>
                        <?
                        if ($row['apply_user_ex3']) {
                            echo '영화 & TV / 라이브 이벤트 / 애니메이션';
                        }
                        ?>
                    </td>
                    <td>
                        <?
                        if ($row['apply_user_ex4']) {
                            echo '9월 1일 (금) - AEC/자동차/시뮬레이션';
                        }
                        ?>
                    </td>
                    <td><?=$row['apply_user_email2']?></td>
                    <!-- <td><?=$row['apply_product_name']?></td> -->
                    <td><?=$row['apply_user_event_agree']?></td>
                    <td><?=$row['apply_reg_datetime']?></td>
                    <td><?=$row['apply_password']?></td>
                    <td><?=$row['apply_reg_datetime']?></td>
                    <td><?=$row['apply_reg_datetime2']?></td>
                </tr>
            <?php } ?>
        </tbody>
    <?php } ?>
    </table>
</div>