<?php
/**
 * 이벤트 목록 AJAX 엔드포인트
 * POST only + XMLHttpRequest 헤더 검증
 *
 * 요청 파라미터:
 *   category  — '커뮤니티 이벤트' | '글로벌 이벤트'
 *   status    — '' | '진행중' | '종료' | '결과발표' | '예고'
 *   page      — 페이지 번호 (1~)
 *   per_page  — 페이지당 건수 (기본 12)
 *
 * 응답 JSON:
 *   { success, html, total_count, has_more, page }
 */
$g5_path = '../../..';
chdir($g5_path);
include_once('./_common.php');
include_once(G5_PATH.'/inc/v4_helpers.php');
include_once(G5_PATH.'/inc/v4_cards.php');

// AJAX 보안 검증
v4_ajax_guard();

// ----- 파라미터 -----
$category = v4_str($_POST['category'] ?? '커뮤니티 이벤트');
$status   = v4_str($_POST['status'] ?? '');
$page     = v4_int($_POST['page'] ?? 1);
$per_page = v4_int($_POST['per_page'] ?? 12);

// whitelist 검증
if (!in_array($category, ['커뮤니티 이벤트', '글로벌 이벤트'])) {
    $category = '커뮤니티 이벤트';
}

$allowed_statuses = ['진행중', '종료', '결과발표', '예고'];
if ($status && !in_array($status, $allowed_statuses)) {
    $status = '';
}

if ($per_page < 1 || $per_page > 100) $per_page = 12;
if ($page < 1) $page = 1;

// ----- DB 조회 -----
$table = ($category === '글로벌 이벤트') ? 'v3_rsc_global_event_bbs' : 'v3_rsc_event_bbs';

$where = "WHERE display_yn = 'Y'";
if ($status) {
    $where .= " AND status = '{$status}'";
}

// 전체 건수
$total_count = (int)sql_fetch("SELECT COUNT(*) as cnt FROM {$table} {$where}")['cnt'];
$total_pages = ($per_page > 0) ? ceil($total_count / $per_page) : 1;

// 목록 조회
$sql = "SELECT * FROM {$table} {$where} ORDER BY ordr DESC, rsc_bbs_idx DESC" . v4_limit($page, $per_page);
$result = sql_query($sql);

// 상세 URL 패턴
$view_url = ($category === '글로벌 이벤트')
    ? '/v3/contents/v4/event_view.php?type=global&rsc_bbs_idx='
    : '/v3/contents/v4/event_view.php?rsc_bbs_idx=';

// ----- HTML 렌더링 -----
$html = '';
while ($row = sql_fetch_array($result)) {
    $html .= render_event_card($row, $view_url);
}

// ----- JSON 응답 -----
echo json_encode([
    'success'     => true,
    'html'        => $html,
    'total_count' => $total_count,
    'page'        => $page,
    'total_pages' => $total_pages,
    'has_more'    => ($page < $total_pages),
], JSON_UNESCAPED_UNICODE);
exit;
