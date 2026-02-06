<?php
$g5_path = '../..';
chdir($g5_path);
include_once('./_common.php');
include_once(G5_PATH.'/inc/v4_helpers.php');
include_once(G5_PATH.'/inc/v4_cards.php');

// SEO
$v3_seo = sql_fetch("SELECT * FROM v3_seo_config WHERE seo_page = 'free'");
if (empty($v3_seo['seo_title'])) {
    $v3_seo = sql_fetch("SELECT * FROM v3_seo_config WHERE seo_page = 'default'");
}
$seo_title       = $v3_seo['seo_title']       ?: '에픽 라운지 | 무료 콘텐츠';
$seo_description = $v3_seo['seo_description']  ?: '';
$seo_keywords    = $v3_seo['seo_keywords']     ?: '';
$seo_og_image    = $v3_seo['seo_og_image']     ?: '';

// 마케팅 태그
$seo_ga_id          = trim($v3_seo['seo_ga_id'] ?? '');
$seo_gtm_id         = trim($v3_seo['seo_gtm_id'] ?? '');
$seo_pixel_id       = trim($v3_seo['seo_pixel_id'] ?? '');
$seo_kakao_pixel_id = trim($v3_seo['seo_kakao_pixel_id'] ?? '');
$seo_naver_verif    = trim($v3_seo['seo_naver_verif'] ?? '');
$seo_google_verif   = trim($v3_seo['seo_google_verif'] ?? '');
$seo_extra_head     = $v3_seo['seo_extra_head'] ?? '';
$seo_extra_body     = $v3_seo['seo_extra_body'] ?? '';

// ----- 필터 파라미터 -----
$page     = v4_int($_GET['page'] ?? 1);
$per_page = 12;

// 카테고리 옵션 조회 (DISTINCT)
$categories = [
    'industry' => [],
    'product'  => [],
    'subject'  => [],
    'difficult' => []
];

// 산업분야
$sql = "SELECT DISTINCT cate_industry FROM v3_rsc_free_bbs WHERE cate_industry != '' AND display_yn = 'Y' ORDER BY cate_industry";
$result = sql_query($sql);
while ($row = sql_fetch_array($result)) {
    $categories['industry'][] = $row['cate_industry'];
}

// 제품
$sql = "SELECT DISTINCT cate_product FROM v3_rsc_free_bbs WHERE cate_product != '' AND display_yn = 'Y' ORDER BY cate_product";
$result = sql_query($sql);
while ($row = sql_fetch_array($result)) {
    $categories['product'][] = $row['cate_product'];
}

// 주제
$sql = "SELECT DISTINCT cate_subject FROM v3_rsc_free_bbs WHERE cate_subject != '' AND display_yn = 'Y' ORDER BY cate_subject";
$result = sql_query($sql);
while ($row = sql_fetch_array($result)) {
    $categories['subject'][] = $row['cate_subject'];
}

// 난이도
$sql = "SELECT DISTINCT cate_difficult FROM v3_rsc_free_bbs WHERE cate_difficult != '' AND display_yn = 'Y' ORDER BY cate_difficult";
$result = sql_query($sql);
while ($row = sql_fetch_array($result)) {
    $categories['difficult'][] = $row['cate_difficult'];
}

// ----- DB 조회 -----
$where = "WHERE display_yn = 'Y'";

// 전체 건수
$total_count = (int)sql_fetch("SELECT COUNT(*) as cnt FROM v3_rsc_free_bbs {$where}")['cnt'];

// 목록 조회
$sql = "SELECT * FROM v3_rsc_free_bbs {$where} ORDER BY ordr DESC, rsc_bbs_idx DESC" . v4_limit($page, $per_page);
$result = sql_query($sql);
$items = [];
while ($row = sql_fetch_array($result)) {
    $items[] = $row;
}

// 상세 URL 패턴
$view_url = '/v3/contents/v4/free_view.php?rsc_bbs_idx=';

$total_pages = ($per_page > 0) ? ceil($total_count / $per_page) : 1;
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo get_text($seo_title); ?>">
    <?php if ($seo_description): ?>
    <meta property="og:description" content="<?php echo get_text($seo_description); ?>">
    <meta name="description" content="<?php echo get_text($seo_description); ?>">
    <?php endif; ?>
    <?php if ($seo_keywords): ?>
    <meta name="keywords" content="<?php echo get_text($seo_keywords); ?>">
    <?php endif; ?>
    <?php if ($seo_og_image): ?>
    <meta property="og:image" content="<?php echo get_text($seo_og_image); ?>">
    <?php endif; ?>
    <meta property="og:url" content="https://epiclounge.co.kr/contents/v4/free_list.php">

    <?php include G5_PATH.'/inc/marketing_head.php'; ?>

    <link rel="icon" type="image/png" sizes="32x32" href="/v3/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/v3/favicon/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/v3/favicon/apple-icon-180x180.png">

    <title><?php echo get_text($seo_title); ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="/v3/resource/css/common26.css">
    <link rel="stylesheet" href="/v3/resource/css/pages/list.css">

    <!-- JS (jQuery first) -->
    <script src="/v3/resource/js/jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
