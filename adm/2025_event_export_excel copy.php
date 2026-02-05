<?php
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');
header( "Content-type: application/vnd.ms-excel; charset=utf8" );

header('Content-Disposition: attachment; filename=event_list' . date('Y_m_d') . '.xls');

header( "Content-Description: PHP4 Generated Data" );

header('Content-Type: text/html; charset=UTF-8');
// header('Content-type: application/vnd.ms-excel');
// header('Content-Disposition: attachment; filename=event_list_' . cdate('Y_m_d') . '.xls');


$sql_common = " from cb_unreal_2024_speaker_apply a ";
$sql_search = " where 1=1 ";

if ($stx) {
    $sql_search .= " and ( ";
    if ($sfl) {
        $sql_search .= " ($sfl like '%$stx%') ";
    }
    $sql_search .= " ) ";
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
$colspan = 15;
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
            <th scope="col">회사</th>
            <th scope="col">연혁</th>
            <th scope="col">세션시간</th>
            <th scope="col">난이도</th>
            <th scope="col">주제</th>
            <th scope="col">플랫폼</th>
            <th scope="col" >세션플랫폼</th>
            <th scope="col" >청강대상</th>
            <th scope="col" >발표자료PDF</th>
            <th scope="col" >발표영상</th>
            <th scope="col" >목차</th>
            <th scope="col">가입일</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for ($i=1; $row=sql_fetch_array($result); $i++) {
            $bg = 'bg'.($i%2);
            ?>

            <tr class="<?php echo $bg; ?>">
                <td class="td_chk">
                    <?php echo $i; ?>
                </td>
                <td><?=$row['apply_user_name']?></td>
                <td><?=$row['apply_user_email']?></td>
                <td><?=$row['apply_user_phone']?></td>
                <td><?=$row['apply_user_company']?><?=$row["etc1"] ? "(".$row["etc1"].")":""?></td>
                <td><?=$row['apply_user_history']?></td>
                <td><?=$row['apply_pt_runtime']?></td>
                <td><?=$row['apply_pt_difficulty']?></td>
                <td><?=$row['apply_pt_subject']?><?=$row["etc3"] ? "(".$row["etc3"].")":""?></td>
                <td><?=$row['apply_pt_platform']?><?=$row["etc2"] ? "(".$row["etc2"].")":""?></td>
                <td ><?=$row['apply_add_field1']?><?=$row["etc4"] ? "(".$row["etc4"].")":""?></td>
                <td ><?=$row['apply_add_field2']?></td>
                <td ><?=$row['apply_add_field3']?></td>
                <td ><?=$row['apply_add_field4']?></td>
                <td ><?=$row['apply_add_field5']?></td>
                <td><?=$row['apply_reg_datetime']?></td>
            </tr>
            <?php
        }
        if ($i == 0)
            echo '<tr><td colspan="11" class="empty_table">자료가 없습니다.</td></tr>';
        ?>
        </tbody>
    </table>
</div>