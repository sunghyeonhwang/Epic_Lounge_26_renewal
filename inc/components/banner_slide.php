<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * 배너 슬라이드 (Swiper 초기화)
 *
 * @param string $position 배너 위치 (예: '다시보기', '무료콘텐츠', '백서')
 */
function render_banner_slide($position) {
    $position = v4_str($position);

    // v3_shop_banner 테이블에서 배너 조회
    $sql = "SELECT *
            FROM v3_shop_banner
            WHERE position = '" . sql_real_escape_string($position) . "'
            AND display_yn = 'Y'
            ORDER BY ordr ASC";
    $result = sql_query($sql);

    if (!$result || sql_num_rows($result) == 0) {
        return;
    }

    $banners = array();
    while ($row = sql_fetch_array($result)) {
        $banners[] = $row;
    }

    if (empty($banners)) {
        return;
    }

    $slide_id = 'v4-banner-' . md5($position);
    ?>
    <div class="v4-banner-slide" id="<?php echo get_text($slide_id); ?>">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <?php foreach ($banners as $banner):
                    $banner_link = get_text($banner['banner_link'] ?? '#');
                    $banner_img = get_text($banner['banner_img'] ?? '');
                    $banner_alt = get_text($banner['banner_alt'] ?? '');
                    $banner_target = get_text($banner['banner_target'] ?? '_self');

                    // 이미지 URL 처리
                    $img_url = '';
                    if (strpos($banner_img, 'http') === 0) {
                        $img_url = $banner_img;
                    } elseif ($banner_img) {
                        $img_url = G5_DATA_URL . '/banner/' . $banner_img;
                    }

                    if (!$img_url) continue;
                    ?>
                    <div class="swiper-slide">
                        <a href="<?php echo $banner_link; ?>" target="<?php echo $banner_target; ?>" rel="<?php echo $banner_target === '_blank' ? 'noopener noreferrer' : ''; ?>">
                            <img src="<?php echo $img_url; ?>" alt="<?php echo $banner_alt; ?>" loading="lazy">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($banners) > 1): ?>
            <!-- 페이지네이션 -->
            <div class="swiper-pagination"></div>

            <!-- 네비게이션 -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
            <?php endif; ?>
        </div>
    </div>

    <style>
    .v4-banner-slide {
        position: relative;
        width: 100%;
        margin-bottom: 40px;
    }

    .v4-banner-slide .swiper-container {
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
    }

    .v4-banner-slide .swiper-slide {
        width: 100%;
    }

    .v4-banner-slide .swiper-slide a {
        display: block;
        width: 100%;
    }

    .v4-banner-slide .swiper-slide img {
        width: 100%;
        height: auto;
        display: block;
    }

    .v4-banner-slide .swiper-pagination {
        bottom: 20px;
    }

    .v4-banner-slide .swiper-pagination-bullet {
        width: 10px;
        height: 10px;
        background: rgba(255,255,255,0.5);
        opacity: 1;
    }

    .v4-banner-slide .swiper-pagination-bullet-active {
        background: #fff;
    }

    .v4-banner-slide .swiper-button-prev,
    .v4-banner-slide .swiper-button-next {
        width: 44px;
        height: 44px;
        background: rgba(0,0,0,0.5);
        border-radius: 50%;
        color: #fff;
    }

    .v4-banner-slide .swiper-button-prev:after,
    .v4-banner-slide .swiper-button-next:after {
        font-size: 18px;
    }

    .v4-banner-slide .swiper-button-prev:hover,
    .v4-banner-slide .swiper-button-next:hover {
        background: rgba(0,0,0,0.7);
    }

    /* 모바일 */
    @media (max-width: 768px) {
        .v4-banner-slide {
            margin-bottom: 24px;
        }

        .v4-banner-slide .swiper-button-prev,
        .v4-banner-slide .swiper-button-next {
            width: 36px;
            height: 36px;
        }

        .v4-banner-slide .swiper-button-prev:after,
        .v4-banner-slide .swiper-button-next:after {
            font-size: 14px;
        }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Swiper 라이브러리 로드 확인
        if (typeof Swiper === 'undefined') {
            console.error('Swiper library is not loaded.');
            return;
        }

        var slideId = '<?php echo get_text($slide_id); ?>';
        var slideEl = document.querySelector('#' + slideId + ' .swiper-container');

        if (!slideEl) {
            return;
        }

        new Swiper(slideEl, {
            loop: <?php echo count($banners) > 1 ? 'true' : 'false'; ?>,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '#' + slideId + ' .swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '#' + slideId + ' .swiper-button-next',
                prevEl: '#' + slideId + ' .swiper-button-prev',
            },
            speed: 600,
            effect: 'slide',
        });
    });
    </script>
    <?php
}