</head>
<body>
<?php include G5_PATH.'/inc/marketing_body.php'; ?>
<?php include G5_PATH.'/inc/common_header26.php'; ?>

<!-- container -->
<div class="container" style="margin-top: 80px;">

    <!-- 페이지 타이틀 -->
    <div class="wrap" style="padding-top: 48px; padding-bottom: 16px;">
        <h1 style="font-size: 32px; font-weight: 900; color: var(--v4-text, #333);">무료 콘텐츠</h1>
    </div>

    <div class="wrap">
        <div class="v4-list-layout">
            <!-- 사이드바 필터 -->
            <div class="v4-list-layout__sidebar">
                <div class="v4-sidebar">
                    <h3 class="v4-sidebar__title">필터</h3>
                    <button type="button" class="v4-sidebar__toggle" id="sidebar-toggle">필터</button>
                    <div class="v4-sidebar__body">
                        <form id="filter-form">
                            <?php if (!empty($categories['industry'])): ?>
                            <div class="v4-filter-group">
                                <div class="v4-filter-group__label">산업분야</div>
                                <div class="v4-filter-group__list">
                                    <?php foreach ($categories['industry'] as $idx => $cat): ?>
                                    <label class="v4-filter-checkbox">
                                        <input type="checkbox" name="cate_industry[]" value="<?php echo get_text($cat); ?>">
                                        <span class="v4-filter-checkbox__box"></span>
                                        <span class="v4-filter-checkbox__text"><?php echo get_text($cat); ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($categories['product'])): ?>
                            <div class="v4-filter-group">
                                <div class="v4-filter-group__label">제품</div>
                                <div class="v4-filter-group__list">
                                    <?php foreach ($categories['product'] as $idx => $cat): ?>
                                    <label class="v4-filter-checkbox">
                                        <input type="checkbox" name="cate_product[]" value="<?php echo get_text($cat); ?>">
                                        <span class="v4-filter-checkbox__box"></span>
                                        <span class="v4-filter-checkbox__text"><?php echo get_text($cat); ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($categories['subject'])): ?>
                            <div class="v4-filter-group">
                                <div class="v4-filter-group__label">주제</div>
                                <div class="v4-filter-group__list">
                                    <?php foreach ($categories['subject'] as $idx => $cat): ?>
                                    <label class="v4-filter-checkbox">
                                        <input type="checkbox" name="cate_subject[]" value="<?php echo get_text($cat); ?>">
                                        <span class="v4-filter-checkbox__box"></span>
                                        <span class="v4-filter-checkbox__text"><?php echo get_text($cat); ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($categories['difficult'])): ?>
                            <div class="v4-filter-group">
                                <div class="v4-filter-group__label">난이도</div>
                                <div class="v4-filter-group__list">
                                    <?php foreach ($categories['difficult'] as $idx => $cat): ?>
                                    <label class="v4-filter-checkbox">
                                        <input type="checkbox" name="cate_difficult[]" value="<?php echo get_text($cat); ?>">
                                        <span class="v4-filter-checkbox__box"></span>
                                        <span class="v4-filter-checkbox__text"><?php echo get_text($cat); ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <button type="button" class="v4-filter-reset" id="filter-reset">초기화</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 메인 콘텐츠 영역 -->
            <div class="v4-list-layout__content">
                <!-- 목록 컨트롤 -->
                <div class="v4-list-controls">
                    <div class="v4-list-controls__left">
                        <span class="v4-list-controls__count" id="total-count">
                            전체 <strong><?php echo number_format($total_count); ?></strong>건
                        </span>
                    </div>
                    <div class="v4-view-toggle" id="view-toggle">
                        <button type="button" class="v4-view-toggle__button active" data-view="gallery" aria-label="갤러리 뷰">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <rect x="2" y="2" width="7" height="7" stroke="currentColor" stroke-width="1.5"/>
                                <rect x="11" y="2" width="7" height="7" stroke="currentColor" stroke-width="1.5"/>
                                <rect x="2" y="11" width="7" height="7" stroke="currentColor" stroke-width="1.5"/>
                                <rect x="11" y="11" width="7" height="7" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                        </button>
                        <button type="button" class="v4-view-toggle__button" data-view="list" aria-label="리스트 뷰">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <rect x="2" y="3" width="16" height="3" stroke="currentColor" stroke-width="1.5"/>
                                <rect x="2" y="8.5" width="16" height="3" stroke="currentColor" stroke-width="1.5"/>
                                <rect x="2" y="14" width="16" height="3" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- 카드 그리드 -->
                <div id="card-list" class="v4-card-grid v4-card-grid--gallery">
                    <?php if (empty($items)): ?>
                        <div class="v4-empty">
                            <div class="v4-empty__icon">&#128196;</div>
                            <div class="v4-empty__title">등록된 무료 콘텐츠가 없습니다.</div>
                            <div class="v4-empty__desc">새로운 콘텐츠가 곧 등록될 예정입니다.</div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <?php echo render_resource_card($item, 'free', $view_url, 'free'); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- 더보기 -->
                <?php if ($total_count > $per_page && $page < $total_pages): ?>
                <div class="v4-load-more" id="load-more">
                    <button type="button" class="v4-load-more__button"
                            data-page="<?php echo $page; ?>"
                            data-total-pages="<?php echo $total_pages; ?>">
                        더보기
                        <span class="v4-load-more__count">(<?php echo min($page * $per_page, $total_count); ?>/<?php echo number_format($total_count); ?>)</span>
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div><!-- /.wrap -->
</div><!-- /.container -->

