<?php
$sub_menu = "700730";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

$sql_common = " from cb_unreal_2025_event2_coupon a ";
$sql_search = " where 1=1 ";

if ($stx) {
    $sql_search .= " and ( ";
    if ($sfl) {
        $sql_search .= " ($sfl like '%$stx%') ";
    }
    $sql_search .= " ) ";
}

$sst  = "a.coupon_key";
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




$g5['title'] = '쿠폰목록';
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

<!--
    <section id="anc_bo_basic">
        <h2 class="h2_frm">쿠폰관리</h2>
        <form method="post" action="2025_event2_coupon_create.php">
        <div class="tbl_frm01 tbl_wrap">
            <table>
                <caption>쿠폰관리</caption>
                <colgroup>
                    <col class="grid_4">
                    <col>
                    <col class="grid_3">
                </colgroup>
                <tbody>
                <tr>
                    <th scope="row" class="text_center"><label for="bo_table">쿠폰생성</label></th>
                    <td>
                        <table class="btn_table_re">
                            <colgroup>
                                <col style="width:40%">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                            </colgroup>
                            <thead class="text_center">
                            <tr>
                                <th>쿠폰명칭</th>
                                <th>구분</th>
                                <th>할인율</th>
                                <th>발행갯수</th>
                                <th>등록</th>
                            </tr>
                            </thead>
                            <tbody class="text_center">

                            <tr class="last">
                                <td><input type="text" style="width:300px;" class="frm_input" id="coupon_name" name="coupon_name" required></td>
                                <td>
                                    <select name="coupon_type">
                                        <option value="일반">일반</option>
                                        <option value="학생">학생</option>
                                    </select>
                                </td>
                                <td><input type="number" class="frm_input" id="discount_rate" name="discount_rate" required>%</td>
                                <td><input type="number" class="frm_input" id="coupon_count" name="coupon_count" required></td>
                                <td><input type="submit" class="frm_input" value="쿠폰 생성"></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        </form>
    </section>-->
















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
            <option value="coupon_serial" <?php if($sfl == 'coupon_serial'){?>selected<?php } ?>>쿠폰번호</option>
            <option value="user_name" <?php if($sfl == 'user_name'){?>selected<?php } ?>>이름</option>
            <option value="coupon_name" <?php if($sfl == 'coupon_name'){?>selected<?php } ?>>쿠폰발급명</option>
            <option value="user_email" <?php if($sfl == 'user_email'){?>selected<?php } ?>>이메일</option>
        </select>
        <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
        <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
        <input type="submit" value="검색" class="btn_submit">
        <button type="button" class="btn btn-outline btn-success btn-sm" id="export_to_excel"><i class="fa fa-file-excel-o"></i> 엑셀 다운로드</button>
    </form>

    <p>검색된 리스트 : 총(<?=$total_count?>명)</p>
    <form class="form-inline" name="fsearch2" action="https://epiclounge.co.kr/v3/adm/2025_event2_coupon_list_excel.php" method="get">
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
                    <th>쿠폰 키</th>
                    <th>쿠폰 일련번호</th>
                    <th>쿠폰구분</th>
                    <th>쿠폰명</th>
                    <th>사용자 명</th>
                    <th>사용자 연락처</th>
                    <th>사용자 이메일</th>
                    <th>관리</th>
                    <!--<th>할인율</th>-->
                    <th>등록일</th>
                    <th>사용일</th>
                </tr>
                </thead>
                <tbody>

                <?php while ($row = sql_fetch_array($result)) { ?>
                    <?
                    $coupon_type = $row['coupon_type'];
                    switch ($coupon_type) {
                        case '25':
                            $coupon_type = '25일권';
                            break;
                        case '26':
                            $coupon_type = '26일권';
                            break;
                        case '25#26':
                            $coupon_type = '양일권';
                            break;
                    }
                    ?>
                    <tr>
                        <td><?php echo $row['coupon_key']; ?></td>
                        <td><?php echo $row['coupon_serial']; ?></td>
                        <td><?php echo $coupon_type; ?></td>
                        <td><?php echo $row['coupon_name']; ?></td>
                        <td><?php echo $row['user_name']; ?>
                            <input type="hidden" name="user_name_<?php echo $row['coupon_key']; ?>"
                                   value="<?php echo $row['user_name']; ?>" class="frm_input"></td>
                        <td><?php
                            if($row['usage_date'] == null || $row['usage_date'] == ''){
                                ?><input type="text" name="user_phone_<?php echo $row['coupon_key']; ?>" value="<?php echo $row['user_phone']; ?>" class="frm_input"><?
                            }else{
                                ?><?php echo $row['user_phone']; ?><?
                            }
                            ?>

                        </td>
                        <td><?php
                            if($row['usage_date'] == null || $row['usage_date'] == ''){
                                ?><input type="text" name="user_email_<?php echo $row['coupon_key']; ?>" value="<?php echo $row['user_email']; ?>" class="frm_input"><?
                            }else{
                                ?><?php echo $row['user_email']; ?><?
                            }
                            ?>

                        </td>
                        <td>
                            <?php
                            if($row['usage_date'] == null || $row['usage_date'] == ''){
                                ?>
                                <button type="button" class="btn_update"
                                        onclick="updateCoupon('<?php echo $row['coupon_key']; ?>')">수정
                                </button>
                                <button type="button" class="btn_delete"
                                        onclick="deleteCoupon('<?php echo $row['coupon_key']; ?>')">삭제
                                </button>
                                <?
                            }else{
                                ?><?
                            }
                            ?>
                        </td>
                        <!--<td><?php /*echo $row['discount_rate']; */?></td>-->
                        <td><?php echo $row['registration_date']; ?></td>
                        <td><?php echo $row['usage_date']; ?></td>
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
    function updateCoupon(coupon_key) {
        var email = $('input[name="user_email_' + coupon_key + '"]').val();
        var phone = $('input[name="user_phone_' + coupon_key + '"]').val();
        var name = $('input[name="user_name_' + coupon_key + '"]').val();

        $.ajax({
            url: '2025_event2_coupon_ajax.php',
            type: 'POST',
            data: {
                mode: 'update',
                coupon_key: coupon_key,
                user_email: email,
                user_phone: phone,
                user_name: name
            },
            success: function (response) {
                var result = JSON.parse(response);
                if (result.result) {
                    alert('수정되었습니다.');
                } else {
                    alert(result.msg);
                }
            },
            error: function () {
                alert('서버 오류가 발생했습니다.');
            }
        });
    }

    function deleteCoupon(coupon_key) {
        if (!confirm('정말 삭제하시겠습니까?')) {
            return;
        }

        $.ajax({
            url: '2025_event2_coupon_ajax.php',
            type: 'POST',
            data: {
                mode: 'delete',
                coupon_key: coupon_key
            },
            success: function (response) {
                var result = JSON.parse(response);
                if (result.result) {
                    alert('삭제되었습니다.');
                    location.reload();
                } else {
                    alert(result.msg);
                }
            },
            error: function () {
                alert('서버 오류가 발생했습니다.');
            }
        });
    }
</script>

    </script>
<?php
include_once('./admin.tail.php');