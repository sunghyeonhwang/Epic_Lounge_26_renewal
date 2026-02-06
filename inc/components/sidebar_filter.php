<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * 사이드바 체크박스 필터
 *
 * @param array $filter_groups 필터 그룹 배열
 *   [
 *     ['name' => 'cate_industry', 'label' => '산업분야', 'table' => 'v3_rsc_review_category', 'field' => 'cate_industry'],
 *     ['name' => 'cate_product',  'label' => '제품군',   'table' => 'v3_rsc_review_category', 'field' => 'cate_product'],
 *   ]
 * @param string $form_id 폼 ID (AJAX serialize용)
 * @param string $ajax_url AJAX 엔드포인트 URL
 */
function render_sidebar_filter($filter_groups, $form_id, $ajax_url) {
    if (empty($filter_groups)) {
        return;
    }

    $form_id = v4_str($form_id);
    $ajax_url = v4_str($ajax_url);
    ?>
    <aside class="v4-sidebar">
        <div class="v4-sidebar-header">
            <h3>필터</h3>
            <button type="button" class="v4-filter-reset" data-form-id="<?php echo get_text($form_id); ?>">
                초기화
            </button>
        </div>

        <form id="<?php echo get_text($form_id); ?>" data-ajax-url="<?php echo get_text($ajax_url); ?>">
            <?php foreach ($filter_groups as $group):
                $name = v4_str($group['name']);
                $label = v4_str($group['label']);
                $table = v4_str($group['table']);
                $field = v4_str($group['field']);

                // DB에서 DISTINCT 값 조회
                $sql = "SELECT DISTINCT " . sql_real_escape_string($field) . "
                        FROM " . sql_real_escape_string($table) . "
                        WHERE " . sql_real_escape_string($field) . " != ''
                        AND " . sql_real_escape_string($field) . " IS NOT NULL
                        ORDER BY " . sql_real_escape_string($field);
                $result = sql_query($sql);

                if (!$result || sql_num_rows($result) == 0) {
                    continue;
                }
                ?>
                <div class="v4-filter-group" data-filter-group="<?php echo get_text($name); ?>">
                    <div class="v4-filter-group-header">
                        <h4><?php echo get_text($label); ?></h4>
                        <button type="button" class="v4-filter-toggle" aria-label="필터 토글">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </div>

                    <div class="v4-filter-group-body">
                        <?php
                        $idx = 0;
                        while ($row = sql_fetch_array($result)):
                            $value = $row[$field];
                            if (empty($value)) continue;

                            $checkbox_id = get_text($name) . '_' . $idx;
                            $idx++;
                            ?>
                            <label class="v4-filter-checkbox" for="<?php echo $checkbox_id; ?>">
                                <input
                                    type="checkbox"
                                    name="<?php echo get_text($name); ?>[]"
                                    id="<?php echo $checkbox_id; ?>"
                                    value="<?php echo get_text($value); ?>"
                                    data-filter-change="true"
                                >
                                <span class="v4-checkbox-label"><?php echo get_text($value); ?></span>
                            </label>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </form>
    </aside>

    <style>
    .v4-sidebar {
        background: #fff;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .v4-sidebar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    .v4-sidebar-header h3 {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
    }

    .v4-filter-reset {
        background: none;
        border: none;
        color: #6366f1;
        font-size: 14px;
        cursor: pointer;
        padding: 4px 8px;
    }

    .v4-filter-reset:hover {
        text-decoration: underline;
    }

    .v4-filter-group {
        margin-bottom: 20px;
    }

    .v4-filter-group:last-child {
        margin-bottom: 0;
    }

    .v4-filter-group-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        padding: 12px 0;
    }

    .v4-filter-group-header h4 {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    .v4-filter-toggle {
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        transition: transform 0.2s;
    }

    .v4-filter-group.collapsed .v4-filter-toggle {
        transform: rotate(-90deg);
    }

    .v4-filter-group.collapsed .v4-filter-group-body {
        display: none;
    }

    .v4-filter-group-body {
        padding-top: 8px;
    }

    .v4-filter-checkbox {
        display: flex;
        align-items: center;
        padding: 8px 0;
        cursor: pointer;
    }

    .v4-filter-checkbox input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-right: 10px;
        cursor: pointer;
    }

    .v4-checkbox-label {
        font-size: 14px;
        color: #374151;
    }

    /* 다크모드 */
    body.dark-theme .v4-sidebar {
        background: #1f2937;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }

    body.dark-theme .v4-sidebar-header {
        border-bottom-color: #374151;
    }

    body.dark-theme .v4-sidebar-header h3 {
        color: #f9fafb;
    }

    body.dark-theme .v4-filter-group-header h4 {
        color: #f9fafb;
    }

    body.dark-theme .v4-checkbox-label {
        color: #d1d5db;
    }

    /* 모바일 */
    @media (max-width: 768px) {
        .v4-sidebar {
            padding: 16px;
        }

        .v4-filter-group {
            margin-bottom: 16px;
        }

        .v4-filter-group.collapsed .v4-filter-group-body {
            display: none;
        }
    }
    </style>
    <?php
}
