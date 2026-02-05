<?php
$sub_menu = "700630";
include_once('./_common.php');

header( "Content-type: application/vnd.ms-excel; charset=utf8" );

header('Content-Disposition: attachment; filename=coupon_list' . date('Y_m_d') . '.xls');

header( "Content-Description: PHP4 Generated Data" );

header('Content-Type: text/html; charset=UTF-8');

$sql_common = " from cb_unreal_2024_event2_coupon a ";
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
    $rows = 999999;
}
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);





$colspan = 15;
?>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        border: 1px solid #000;
        padding: 5px;
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
                    <th>할인율</th>
                    <th>등록일</th>
                    <th>사용일</th>
                </tr>
                </thead>
                <tbody>

                <?php while ($row = sql_fetch_array($result)) { ?>
                    <tr>
                        <td><?php echo $row['coupon_key']; ?></td>
                        <td><?php echo $row['coupon_serial']; ?></td>
                        <td><?php echo $row['coupon_type']; ?></td>
                        <td><?php echo $row['coupon_name']; ?></td>
                        <td><?php echo $row['user_name']; ?></td>
                        <td><?php echo $row['user_email']; ?></td>
                        <td><?php echo $row['discount_rate']; ?></td>
                        <td><?php echo $row['registration_date']; ?></td>
                        <td><?php echo $row['usage_date']; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>