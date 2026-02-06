<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * v4 카드 렌더링 함수 모음
 * 목록 페이지와 AJAX 엔드포인트에서 공유
 */

/**
 * 이벤트 카드 HTML 렌더링
 *
 * @param array  $item     DB 행 (v3_rsc_event_bbs / v3_rsc_global_event_bbs)
 * @param string $view_url 상세 페이지 URL 패턴 (예: '/v3/contents/event_view.php?rsc_bbs_idx=')
 * @return string 카드 HTML
 */
function render_event_card($item, $view_url) {
    $idx = v4_int($item['rsc_bbs_idx']);
    $title = get_text($item['title']);
    $status = get_text($item['status']);
    $thumb_url = v4_thumb_url($item, 'event');
    $detail_url = $view_url . $idx;

    // 상태 배지 클래스
    $badge_class = '';
    if ($status === '진행중')    $badge_class = 'v4-badge--active';
    elseif ($status === '종료')  $badge_class = 'v4-badge--ended';
    elseif ($status === '결과발표') $badge_class = 'v4-badge--result';
    elseif ($status === '예고')  $badge_class = 'v4-badge--new';

    // 일정 표시
    $sdate = $item['sdate'] ?? '';
    $edate = $item['edate'] ?? '';
    $date_str = '';
    if ($sdate) {
        $date_str = date('Y.m.d', strtotime($sdate));
        if ($edate) {
            $date_str .= ' ~ ' . date('Y.m.d', strtotime($edate));
        }
    }

    // 외부 링크 처리
    $target = '';
    if (!empty($item['site_url'])) {
        $detail_url = get_text($item['site_url']);
        $target = ' target="_blank" rel="noopener noreferrer"';
    }

    ob_start();
    ?>
    <a href="<?php echo $detail_url; ?>" class="v4-card"<?php echo $target; ?>>
        <div class="v4-card__thumbnail">
            <img src="<?php echo $thumb_url; ?>" alt="<?php echo $title; ?>" loading="lazy">
            <?php if ($status): ?>
            <div class="v4-card__badge">
                <span class="v4-badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
            </div>
            <?php endif; ?>
        </div>
        <div class="v4-card__body">
            <h3 class="v4-card__title"><?php echo $title; ?></h3>
            <?php if ($date_str): ?>
            <div class="v4-card__meta">
                <span class="v4-card__meta-item"><?php echo $date_str; ?></span>
            </div>
            <?php endif; ?>
        </div>
    </a>
    <?php
    return ob_get_clean();
}

/**
 * 리소스(다시보기/뉴스/무료/백서) 카드 HTML 렌더링
 *
 * @param array  $item      DB 행 (v3_rsc_*_bbs)
 * @param string $type      콘텐츠 타입 ('replay'|'news'|'free'|'book')
 * @param string $view_url  상세 페이지 URL 패턴
 * @param string $data_subdir 썸네일 데이터 하위 디렉토리
 * @return string 카드 HTML
 */
function render_resource_card($item, $type, $view_url, $data_subdir) {
    $idx = v4_int($item['rsc_bbs_idx']);
    $title = get_text($item['title']);
    $thumb_url = v4_thumb_url($item, $data_subdir);
    $detail_url = $view_url . $idx;
    $reg_date = $item['reg_date'] ?? '';

    // 카테고리 태그
    $categories = array_filter([
        get_text($item['cate_industry'] ?? ''),
        get_text($item['cate_product'] ?? ''),
        get_text($item['cate_subject'] ?? ''),
    ]);

    // 설명 (content에서 태그 제거 후 100자)
    $desc = '';
    if (!empty($item['content'])) {
        $desc = mb_substr(strip_tags($item['content']), 0, 100, 'UTF-8');
        if (mb_strlen(strip_tags($item['content']), 'UTF-8') > 100) {
            $desc .= '...';
        }
    }

    ob_start();
    ?>
    <a href="<?php echo $detail_url; ?>" class="v4-card">
        <div class="v4-card__thumbnail">
            <img src="<?php echo $thumb_url; ?>" alt="<?php echo $title; ?>" loading="lazy">
        </div>
        <div class="v4-card__body">
            <?php if (!empty($categories)): ?>
            <div class="v4-card__category">
                <?php foreach (array_slice($categories, 0, 2) as $cat): ?>
                <span class="v4-tag v4-tag--primary"><?php echo $cat; ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <h3 class="v4-card__title"><?php echo $title; ?></h3>

            <?php if ($desc): ?>
            <p class="v4-card__desc"><?php echo get_text($desc); ?></p>
            <?php endif; ?>

            <?php if ($reg_date): ?>
            <div class="v4-card__meta">
                <span class="v4-card__meta-item">
                    <?php echo ($type === 'news') ? v4_relative_time($reg_date) : date('Y.m.d', strtotime($reg_date)); ?>
                </span>
            </div>
            <?php endif; ?>
        </div>
    </a>
    <?php
    return ob_get_clean();
}
