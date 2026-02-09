<?php
$g5_path = '../..';
chdir($g5_path);
include_once('./_common.php');
include_once(G5_PATH.'/inc/v4_helpers.php');
include_once(G5_PATH.'/inc/v4_cards.php');

// SEO
$v3_seo = sql_fetch("SELECT * FROM v3_seo_config WHERE seo_page = 'replay'");
if (empty($v3_seo['seo_title'])) {
    $v3_seo = sql_fetch("SELECT * FROM v3_seo_config WHERE seo_page = 'default'");
}
$seo_title       = $v3_seo['seo_title']       ?: '에픽 라운지 | 다시보기';
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
$keyword  = v4_str($_GET['keyword'] ?? '');
$page     = v4_int($_GET['page'] ?? 1);
$per_page = 12;

// ----- 리소스 탭 카운트 -----
$replay_cnt = sql_fetch("SELECT COUNT(*) as cnt FROM v3_rsc_review_bbs WHERE display_yn='Y'")['cnt'];
$free_cnt = sql_fetch("SELECT COUNT(*) as cnt FROM v3_rsc_free_bbs WHERE display_yn='Y'")['cnt'];
$book_cnt = sql_fetch("SELECT COUNT(*) as cnt FROM v3_rsc_book_bbs WHERE display_yn='Y'")['cnt'];

// ----- 배너 슬라이더 -----
$banner_result = sql_query("SELECT * FROM v3_shop_banner WHERE bn_position = '다시보기' ORDER BY bn_id DESC");
$banners = [];
while ($bn = sql_fetch_array($banner_result)) {
    $banners[] = $bn;
}

// ----- 카테고리 조회 (DISTINCT) -----
$categories = [
    'industry' => [],
    'product' => [],
    'subject' => [],
    'difficult' => []
];

// 산업분야
$result = sql_query("SELECT * FROM v3_rsc_review_category WHERE rsc_type='산업분야' ORDER BY sort ASC");
while ($row = sql_fetch_array($result)) {
    $categories['industry'][] = get_text($row['rsc_name']);
}

// 제품군
$result = sql_query("SELECT * FROM v3_rsc_review_category WHERE rsc_type='제품군' ORDER BY sort ASC");
while ($row = sql_fetch_array($result)) {
    $categories['product'][] = get_text($row['rsc_name']);
}

// 주제
$result = sql_query("SELECT * FROM v3_rsc_review_category WHERE rsc_type='주제' ORDER BY sort ASC");
while ($row = sql_fetch_array($result)) {
    $categories['subject'][] = get_text($row['rsc_name']);
}

// 난이도
$result = sql_query("SELECT * FROM v3_rsc_review_category WHERE rsc_type='난이도' ORDER BY sort ASC");
while ($row = sql_fetch_array($result)) {
    $categories['difficult'][] = get_text($row['rsc_name']);
}

// ----- DB 조회 -----
$where = "WHERE display_yn = 'Y'";

// 키워드 검색
if ($keyword) {
    $keyword_esc = sql_real_escape_string($keyword);
    $where .= " AND title LIKE '%{$keyword_esc}%'";
}

// 전체 건수
$total_count = (int)sql_fetch("SELECT COUNT(*) as cnt FROM v3_rsc_review_bbs {$where}")['cnt'];

// 목록 조회
$sql = "SELECT * FROM v3_rsc_review_bbs {$where} ORDER BY ordr DESC, rsc_bbs_idx DESC" . v4_limit($page, $per_page);
$result = sql_query($sql);
$items = [];
while ($row = sql_fetch_array($result)) {
    $items[] = $row;
}

// 상세 URL
$view_url = '/v3/contents/v4/replay_view.php?rsc_bbs_idx=';

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
    <meta property="og:url" content="https://epiclounge.co.kr/contents/v4/replay_list.php">

    <?php include G5_PATH.'/inc/marketing_head.php'; ?>

    <link rel="icon" type="image/png" sizes="32x32" href="/v3/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/v3/favicon/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/v3/favicon/apple-icon-180x180.png">

    <title><?php echo get_text($seo_title); ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="/v3/resource/css/main26.css">
    <link rel="stylesheet" href="/v3/resource/css/sub.css">
    <link rel="stylesheet" href="/v3/resource/css/pages/list.css?v=20260209b">

    <!-- JS (jQuery first) -->
    <script src="/v3/resource/js/jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
