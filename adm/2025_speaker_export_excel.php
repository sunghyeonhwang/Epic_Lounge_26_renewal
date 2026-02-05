<?php
// 엑셀 다운로드용 파일: 예를 들어 2025_speaker_export_excel.php

$sub_menu = "700710";
include_once('./_common.php');

// DB 연결 및 검색 쿼리 (필요한 조건에 맞게 수정)
$sql_common = " FROM cb_unreal_2025_speaker_apply ";
$sql_search = " WHERE 1=1 ";

if ($stx) {
    if ($sfl) {
        if ($sfl == 'apply_user_name') {
            $sfl = 'speaker_name';
        } elseif ($sfl == 'apply_user_email') {
            $sfl = 'speaker_email';
        } elseif ($sfl == 'apply_user_phone') {
            $sfl = 'speaker_ph';
        }
        $sql_search .= " AND ($sfl LIKE '%$stx%') ";
    }
}
if ($start_date) {
    $sql_search .= " AND created_at > '$start_date'";
}
if ($end_date) {
    $sql_search .= " AND created_at < '$end_date 23:59:59'";
}
$sql_order = " ORDER BY id DESC";
$sql = "SELECT * $sql_common $sql_search $sql_order";

// sql_query() 함수를 사용하여 결과 가져오기 (그룹웨어 시스템에 맞게 호출)
sql_query("set names utf8");
$result = sql_query($sql);

$sql1 = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row1 = sql_fetch($sql1);
$total_count = $row1['cnt'];
// 엑셀 다운로드 헤더 설정
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=2025_speaker_export.xls");

?>

<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
</head>
<?
// 간단한 HTML table 출력 (border=1 로 선이 보이게 함)
echo "<table border='1'>";
echo "<tr>";
echo "<th>번호</th>";
echo "<th>이름</th>";
echo "<th>이메일</th>";
echo "<th>연락처</th>";
echo "<th>회사(직책)</th>";
echo "<th>분야</th>";
echo "<th>난이도</th>";
echo "<th>주제</th>";
echo "<th>플랫폼</th>";
echo "<th>약력</th>";
echo "<th>세션 제목</th>";
echo "<th>세션 소개</th>";
echo "<th>요청사항</th>";
echo "<th>제품군</th>";
echo "<th>청강대상</th>";
echo "<th>PDF 공개</th>";
echo "<th>영상 공개</th>";
echo "<th>목차</th>";
echo "<th>데모 여부</th>";
echo "<th>인 에디터 시연</th>";
echo "<th>시연 제품 버전</th>";
echo "<th>배울내용</th>";
echo "<th>참고링크</th>";
echo "<th>등록일</th>";
echo "<th>스피커 이미지</th>";
echo "</tr>";

$i = 0;
while ($row = sql_fetch_array($result)) {
    $i++;
    // 회사와 직책 합치기 (직책이 있으면 괄호로 추가)
    $company = $row['speaker_cp'];
    if ($row['speaker_cp_j']) {
        $company .= " (" . $row['speaker_cp_j'] . ")";
    }


    $number = $total_count + 1 - $i;
    // PDF/영상 공개는 저장된 값 그대로 (예/yes 또는 아니오/no)
    echo "<tr>";
    echo "<td style='height:200px;'>" . $number . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_email']) . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_ph']) . "&nbsp;</td>";
    echo "<td>" . htmlspecialchars($company) . "</td>";
    echo "<td>" . htmlspecialchars($row['industry']) . "</td>";
    echo "<td>" . htmlspecialchars($row['level']) . "</td>";
    echo "<td>" . htmlspecialchars($row['topic']) . "</td>";
    echo "<td>" . htmlspecialchars($row['platform']) . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_hi']) . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_session']) . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_takeaway']) . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_requests']) . "</td>";
    echo "<td>" . htmlspecialchars($row['product']) . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_target']) . "</td>";
    echo "<td>" . htmlspecialchars($row['pdf_consent']) . "</td>";
    echo "<td>" . htmlspecialchars($row['video_consent']) . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_table']) . "</td>";
    echo "<td>" . htmlspecialchars($row['demo']) . "</td>";
    echo "<td>" . htmlspecialchars($row['does_demo']) . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_version']) . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_key']) . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_reference']) . "</td>";

    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";

    // 세션 이미지 출력 (이미지 태그 사용)
    echo "<td>";
    if (!empty($row['speaker_pic'])) {
        echo '<img src="https://epiclounge.co.kr/v3/data/file/speak/' . ($row['speaker_pic']) . '" width="300" height="200" />';
    }
    echo "</td>";
    echo "</tr>";
}
echo "</table>";
?>
