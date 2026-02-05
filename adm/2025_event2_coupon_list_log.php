<?php
$sub_menu = "700730";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

$sql_common = " from cb_unreal_2025_event2_coupon_log a ";
$sql_search = " where 1=1 ";

if ($stx) {
    $sql_search .= " and ( ";
    if ($sfl) {
        $sql_search .= " ($sfl like '%$stx%') ";
    }
    $sql_search .= " ) ";
}

$sst  = "a.log_idx";
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




$g5['title'] = '쿠폰수정내역';
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

    <script type="text/javascript">
        $(document).on('click', '#export_to_excel', function() {
            var f = document.fsearch2;
            f.export.value = "excel";
            f.submit();
            f.export.value = "";
        });
    </script>



    <form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">

        <select class="" name="sfl">
            <option value="old_email" <?php if($sfl == 'old_email'){?>selected<?php } ?>>기존이메일</option>
            <option value="old_phone" <?php if($sfl == 'old_phone'){?>selected<?php } ?>>기존연락처</option>
            <option value="new_email" <?php if($sfl == 'new_email'){?>selected<?php } ?>>변경이메일</option>
            <option value="new_phone" <?php if($sfl == 'new_phone'){?>selected<?php } ?>>변경연락처</option>
        </select>
        <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
        <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
        <input type="submit" value="검색" class="btn_submit">
    </form>

    <p>검색된 리스트 : 총(<?=$total_count?>명)</p>
    <form class="form-inline" name="fsearch2" action="#n" method="get">
        <input type="hidden" name="export" value="">
        <input type="hidden" name="sst" value="<?php echo $sst ?>">
        <input type="hidden" name="sod" value="<?php echo $sod ?>">
        <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
        <input type="hidden" name="stx" value="<?php echo $stx ?>">
        <input type="hidden" name="page" value="<?php echo $page ?>">


        <div class="tbl_head01 tbl_wrap">
            <table border="1">
                <thead>
                <tr>
                    <th>고유번호</th>
                    <th>신청자고유번호</th>
                    <th>이름</th>
                    <th>이전이메일</th>
                    <th>신규이메일</th>
                    <th>이전연락처</th>
                    <th>신규연락처</th>
                    <th>수정일</th>
                </tr>
                </thead>
                <tbody>

                <?php while ($row = sql_fetch_array($result)) { ?>
                    <tr>
                        <td><?php echo $row['log_idx']; ?></td>
                        <td><?php echo $row['apply_no']; ?></td>
                        <td><?php echo $row['new_name']; ?></td>
                        <td><?php echo $row['old_email']; ?></td>
                        <td><?php echo $row['new_email']; ?></td>
                        <td><?php echo $row['old_phone']; ?></td>
                        <td><?php echo $row['new_phone']; ?></td>
                        <td><?php echo $row['regdate']; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </form>


    <style>
        .pagination {width:100%; margin:auto; text-align:center}
        .pagination ul { width:520px; margin:auto; text-align:center}
        .pagination li {float:left; margin: 10px;}
        .pagination li .active{ font-weight: 800;}
    </style>
<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>
<script>
</script>

    </script>
<?php
include_once('./admin.tail.php');