</head>
<body>
<?php include G5_PATH.'/inc/marketing_body.php'; ?>
<?php include G5_PATH.'/inc/common_header26.php'; ?>

<!-- sub_visual -->
<div id="sub_visual" class="resource_vi">
    <h2>리소스</h2>
    <p>언리얼 페스트, 에픽 라이브, 웨비나 등 놓친 영상을 다시보기로 확인하세요.</p>
</div>

<!-- container -->
<div class="container">

    <div class="wrap" style="padding-top: 40px;">
        <!-- 상단 검색 -->
        <div class="v4-search-bar" style="margin-bottom: 24px; max-width: 400px;">
            <input type="text"
                   class="v4-search-bar__input"
                   id="top-keyword-input"
                   placeholder="검색어를 입력하세요"
                   value="<?php echo get_text($keyword); ?>">
            <button type="button" class="v4-search-bar__button" id="top-search-btn">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="2"/>
                    <path d="M12.5 12.5L17 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <!-- 리소스 카테고리 탭 -->
        <div class="v4-resource-tabs">
            <a href="/v3/contents/v4/replay_list.php" class="v4-resource-tabs__item active">
                <span class="v4-resource-tabs__text">영상</span>
                <span class="v4-resource-tabs__count"><?php echo number_format($replay_cnt); ?></span>
            </a>
            <a href="/v3/contents/v4/free_list.php" class="v4-resource-tabs__item">
                <span class="v4-resource-tabs__text">무료 콘텐츠</span>
                <span class="v4-resource-tabs__count"><?php echo number_format($free_cnt); ?></span>
            </a>
            <a href="/v3/contents/v4/book_list.php" class="v4-resource-tabs__item">
                <span class="v4-resource-tabs__text">백서</span>
                <span class="v4-resource-tabs__count"><?php echo number_format($book_cnt); ?></span>
            </a>
        </div>

        <!-- 배너 슬라이더 -->
        <?php if (!empty($banners)): ?>
        <div class="v4-banner-slider">
            <div class="swiper" id="banner-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($banners as $bn): ?>
                    <?php $bn_img = G5_DATA_URL . '/banner/' . $bn['bn_id']; ?>
                    <div class="swiper-slide">
                        <a href="<?php echo get_text($bn['bn_url']); ?>" target="_blank" rel="noopener noreferrer">
                            <img class="v4-banner-slider__item" src="<?php echo $bn_img; ?>" alt="배너">
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($banners) > 1): ?>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-pagination"></div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- 리스트 레이아웃 (사이드바 + 콘텐츠) -->
    <div class="wrap">
        <div class="v4-list-layout">

            <!-- 사이드바 -->
            <div class="v4-list-layout__sidebar">
                <div class="v4-sidebar">
                    <h3 class="v4-sidebar__title">필터</h3>
                    <button type="button" class="v4-sidebar__toggle" id="sidebar-toggle">필터</button>
                    <div class="v4-sidebar__body">

                        <!-- 선택된 필터 요약 -->
                        <div class="v4-filter-active" id="active-filters" style="display:none;">
                            <div class="v4-filter-active__tags" id="active-tags"></div>
                            <button type="button" class="v4-filter-active__clear" id="filter-reset-top">전체 초기화</button>
                        </div>

                        <!-- 산업분야 (기본 열림) -->
                        <div class="v4-filter-group" data-filter="industry">
                            <button type="button" class="v4-filter-group__header open">
                                <span class="v4-filter-group__label">산업분야</span>
                                <span class="v4-filter-group__badge" style="display:none;">0</span>
                                <svg class="v4-filter-group__arrow" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M5 8l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <div class="v4-filter-group__body">
                                <div class="v4-filter-chips">
                                    <?php foreach ($categories['industry'] as $idx => $cat): ?>
                                    <label class="v4-filter-chip">
                                        <input type="checkbox"
                                               name="cate_industry[]"
                                               value="<?php echo get_text($cat); ?>">
                                        <span class="v4-filter-chip__text"><?php echo $cat; ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- 제품군 -->
                        <div class="v4-filter-group" data-filter="product">
                            <button type="button" class="v4-filter-group__header">
                                <span class="v4-filter-group__label">제품군</span>
                                <span class="v4-filter-group__badge" style="display:none;">0</span>
                                <svg class="v4-filter-group__arrow" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M5 8l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <div class="v4-filter-group__body" style="display:none;">
                                <div class="v4-filter-chips">
                                    <?php foreach ($categories['product'] as $idx => $cat): ?>
                                    <label class="v4-filter-chip">
                                        <input type="checkbox"
                                               name="cate_product[]"
                                               value="<?php echo get_text($cat); ?>">
                                        <span class="v4-filter-chip__text"><?php echo $cat; ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- 주제 -->
                        <div class="v4-filter-group" data-filter="subject">
                            <button type="button" class="v4-filter-group__header">
                                <span class="v4-filter-group__label">주제</span>
                                <span class="v4-filter-group__badge" style="display:none;">0</span>
                                <svg class="v4-filter-group__arrow" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M5 8l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <div class="v4-filter-group__body" style="display:none;">
                                <div class="v4-filter-chips">
                                    <?php foreach ($categories['subject'] as $idx => $cat): ?>
                                    <label class="v4-filter-chip">
                                        <input type="checkbox"
                                               name="cate_subject[]"
                                               value="<?php echo get_text($cat); ?>">
                                        <span class="v4-filter-chip__text"><?php echo $cat; ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- 난이도 -->
                        <div class="v4-filter-group" data-filter="difficult">
                            <button type="button" class="v4-filter-group__header">
                                <span class="v4-filter-group__label">난이도</span>
                                <span class="v4-filter-group__badge" style="display:none;">0</span>
                                <svg class="v4-filter-group__arrow" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M5 8l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <div class="v4-filter-group__body" style="display:none;">
                                <div class="v4-filter-chips">
                                    <?php foreach ($categories['difficult'] as $idx => $cat): ?>
                                    <label class="v4-filter-chip">
                                        <input type="checkbox"
                                               name="cate_difficult[]"
                                               value="<?php echo get_text($cat); ?>">
                                        <span class="v4-filter-chip__text"><?php echo $cat; ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- 필터 리셋 -->
                        <button type="button" class="v4-filter-reset" id="filter-reset">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M2 8a6 6 0 0112 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M14 8a6 6 0 01-12 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            필터 초기화
                        </button>

                    </div>
                </div>
            </div>

            <!-- 메인 콘텐츠 -->
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
                            <div class="v4-empty__icon">&#128250;</div>
                            <div class="v4-empty__title">등록된 다시보기가 없습니다.</div>
                            <div class="v4-empty__desc">새로운 콘텐츠가 곧 등록될 예정입니다.</div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <?php echo render_resource_card($item, 'replay', $view_url, 'rsc'); ?>
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

        </div><!-- /.v4-list-layout -->
    </div><!-- /.wrap -->