<?php include G5_PATH.'/inc/common_footer.php'; ?>

<!-- v4 통합 JS -->
<script src="/v3/resource/js/v4.app.js"></script>

<script>
/**
 * 무료 콘텐츠 목록 페이지 JS
 */
(function($) {
    'use strict';

    var currentPage     = <?php echo $page; ?>;
    var perPage         = <?php echo $per_page; ?>;
    var isLoading       = false;

    // ----- 사이드바 필터 -----
    $('#filter-form').on('change', 'input[type="checkbox"]', function() {
        currentPage = 1;
        loadContent(false);
    });

    // 필터 초기화
    $('#filter-reset').on('click', function() {
        $('#filter-form input[type="checkbox"]').prop('checked', false);
        currentPage = 1;
        loadContent(false);
    });

    // ----- 뷰 전환 -----
    $('#view-toggle').on('click', '.v4-view-toggle__button', function() {
        var view = $(this).data('view');
        $('#view-toggle .v4-view-toggle__button').removeClass('active');
        $(this).addClass('active');
        $('#card-list')
            .removeClass('v4-card-grid--gallery v4-card-grid--list')
            .addClass('v4-card-grid--' + view);
        try { localStorage.setItem('v4_view_mode', view); } catch(e) {}
    });

    // 저장된 뷰 모드 복원
    try {
        var savedView = localStorage.getItem('v4_view_mode');
        if (savedView && (savedView === 'gallery' || savedView === 'list')) {
            $('#view-toggle .v4-view-toggle__button').removeClass('active');
            $('#view-toggle .v4-view-toggle__button[data-view="' + savedView + '"]').addClass('active');
            $('#card-list')
                .removeClass('v4-card-grid--gallery v4-card-grid--list')
                .addClass('v4-card-grid--' + savedView);
        }
    } catch(e) {}

    // ----- 더보기 -----
    $(document).on('click', '#load-more .v4-load-more__button', function() {
        if (isLoading) return;
        currentPage++;
        loadContent(true);
    });

    // ----- AJAX 로드 -----
    function loadContent(append) {
        if (isLoading) return;
        isLoading = true;

        var $grid = $('#card-list');
        var $loadMore = $('#load-more');
        var $btn = $loadMore.find('.v4-load-more__button');

        if (append) {
            $btn.prop('disabled', true).html('로딩 중...');
        } else {
            $grid.css('opacity', '0.5');
        }

        var formData = $('#filter-form').serialize();

        $.ajax({
            url: '/v3/contents/v4/ajax/free.ajax.php',
            type: 'POST',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            data: formData + '&page=' + currentPage + '&per_page=' + perPage,
            success: function(res) {
                if (!res.success) {
                    alert(res.error || '데이터를 불러오는 중 오류가 발생했습니다.');
                    if (append) currentPage--;
                    return;
                }

                if (append) {
                    $grid.append(res.html);
                } else {
                    if (res.html) {
                        $grid.html(res.html);
                    } else {
                        $grid.html(
                            '<div class="v4-empty">' +
                            '<div class="v4-empty__icon">&#128196;</div>' +
                            '<div class="v4-empty__title">등록된 무료 콘텐츠가 없습니다.</div>' +
                            '<div class="v4-empty__desc">새로운 콘텐츠가 곧 등록될 예정입니다.</div>' +
                            '</div>'
                        );
                    }
                }

                // 건수 업데이트
                $('#total-count').html('전체 <strong>' + Number(res.total_count).toLocaleString() + '</strong>건');

                // 더보기 버튼 상태
                if (res.has_more) {
                    var shown = Math.min(currentPage * perPage, res.total_count);
                    $loadMore.show();
                    $btn.prop('disabled', false)
                        .html('더보기 <span class="v4-load-more__count">(' + shown + '/' + Number(res.total_count).toLocaleString() + ')</span>');
                } else {
                    $loadMore.hide();
                }

                // URL 업데이트 (히스토리)
                var params = new URLSearchParams();
                var newUrl = window.location.pathname;
                history.replaceState(null, '', newUrl);
            },
            error: function() {
                alert('네트워크 오류가 발생했습니다.');
                if (append) currentPage--;
            },
            complete: function() {
                isLoading = false;
                $grid.css('opacity', '1');
                $btn.prop('disabled', false);
            }
        });
    }

})(jQuery);
</script>
</body>
</html>
