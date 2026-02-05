<?php
// /v3/adm/export_excel_ch.php

$sub_menu = "700820";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

// ====== GET 파라미터 ======
$sfl        = isset($_GET['sfl']) ? trim($_GET['sfl']) : '';
$stx        = isset($_GET['stx']) ? trim($_GET['stx']) : '';
$start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$end_date   = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

// ====== 엑셀 헤더 (UTF-8 한글 안깨짐) ======
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=event_list_" . date('Y_m_d') . ".xls");
header("Content-Description: PHP Generated Data");
header("Cache-Control: max-age=0");

// UTF-8 BOM (엑셀이 UTF-8로 인식하도록)
echo "\xEF\xBB\xBF";

// ====== 유틸 ======
function xls_safe($str) {
    // null 방지
    if ($str === null) return '';
    $str = (string)$str;

    // 엑셀 수식 인젝션 방지: = + - @ 로 시작하면 앞에 ' 붙임
    if ($str !== '' && preg_match('/^[=\+\-@]/u', $str)) {
        $str = "'" . $str;
    }

    // HTML 이스케이프
    $str = htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    // 줄바꿈을 <br>로 (엑셀에서 줄바꿈처럼 보임)
    $str = nl2br($str);

    return $str;
}

// ====== 쿼리 구성 ======
$sql_common = " FROM cb_unreal_2025_luman_apply a ";
$sql_where  = " WHERE a.apply_pay_status = 10 ";

// 검색
if ($stx !== '') {
    // 허용 컬럼만 (필요하면 여기에 추가)
    $allowed = array(
        'apply_user_name',
        'apply_user_email',
        'apply_user_phone',
        'apply_user_job',
        'apply_title',
        'apply_sector',
        'apply_sns'
    );

    if (in_array($sfl, $allowed, true)) {
        $stx_esc = sql_real_escape_string($stx);
        $sql_where .= " AND a.`{$sfl}` LIKE '%{$stx_esc}%' ";
    }
}

// 날짜 필터 (입력 포맷이 YYYY-MM-DD 라고 가정)
if ($start_date !== '') {
    $sd = sql_real_escape_string($start_date);
    $sql_where .= " AND a.apply_reg_datetime >= '{$sd} 00:00:00' ";
}
if ($end_date !== '') {
    $ed = sql_real_escape_string($end_date);
    $sql_where .= " AND a.apply_reg_datetime <= '{$ed} 23:59:59' ";
}

$sst = "a.apply_no";
$sod = "DESC";
$sql_order = " ORDER BY {$sst} {$sod} ";

// 실제 데이터 (엑셀은 전체 다운로드가 보통이라 LIMIT 제거)
$sql = " SELECT a.* {$sql_common} {$sql_where} {$sql_order} ";
$result = sql_query($sql);

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style>
    /* 엑셀에서 테이블처럼 보이게 */
    table { border-collapse: collapse; }
    th, td { border:1px solid #d9d9d9; padding:6px; vertical-align: top; }
    th { background:#f2f2f2; }
</style>
</head>
<body>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>이름</th>
            <th>휴대폰</th>
            <th>이메일</th>
            <th>소속</th>
            <th>작품명</th>
            <th>부문</th>
            <th>신분증</th>
            <th>SNS</th>
            <th style="width:200px;">작품소개</th>
            <th style="width:200px;">에셋출처</th>
            <th>T사이즈</th>
            <th>주소</th>
            <th>제출시간</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $i = 0;
    while ($row = sql_fetch_array($result)) {
        $i++;

        $addr = trim(
            ($row['addr_1'] ?? '') . ' ' .
            ($row['addr_2'] ?? '') . ' ' .
            ($row['addr_3'] ?? '')
        );

        // 폰번호는 텍스트로 강제 (앞자리 0 보존)
        $phone = isset($row['apply_user_phone']) ? (string)$row['apply_user_phone'] : '';
    ?>
        <tr>
            <td><?php echo $i; ?></td>
            <td><?php echo xls_safe($row['apply_user_name'] ?? ''); ?></td>

            <!-- ✅ 휴대폰 / 이메일 매핑 바로잡음 -->
            <td style="mso-number-format:'\@';"><?php echo xls_safe($phone); ?></td>
            <td><?php echo xls_safe($row['apply_user_email'] ?? ''); ?></td>

            <td><?php echo xls_safe($row['apply_user_job'] ?? ''); ?></td>
            <td><?php echo xls_safe($row['apply_title'] ?? ''); ?></td>
            <td><?php echo xls_safe($row['apply_sector'] ?? ''); ?></td>

            <td><?php echo xls_safe($row['apply_file'] ?? ''); ?></td>
            <td><?php echo xls_safe($row['apply_sns'] ?? ''); ?></td>

            <td><?php echo xls_safe($row['apply_introduce'] ?? ''); ?></td>
            <td><?php echo xls_safe($row['apply_source'] ?? ''); ?></td>

            <td><?php echo xls_safe($row['t_size'] ?? ''); ?></td>
            <td><?php echo xls_safe($addr); ?></td>
            <td><?php echo xls_safe($row['apply_reg_datetime'] ?? ''); ?></td>
        </tr>
    <?php } ?>

    <?php if ($i === 0) { ?>
        <tr>
            <td colspan="14">자료가 없습니다.</td>
        </tr>
    <?php } ?>
    </tbody>
</table>

</body>
</html>
