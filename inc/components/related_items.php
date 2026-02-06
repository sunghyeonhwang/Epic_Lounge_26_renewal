<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * 관련 콘텐츠 3개 표시
 *
 * @param string $table DB 테이블명
 * @param int $current_idx 현재 게시물 idx (제외용)
 * @param string $view_url 상세 페이지 URL 패턴
 * @param int $count 표시 개수 (기본 3)
 */
function render_related_items($table, $current_idx, $view_url, $count = 3) {
    $table = v4_str($table);
    $current_idx = v4_int($current_idx);
    $view_url = v4_str($view_url);
    $count = v4_int($count);

    if ($count <= 0) {
        $count = 3;
    }

    // 현재 게시물과 가장 가까운 idx 기준으로 관련 콘텐츠 조회
    // 이전/이후 게시물을 섞어서 가져오기
    $sql = "(SELECT *
             FROM " . sql_real_escape_string($table) . "
             WHERE rsc_bbs_idx < " . $current_idx . "
             AND display_yn = 'Y'
             ORDER BY rsc_bbs_idx DESC
             LIMIT " . ceil($count / 2) . ")
            UNION ALL
            (SELECT *
             FROM " . sql_real_escape_string($table) . "
             WHERE rsc_bbs_idx > " . $current_idx . "
             AND display_yn = 'Y'
             ORDER BY rsc_bbs_idx ASC
             LIMIT " . ceil($count / 2) . ")
            ORDER BY RAND()
            LIMIT " . $count;

    $result = sql_query($sql);

    if (!$result || sql_num_rows($result) == 0) {
        return;
    }

    $items = array();
    while ($row = sql_fetch_array($result)) {
        $items[] = $row;
    }

    if (empty($items)) {
        return;
    }
    ?>
    <section class="v4-related">
        <div class="v4-related-header">
            <h3>관련 콘텐츠</h3>
        </div>

        <div class="v4-related-list">
            <?php foreach ($items as $item):
                $idx = v4_int($item['rsc_bbs_idx'] ?? 0);
                $title = get_text($item['title'] ?? '');
                $reg_date = $item['reg_date'] ?? '';

                // 썸네일 처리
                $thumb_url = '';
                if (!empty($item['thumb_img_url'])) {
                    $thumb_url = get_text($item['thumb_img_url']);
                } elseif (!empty($item['thumb_img'])) {
                    $thumb_url = G5_DATA_URL . '/resource/' . get_text($item['thumb_img']);
                }

                $detail_url = get_text($view_url . $idx);
                ?>
                <article class="v4-related-item">
                    <a href="<?php echo $detail_url; ?>" class="v4-related-link">
                        <?php if ($thumb_url): ?>
                        <div class="v4-related-thumb">
                            <img src="<?php echo $thumb_url; ?>" alt="<?php echo $title; ?>" loading="lazy">
                        </div>
                        <?php endif; ?>

                        <div class="v4-related-body">
                            <h4 class="v4-related-title"><?php echo $title; ?></h4>

                            <?php if ($reg_date): ?>
                            <time class="v4-related-time"><?php echo date('Y.m.d', strtotime($reg_date)); ?></time>
                            <?php endif; ?>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <style>
    .v4-related {
        margin-top: 60px;
        padding-top: 40px;
        border-top: 1px solid #e5e7eb;
    }

    .v4-related-header {
        margin-bottom: 24px;
    }

    .v4-related-header h3 {
        font-size: 24px;
        font-weight: 700;
        margin: 0;
    }

    .v4-related-list {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    .v4-related-item {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .v4-related-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .v4-related-link {
        display: block;
        color: inherit;
        text-decoration: none;
    }

    .v4-related-thumb {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%;
        background: #f3f4f6;
        overflow: hidden;
    }

    .v4-related-thumb img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .v4-related-body {
        padding: 16px;
    }

    .v4-related-title {
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 8px 0;
        line-height: 1.4;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .v4-related-time {
        font-size: 13px;
        color: #9ca3af;
    }

    /* 다크모드 */
    body.dark-theme .v4-related {
        border-top-color: #374151;
    }

    body.dark-theme .v4-related-header h3 {
        color: #f9fafb;
    }

    body.dark-theme .v4-related-item {
        background: #1f2937;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }

    body.dark-theme .v4-related-title {
        color: #f9fafb;
    }

    /* 태블릿 */
    @media (max-width: 1024px) {
        .v4-related-list {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* 모바일 */
    @media (max-width: 768px) {
        .v4-related {
            margin-top: 40px;
            padding-top: 32px;
        }

        .v4-related-header h3 {
            font-size: 20px;
        }

        .v4-related-list {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }
    </style>
    <?php
}
