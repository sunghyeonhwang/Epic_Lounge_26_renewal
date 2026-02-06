<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * SNS 공유 버튼
 *
 * @param string $title 공유할 제목
 * @param string $url 공유할 URL
 */
function render_social_share($title, $url) {
    $title = v4_str($title);
    $url = v4_str($url);

    // URL이 상대 경로인 경우 절대 경로로 변환
    if (strpos($url, 'http') !== 0) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $url = $protocol . '://' . $host . $url;
    }

    $encoded_title = urlencode($title);
    $encoded_url = urlencode($url);
    ?>
    <div class="v4-share">
        <button type="button" class="v4-share-toggle" aria-label="공유하기">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M15 6.5C16.3807 6.5 17.5 5.38071 17.5 4C17.5 2.61929 16.3807 1.5 15 1.5C13.6193 1.5 12.5 2.61929 12.5 4C12.5 5.38071 13.6193 6.5 15 6.5Z" stroke="currentColor" stroke-width="1.5"/>
                <path d="M5 12.5C6.38071 12.5 7.5 11.3807 7.5 10C7.5 8.61929 6.38071 7.5 5 7.5C3.61929 7.5 2.5 8.61929 2.5 10C2.5 11.3807 3.61929 12.5 5 12.5Z" stroke="currentColor" stroke-width="1.5"/>
                <path d="M15 18.5C16.3807 18.5 17.5 17.3807 17.5 16C17.5 14.6193 16.3807 13.5 15 13.5C13.6193 13.5 12.5 14.6193 12.5 16C12.5 17.3807 13.6193 18.5 15 18.5Z" stroke="currentColor" stroke-width="1.5"/>
                <path d="M7.19995 11.2L12.8 14.8" stroke="currentColor" stroke-width="1.5"/>
                <path d="M12.8 5.2L7.19995 8.8" stroke="currentColor" stroke-width="1.5"/>
            </svg>
            <span>공유</span>
        </button>

        <div class="v4-share-popup snsbox" style="display: none;">
            <div class="v4-share-header">
                <h4>공유하기</h4>
                <button type="button" class="v4-share-close" aria-label="닫기">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M15 5L5 15M5 5L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            <div class="v4-share-buttons">
                <button type="button" class="v4-share-btn sns_btn" data-sns="twitter" data-title="<?php echo get_text($title); ?>" data-url="<?php echo get_text($url); ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                    </svg>
                    <span>Twitter</span>
                </button>

                <button type="button" class="v4-share-btn sns_btn" data-sns="facebook" data-title="<?php echo get_text($title); ?>" data-url="<?php echo get_text($url); ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span>Facebook</span>
                </button>

                <button type="button" class="v4-share-btn sns_btn" data-sns="naver" data-title="<?php echo get_text($title); ?>" data-url="<?php echo get_text($url); ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M16.273 12.845L7.376 0H0v24h7.727V11.155L16.624 24H24V0h-7.727v12.845z"/>
                    </svg>
                    <span>네이버 블로그</span>
                </button>

                <button type="button" class="v4-share-btn" data-action="copy" data-url="<?php echo get_text($url); ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" stroke-width="2" stroke-linecap="round"/>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>링크 복사</span>
                </button>
            </div>
        </div>
    </div>

    <style>
    .v4-share {
        position: relative;
        display: inline-block;
    }

    .v4-share-toggle {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 8px 16px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
    }

    .v4-share-toggle:hover {
        background: #e5e7eb;
    }

    .v4-share-popup {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        padding: 20px;
        min-width: 280px;
        z-index: 1000;
    }

    .v4-share-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e5e7eb;
    }

    .v4-share-header h4 {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    .v4-share-close {
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        color: #6b7280;
    }

    .v4-share-close:hover {
        color: #111827;
    }

    .v4-share-buttons {
        display: grid;
        gap: 10px;
    }

    .v4-share-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 12px 16px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
        text-align: left;
    }

    .v4-share-btn:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    .v4-share-btn svg {
        flex-shrink: 0;
    }

    .v4-share-btn[data-sns="twitter"] svg {
        color: #1DA1F2;
    }

    .v4-share-btn[data-sns="facebook"] svg {
        color: #1877F2;
    }

    .v4-share-btn[data-sns="naver"] svg {
        color: #03C75A;
    }

    .v4-share-btn[data-action="copy"] svg {
        color: #6b7280;
    }

    /* 다크모드 */
    body.dark-theme .v4-share-toggle {
        background: #374151;
        border-color: #4b5563;
        color: #d1d5db;
    }

    body.dark-theme .v4-share-toggle:hover {
        background: #4b5563;
    }

    body.dark-theme .v4-share-popup {
        background: #1f2937;
        box-shadow: 0 4px 20px rgba(0,0,0,0.4);
    }

    body.dark-theme .v4-share-header {
        border-bottom-color: #374151;
    }

    body.dark-theme .v4-share-header h4 {
        color: #f9fafb;
    }

    body.dark-theme .v4-share-btn {
        background: #374151;
        border-color: #4b5563;
        color: #d1d5db;
    }

    body.dark-theme .v4-share-btn:hover {
        background: #4b5563;
    }

    /* 모바일 */
    @media (max-width: 768px) {
        .v4-share-popup {
            position: fixed;
            top: auto;
            bottom: 0;
            left: 0;
            right: 0;
            border-radius: 12px 12px 0 0;
            max-width: 100%;
        }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 공유 토글
        var shareToggle = document.querySelector('.v4-share-toggle');
        var sharePopup = document.querySelector('.v4-share-popup');
        var shareClose = document.querySelector('.v4-share-close');

        if (shareToggle && sharePopup) {
            shareToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                sharePopup.style.display = sharePopup.style.display === 'none' ? 'block' : 'none';
            });

            if (shareClose) {
                shareClose.addEventListener('click', function() {
                    sharePopup.style.display = 'none';
                });
            }

            // 외부 클릭 시 닫기
            document.addEventListener('click', function(e) {
                if (!sharePopup.contains(e.target) && !shareToggle.contains(e.target)) {
                    sharePopup.style.display = 'none';
                }
            });
        }

        // SNS 공유 버튼
        var snsButtons = document.querySelectorAll('.v4-share-btn.sns_btn');
        snsButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var sns = this.getAttribute('data-sns');
                var title = this.getAttribute('data-title');
                var url = this.getAttribute('data-url');

                if (sns === 'twitter') {
                    window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(title) + '&url=' + encodeURIComponent(url), '_blank', 'width=600,height=400');
                } else if (sns === 'facebook') {
                    if (typeof facebookSns === 'function') {
                        facebookSns(url);
                    } else {
                        window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url), '_blank', 'width=600,height=400');
                    }
                } else if (sns === 'naver') {
                    if (typeof naverSns === 'function') {
                        naverSns(title, url);
                    } else {
                        window.open('https://blog.naver.com/openapi/share?url=' + encodeURIComponent(url) + '&title=' + encodeURIComponent(title), '_blank', 'width=600,height=400');
                    }
                }
            });
        });

        // 링크 복사
        var copyButton = document.querySelector('.v4-share-btn[data-action="copy"]');
        if (copyButton) {
            copyButton.addEventListener('click', function() {
                var url = this.getAttribute('data-url');
                var textarea = document.createElement('textarea');
                textarea.value = url;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();

                try {
                    document.execCommand('copy');
                    alert('링크가 복사되었습니다.');
                } catch (err) {
                    console.error('복사 실패:', err);
                }

                document.body.removeChild(textarea);
            });
        }
    });
    </script>
    <?php
}
