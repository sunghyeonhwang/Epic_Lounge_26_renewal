<?php
/**
 * 무료 콘텐츠 목록 AJAX 엔드포인트
 * POST only + XMLHttpRequest 헤더 검증
 *
 * 요청 파라미터:
 *   cate_industry[]  — 산업분야 (배열)
 *   cate_engine[]    — 엔진버전 (배열)
 *   category[]       — 카테고리 (배열)
 *   keyword          — 검색 키워드
 *   page             — 페이지 번호 (1~)
 *   per_page         — 페이지당 건수 (기본 12)
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
$cate_industry = v4_filter_array($_POST['cate_industry'] ?? []);
$cate_engine   = v4_filter_array($_POST['cate_engine'] ?? []);
$category      = v4_filter_array($_POST['category'] ?? []);
$keyword       = v4_str($_POST['keyword'] ?? '');
$page          = v4_int($_POST['page'] ?? 1);
$per_page      = v4_int($_POST['per_page'] ?? 12);

if ($per_page < 1 || $per_page > 100) $per_page = 12;
if ($page < 1) $page = 1;

// ----- DB 조회 -----
$where = "WHERE display_yn = 'Y'";

// 카테고리 필터 (LIKE 검색)
if (!empty($cate_industry)) {
    $where .= v4_where_like('cate_industry', $cate_industry);
}
if (!empty($cate_engine)) {
    $where .= v4_where_like('cate_engine', $cate_engine);
}
if (!empty($category)) {
    $where .= v4_where_like('category', $category);
}

// 키워드 검색 (제목)
if ($keyword) {
    $where .= v4_where_like('title', [$keyword]);
}

// 전체 건수
$total_count = (int)sql_fetch("SELECT COUNT(*) as cnt FROM v3_rsc_free_bbs {$where}")['cnt'];
$total_pages = ($per_page > 0) ? ceil($total_count / $per_page) : 1;

// 목록 조회
$sql = "SELECT * FROM v3_rsc_free_bbs {$where} ORDER BY ordr DESC, rsc_bbs_idx DESC" . v4_limit($page, $per_page);
$result = sql_query($sql);

// 상세 URL 패턴
$view_url = '/v3/contents/v4/free_view.php?rsc_bbs_idx=';

// ----- HTML 렌더링 -----
$html = '';
while ($row = sql_fetch_array($result)) {
    $html .= render_resource_card($row, 'free', $view_url, 'rsc');
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
