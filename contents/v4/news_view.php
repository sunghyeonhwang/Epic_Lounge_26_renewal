<?php
$g5_path = '../..';
chdir($g5_path);
include_once('./_common.php');
include_once(G5_PATH.'/inc/v4_helpers.php');
include_once(G5_PATH.'/inc/v4_cards.php');

// ----- 파라미터 -----
$rsc_bbs_idx = v4_int($_GET['rsc_bbs_idx'] ?? 0);

if (!$rsc_bbs_idx) {
    alert('잘못된 접근입니다.', '/v3/contents/v4/news_list.php');
}

// ----- DB 조회 -----
$RData = sql_fetch("SELECT * FROM v3_rsc_news_bbs WHERE rsc_bbs_idx = {$rsc_bbs_idx}");

if (!$RData) {
    alert('게시물이 존재하지 않습니다.', '/v3/contents/v4/news_list.php');
}

// 권한 체크: 비공개 게시물은 관리자(레벨10)만 접근
if ($RData['display_yn'] !== 'Y' && (int)($member['mb_level'] ?? 0) < 10) {
    alert('게시물이 존재하지 않습니다.', '/v3/contents/v4/news_list.php');
}

// ----- 데이터 가공 -----
$title    = get_text($RData['title']);
$category = get_text($RData['category']);
$contents = $RData['contents']; // HTML 본문
$reg_date = $RData['reg_date'] ?? '';
$tag      = get_text($RData['tag'] ?? '');
$site_url = trim($RData['site_url'] ?? '');

// 히어로 이미지
$hero_img = '';
if (!empty($RData['top_bbs_img'])) {
    $hero_img = $RData['top_bbs_img'];
    if (strpos($hero_img, 'http') !== 0) {
        $hero_img = G5_DATA_URL . '/news/' . $hero_img;
    }
}
$thumb_url = v4_thumb_url($RData, 'news');

// 날짜 포맷
$date_str = '';
if ($reg_date) {
    $date_str = date('Y.m.d', strtotime($reg_date));
}

// 태그 처리 (콤마 구분)
$tags_array = [];
if ($tag) {
    $tags_array = array_filter(array_map('trim', explode(',', $tag)));
}

// 목록 URL
$list_url = '/v3/contents/v4/news_list.php';

// 현재 URL (소셜 공유용)
$current_url = 'https://epiclounge.co.kr/contents/v4/news_view.php?rsc_bbs_idx=' . $rsc_bbs_idx;

// ----- 관련 콘텐츠 3개 -----
$related_sql = "SELECT * FROM v3_rsc_news_bbs WHERE display_yn='Y' AND rsc_bbs_idx <> {$rsc_bbs_idx}
                ORDER BY ABS({$rsc_bbs_idx} - rsc_bbs_idx) ASC LIMIT 3";
$related_result = sql_query($related_sql);
$related_items = [];
while ($rel = sql_fetch_array($related_result)) {
    $related_items[] = $rel;
}

// ----- SEO -----
$v3_seo = sql_fetch("SELECT * FROM v3_seo_config WHERE seo_page = 'news'");
if (empty($v3_seo['seo_title'])) {
    $v3_seo = sql_fetch("SELECT * FROM v3_seo_config WHERE seo_page = 'default'");
}
$seo_title = $title . ' | 에픽 라운지';

$seo_ga_id          = trim($v3_seo['seo_ga_id'] ?? '');
$seo_gtm_id         = trim($v3_seo['seo_gtm_id'] ?? '');
$seo_pixel_id       = trim($v3_seo['seo_pixel_id'] ?? '');
$seo_kakao_pixel_id = trim($v3_seo['seo_kakao_pixel_id'] ?? '');
$seo_naver_verif    = trim($v3_seo['seo_naver_verif'] ?? '');
$seo_google_verif   = trim($v3_seo['seo_google_verif'] ?? '');
$seo_extra_head     = $v3_seo['seo_extra_head'] ?? '';
$seo_extra_body     = $v3_seo['seo_extra_body'] ?? '';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta property="og:type" content="article">
    <meta property="og:title" content="<?php echo $title; ?>">
    <meta property="og:image" content="<?php echo $hero_img ?: $thumb_url; ?>">
    <meta property="og:url" content="<?php echo $current_url; ?>">

    <?php include G5_PATH.'/inc/marketing_head.php'; ?>

    <link rel="icon" type="image/png" sizes="32x32" href="/v3/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/v3/favicon/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/v3/favicon/apple-icon-180x180.png">

    <title><?php echo get_text($seo_title); ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="/v3/resource/css/main26.css">
    <link rel="stylesheet" href="/v3/resource/css/pages/detail.css?v=20250209b">

    <!-- JS -->
    <script src="/v3/resource/js/jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
</head>
<body>
<?php include G5_PATH.'/inc/marketing_body.php'; ?>
<?php include G5_PATH.'/inc/common_header26.php'; ?>

<!-- 히어로 이미지 -->
<?php if ($hero_img): ?>
<div class="v4-detail-hero">
    <img class="v4-detail-hero__image" src="<?php echo $hero_img; ?>" alt="<?php echo $title; ?>">
    <div class="v4-detail-hero__overlay"></div>
    <?php if ($category): ?>
    <div class="v4-detail-hero__badge">
        <span class="v4-badge"><?php echo $category; ?></span>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- 본문 컨테이너 -->