</div><!-- /.container -->

<?php include G5_PATH.'/inc/common_footer.php'; ?>

<!-- v4 통합 JS -->
<script src="/v3/resource/js/v4.app.js"></script>

<script>
/**
 * 다시보기 목록 페이지 JS
 */
(function($) {
    'use strict';

    var currentFilters = {
        cate_industry: [],
        cate_product: [],
        cate_subject: [],
        cate_difficult: []
    };
    var currentKeyword = '<?php echo addslashes(get_text($keyword)); ?>';
    var currentPage    = <?php echo $page; ?>;
    var perPage        = <?php echo $per_page; ?>;
    var isLoading      = false;

    // ----- 검색 -----
    $('#top-search-btn').on('click', function() {
        currentKeyword = $('#top-keyword-input').val().trim();
        currentPage = 1;
        loadReplays(false);
    });
    $('#top-keyword-input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            currentKeyword = $(this).val().trim();
            currentPage = 1;
            loadReplays(false);
        }
    });

    // ----- 아코디언 토글 -----
    $('.v4-sidebar').on('click', '.v4-filter-group__header', function() {
        var $header = $(this);
        var $body = $header.next('.v4-filter-group__body');
        $header.toggleClass('open');
        $body.slideToggle(200);
    });

    // ----- 칩/체크박스 변경 -----
    $('.v4-sidebar').on('change', 'input[type=checkbox]', function() {
        var name = $(this).attr('name');
        var filterKey = name.replace('[]', '');

        currentFilters[filterKey] = [];
        $('input[name="' + name + '"]:checked').each(function() {
            currentFilters[filterKey].push($(this).val());
        });

        updateActiveTags();
        updateBadges();
        currentPage = 1;
        loadReplays(false);
    });

    // ----- 활성 태그 업데이트 -----
    function updateActiveTags() {
        var $container = $('#active-tags');
        var $wrap = $('#active-filters');
        $container.empty();

        var hasAny = false;
        $.each(currentFilters, function(key, vals) {
            $.each(vals, function(i, val) {
                hasAny = true;
                var $tag = $('<button type="button" class="v4-filter-active__tag">' +
                    '<span>' + val + '</span>' +
                    '<span class="v4-filter-active__tag-x">&times;</span>' +
                    '</button>');
                $tag.data('filter-key', key);
                $tag.data('filter-val', val);
                $container.append($tag);
            });
        });

        if (hasAny) {
            $wrap.show();
        } else {
            $wrap.hide();
        }
    }

    // ----- 활성 태그 X 클릭 -----
    $('#active-tags').on('click', '.v4-filter-active__tag', function() {
        var key = $(this).data('filter-key');
        var val = $(this).data('filter-val');
        var name = key + '[]';

        // 해당 체크박스 해제
        $('input[name="' + name + '"]').each(function() {
            if ($(this).val() === val) {
                $(this).prop('checked', false);
            }
        });

        // 필터 배열 업데이트
        currentFilters[key] = $.grep(currentFilters[key], function(v) {
            return v !== val;
        });

        updateActiveTags();
        updateBadges();
        currentPage = 1;
        loadReplays(false);
    });

    // ----- 배지 카운트 업데이트 -----
    function updateBadges() {
        $('.v4-filter-group').each(function() {
            var filterName = $(this).data('filter');
            var key = 'cate_' + filterName;
            var count = currentFilters[key] ? currentFilters[key].length : 0;
            var $badge = $(this).find('.v4-filter-group__badge');

            if (count > 0) {
                $badge.text(count).show();
            } else {
                $badge.hide();
            }
        });
    }

    // ----- 필터 초기화 -----
    function resetAllFilters() {
        $('.v4-sidebar input[type=checkbox]').prop('checked', false);
        $('#top-keyword-input').val('');
        currentFilters = {
            cate_industry: [],
            cate_product: [],
            cate_subject: [],
            cate_difficult: []
        };
        currentKeyword = '';
        updateActiveTags();
        updateBadges();
        currentPage = 1;
        loadReplays(false);
    }

    $('#filter-reset, #filter-reset-top').on('click', resetAllFilters);

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
        loadReplays(true);
    });

    // ----- AJAX 로드 -----
    function loadReplays(append) {
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

        var data = {
            cate_industry: currentFilters.cate_industry,
            cate_product: currentFilters.cate_product,
            cate_subject: currentFilters.cate_subject,
            cate_difficult: currentFilters.cate_difficult,
            keyword: currentKeyword,
            page: currentPage,
            per_page: perPage
        };

        $.ajax({
            url: '/v3/contents/v4/ajax/replay.ajax.php',
            type: 'POST',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            data: data,
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
                            '<div class="v4-empty__icon">&#128250;</div>' +
                            '<div class="v4-empty__title">검색 결과가 없습니다.</div>' +
                            '<div class="v4-empty__desc">다른 검색어나 필터를 시도해 보세요.</div>' +
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
                if (currentKeyword) params.set('keyword', currentKeyword);
                var newUrl = window.location.pathname;
                if (params.toString()) newUrl += '?' + params.toString();
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

    // 배너 슬라이더
    if ($('#banner-swiper').length) {
        new Swiper('#banner-swiper', {
            slidesPerView: 1,
            loop: true,
            autoplay: { delay: 4000, disableOnInteraction: false },
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' }
        });
    }

})(jQuery);
</script>
</body>
</html>
