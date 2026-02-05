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
$sql_search = " where (apply_user_email like '%del:%') and apply_user_phone not in ('01029959522','01025752245','01085657487','01033299566','01025510017','01025510017','01041901933','01039543736') ";
$sql_search .= " ";


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


$g5['title'] = '온라인취소목록';
$colspan = 14;
?>
<style>
    th{border:1px solid;}
    td{border:1px solid;}
    </style>
<div class="tbl_head01 tbl_wrap">
    <table border="1">
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
            <th scope="col">환불일</th>
            <th scope="col">환불시간</th>
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
                <td><?=$row['refund_date']?></td>
                <td><?=$row['refund_time']?></td>
            </tr>
            <?php
        }
        if ($i == 0)
            echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
        ?>
        </tbody>
    </table>
</div>
