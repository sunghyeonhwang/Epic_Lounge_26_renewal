<?php
$g5_path = '../..';
chdir($g5_path);
include_once('./_common.php');
include_once(G5_PATH.'/inc/v4_helpers.php');
include_once(G5_PATH.'/inc/v4_cards.php');

// SEO
$v3_seo = sql_fetch("SELECT * FROM v3_seo_config WHERE seo_page = 'news'");
if (empty($v3_seo['seo_title'])) {
    $v3_seo = sql_fetch("SELECT * FROM v3_seo_config WHERE seo_page = 'default'");
}
$seo_title       = $v3_seo['seo_title']       ?: '에픽 라운지 | 새소식';
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
$keyword   = v4_str($_GET['keyword'] ?? '');
$category  = v4_str($_GET['category'] ?? '');
$page      = v4_int($_GET['page'] ?? 1);
$per_page  = 12;

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

// 키워드 검색
if ($keyword) {
    $keyword_esc = sql_real_escape_string($keyword);
    $where .= " AND (title LIKE '%{$keyword_esc}%' OR content LIKE '%{$keyword_esc}%')";
}

// 전체 건수
$total_count = (int)sql_fetch("SELECT COUNT(*) as cnt FROM v3_rsc_news_bbs {$where}")['cnt'];

// 목록 조회
$sql = "SELECT * FROM v3_rsc_news_bbs {$where} ORDER BY rsc_bbs_idx DESC" . v4_limit($page, $per_page);
$result = sql_query($sql);
$items = [];
while ($row = sql_fetch_array($result)) {
    $items[] = $row;
}

// 상세 URL
$view_url = '/v3/contents/v4/news_view.php?rsc_bbs_idx=';

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
    <meta property="og:url" content="https://epiclounge.co.kr/contents/v4/news_list.php">

    <?php include G5_PATH.'/inc/marketing_head.php'; ?>

    <link rel="icon" type="image/png" sizes="32x32" href="/v3/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/v3/favicon/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/v3/favicon/apple-icon-180x180.png">

    <title><?php echo get_text($seo_title); ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="/v3/resource/css/main26.css">
    <link rel="stylesheet" href="/v3/resource/css/sub.css">
    <link rel="stylesheet" href="/v3/resource/css/pages/list.css">

    <!-- JS (jQuery first) -->
    <script src="/v3/resource/js/jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
</head>
<body>
<?php include G5_PATH.'/inc/marketing_body.php'; ?>
<?php include G5_PATH.'/inc/common_header26.php'; ?>

<!-- sub_visual -->
<div id="sub_visual" class="news_vi">
    <h2>새소식</h2>
    <p>언리얼 엔진 뉴스, 이벤트 그리고 영감을 주는 사례를 확인하세요.</p>
</div>

<!-- container -->
<div class="container">

    <div class="wrap" style="padding-top: 40px;">

        <!-- 카테고리 탭 -->
        <div class="v4-news-tabs" id="category-tabs">
            <button type="button" class="v4-news-tabs__item<?php echo empty($category) ? ' active' : ''; ?>" data-category="">전체</button>
            <button type="button" class="v4-news-tabs__item<?php echo ($category === '뉴스') ? ' active' : ''; ?>" data-category="뉴스">뉴스</button>
            <button type="button" class="v4-news-tabs__item<?php echo ($category === '업데이트/출시') ? ' active' : ''; ?>" data-category="업데이트/출시">출시&업데이트</button>
            <button type="button" class="v4-news-tabs__item<?php echo ($category === '블로그') ? ' active' : ''; ?>" data-category="블로그">블로그</button>
        </div>

        <!-- 검색바 -->
        <div class="v4-search-bar" style="margin-bottom: 32px; max-width: 400px;">
            <div class="v4-search-bar__inner">
                <input type="text"
                       class="v4-search-bar__input"
                       id="keyword-input"
                       placeholder="제목 또는 내용으로 검색"
                       value="<?php echo get_text($keyword); ?>">
                <button type="button" class="v4-search-bar__button" id="search-btn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="2"/>
                        <path d="M15 15L20 20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    검색
                </button>
            </div>
        </div>

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
                    <div class="v4-empty__icon">&#128240;</div>
                    <div class="v4-empty__title">등록된 새소식이 없습니다.</div>
                    <div class="v4-empty__desc">새로운 소식이 곧 등록될 예정입니다.</div>
                </div>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <?php echo render_resource_card($item, 'news', $view_url, 'news'); ?>
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

    </div><!-- /.wrap -->
</div><!-- /.container -->

<?php include G5_PATH.'/inc/common_footer.php'; ?>

<!-- v4 통합 JS -->
<script src="/v3/resource/js/v4.app.js"></script>

<script>
/**
 * 새소식 목록 페이지 JS
 */
(function($) {
    'use strict';

    var currentKeyword  = '<?php echo addslashes(get_text($keyword)); ?>';
    var currentCategory = '<?php echo addslashes(get_text($category)); ?>';
    var currentPage     = <?php echo $page; ?>;
    var perPage         = <?php echo $per_page; ?>;
    var isLoading       = false;

    // ----- 카테고리 탭 -----
    $('#category-tabs').on('click', '.v4-news-tabs__item', function() {
        currentCategory = $(this).data('category');
        currentPage = 1;
        $('#category-tabs .v4-news-tabs__item').removeClass('active');
        $(this).addClass('active');
        loadNews(false);
    });

    // ----- 검색 -----
    $('#search-btn').on('click', function() {
        currentKeyword = $('#keyword-input').val().trim();
        currentPage = 1;
        loadNews(false);
    });
    $('#keyword-input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#search-btn').click();
        }
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
        loadNews(true);
    });

    // ----- AJAX 로드 -----
    function loadNews(append) {
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

        $.ajax({
            url: '/v3/contents/v4/ajax/news.ajax.php',
            type: 'POST',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            data: {
                keyword: currentKeyword,
                category: currentCategory,
                page: currentPage,
                per_page: perPage
            },
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
                            '<div class="v4-empty__icon">&#128240;</div>' +
                            '<div class="v4-empty__title">검색 결과가 없습니다.</div>' +
                            '<div class="v4-empty__desc">다른 검색어를 시도해 보세요.</div>' +
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
                if (currentCategory) params.set('category', currentCategory);
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

})(jQuery);
</script>
</body>
</html>
