<?php
include_once('./_common.php');
ini_set('display_errors', 1);

auth_check_menu($auth, $sub_menu, 'r');
header( "Content-type: application/vnd.ms-excel; charset=utf8" );

header('Content-Disposition: attachment; filename=event_list' . date('Y_m_d') . '.xls');

header( "Content-Description: PHP4 Generated Data" );

header('Content-Type: text/html; charset=UTF-8');
// header('Content-type: application/vnd.ms-excel');
// header('Content-Disposition: attachment; filename=event_list_' . cdate('Y_m_d') . '.xls');


$sql_common = " from cb_unreal_2024_event2_apply a ";
$sql_search = " where (apply_pay_status = 10 or apply_pay_status = 1) ";
$sql_search .= " and apply_temp_yn = 'N' ";
if ($stx) {
    $sql_search .= " and ( ";
    if ($sfl) {
        $sql_search .= " ($sfl like '%$stx%') ";
    }
    $sql_search .= " ) ";
}

if ($scode) {
    $sql_search .= "and apply_product_code = '$scode'";
    $qstr .= '&amp;scode=' . urlencode($scode);
}
if ($strack) {
    $sql_search .= "and apply_track = '$strack'";
    $qstr .= '&amp;strack=' . urlencode($strack);
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

}

$sst  = "a.apply_no";
$sod = "desc";
$sql_order = " order by $sst $sod ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select * {$sql_common} {$sql_search} {$sql_order}";// limit {$from_record}, {$rows}
$result = sql_query($sql);


$g5['title'] = '이벤트신청리스트';
$colspan = 14;
?>

<div class="tbl_head01 tbl_wrap">
    <table>
        <caption><?php echo $g5['title']; ?> 목록</caption>
        <thead>
        <tr>
            <th scope="col">번호</th>
            <th scope="col">이름</th>
            <th scope="col">이메일</th>
            <th scope="col">연락처</th>
            <th scope="col">트랙</th>
            <th scope="col">회사,학교</th>
            <th scope="col">부서,학과</th>
            <th scope="col">직무,학년</th>
            <th scope="col">산업/관심분야</th>
            <th scope="col">신청구분</th>
            <th scope="col">가입일</th>
            <th scope="col">결제방식</th>
            <th scope="col">초대코드</th>
            <th scope="col">거래아이디</th>
            <th scope="col">수신동의</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for ($i=1; $row=sql_fetch_array($result); $i++) {
            $bg = 'bg'.($i%2);
            $pay_method = $row["pay_paymethod"];
            $mpay_type = $row["mpay_type"];
            $str_pay_method = "";
            if($pay_method == "Card"){
                $str_pay_method = "신용카드";
            }else if($pay_method == "VBank"){
                if($row["apply_pay_status"] == "1"){
                    $str_pay_method = "가상계좌[미입금]";
                }else{
                    $str_pay_method = "가상계좌";
                }

            }else if($mpay_type == "CARD"){
                $str_pay_method = "신용카드";

            }else if($pay_method == "VCard"){
                $str_pay_method = "신용카드";
            }else if($pay_method == "VCARD"){
                $str_pay_method = "신용카드";
            }else  if($mpay_type == "VBANK"){
                if($row["apply_pay_status"] == "1"){
                    $str_pay_method = "가상계좌[미입금]";
                }else{
                    $str_pay_method = "가상계좌";
                }

            }
            ?>

            <tr class="<?php echo $bg; ?>">
                <td class="td_chk">
                    <?php echo $i; ?>
                </td>
                <td><?=$row['apply_user_name']?></td>
                <td><?=$row['apply_user_email']?></td>
                <td  style='mso-number-format:\@;'><?=$row['apply_user_phone']?></td>
                <td><?=$row['apply_track']?></td>
                <td><?=$row['apply_user_company']?></td>
                <td><?=$row['apply_user_depart']?></td>
                <td><?=$row['apply_user_grade']?></td>
                <td><?=$row['apply_user_ex1']?></td>
                <td><?php
                    if($row['free_yn'] == 'Y'){
                        echo "온라인";
                    }else if($row['apply_product_code'] == "NORMAL_ALL"){
                        echo "양일권";
                    }else if($row['apply_product_code'] == "NORMAL_28"){
                        echo "28일권";
                    }else if($row['apply_product_code'] == "NORMAL_29"){
                        echo "29일권";
                    }else if($row['apply_product_code'] == "STD_ALL"){
                        echo "학생 양일권";
                    }else if($row['apply_product_code'] == "STD_28"){
                        echo "학생 28일권";
                    }else if($row['apply_product_code'] == "STD_29"){
                        echo "학생 29일권";
                    }
                    ?></td>
                <td><?=$row['apply_reg_datetime']?></td>
                <td><?=$str_pay_method?></td>
                <td><?=$row['apply_password']?></td>
                <td><?=$row['mpay_tid']?$row['mpay_tid']:$row['pay_tid']?></td>
               <td><?=$row['apply_user_event_agree']  =="1" ? "동의":"미동의"?></td>
            </tr>
            <?php
        }
        if ($i == 0)
            echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
        ?>
        </tbody>
    </table>
</div>
