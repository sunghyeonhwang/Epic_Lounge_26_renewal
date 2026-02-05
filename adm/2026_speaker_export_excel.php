<?php
$sub_menu = "700310";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

// DB 연결 및 검색 쿼리
$sql_common = " FROM cb_unreal_2026_speaker_apply ";
$sql_search = " WHERE 1=1 ";

// 검색 조건 적용
if ($stx) {
    $stx = clean_xss_tags($stx);
    if ($sfl) {
        // 검색 필드 매핑
        if ($sfl == 'apply_user_name') {
            $sfl = 'speaker_name';
        } elseif ($sfl == 'apply_user_email') {
            $sfl = 'speaker_email';
        } elseif ($sfl == 'apply_user_phone') {
            $sfl = 'speaker_ph';
        }
        
        // 화이트리스트 검증
        $allowed_fields = ['speaker_name', 'speaker_email', 'speaker_ph'];
        if (!in_array($sfl, $allowed_fields)) {
            $sfl = 'speaker_name';
        }
        
        $sql_search .= " AND ($sfl LIKE '%$stx%') ";
    }
}

// 날짜 필터
if ($start_date) {
    $start_date = clean_xss_tags($start_date);
    $sql_search .= " AND created_at > '$start_date'";
}
if ($end_date) {
    $end_date = clean_xss_tags($end_date);
    $sql_search .= " AND created_at < '$end_date 23:59:59'";
}

$sql_order = " ORDER BY id DESC";
$sql = "SELECT * $sql_common $sql_search $sql_order";

sql_query("set names utf8");
$result = sql_query($sql);

// 전체 개수 조회
$sql1 = "SELECT count(*) as cnt $sql_common $sql_search";
$row1 = sql_fetch($sql1);
$total_count = $row1['cnt'];

// 엑셀 다운로드 헤더 설정
$filename = '2026_speaker_export_' . date('Ymd_His') . '.xls';
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$filename");
header("Cache-Control: max-age=0");
?>

<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
</head>
<body>
<?php
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
    
    // 회사와 직책 합치기
    $company = htmlspecialchars($row['speaker_cp']);
    if (!empty($row['speaker_cp_j'])) {
        $company .= " (" . htmlspecialchars($row['speaker_cp_j']) . ")";
    }
    
    $number = $total_count + 1 - $i;
    
    echo "<tr>";
    echo "<td style='height:200px;'>" . $number . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_email']) . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_ph']) . "&nbsp;</td>";
    echo "<td>" . $company . "</td>";
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
    echo "<td>" . (!empty($row['pdf_consent']) ? "예" : "아니오") . "</td>";
    echo "<td>" . (!empty($row['video_consent']) ? "예" : "아니오") . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_table']) . "</td>";
    echo "<td>" . htmlspecialchars($row['demo']) . "</td>";
    echo "<td>" . htmlspecialchars($row['does_demo']) . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_version']) . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_key']) . "</td>";
    echo "<td>" . htmlspecialchars($row['speaker_reference']) . "</td>";
    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
    
    // 스피커 이미지
    echo "<td>";
    if (!empty($row['speaker_pic'])) {
        echo '<img src="https://epiclounge.co.kr/v3/data/file/speak/' . htmlspecialchars($row['speaker_pic']) . '" width="300" height="200" />';
    } else {
        echo '없음';
    }
    echo "</td>";
    
    echo "</tr>";
}

if ($i == 0) {
    echo "<tr><td colspan='25' style='text-align:center;padding:20px;'>데이터가 없습니다.</td></tr>";
}

echo "</table>";
?>
</body>
</html>
