<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * 더보기 버튼 (AJAX 페이지네이션)
 *
 * @param string $ajax_url AJAX 엔드포인트 URL
 * @param int $current_page 현재 페이지 번호
 * @param int $total_count 전체 건수
 * @param int $per_page 페이지당 건수 (기본 12)
 * @param string $container_id 카드 리스트 컨테이너 ID
 */
function render_pagination($ajax_url, $current_page, $total_count, $per_page = 12, $container_id = 'card-list') {
    $ajax_url = v4_str($ajax_url);
    $current_page = v4_int($current_page);
    $total_count = v4_int($total_count);
    $per_page = v4_int($per_page);
    $container_id = v4_str($container_id);

    if ($per_page <= 0) {
        $per_page = 12;
    }

    // 전체 페이지 수 계산
    $total_pages = ceil($total_count / $per_page);

    // 현재 페이지가 마지막 페이지면 버튼 숨김
    if ($current_page >= $total_pages) {
        return;
    }

    $next_page = $current_page + 1;
    ?>
    <div class="v4-load-more-wrapper">
        <button
            type="button"
            class="v4-load-more"
            data-ajax-url="<?php echo get_text($ajax_url); ?>"
            data-current-page="<?php echo $current_page; ?>"
            data-next-page="<?php echo $next_page; ?>"
            data-total-pages="<?php echo $total_pages; ?>"
            data-container-id="<?php echo get_text($container_id); ?>"
        >
            <span class="v4-load-more-text">더보기</span>
            <svg class="v4-load-more-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M10 5V15M5 10H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>

        <div class="v4-load-more-info">
            <span><?php echo number_format($current_page * $per_page); ?></span> / <span><?php echo number_format($total_count); ?></span>
        </div>
    </div>

    <style>
    .v4-load-more-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        margin-top: 40px;
        padding-top: 40px;
        border-top: 1px solid #e5e7eb;
    }

    .v4-load-more {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        border: 2px solid #6366f1;
        border-radius: 8px;
        padding: 12px 32px;
        font-size: 16px;
        font-weight: 600;
        color: #6366f1;
        cursor: pointer;
        transition: all 0.3s;
    }

    .v4-load-more:hover {
        background: #6366f1;
        color: #fff;
    }

    .v4-load-more:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .v4-load-more.loading {
        pointer-events: none;
    }

    .v4-load-more.loading .v4-load-more-icon {
        animation: spin 1s linear infinite;
    }

    .v4-load-more-icon {
        flex-shrink: 0;
    }

    .v4-load-more-info {
        font-size: 14px;
        color: #6b7280;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    /* 다크모드 */
    body.dark-theme .v4-load-more-wrapper {
        border-top-color: #374151;
    }

    body.dark-theme .v4-load-more {
        background: #1f2937;
        border-color: #6366f1;
        color: #6366f1;
    }

    body.dark-theme .v4-load-more:hover {
        background: #6366f1;
        color: #fff;
    }

    body.dark-theme .v4-load-more-info {
        color: #9ca3af;
    }

    /* 모바일 */
    @media (max-width: 768px) {
        .v4-load-more-wrapper {
            margin-top: 32px;
            padding-top: 32px;
        }

        .v4-load-more {
            padding: 10px 24px;
            font-size: 14px;
        }

        .v4-load-more-icon {
            width: 18px;
            height: 18px;
        }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var loadMoreBtn = document.querySelector('.v4-load-more');

        if (!loadMoreBtn) {
            return;
        }

        loadMoreBtn.addEventListener('click', function() {
            var btn = this;
            var ajaxUrl = btn.getAttribute('data-ajax-url');
            var nextPage = parseInt(btn.getAttribute('data-next-page'), 10);
            var totalPages = parseInt(btn.getAttribute('data-total-pages'), 10);
            var containerId = btn.getAttribute('data-container-id');
            var container = document.getElementById(containerId);

            if (!container) {
                console.error('Container not found: ' + containerId);
                return;
            }

            // 로딩 상태
            btn.classList.add('loading');
            btn.disabled = true;
            var originalText = btn.querySelector('.v4-load-more-text').textContent;
            btn.querySelector('.v4-load-more-text').textContent = '로딩 중...';

            // AJAX 요청
            var xhr = new XMLHttpRequest();
            xhr.open('GET', ajaxUrl + '&page=' + nextPage, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);

                        if (response.success && response.html) {
                            // 컨테이너에 HTML 추가
                            var tempDiv = document.createElement('div');
                            tempDiv.innerHTML = response.html;

                            while (tempDiv.firstChild) {
                                container.appendChild(tempDiv.firstChild);
                            }

                            // 페이지 번호 업데이트
                            btn.setAttribute('data-current-page', nextPage);
                            btn.setAttribute('data-next-page', nextPage + 1);

                            // 마지막 페이지면 버튼 숨김
                            if (nextPage >= totalPages) {
                                btn.parentElement.style.display = 'none';
                            }

                            // 정보 업데이트
                            var infoSpan = document.querySelector('.v4-load-more-info span:first-child');
                            if (infoSpan && response.current_count) {
                                infoSpan.textContent = response.current_count.toLocaleString();
                            }
                        } else {
                            alert('콘텐츠를 불러올 수 없습니다.');
                        }
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        alert('데이터 처리 중 오류가 발생했습니다.');
                    }
                } else {
                    alert('서버 오류가 발생했습니다.');
                }

                // 로딩 상태 해제
                btn.classList.remove('loading');
                btn.disabled = false;
                btn.querySelector('.v4-load-more-text').textContent = originalText;
            };

            xhr.onerror = function() {
                alert('네트워크 오류가 발생했습니다.');
                btn.classList.remove('loading');
                btn.disabled = false;
                btn.querySelector('.v4-load-more-text').textContent = originalText;
            };

            xhr.send();
        });
    });
    </script>
    <?php
}
