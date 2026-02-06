<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * v4 안전 쿼리 헬퍼 — GNU Board sql_* 함수 기반
 *
 * 기존 함수 그대로 사용:
 * - get_text()              → 출력 이스케이프 (XSS 방지)
 * - clean_xss_tags()        → HTML 태그 정제
 * - escape_trim()           → 입력 정제
 * - sql_real_escape_string() → SQL 이스케이프
 * - get_token() / check_token() → CSRF
 * - conv_content()          → 본문 HTML 처리
 * - conv_subject()          → 제목 자르기
 * - get_paging()            → 페이지네이션 HTML
 */

/**
 * 정수 파라미터 강제 캐스팅
 *
 * @param mixed $val
 * @return int
 */
function v4_int($val) {
    return (int)$val;
}

/**
 * 문자열 파라미터 안전 처리 (이스케이프 + trim + 태그 제거)
 * GNU Board가 이미 $_GET/$_POST/$_REQUEST에 addslashes()를 적용하므로,
 * stripslashes 후 재이스케이프하여 이중 이스케이프를 방지한다.
 *
 * @param mixed $val
 * @return string
 */
function v4_str($val) {
    $val = strip_tags(trim($val ?? ''));
    $val = stripslashes($val);
    return sql_real_escape_string($val);
}

/**
 * 필터 배열 안전 처리 (whitelist 검증)
 *
 * @param mixed $arr 입력 배열
 * @param array $allowed_values 허용 값 목록 (빈 배열이면 검증 생략)
 * @return array
 */
function v4_filter_array($arr, $allowed_values = []) {
    if (!is_array($arr)) return [];
    $safe = [];
    foreach ($arr as $val) {
        $val = v4_str($val);
        if ($val === '') continue;
        if (empty($allowed_values) || in_array($val, $allowed_values)) {
            $safe[] = $val;
        }
    }
    return $safe;
}

/**
 * WHERE 조건 빌더 (LIKE 검색)
 * 여러 값에 대해 OR 조건으로 LIKE 검색
 *
 * @param string $field DB 필드명
 * @param array $values 검색할 값 배열 (이미 v4_str() 처리된)
 * @return string SQL WHERE 절 (AND로 시작, 빈 배열이면 빈 문자열)
 */
function v4_where_like($field, $values) {
    if (empty($values)) return '';
    $conditions = [];
    foreach ($values as $val) {
        $conditions[] = "{$field} LIKE '%{$val}%'";
    }
    return ' AND (' . implode(' OR ', $conditions) . ')';
}

/**
 * WHERE 조건 빌더 (정확히 일치)
 *
 * @param string $field DB 필드명
 * @param array $values 검색할 값 배열
 * @return string SQL WHERE 절
 */
function v4_where_in($field, $values) {
    if (empty($values)) return '';
    $escaped = array_map(function($v) {
        return "'" . v4_str($v) . "'";
    }, $values);
    return " AND {$field} IN (" . implode(',', $escaped) . ")";
}

/**
 * 페이지네이션 LIMIT 계산
 *
 * @param mixed $page 현재 페이지 번호
 * @param int $per_page 페이지당 건수
 * @return string SQL LIMIT 절
 */
function v4_limit($page, $per_page = 12) {
    $page = max(1, v4_int($page));
    $per_page = max(1, v4_int($per_page));
    $start = ($page - 1) * $per_page;
    return " LIMIT {$start}, {$per_page}";
}

/**
 * 상대 시간 표시 (방금전, X분전, X시간전, X일전...)
 *
 * @param string $datetime MySQL DATETIME 형식 (예: '2026-02-06 14:30:00')
 * @return string 상대 시간 문자열
 */
function v4_relative_time($datetime) {
    if (empty($datetime)) return '';

    $now = time();
    $time = strtotime($datetime);
    if ($time === false) return $datetime;

    $diff = $now - $time;

    if ($diff < 0) return date('Y.m.d', $time);
    if ($diff < 60) return '방금전';
    if ($diff < 3600) return floor($diff / 60) . '분전';
    if ($diff < 86400) return floor($diff / 3600) . '시간전';
    if ($diff < 604800) return floor($diff / 86400) . '일전';

    // 7일 이상: 날짜 표시
    $year = date('Y', $time);
    $current_year = date('Y', $now);

    if ($year === $current_year) {
        return date('m.d', $time);
    }
    return date('Y.m.d', $time);
}

/**
 * 검색 키워드 하이라이팅
 * 텍스트 내 키워드를 <mark> 태그로 감싸기
 *
 * @param string $text 원본 텍스트 (이미 XSS 처리된)
 * @param string $keyword 하이라이트할 키워드
 * @return string 하이라이트된 텍스트
 */
function v4_highlight($text, $keyword) {
    if (empty($keyword) || empty($text)) return $text;
    $keyword = preg_quote($keyword, '/');
    return preg_replace(
        '/(' . $keyword . ')/iu',
        '<mark class="v4-highlight">$1</mark>',
        $text
    );
}

/**
 * 썸네일 URL 가져오기
 * thumb_img_url 우선, 없으면 G5_DATA_URL 기반 thumb_img, 없으면 기본 이미지
 *
 * @param array $row DB 행 배열
 * @param string $data_subdir 데이터 하위 디렉토리 (예: 'event', 'review', 'news', 'free', 'book')
 * @param string $default 기본 이미지 URL
 * @return string 이미지 URL
 */
function v4_thumb_url($row, $data_subdir, $default = '/v3/resource/images/sub/event_list_img.jpg') {
    if (!empty($row['thumb_img_url'])) {
        return $row['thumb_img_url'];
    }
    if (!empty($row['thumb_img']) && file_exists(G5_DATA_PATH . '/' . $data_subdir . '/' . $row['thumb_img'])) {
        return G5_DATA_URL . '/' . $data_subdir . '/' . $row['thumb_img'];
    }
    return $default;
}

/**
 * AJAX 엔드포인트 공통 보안 검증
 * 요청 메서드(POST) + X-Requested-With 헤더 확인
 *
 * @return void 검증 실패 시 HTTP 에러 응답 후 exit
 */
function v4_ajax_guard() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
        $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    header('Content-Type: application/json; charset=utf-8');
}

/**
 * YouTube URL에서 임베드 ID 추출
 *
 * @param string $url YouTube URL (youtu.be, watch?v=, embed/)
 * @return string 비디오 ID (유효하지 않으면 빈 문자열)
 */
function v4_youtube_embed_id($url) {
    if (empty($url)) return '';
    if (stripos($url, 'alert') !== false) return ''; // XSS 방지

    $patterns = [
        '/youtu\.be\/([a-zA-Z0-9_-]+)/',
        '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
        '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
    ];
    foreach ($patterns as $p) {
        if (preg_match($p, $url, $m)) return $m[1];
    }
    return '';
}
