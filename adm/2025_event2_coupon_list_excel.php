<?php
$sub_menu = "700730";
include_once('./_common.php');

// 엑셀 다운로드를 위한 헤더 설정 (중복 제거 및 개선)
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename=coupon_list_' . date('Y_m_d') . '.xls');
header('Content-Description: PHP Generated Data');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Expires: 0');
header('Pragma: public');

// 출력 버퍼 정리
ob_clean();

// SQL 쿼리 구성
$sql_common = " from cb_unreal_2025_event2_coupon a ";
$sql_search = " where 1=1 ";

// 검색 조건 처리
if ($stx) {
    $sql_search .= " and ( ";
    if ($sfl) {
        $sql_search .= " ($sfl like '%$stx%') ";
    }
    $sql_search .= " ) ";
}

$sst = "a.coupon_key";
$sod = "desc";
$sql_order = " order by $sst $sod ";

// 전체 카운트 조회
$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

// 페이징 처리 (엑셀 다운로드시에는 전체 데이터 조회)
if (!$rows) {
    $rows = 999999;
}

$total_page = ceil($total_count / $rows);
if ($page < 1) { $page = 1; }
$from_record = ($page - 1) * $rows;

// 데이터 조회
$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);

// BOM 추가 (한글 깨짐 방지)
echo "\xEF\xBB\xBF";
?>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #000;
        padding: 5px;
        text-align: left;
    }
    th {
        background-color: #f0f0f0;
        font-weight: bold;
    }
</style>

<table border="1">
    <thead>
        <tr>
            <th>쿠폰 키</th>
            <th>쿠폰 일련번호</th>
            <th>쿠폰구분</th>
            <th>쿠폰명</th>
            <th>사용자 이름</th>
            <th>사용자 이메일</th>
            <th>전화번호</th>
            <th>할인율</th>
            <th>등록일</th>
            <th>사용일</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = sql_fetch_array($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['coupon_key']); ?></td>
                <td><?php echo htmlspecialchars($row['coupon_serial']); ?></td>
                <td><?php echo htmlspecialchars($row['coupon_type']); ?></td>
                <td><?php echo htmlspecialchars($row['coupon_name']); ?></td>
                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                <td><?php echo htmlspecialchars($row['user_phone']); ?></td>
                <td><?php echo htmlspecialchars($row['discount_rate']); ?></td>
                <td><?php echo htmlspecialchars($row['registration_date']); ?></td>
                <td><?php echo htmlspecialchars($row['usage_date']); ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>