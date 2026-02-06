<?php
$g5_path = '../..';
chdir($g5_path);
include_once('./_common.php');
include_once(G5_PATH.'/inc/v4_helpers.php');

// SEO
$v3_seo = sql_fetch("SELECT * FROM v3_seo_config WHERE seo_page = 'default'");
$seo_title = '에픽 라운지 | 검색';

// 마케팅 태그
$seo_ga_id          = trim($v3_seo['seo_ga_id'] ?? '');
$seo_gtm_id         = trim($v3_seo['seo_gtm_id'] ?? '');
$seo_pixel_id       = trim($v3_seo['seo_pixel_id'] ?? '');
$seo_kakao_pixel_id = trim($v3_seo['seo_kakao_pixel_id'] ?? '');
$seo_naver_verif    = trim($v3_seo['seo_naver_verif'] ?? '');
$seo_google_verif   = trim($v3_seo['seo_google_verif'] ?? '');
$seo_extra_head     = $v3_seo['seo_extra_head'] ?? '';
$seo_extra_body     = $v3_seo['seo_extra_body'] ?? '';

// 검색어
$keyword = v4_str($_GET['keyword'] ?? '');

// 초기 검색 결과 (서버 사이드)
$initial_results = [];
$total_all = 0;

if ($keyword) {
    $search_tables = [
        'news'   => ['table' => 'v3_rsc_news_bbs',   'fields' => 'title, contents', 'label' => '새소식',      'order' => 'rsc_bbs_idx DESC'],
        'event'  => ['table' => 'v3_rsc_event_bbs',  'fields' => 'title, contents', 'label' => '이벤트',      'order' => 'ordr DESC, rsc_bbs_idx DESC'],
        'replay' => ['table' => 'v3_rsc_review_bbs', 'fields' => 'title, content',  'label' => '다시보기',     'order' => 'ordr DESC, rsc_bbs_idx DESC'],
        'free'   => ['table' => 'v3_rsc_free_bbs',   'fields' => 'title, content',  'label' => '무료 콘텐츠', 'order' => 'ordr DESC, rsc_bbs_idx DESC'],
        'book'   => ['table' => 'v3_rsc_book_bbs',   'fields' => 'title, content',  'label' => '백서/eBook',  'order' => 'ordr DESC, rsc_bbs_idx DESC'],
    ];

    foreach ($search_tables as $key => $cfg) {
        $where = "WHERE display_yn = 'Y'";
        $fields = explode(', ', $cfg['fields']);
        $likes = [];
        foreach ($fields as $f) {
            $likes[] = trim($f) . " LIKE '%{$keyword}%'";
        }
        $where .= " AND (" . implode(' OR ', $likes) . ")";

        $cnt = (int)sql_fetch("SELECT COUNT(*) as cnt FROM {$cfg['table']} {$where}")['cnt'];
        $total_all += $cnt;
        $initial_results[$key] = [
            'count' => $cnt,
            'label' => $cfg['label'],
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <?php include G5_PATH.'/inc/marketing_head.php'; ?>

    <link rel="icon" type="image/png" sizes="32x32" href="/v3/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/v3/favicon/favicon-16x16.png">

    <title><?php echo get_text($seo_title); ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="/v3/resource/css/main26.css">
    <link rel="stylesheet" href="/v3/resource/css/pages/search.css">

    <!-- JS -->
    <script src="/v3/resource/js/jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
</head>
<body>
<?php include G5_PATH.'/inc/marketing_body.php'; ?>
<?php include G5_PATH.'/inc/common_header26.php'; ?>

<div class="container" style="margin-top: 80px;">
    <div class="v4-search-container">

        <!-- 검색 헤더 -->
        <div class="v4-search-header">
            <h1 class="v4-search-header__title">
                <?php if ($keyword): ?>
                    <em>"<?php echo get_text($keyword); ?>"</em> 검색 결과
                <?php else: ?>
                    검색
                <?php endif; ?>
            </h1>

            <form class="v4-search-header__form" action="/v3/contents/v4/total_search.php" method="get" id="search-form">
                <input type="text"
                       name="keyword"
                       class="v4-search-header__input"
                       placeholder="키워드를 입력하세요"
                       value="<?php echo get_text($keyword); ?>"
                       autocomplete="off">
                <button type="submit" class="v4-search-header__button" aria-label="검색">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16zM18 18l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </form>

            <?php if ($keyword): ?>
            <div class="v4-search-header__info">
                <span class="v4-search-header__count">
                    총 <strong id="total-count"><?php echo number_format($total_all); ?></strong>건
                </span>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($keyword): ?>

        <!-- 섹션 탭 -->
        <div class="v4-search-tabs" id="search-tabs">
            <button type="button" class="v4-search-tabs__item active" data-section="all">
                전체
                <span class="v4-search-tabs__badge" id="badge-all"><?php echo number_format($total_all); ?></span>
            </button>
            <?php foreach ($initial_results as $key => $sec): ?>
            <button type="button" class="v4-search-tabs__item" data-section="<?php echo $key; ?>">
                <?php echo get_text($sec['label']); ?>
                <span class="v4-search-tabs__badge" id="badge-<?php echo $key; ?>"><?php echo number_format($sec['count']); ?></span>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- 검색 결과 영역 -->
        <div id="search-results">
            <div class="v4-search-loading" id="search-loading" style="display: none;">
                <div class="v4-search-loading__spinner"></div>
                <div class="v4-search-loading__text">검색 중...</div>
            </div>

            <!-- 각 섹션별 결과 (JS로 채움) -->
            <?php foreach ($initial_results as $key => $sec): ?>
            <?php if ($sec['count'] > 0): ?>
            <div class="v4-search-section" id="section-<?php echo $key; ?>" data-section="<?php echo $key; ?>">
                <div class="v4-search-section__header">
                    <h2 class="v4-search-section__title">
                        <?php echo get_text($sec['label']); ?>
                        <span class="v4-search-section__count"><?php echo number_format($sec['count']); ?></span>
                    </h2>
                </div>
                <div class="v4-search-results" id="results-<?php echo $key; ?>">
                    <!-- AJAX로 채워짐 -->
                </div>
                <?php if ($sec['count'] > 6): ?>
                <div class="v4-search-more">
                    <button type="button" class="v4-search-more__button" data-section="<?php echo $key; ?>" data-page="1">
                        더보기 (<?php echo min(6, $sec['count']); ?>/<?php echo number_format($sec['count']); ?>)
                    </button>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>

            <?php if ($total_all === 0): ?>
            <div class="v4-search-empty">
                <div class="v4-search-empty__icon">&#128269;</div>
                <div class="v4-search-empty__title">"<?php echo get_text($keyword); ?>"에 대한 검색 결과가 없습니다.</div>
                <div class="v4-search-empty__description">다른 키워드로 검색해 보세요.</div>
                <div class="v4-search-empty__suggestions">
                    <div class="v4-search-empty__suggestion">검색어의 철자가 정확한지 확인하세요.</div>
                    <div class="v4-search-empty__suggestion">다른 키워드를 사용해 보세요.</div>
                    <div class="v4-search-empty__suggestion">더 일반적인 키워드로 검색해 보세요.</div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php else: ?>

        <!-- 검색어 없을 때 -->
        <div class="v4-search-empty">
            <div class="v4-search-empty__icon">&#128269;</div>
            <div class="v4-search-empty__title">검색어를 입력해주세요.</div>
            <div class="v4-search-empty__description">
                에픽 라운지의 이벤트, 뉴스, 다시보기, 무료 콘텐츠, 백서를 검색할 수 있습니다.
            </div>
        </div>

        <?php endif; ?>

    </div><!-- /.v4-search-container -->
</div><!-- /.container -->

<?php include G5_PATH.'/inc/common_footer.php'; ?>

<script src="/v3/resource/js/v4.app.js"></script>

<?php if ($keyword): ?>
<script>
/**
 * 통합 검색 페이지 JS
 */
(function($) {
    'use strict';

    var keyword      = '<?php echo addslashes(get_text($keyword)); ?>';
    var activeSection = 'all';
    var sectionPages  = {};
    var isLoading     = false;

    // 초기 로드
    $(document).ready(function() {
        loadAllSections();
    });

    // ----- 탭 전환 -----
    $('#search-tabs').on('click', '.v4-search-tabs__item', function() {
        activeSection = $(this).data('section');
        $('#search-tabs .v4-search-tabs__item').removeClass('active');
        $(this).addClass('active');

        if (activeSection === 'all') {
            $('.v4-search-section').show();
        } else {
            $('.v4-search-section').hide();
            $('#section-' + activeSection).show();
        }
    });

    // ----- 더보기 -----
    $(document).on('click', '.v4-search-more__button', function() {
        var sec = $(this).data('section');
        if (!sectionPages[sec]) sectionPages[sec] = 1;
        sectionPages[sec]++;
        loadSection(sec, sectionPages[sec], true);
    });

    // ----- 검색 폼 유효성 -----
    $('#search-form').on('submit', function(e) {
        var kw = $(this).find('input[name="keyword"]').val().trim();
        if (!kw) {
            e.preventDefault();
            alert('검색어를 입력하세요.');
            return false;
        }
    });

    // ----- 전체 섹션 초기 로드 -----
    function loadAllSections() {
        var sections = ['news', 'event', 'replay', 'free', 'book'];
        sections.forEach(function(sec) {
            if ($('#section-' + sec).length) {
                sectionPages[sec] = 1;
                loadSection(sec, 1, false);
            }
        });
    }

    // ----- 섹션별 AJAX 로드 -----
    function loadSection(sec, page, append) {
        var $container = $('#results-' + sec);
        var $moreBtn = $('#section-' + sec + ' .v4-search-more__button');

        if (!append) {
            $container.html('<div class="v4-search-loading"><div class="v4-search-loading__spinner"></div></div>');
        } else {
            $moreBtn.prop('disabled', true).text('로딩 중...');
        }

        $.ajax({
            url: '/v3/contents/v4/ajax/search.ajax.php',
            type: 'POST',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            data: {
                keyword: keyword,
                section: sec,
                page: page,
                per_page: 6
            },
            success: function(res) {
                if (!res.success) return;

                var secData = res.sections[sec];
                if (!secData) return;

                if (append) {
                    $container.append(secData.html);
                } else {
                    $container.html(secData.html || '<div style="padding:20px;color:#999;">결과가 없습니다.</div>');
                }

                // 더보기 버튼 상태
                if (secData.has_more) {
                    var shown = Math.min(page * 6, secData.total);
                    $moreBtn.show().prop('disabled', false)
                        .text('더보기 (' + shown + '/' + Number(secData.total).toLocaleString() + ')');
                } else {
                    $moreBtn.parent('.v4-search-more').hide();
                }
            },
            error: function() {
                if (!append) {
                    $container.html('<div style="padding:20px;color:#999;">검색 중 오류가 발생했습니다.</div>');
                }
                $moreBtn.prop('disabled', false).text('다시 시도');
            }
        });
    }

})(jQuery);
</script>
<?php endif; ?>

</body>
</html>
