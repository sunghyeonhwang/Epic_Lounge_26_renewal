<?php
/**
 * 새소식 목록 AJAX 엔드포인트
 * POST only + XMLHttpRequest 헤더 검증
 *
 * 요청 파라미터:
 *   keyword   — 검색 키워드 (title, content)
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
$keyword   = v4_str($_POST['keyword'] ?? '');
$category  = v4_str($_POST['category'] ?? '');
$page      = v4_int($_POST['page'] ?? 1);
$per_page  = v4_int($_POST['per_page'] ?? 12);

if ($per_page < 1 || $per_page > 100) $per_page = 12;
if ($page < 1) $page = 1;

// 카테고리 whitelist
$allowed_categories = ['뉴스', '업데이트/출시', '블로그'];
if ($category && !in_array($category, $allowed_categories)) {
    $category = '';
}

// ----- DB 조회 -----
$where = "WHERE display_yn = 'Y'";

// 카테고리 필터
if ($category) {
    $category_esc = sql_real_escape_string($category);
    $where .= " AND category = '{$category_esc}'";
}

// 키워드 검색 (title, content)
if ($keyword) {
    $keyword_esc = sql_real_escape_string($keyword);
    $where .= " AND (title LIKE '%{$keyword_esc}%' OR contents LIKE '%{$keyword_esc}%')";
}

// 전체 건수
$total_count = (int)sql_fetch("SELECT COUNT(*) as cnt FROM v3_rsc_news_bbs {$where}")['cnt'];
$total_pages = ($per_page > 0) ? ceil($total_count / $per_page) : 1;

// 목록 조회 (최신순)
$sql = "SELECT * FROM v3_rsc_news_bbs {$where} ORDER BY rsc_bbs_idx DESC" . v4_limit($page, $per_page);
$result = sql_query($sql);

// 상세 URL
$view_url = '/v3/contents/v4/news_view.php?rsc_bbs_idx=';

// ----- HTML 렌더링 -----
$html = '';
while ($row = sql_fetch_array($result)) {
    $html .= render_resource_card($row, 'news', $view_url, 'news');
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
