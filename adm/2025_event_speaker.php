<?php
$sub_menu = "700707";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

// 새 테이블 사용
$sql_common = " from cb_unreal_2025_speaker_apply a ";
$sql_search = " where 1=1 ";

// 검색어 조건 : 기존 검색 필드가 apply_user_name/email/phone 인 경우 새 테이블의 컬럼명으로 매핑
if ($stx) {
    $sql_search .= " and ( ";
    if ($sfl) {
        if ($sfl == 'apply_user_name') {
            $sfl = 'speaker_name';
        } elseif ($sfl == 'apply_user_email') {
            $sfl = 'speaker_email';
        } elseif ($sfl == 'apply_user_phone') {
            $sfl = 'speaker_ph';
        }
        $sql_search .= " ($sfl like '%$stx%') ";
    }
    $sql_search .= " ) ";
}

// 날짜 필터링 (등록일 기준 : created_at)
if ($start_date) {
    $sql_search .= " and a.created_at > '$start_date'";
}
if ($end_date) {
    $sql_search .= " and a.created_at < '$end_date 23:59:59'";
}

$qstr .= "&amp;stx=" . urlencode($stx) . "&amp;sfl=" . $sfl;

// 정렬 : 기본적으로 id를 내림차순 정렬
$sst  = "a.id";
$sod = "desc";
$sql_order = " order by $sst $sod ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

if (!$rows) {
    $rows = 51;
}
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; }
$from_record = ($page - 1) * $rows;

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$g5['title'] = '스피커 신청 리스트';
include_once('./admin.head.php');

$colspan = 12;
?>
<!-- 부트스트랩, 폰트 등 외부 CSS/JS 로드 (기존 코드와 동일) -->
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
    h1 {
        font-weight: bold;
        font-size: 1.5em;
        font-family: 'Malgun Gothic', "맑은 고딕", AppleGothic, Dotum, "돋움", sans-serif;
    }
    body table td {
        font-size: 12px;
    }
</style>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
    <div class="local_ov01 local_ov">
        <a href="2025_speaker_list.php" class="btn btn_03 ft_11">전체 <?php echo $total_count; ?>명</a>
    </div>
    <select name="sfl">
        <option value="apply_user_name" <?php if($sfl=='speaker_name'){?>selected<?php } ?>>이름</option>
        <option value="apply_user_email" <?php if($sfl=='speaker_email'){?>selected<?php } ?>>이메일</option>
        <option value="apply_user_phone" <?php if($sfl=='speaker_ph'){?>selected<?php } ?>>전화번호</option>
    </select>
    <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
    <input type="submit" value="검색" class="btn_submit">
    <button type="button" class="btn btn-outline btn-success btn-sm" id="export_to_excel">
        <i class="fa fa-file-excel-o"></i> 엑셀 다운로드
    </button>
