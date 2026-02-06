<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * 키워드 검색바
 *
 * @param string $keyword 현재 검색 키워드 (기존 값 유지용)
 * @param string $form_id 폼 ID
 * @param string $placeholder placeholder 텍스트
 */
function render_search_bar($keyword = '', $form_id = 'search-form', $placeholder = '키워드를 입력하세요') {
    $keyword = v4_str($keyword);
    $form_id = v4_str($form_id);
    $placeholder = v4_str($placeholder);
    ?>
    <div class="v4-search-bar">
        <form id="<?php echo get_text($form_id); ?>" class="v4-search-form" method="get">
            <div class="v4-search-input-wrapper">
                <svg class="v4-search-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16zM18 18l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>

                <input
                    type="text"
                    name="keyword"
                    class="v4-search-input"
                    placeholder="<?php echo get_text($placeholder); ?>"
                    value="<?php echo get_text($keyword); ?>"
                    autocomplete="off"
                >

                <?php if ($keyword): ?>
                <button type="button" class="v4-search-clear" aria-label="검색어 지우기">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M15 5L5 15M5 5L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
                <?php endif; ?>

                <button type="submit" class="v4-search-submit">
                    검색
                </button>
            </div>
        </form>
    </div>

    <style>
    .v4-search-bar {
        width: 100%;
        margin-bottom: 32px;
    }

    .v4-search-form {
        width: 100%;
    }

    .v4-search-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        background: #fff;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 16px;
        transition: border-color 0.2s;
    }

    .v4-search-input-wrapper:focus-within {
        border-color: #6366f1;
    }

    .v4-search-icon {
        flex-shrink: 0;
        color: #9ca3af;
        margin-right: 12px;
    }

    .v4-search-input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 16px;
        color: #111827;
        background: transparent;
    }

    .v4-search-input::placeholder {
        color: #9ca3af;
    }

    .v4-search-clear {
        flex-shrink: 0;
        background: none;
        border: none;
        padding: 4px;
        margin-right: 8px;
        cursor: pointer;
        color: #9ca3af;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .v4-search-clear:hover {
        color: #6b7280;
    }

    .v4-search-submit {
        flex-shrink: 0;
        background: #6366f1;
        border: none;
        border-radius: 8px;
        padding: 8px 20px;
        font-size: 15px;
        font-weight: 600;
        color: #fff;
        cursor: pointer;
        transition: background 0.2s;
    }

    .v4-search-submit:hover {
        background: #4f46e5;
    }

    /* 다크모드 */
    body.dark-theme .v4-search-input-wrapper {
        background: #1f2937;
        border-color: #374151;
    }

    body.dark-theme .v4-search-input-wrapper:focus-within {
        border-color: #6366f1;
    }

    body.dark-theme .v4-search-input {
        color: #f9fafb;
    }

    body.dark-theme .v4-search-input::placeholder {
        color: #6b7280;
    }

    body.dark-theme .v4-search-icon,
    body.dark-theme .v4-search-clear {
        color: #6b7280;
    }

    body.dark-theme .v4-search-clear:hover {
        color: #9ca3af;
    }

    /* 모바일 */
    @media (max-width: 768px) {
        .v4-search-bar {
            margin-bottom: 24px;
        }

        .v4-search-input-wrapper {
            padding: 10px 12px;
        }

        .v4-search-input {
            font-size: 14px;
        }

        .v4-search-submit {
            padding: 6px 16px;
            font-size: 14px;
        }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var searchForm = document.getElementById('<?php echo get_text($form_id); ?>');
        var searchInput = searchForm ? searchForm.querySelector('.v4-search-input') : null;
        var searchClear = searchForm ? searchForm.querySelector('.v4-search-clear') : null;

        // 검색어 지우기
        if (searchClear && searchInput) {
            searchClear.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.focus();
                this.style.display = 'none';
            });
        }

        // 입력 시 지우기 버튼 표시/숨김
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                if (searchClear) {
                    searchClear.style.display = this.value ? 'flex' : 'none';
                }
            });

            // Enter 키 이벤트
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchForm.submit();
                }
            });
        }

        // 폼 submit 이벤트
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                var keyword = searchInput ? searchInput.value.trim() : '';

                if (!keyword) {
                    e.preventDefault();
                    alert('검색어를 입력하세요.');
                    if (searchInput) {
                        searchInput.focus();
                    }
                    return false;
                }

                // AJAX 요청 여부 확인 (data-ajax 속성이 있으면 AJAX로 처리)
                if (this.hasAttribute('data-ajax')) {
                    e.preventDefault();
                    var ajaxUrl = this.getAttribute('data-ajax-url') || this.action;

                    // AJAX 요청 구현은 v4.app.js에서 처리하도록 이벤트 발생
                    var event = new CustomEvent('v4SearchSubmit', {
                        detail: {
                            keyword: keyword,
                            url: ajaxUrl,
                            form: this
                        }
                    });
                    document.dispatchEvent(event);
                }
            });
        }
    });
    </script>
    <?php
}