<div class="v4-detail-container">
    <div class="v4-detail-wrapper">

        <!-- 헤더 -->
        <div class="v4-detail-header">
            <?php if ($category && !$hero_img): ?>
            <span class="v4-detail-header__category">
                <span class="v4-badge"><?php echo $category; ?></span>
            </span>
            <?php endif; ?>

            <h1 class="v4-detail-header__title"><?php echo $title; ?></h1>

            <div class="v4-detail-header__meta">
                <?php if ($date_str): ?>
                <span class="v4-detail-header__meta-item"><?php echo $date_str; ?></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- 본문 -->
        <div class="v4-detail-content">
            <?php echo $contents; ?>
        </div>

        <!-- 태그 -->
        <?php if (!empty($tags_array)): ?>
        <div class="v4-detail-tags">
            <span class="v4-detail-tags__label">태그</span>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                <?php foreach ($tags_array as $t): ?>
                <span class="v4-tag"><?php echo get_text($t); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- 소셜 공유 -->
        <div class="v4-share">
            <span class="v4-share__label">공유하기</span>
            <div class="v4-share__buttons">
                <button type="button" class="v4-share__button v4-share__button--facebook"
                        onclick="window.open('https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent('<?php echo $current_url; ?>'),'_blank','width=600,height=400');"
                        aria-label="페이스북 공유">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M18 10a8 8 0 10-9.25 7.9v-5.59H6.74V10h2.01V8.14c0-1.99 1.18-3.08 2.99-3.08.87 0 1.78.15 1.78.15v1.95h-1c-.99 0-1.3.61-1.3 1.24V10h2.2l-.35 2.31h-1.85v5.59A8 8 0 0018 10z"/>
                    </svg>
                </button>
                <button type="button" class="v4-share__button v4-share__button--x"
                        onclick="window.open('https://x.com/intent/tweet?text='+encodeURIComponent('<?php echo addslashes($title); ?>')+'&url='+encodeURIComponent('<?php echo $current_url; ?>'),'_blank','width=600,height=400');"
                        aria-label="X 공유">
                    <svg width="20" height="20" viewBox="0 0 1200 1227" fill="currentColor">
                        <path d="M714.163 519.284L1160.89 0H1055.03L667.137 450.887L357.328 0H0L468.492 681.821L0 1226.37H105.866L515.491 750.218L842.672 1226.37H1200L714.137 519.284H714.163ZM569.165 687.828L521.697 619.934L144.011 79.6944H306.615L611.412 515.685L658.88 583.579L1055.08 1150.3H892.476L569.165 687.854V687.828Z"/>
                    </svg>
                </button>
                <button type="button" class="v4-share__button v4-share__button--copy"
                        onclick="v4CopyUrl('<?php echo $current_url; ?>')"
                        aria-label="URL 복사">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M8.5 11.5a3 3 0 004.24 0l3-3a3 3 0 00-4.24-4.24l-1.5 1.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M11.5 8.5a3 3 0 00-4.24 0l-3 3a3 3 0 004.24 4.24l1.5-1.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- 관련 콘텐츠 -->
        <?php if (!empty($related_items)): ?>
        <div class="v4-related">
            <h2 class="v4-related__title">다른 뉴스</h2>
            <div class="v4-related__grid">
                <?php foreach ($related_items as $rel): ?>
                <?php
                    $rel_idx = v4_int($rel['rsc_bbs_idx']);
                    $rel_title = get_text($rel['title']);
                    $rel_thumb = v4_thumb_url($rel, 'news');
                    $rel_category = get_text($rel['category']);
                    $rel_site_url = trim($rel['site_url'] ?? '');
                    $rel_reg_date = $rel['reg_date'] ?? '';
                    $rel_date = $rel_reg_date ? date('Y.m.d', strtotime($rel_reg_date)) : '';

                    // 외부 링크가 있으면 외부 링크로, 없으면 상세 페이지로
                    if ($rel_site_url) {
                        $rel_url = get_text($rel_site_url);
                        $rel_target = ' target="_blank" rel="noopener noreferrer"';
                    } else {
                        $rel_url = '/v3/contents/v4/news_view.php?rsc_bbs_idx=' . $rel_idx;
                        $rel_target = '';
                    }
                ?>
                <a href="<?php echo $rel_url; ?>" class="v4-related__item"<?php echo $rel_target; ?>>
                    <div class="v4-related__thumbnail">
                        <img src="<?php echo $rel_thumb; ?>" alt="<?php echo $rel_title; ?>" loading="lazy">
                    </div>
                    <div class="v4-related__body">
                        <?php if ($rel_category): ?>
                        <span class="v4-related__category"><?php echo $rel_category; ?></span>
                        <?php endif; ?>
                        <h3 class="v4-related__title-text"><?php echo $rel_title; ?></h3>
                        <?php if ($rel_date): ?>
                        <span class="v4-related__date"><?php echo $rel_date; ?></span>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- 목록으로 돌아가기 -->
        <div class="v4-detail-nav">
            <a href="<?php echo $list_url; ?>" class="v4-detail-nav__button--list">
                <span class="v4-detail-nav__label">목록으로</span>
            </a>
        </div>

    </div><!-- /.v4-detail-wrapper -->
</div><!-- /.v4-detail-container -->

<?php include G5_PATH.'/inc/common_footer.php'; ?>

<script src="/v3/resource/js/v4.app.js"></script>
<script>
function v4CopyUrl(url) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function() {
            alert('URL이 복사되었습니다.');
        });
    } else {
        var t = document.createElement('textarea');
        t.value = url;
        document.body.appendChild(t);
        t.select();
        document.execCommand('copy');
        document.body.removeChild(t);
        alert('URL이 복사되었습니다.');
    }
}
</script>
</body>
</html>