</form>
<p>검색된 리스트 : 총(<?php echo $total_count; ?>명)</p>
<form name="fboardlist" id="fboardlist" action="#b" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <!-- 토큰 값 등 필요 시 추가 -->
    <div class="tbl_head01 tbl_wrap">
        <table>
            <caption><?php echo $g5['title']; ?> 목록</caption>
            <colgroup>
                <col style="width:70px">
                <col style="width:70px">
                <col style="width:110px">
                <col style="width:70px">
                <col style="width:100px">
                <col style="width:300px">
                <col style="width:70px">
                <col style="width:70px">
                <col style="width:200px">
                <col style="width:200px">
                <col style="width:70px">
                <col style="width:70px">
            </colgroup>
            <thead>
            <!-- 첫 번째 행 -->
            <tr>
                <th scope="col" rowspan="3">번호</th>
                <th scope="col">이름</th>
                <th scope="col">이메일</th>
                <th scope="col">연락처</th>
                <th scope="col">회사</th>
                <th scope="col" colspan="2">분야</th>
                <th scope="col">난이도</th>
                <th scope="col">주제</th>
                <th scope="col">플랫폼</th>
                <th scope="col">가입일</th>
                <th scope="col" rowspan="3">삭제</th>
            </tr>
            <!-- 두 번째 행 -->
            <tr>
                <th scope="col" colspan="2">약력</th>
                <th scope="col" colspan="2">세션제목</th>
                <th scope="col" colspan="2">세션소개</th>
                <th scope="col" colspan="2">요청사항</th>
                <th scope="col" colspan="2">스피커이미지</th>
            </tr>
            <!-- 세 번째 행 -->
            <tr>
                <th scope="col" colspan="2">제품군</th>
                <th scope="col" colspan="2">청강대상</th>
                <th scope="col" colspan="2">발표자료PDF</th>
                <th scope="col" colspan="2">발표영상</th>
                <th scope="col" colspan="2">목차</th>
            </tr>
            </thead>
            <tbody>
            <?php
            for ($i = 1; $row = sql_fetch_array($result); $i++) {
                $bg = 'bg' . ($i % 2);
                // 번호 : 전체 개수에서 역순으로 계산
                $number = $total_count + 1 - ($i + (($page - 1) * $rows));
                ?>
                <!-- 1행 -->
                <tr class="<?php echo $bg; ?>">
                    <td class="td_chk" rowspan="3"><?php echo $number; ?></td>
                    <td><?php echo $row['speaker_name']; ?></td>
                    <td><?php echo $row['speaker_email']; ?></td>
                    <td><?php echo $row['speaker_ph']; ?></td>
                    <td><?php echo $row['speaker_cp']; ?><?php echo ($row['speaker_cp_j']) ? " (" . $row['speaker_cp_j'] . ")" : ""; ?></td>
                    <td colspan="2"><?php echo $row['industry']; ?></td>
                    <td><?php echo $row['level']; ?></td>
                    <td><?php echo $row['topic']; ?></td>
                    <td><?php echo $row['platform']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td rowspan="3">
                        <a style="font-weight: 900;" href="javascript:;" onclick="del(<?=$row['id']?>)">
                            삭제
                        </a>
                    </td>
                </tr>
                <!-- 2행 -->
                <tr class="<?php echo $bg; ?>">
                    <td colspan="2"><?php echo $row['speaker_hi']; ?></td>
                    <td colspan="2"><?php echo $row['speaker_session']; ?></td>
                    <td colspan="2"><?php echo $row['speaker_takeaway']; ?></td>
                    <td colspan="2"><?php echo $row['speaker_requests']; ?></td>
                    <td colspan="2">
                        <?php
                        if (!empty($row['speaker_pic'])) {
                            echo '<a href="/v3/data/file/speak/' . $row['speaker_pic'] . '" target="_blank">세션이미지</a>';
                        }
                        ?>
                    </td>
                </tr>
                <!-- 3행 -->
                <tr class="<?php echo $bg; ?>">
                    <td colspan="2"><?php echo $row['product']; ?></td>
                    <td colspan="2"><?php echo $row['speaker_target']; ?></td>
                    <td colspan="2"><?php echo ($row['pdf_consent']) ? "예" : "아니오"; ?></td>
                    <td colspan="2"><?php echo ($row['video_consent']) ? "예" : "아니오"; ?></td>
                    <td colspan="2"><?php echo $row['speaker_table']; ?></td>
                </tr>
                <tr style="height:5px;"><td colspan="<?php echo $colspan; ?>"></td></tr>
                <?php
            }
            if ($i == 1)
                echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
            ?>
            </tbody>
        </table>
    </div>
    <script>
        function pop_pay_log(num) {
            $("#modal_log_textarea").text($("#pop_log_pan_" + num).text());
            $('#pay_log_modal').modal('toggle');
        }
    </script>
    <style>
        .pagination { width: 100%; margin: auto; text-align: center; }
        .pagination ul { width: 520px; margin: auto; text-align: center; }
        .pagination li { float: left; margin: 10px; }
        .pagination li .active { font-weight: 800; }
    </style>
    <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>
</form>
<form class="form-inline" name="fsearch2" action="https://epiclounge.co.kr/v3/adm/2025_speaker_export_excel.php" method="get">
    <input type="hidden" name="export" value="">
    <input type="hidden" name="start_date" value="<?=$start_date?>">
    <input type="hidden" name="end_date" value="<?=$end_date?>">
    <input type="hidden" name="stx" value="<?=$stx?>">
    <input type="hidden" name="sfl" value="<?=$sfl?>">
</form>
<script type="text/javascript">
    $(document).on('click', '#export_to_excel', function() {
        var f = document.fsearch2;
        f.export.value = "excel";
        f.submit();
        f.export.value = "";
    });
    function del(val) {
        if (confirm('삭제 하시겠습니까?')) {
            location.replace('2025_event_speaker_proc.php?mode2=del&no=' + val);
        }
    }
</script>
<div class="modal fade" id="pay_log_modal" tabindex="-1" aria-labelledby="admin_modfy" aria-hidden="true">
    <div class="modal-dialog" style="width: 650px;">
        <div class="modal-content" style="max-height: 650px; overflow-y: scroll;">
            <div class="modal-body body_pop_bid">
                <textarea style="width: 600px; height: 600px;" id="modal_log_textarea"></textarea>
            </div>
        </div>
    </div>
</div>
<?php
include_once('./admin.tail.php');
?>
