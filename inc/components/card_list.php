<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * 카드 리스트 렌더링 (리스트뷰/갤러리뷰 전환)
 *
 * @param array $items 콘텐츠 배열 (sql_fetch_array 결과)
 * @param string $type 콘텐츠 타입 ('event'|'replay'|'news'|'free'|'book')
 * @param string $view_url 상세 페이지 URL 패턴 (예: '/v3/contents/v4/replay_view.php?rsc_bbs_idx=')
 * @param bool $show_view_toggle 리스트뷰/갤러리뷰 전환 버튼 표시 여부
 */
function render_card_list($items, $type, $view_url, $show_view_toggle = true) {
    if (empty($items)) {
        echo '<div class="v4-card-empty">콘텐츠가 없습니다.</div>';
        return;
    }

    $type = v4_str($type);
    $view_url = v4_str($view_url);
    ?>
    <div class="v4-card-list-wrapper">
        <?php if ($show_view_toggle): ?>
        <div class="v4-view-toggle">
            <button type="button" class="v4-view-btn active" data-view="gallery" aria-label="갤러리 뷰">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <rect x="2" y="2" width="7" height="7" stroke="currentColor" stroke-width="1.5"/>
                    <rect x="11" y="2" width="7" height="7" stroke="currentColor" stroke-width="1.5"/>
                    <rect x="2" y="11" width="7" height="7" stroke="currentColor" stroke-width="1.5"/>
                    <rect x="11" y="11" width="7" height="7" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </button>
            <button type="button" class="v4-view-btn" data-view="list" aria-label="리스트 뷰">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <rect x="2" y="3" width="16" height="3" stroke="currentColor" stroke-width="1.5"/>
                    <rect x="2" y="8.5" width="16" height="3" stroke="currentColor" stroke-width="1.5"/>
                    <rect x="2" y="14" width="16" height="3" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </button>
        </div>
        <?php endif; ?>

        <div class="v4-card-list" data-view-mode="gallery">
            <?php foreach ($items as $item):
                $idx = v4_int($item['rsc_bbs_idx'] ?? $item['idx'] ?? 0);
                $title = get_text($item['title'] ?? '');
                $content = get_text($item['content'] ?? '');
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
                <article class="v4-card" data-idx="<?php echo $idx; ?>">
                    <a href="<?php echo $detail_url; ?>" class="v4-card-link">
                        <?php if ($thumb_url): ?>
                        <div class="v4-card-thumb">
                            <img src="<?php echo $thumb_url; ?>" alt="<?php echo $title; ?>" loading="lazy">
                            <?php
                            // 이벤트 상태 배지
                            if ($type === 'event' && isset($item['event_status'])):
                                $status = get_text($item['event_status']);
                                $status_class = '';
                                if ($status === '진행중') $status_class = 'status-active';
                                elseif ($status === '종료') $status_class = 'status-ended';
                                elseif ($status === '결과발표') $status_class = 'status-result';
                                ?>
                                <span class="v4-card-badge <?php echo $status_class; ?>"><?php echo $status; ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="v4-card-body">
                            <?php
                            // 리소스 카테고리 태그
                            if (in_array($type, ['free', 'book']) && isset($item['cate_industry'])):
                                $cate_industry = get_text($item['cate_industry']);
                                if ($cate_industry):
                                ?>
                                <div class="v4-card-tags">
                                    <span class="v4-tag"><?php echo $cate_industry; ?></span>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <h3 class="v4-card-title"><?php echo $title; ?></h3>

                            <p class="v4-card-desc"><?php echo mb_substr(strip_tags($content), 0, 100, 'UTF-8') . (mb_strlen(strip_tags($content), 'UTF-8') > 100 ? '...' : ''); ?></p>

                            <?php if ($type === 'news' && $reg_date): ?>
                            <time class="v4-card-time"><?php echo v4_relative_time($reg_date); ?></time>
                            <?php elseif ($reg_date): ?>
                            <time class="v4-card-time"><?php echo date('Y.m.d', strtotime($reg_date)); ?></time>
                            <?php endif; ?>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>

    <style>
    .v4-card-list-wrapper {
        position: relative;
    }

    .v4-view-toggle {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        margin-bottom: 20px;
    }

    .v4-view-btn {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .v4-view-btn:hover {
        border-color: #6366f1;
        color: #6366f1;
    }

    .v4-view-btn.active {
        background: #6366f1;
        border-color: #6366f1;
        color: #fff;
    }

    .v4-card-list {
        display: grid;
        gap: 24px;
    }

    /* 갤러리 뷰 */
    .v4-card-list[data-view-mode="gallery"] {
        grid-template-columns: repeat(3, 1fr);
    }

    /* 리스트 뷰 */
    .v4-card-list[data-view-mode="list"] {
        grid-template-columns: 1fr;
    }

    .v4-card-list[data-view-mode="list"] .v4-card {
        display: flex;
    }

    .v4-card-list[data-view-mode="list"] .v4-card-link {
        display: flex;
        flex-direction: row;
        width: 100%;
    }

    .v4-card-list[data-view-mode="list"] .v4-card-thumb {
        width: 200px;
        flex-shrink: 0;
    }

    .v4-card-list[data-view-mode="list"] .v4-card-body {
        flex: 1;
    }

    .v4-card {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .v4-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .v4-card-link {
        display: block;
        color: inherit;
        text-decoration: none;
    }

    .v4-card-thumb {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%;
        background: #f3f4f6;
        overflow: hidden;
    }

    .v4-card-thumb img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .v4-card-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 600;
        background: rgba(0,0,0,0.7);
        color: #fff;
    }

    .v4-card-badge.status-active {
        background: #10b981;
    }

    .v4-card-badge.status-ended {
        background: #6b7280;
    }

    .v4-card-badge.status-result {
        background: #6366f1;
    }

    .v4-card-body {
        padding: 20px;
    }

    .v4-card-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 12px;
    }

    .v4-tag {
        display: inline-block;
        padding: 4px 10px;
        background: #eff6ff;
        color: #3b82f6;
        font-size: 12px;
        border-radius: 4px;
    }

    .v4-card-title {
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 12px 0;
        line-height: 1.4;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .v4-card-desc {
        font-size: 14px;
        color: #6b7280;
        line-height: 1.6;
        margin: 0 0 12px 0;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }

    .v4-card-time {
        font-size: 13px;
        color: #9ca3af;
    }

    .v4-card-empty {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
        font-size: 16px;
    }

    /* 다크모드 */
    body.dark-theme .v4-view-btn {
        background: #374151;
        border-color: #4b5563;
        color: #d1d5db;
    }

    body.dark-theme .v4-view-btn.active {
        background: #6366f1;
        border-color: #6366f1;
    }

    body.dark-theme .v4-card {
        background: #1f2937;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }

    body.dark-theme .v4-card-title {
        color: #f9fafb;
    }

    body.dark-theme .v4-card-desc {
        color: #9ca3af;
    }

    body.dark-theme .v4-tag {
        background: #1e3a8a;
        color: #93c5fd;
    }

    /* 태블릿 */
    @media (max-width: 1024px) {
        .v4-card-list[data-view-mode="gallery"] {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* 모바일 */
    @media (max-width: 768px) {
        .v4-card-list[data-view-mode="gallery"] {
            grid-template-columns: 1fr;
        }

        .v4-card-list[data-view-mode="list"] .v4-card-link {
            flex-direction: column;
        }

        .v4-card-list[data-view-mode="list"] .v4-card-thumb {
            width: 100%;
        }
    }
    </style>
    <?php
}
