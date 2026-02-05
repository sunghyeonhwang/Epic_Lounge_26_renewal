<?php
$sub_menu = "700610";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');
$sql_common = " from cb_unreal_2024_event2_apply_inquire_qna a ";
$sql_search = " where 1 ";

if ($stx) {
    $sql_search .= " and ( ";
    if ($sfl) {
        $sql_search .= " ($sfl like '%$stx%') ";
    }
    $sql_search .= " ) ";
}


$sst  = "a.apply_no";
$sod = "desc";
$sql_order = " order by $sst $sod ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
if (!$rows) {
    $rows = 10;
}
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);


$sql_all_cnt = " select count(*) as cnt from cb_unreal_2024_event2_apply where apply_pay_status = 10  ";
$row_all_cnt = sql_fetch($sql_all_cnt);


$sql_free_cnt = " select count(*) as cnt from cb_unreal_2024_event2_apply  where apply_pay_status = 10 and free_yn='Y' ";
$row_free_cnt = sql_fetch($sql_free_cnt);

$sql_pay_cnt = " select count(*) as cnt from cb_unreal_2024_event2_apply  where apply_pay_status = 10 and free_yn='N' ";
$row_pay_cnt = sql_fetch($sql_pay_cnt);

$sql_refund_cnt = " select count(*) as cnt from cb_unreal_2024_event2_apply  where IFNULL(refund_date,'') <> '' ";
$row_refund_cnt = sql_fetch($sql_refund_cnt);


$sql_wait_cnt = " select count(*) as cnt from cb_unreal_2024_event2_apply_inquire_qna ";
$row_wait_cnt = sql_fetch($sql_wait_cnt);


$g5['title'] = '문의내역';
include_once('./admin.head.php');

$colspan = 15;
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
        body table td
        {
            font-size: 12px;
        }
    </style>

    <form name="fboardlist" id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
        <input type="hidden" name="sst" value="<?php echo $sst ?>">
        <input type="hidden" name="sod" value="<?php echo $sod ?>">
        <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
        <input type="hidden" name="stx" value="<?php echo $stx ?>">
        <input type="hidden" name="page" value="<?php echo $page ?>">
        <input type="hidden" name="token" value="<?php echo isset($token) ? $token : ''; ?>">

        <div class="tbl_head01 tbl_wrap">
            <table>
                <caption><?php echo $g5['title']; ?> 목록</caption>
                <thead>
                <tr>
                    <th scope="col">번호</th>
                    <th scope="col">이름</th>
                    <th scope="col">이메일</th>
                    <th scope="col">연락처</th>
                    <th scope="col">회사명</th>
                    <th scope="col">부서</th>
                    <th scope="col">분류</th>
                    <th scope="col" style="width: 400px;">내용</th>
                    <th scope="col">입렵일</th>
                </tr>
                </thead>
                <tbody>
                <?php
                for ($i=1; $row=sql_fetch_array($result); $i++) {
                    $bg = 'bg'.($i%2);
                    ?>

                    <tr class="<?php echo $bg; ?>">
                        <td class="td_chk">
                            <?php echo $total_count+1-($i+(($page-1)*$rows)); ?>
                        </td>
                        <td><?=$row['apply_user_name']?></td>
                        <td><?=$row['apply_user_email']?></td>
                        <td><?=$row['apply_user_phone']?></td>
                        <td><?=$row['apply_user_company']?></td>
                        <td><?=$row['apply_user_depart']?></td>
                        <td><?=$row['apply_user_grade']?></td>
                        <td><?=$row['apply_content']?></td>
                        <td><?=$row['apply_reg_datetime']?></td>
                    </tr>
                    <?php
                }
                if ($i == 0)
                    echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
                ?>
                </tbody>
            </table>
        </div>
        <style>
            .pagination {width:100%; margin:auto; text-align:center}
            .pagination ul { width:520px; margin:auto; text-align:center}
            .pagination li {float:left; margin: 10px;}
            .pagination li .active{ font-weight: 800;}
        </style>
        <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

    </form>
    <form class="form-inline" name="fsearch2" action="https://epiclounge.co.kr/v2/adm/export_excel_inquire.php" method="get">
        <input type="hidden" name="export" value="">
        <input type="hidden" name="apply_reg_datetime" value="<?=$apply_reg_datetime?>">
        <input type="hidden" name="track" value="<?=$track?>">
        <input type="hidden" name="stx" value="<?=$stx?>">
    </form>
    <script type="text/javascript">
        $(document).on('click', '#export_to_excel', function() {
            var f = document.fsearch2;
            f.export.value = "excel";
            f.submit();
            f.export.value = "";
        });
    </script>
<?php
include_once('./admin.tail.php');