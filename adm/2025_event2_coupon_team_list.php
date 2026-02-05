<?php
$sub_menu = "700740";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

$sql_common = " from cb_unreal_2025_event2_coupon a ";
$sql_search = " where 1=1    ";

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

$sql = " select count(distinct creator_gcode) as cnt {$sql_common} {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

if (!$rows) {
    $rows = 51;
}
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select coupon_name,creator_email, creator_phone, creator_name, creator_file, creator_filename, creator_gcode, creator_memo,registration_date,
         count(*) as total_count,
         sum(case when coupon_type='25#26' then 1 else 0 end) as count_all,
         sum(case when coupon_type='25' then 1 else 0 end) as count_25,
         sum(case when coupon_type='26' then 1 else 0 end) as count_26
         {$sql_common} {$sql_search} 
         group by creator_gcode {$sql_order} 
         limit {$from_record}, {$rows} ";
$result = sql_query($sql);




$g5['title'] = '단체신청';
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

        function downloadFile(fileUrl, fileName) {
            var link = document.createElement('a');
            link.href = "/v3/unrealfest2025/student/"+fileUrl;
            link.download = fileName || 'download';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>

    <script type="text/javascript">
        $(document).on('click', '.save-memo', function () {
            var gcode = $(this).data('gcode');
            var memo = $('textarea[data-gcode="' + gcode + '"]').val();

            $.ajax({
                url: '2025_event2_coupon_team_ajax.php',
                type: 'POST',
                data: {
                    gcode: gcode,
                    memo: memo
                },
                success: function (response) {
                    var data = JSON.parse(response);
                    if (data.result) {
                        alert('메모가 저장되었습니다.');
                    } else {
                        alert('메모 저장에 실패했습니다.');
                    }
                }
            });
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
                    <th>No</th>
                    <th>회사명</th>
                    <th>담당자명</th>
                    <th>담당자 연락처</th>
                    <th>결제수단</th>
                    <th>사업자등록증</th>
                    <th>참가자수</th>
                    <th>총액</th>
                    <th>메모사항</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $seq = $from_record + 1;
                while ($row = sql_fetch_array($result)) {
                    ?>
                    <tr>
                        <td><?php echo $seq++; ?></td>

                        <td>
                            <a href="2025_event2_coupon_list.php?creator_gcode=<?php echo $row['creator_gcode']; ?>"><?php echo $row['coupon_name']; ?></a>
                        </td>
                        <td><?php echo $row['creator_name']; ?></td>
                        <td><?php echo $row['creator_phone']; ?></td>
                        <td>입금</td>
                        <td><?php if ($row['creator_file']) { ?>
                            <a href="#"
                               onclick="downloadFile('<?php echo $row['creator_file']; ?>', '<?php echo $row['creator_filename']; ?>')"
                               class="btn btn_01">사업자등록증</a><?php } ?>
                        </td>
                        <td>
                            <span>양일권 <?php echo $row['count_all']; ?>명</span><br />
                            <span>25일권 <?php echo $row['count_25']; ?>명</span><br />
                            <span>26일권 <?php echo $row['count_26']; ?>명</span>
                        </td>
                        <!-- 쿠폰목록에서 해당쿠폰 삭제시 반영-->
                        <!--담당자 이메일 dsmslove@naver.com 세금계산서 발급 예정 2025-02-15-->
                        <td><?php

                            $registration_date = $row["registration_date"];

                            if ($registration_date <= "2025-07-23 23:59:59") {
                                echo number_format($row['count_all'] * 60000 + ($row['count_25'] + $row['count_26']) * 30000);
                            } else {
                                echo number_format($row['count_all'] * 120000 + ($row['count_25'] + $row['count_26']) * 60000);
                            }

                            ?>
                            원
                        </td>
                        <td>
                            <textarea name="creator_memo"
                                      data-gcode="<?php echo $row['creator_gcode']; ?>"><?php echo $row['creator_memo']; ?></textarea>
                            <a href="#n" class="btn btn_01 save-memo" data-gcode="<?php echo $row['creator_gcode']; ?>">저장</a>
                        </td>
                    </tr>
                    <?php
                }
                if ($result->num_rows == 0) {
                    echo '<tr><td colspan="8" class="empty_table">자료가 없습니다.</td></tr>';
                }
                ?>
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

<?php
include_once('./admin.tail.php');