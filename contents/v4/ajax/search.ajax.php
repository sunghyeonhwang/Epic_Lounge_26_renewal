<?php
/**
 * 통합 검색 AJAX 엔드포인트
 * POST + XMLHttpRequest 헤더 검증
 *
 * 요청 파라미터:
 *   keyword  — 검색 키워드 (필수)
 *   section  — 'all' | 'news' | 'event' | 'replay' | 'free' | 'book'
 *   page     — 페이지 번호
 *   per_page — 페이지당 건수 (기본 6)
 *
 * 응답 JSON:
 *   { success, sections: { news: {items, total}, event: {...}, ... }, keyword }
 */
$g5_path = '../../..';
chdir($g5_path);
include_once('./_common.php');
include_once(G5_PATH.'/inc/v4_helpers.php');
include_once(G5_PATH.'/inc/v4_cards.php');

// AJAX 보안 검증
v4_ajax_guard();

// ----- 파라미터 -----
$keyword  = v4_str($_POST['keyword'] ?? '');
$section  = v4_str($_POST['section'] ?? 'all');
$page     = v4_int($_POST['page'] ?? 1);
$per_page = v4_int($_POST['per_page'] ?? 6);

if (empty($keyword)) {
    echo json_encode(['success' => false, 'error' => '검색어를 입력하세요.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($per_page < 1 || $per_page > 50) $per_page = 6;
if ($page < 1) $page = 1;

$allowed_sections = ['all', 'news', 'event', 'replay', 'free', 'book'];
if (!in_array($section, $allowed_sections)) $section = 'all';

// ----- 검색 설정 -----
$search_configs = [
    'news' => [
        'table'     => 'v3_rsc_news_bbs',
        'fields'    => "title, contents",
        'view_url'  => '/v3/contents/v4/news_view.php?rsc_bbs_idx=',
        'type'      => 'news',
        'subdir'    => 'news',
        'order'     => 'rsc_bbs_idx DESC',
        'label'     => '새소식',
    ],
    'event' => [
        'table'     => 'v3_rsc_event_bbs',
        'fields'    => "title, contents",
        'view_url'  => '/v3/contents/v4/event_view.php?rsc_bbs_idx=',
        'type'      => 'event',
        'subdir'    => 'event',
        'order'     => 'ordr DESC, rsc_bbs_idx DESC',
        'label'     => '이벤트',
    ],
    'replay' => [
        'table'     => 'v3_rsc_review_bbs',
        'fields'    => "title, contents",
        'view_url'  => '/v3/contents/v4/replay_view.php?rsc_bbs_idx=',
        'type'      => 'replay',
        'subdir'    => 'rsc',
        'order'     => 'ordr DESC, rsc_bbs_idx DESC',
        'label'     => '다시보기',
    ],
    'free' => [
        'table'     => 'v3_rsc_free_bbs',
        'fields'    => "title, contents",
        'view_url'  => '/v3/contents/v4/free_view.php?rsc_bbs_idx=',
        'type'      => 'free',
        'subdir'    => 'rsc',
        'order'     => 'ordr DESC, rsc_bbs_idx DESC',
        'label'     => '무료 콘텐츠',
    ],
    'book' => [
        'table'     => 'v3_rsc_book_bbs',
        'fields'    => "title, contents",
        'view_url'  => '/v3/contents/v4/book_view.php?rsc_bbs_idx=',
        'type'      => 'book',
        'subdir'    => 'rsc',
        'order'     => 'ordr DESC, rsc_bbs_idx DESC',
        'label'     => '백서/eBook',
    ],
];

// ----- 검색 실행 -----
$sections_to_search = ($section === 'all') ? array_keys($search_configs) : [$section];
$results = [];
$total_all = 0;

foreach ($sections_to_search as $sec) {
    $cfg = $search_configs[$sec];

    // WHERE 조건
    $where = "WHERE display_yn = 'Y'";
    $search_fields = explode(', ', $cfg['fields']);
    $like_conditions = [];
    foreach ($search_fields as $f) {
        $f = trim($f);
        $like_conditions[] = "{$f} LIKE '%{$keyword}%'";
    }
    $where .= " AND (" . implode(' OR ', $like_conditions) . ")";

    // 건수
    $cnt = (int)sql_fetch("SELECT COUNT(*) as cnt FROM {$cfg['table']} {$where}")['cnt'];
    $total_all += $cnt;

    // 데이터
    $limit_str = v4_limit($page, $per_page);
    $sql = "SELECT * FROM {$cfg['table']} {$where} ORDER BY {$cfg['order']}" . $limit_str;
    $result = sql_query($sql);

    $html = '';
    while ($row = sql_fetch_array($result)) {
        if ($sec === 'event') {
            $html .= render_search_result_card($row, $cfg, $keyword);
        } else {
            $html .= render_search_result_card($row, $cfg, $keyword);
        }
    }

    $results[$sec] = [
        'label'      => $cfg['label'],
        'total'      => $cnt,
        'html'       => $html,
        'has_more'   => ($page * $per_page < $cnt),
        'page'       => $page,
    ];
}

// ----- 응답 -----
echo json_encode([
    'success'     => true,
    'keyword'     => $keyword,
    'total_all'   => $total_all,
    'sections'    => $results,
], JSON_UNESCAPED_UNICODE);
exit;

/**
 * 검색 결과 카드 HTML 렌더링
 */
function render_search_result_card($item, $cfg, $keyword) {
    $idx = v4_int($item['rsc_bbs_idx']);
    $title = get_text($item['title']);
    $highlighted_title = v4_highlight($title, $keyword);
    $thumb_url = v4_thumb_url($item, $cfg['subdir']);
    $detail_url = $cfg['view_url'] . $idx;
    $reg_date = $item['reg_date'] ?? '';

    // 본문 요약
    $content_field = isset($item['contents']) ? $item['contents'] : ($item['content'] ?? '');
    $desc = mb_substr(strip_tags($content_field), 0, 120, 'UTF-8');
    if (mb_strlen(strip_tags($content_field), 'UTF-8') > 120) {
        $desc .= '...';
    }
    $highlighted_desc = v4_highlight(get_text($desc), $keyword);

    // 날짜
    $date_str = '';
    if ($cfg['type'] === 'news' && $reg_date) {
        $date_str = v4_relative_time($reg_date);
    } elseif ($cfg['type'] === 'event') {
        $sdate = $item['sdate'] ?? '';
        $edate = $item['edate'] ?? '';
        if ($sdate) {
            $date_str = date('Y.m.d', strtotime($sdate));
            if ($edate) $date_str .= ' ~ ' . date('Y.m.d', strtotime($edate));
        }
    } elseif ($reg_date) {
        $date_str = date('Y.m.d', strtotime($reg_date));
    }

    // 외부 링크
    $target = '';
    if (!empty($item['site_url'])) {
        $detail_url = get_text($item['site_url']);
        $target = ' target="_blank" rel="noopener noreferrer"';
    }

    // 카테고리
    $category_label = '';
    if (!empty($item['category'])) {
        $category_label = get_text($item['category']);
    } elseif (!empty($item['cate_industry'])) {
        $category_label = get_text($item['cate_industry']);
    }

    // 상태 배지 (이벤트)
    $badge_html = '';
    if ($cfg['type'] === 'event' && !empty($item['status'])) {
        $status = get_text($item['status']);
        $bc = '';
        if ($status === '진행중')    $bc = 'v4-badge--active';
        elseif ($status === '종료')  $bc = 'v4-badge--ended';
        elseif ($status === '결과발표') $bc = 'v4-badge--result';
        $badge_html = '<span class="v4-search-result__badge ' . $bc . '">' . $status . '</span>';
    }

    ob_start();
    ?>
    <a href="<?php echo $detail_url; ?>" class="v4-search-result"<?php echo $target; ?>>
        <div class="v4-search-result__thumbnail">
            <img src="<?php echo $thumb_url; ?>" alt="<?php echo $title; ?>" loading="lazy">
        </div>
        <div class="v4-search-result__content">
            <div class="v4-search-result__header">
                <?php if ($category_label): ?>
                <span class="v4-search-result__category"><?php echo $category_label; ?></span>
                <?php endif; ?>
                <?php echo $badge_html; ?>
            </div>
            <h3 class="v4-search-result__title"><?php echo $highlighted_title; ?></h3>
            <?php if ($highlighted_desc): ?>
            <p class="v4-search-result__description"><?php echo $highlighted_desc; ?></p>
            <?php endif; ?>
            <?php if ($date_str): ?>
            <div class="v4-search-result__meta">
                <span class="v4-search-result__meta-item"><?php echo $date_str; ?></span>
            </div>
            <?php endif; ?>
        </div>
    </a>
    <?php
    return ob_get_clean();
}
