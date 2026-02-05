<?php
// 에러 표시 활성화 (임시 디버깅)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$sub_menu = '900100';
include_once ('./_common.php');

// 권한 체크 (배열이 없을 경우 대비)
if (isset($auth[$sub_menu])) {
    auth_check($auth[$sub_menu], 'w');
} else {
    // 로그인 여부만 체크
    if (!isset($member['mb_id']) || $member['mb_id'] == '') {
        alert('로그인이 필요합니다.');
    }
}

$w = isset($_REQUEST['w']) ? $_REQUEST['w'] : '';
$vm_id = isset($_REQUEST['vm_id']) ? (int) $_REQUEST['vm_id'] : 0;

// 디버깅 (임시)
if ($w == 'add_form') {
    error_log('Visual Main: add_form 요청 받음');
}

// 테이블 구조 확인 및 재생성
$check_table = sql_query("SHOW TABLES LIKE 'v3_visual_main'", false);
if (!sql_num_rows($check_table)) {
    // 테이블이 없으면 생성
    $sql = " CREATE TABLE v3_visual_main (
        vm_id int(11) NOT NULL AUTO_INCREMENT,
        vm_bg_type varchar(10) NOT NULL DEFAULT 'video',
        vm_bg_url text NOT NULL,
        vm_title_img text NOT NULL,
        vm_title_text text NOT NULL,
        vm_btn_text varchar(255) NOT NULL,
        vm_link_url text NOT NULL,
        vm_display tinyint(4) NOT NULL DEFAULT '1',
        vm_order int(11) NOT NULL DEFAULT '0',
        vm_duration int(11) NOT NULL DEFAULT '5000',
        vm_reg_dt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (vm_id)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ";
    sql_query($sql);
} else {
    // 테이블이 있으면 구조 확인 (누락된 컬럼 추가)
    $columns = [];
    $result = sql_query("SHOW COLUMNS FROM v3_visual_main", false);
    while ($row = sql_fetch_array($result)) {
        $columns[] = $row['Field'];
    }

    // 필요한 컬럼들이 있는지 확인하고 없으면 추가
    if (!in_array('vm_bg_type', $columns)) {
        @sql_query("ALTER TABLE v3_visual_main ADD vm_bg_type varchar(10) NOT NULL DEFAULT 'video'");
    }
    if (!in_array('vm_title_img', $columns)) {
        @sql_query("ALTER TABLE v3_visual_main ADD vm_title_img text NOT NULL");
    }
    if (!in_array('vm_title_text', $columns)) {
        @sql_query("ALTER TABLE v3_visual_main ADD vm_title_text text NOT NULL");
    }
    if (!in_array('vm_reg_dt', $columns)) {
        @sql_query("ALTER TABLE v3_visual_main ADD vm_reg_dt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP");
    }
}

// 초기 데이터 (테이블이 비어있을 경우만)
$row = sql_fetch(' select count(*) as cnt from v3_visual_main ');
if ($row['cnt'] == 0) {
    for ($i = 1; $i <= 3; $i++) {
        $sql = " insert into v3_visual_main set 
            vm_bg_type = 'video',
            vm_bg_url = 'https://cdn.pixabay.com/vimeo/321876543/flower-23456.mp4',
            vm_title_text = 'Visual Title {$i}',
            vm_btn_text = '자세히 보기',
            vm_link_url = '#',
            vm_display = 1,
            vm_order = {$i},
            vm_duration = 5000,
            vm_reg_dt = '" . G5_TIME_YMDHIS . "'
        ";
        sql_query($sql);
    }
}

// 새 슬라이드 추가는 visual_main_process.php에서 AJAX로 처리

// 개별 삭제 (GET 방식)
if ($w == 'd' && $vm_id) {
    sql_query(" delete from v3_visual_main where vm_id = '{$vm_id}' ");
    alert('삭제되었습니다.', './visual_main.php');
    exit; // 스크립트 종료
}

// 폼 처리 (POST 방식)
if ($_POST['w'] == 'u') {
    // 선택 삭제
    if ($_POST['act_button'] == '선택삭제') {
        $count = count($_POST['chk']);
        for ($i = 0; $i < $count; $i++) {
            $k = $_POST['chk'][$i];
            $del_id = $_POST['vm_id'][$k];
            $sql = " delete from v3_visual_main where vm_id = '{$del_id}' ";
            sql_query($sql);
        }
        alert('선택한 슬라이드가 삭제되었습니다.', './visual_main.php');
        exit; // 스크립트 종료
    }
    // 일괄 수정/저장
    else {
        $count = count($_POST['vm_id']);
        for ($i = 0; $i < $count; $i++) {
            $vm_id = $_POST['vm_id'][$i];
            $vm_bg_type = $_POST['vm_bg_type'][$i];
            $vm_bg_url = $_POST['vm_bg_url'][$i];
            $vm_title_img = $_POST['vm_title_img'][$i];
            $vm_title_text = $_POST['vm_title_text'][$i];
            $vm_btn_text = $_POST['vm_btn_text'][$i];
            $vm_link_url = $_POST['vm_link_url'][$i];
            $vm_display = $_POST['vm_display'][$i];
            $vm_order = $_POST['vm_order'][$i];
            $vm_duration = $_POST['vm_duration'][$i] * 1000;  // 초 단위 입력을 밀리초로 저장

            $sql = " update v3_visual_main set
                vm_bg_type = '{$vm_bg_type}',
                vm_bg_url = '{$vm_bg_url}',
                vm_title_img = '{$vm_title_img}',
                vm_title_text = '{$vm_title_text}',
                vm_btn_text = '{$vm_btn_text}',
                vm_link_url = '{$vm_link_url}',
                vm_display = '{$vm_display}',
                vm_order = '{$vm_order}',
                vm_duration = '{$vm_duration}'
                where vm_id = '{$vm_id}'
            ";
            sql_query($sql);
        }
        alert('성공적으로 저장되었습니다.', './visual_main.php');
        exit; // 스크립트 종료
    }
}

// 목록 조회
$sql = ' SELECT * FROM v3_visual_main ORDER BY vm_order ASC ';
$result = sql_query($sql);

// 요약 정보 계산
$sql_total = ' SELECT count(*) as cnt FROM v3_visual_main ';
$row_total = sql_fetch($sql_total);
$cnt_total = $row_total['cnt'];

$sql_show = ' SELECT count(*) as cnt FROM v3_visual_main WHERE vm_display = 1 ';
$row_show = sql_fetch($sql_show);
$cnt_show = $row_show['cnt'];

$cnt_hide = $cnt_total - $cnt_show;
include_once (G5_ADMIN_PATH . '/admin.head.php');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
<link rel="stylesheet" as="style" crossorigin href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css" />
<style>
    :root {
        --primary-color: #4F46E5;
        --primary-hover: #4338ca;
        --text-main: #111827;
        --text-sub: #6B7280;
        --bg-color: #F9FAFB;
        --border-color: #E5E7EB;
        --radius: 12px;
        --input-bg: #F3F4F6;
    }

    #wrapper { height: auto !important; min-height: 100%; }
    .vm-container { font-family: 'Pretendard', sans-serif; color: var(--text-main); padding: 20px; max-width: 1400px; margin: 0 auto; min-height: 800px; padding-bottom: 150px; }
    
    /* 카드 관리자 내 통제 */
    .vm-container * { box-sizing: border-box; }
    .vm-container a { text-decoration: none; color: inherit; }
    .vm-container ul, .vm-container li { list-style: none; padding: 0; margin: 0; }

    /* 헤더 영역 */
    .page-header { margin-bottom: 30px; }
    .page-title { font-size: 24px; font-weight: 800; margin-bottom: 8px; color: #111; }
    .page-desc { font-size: 14px; color: var(--text-sub); }

    /* Summary Box (Flat & Border) */
    .summary-box {
        display: flex; gap: 16px; margin-top: 24px; margin-bottom: 24px;
    }
    .summary-item {
        background: #fff; padding: 20px 24px;
        border-radius: var(--radius);
        flex: 1; display: flex; flex-direction: column; align-items: flex-start;
        border: 1px solid #E5E7EB;
    }
    .summary-label { font-size: 13px; color: var(--text-sub); font-weight: 500; margin-bottom: 8px; }
    .summary-value { font-size: 24px; font-weight: 700; color: #111; letter-spacing: -0.5px; }
    .text-primary { color: var(--primary-color); }
    .text-gray { color: #9CA3AF; }

    /* Action Bar */
    .action-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .action-left { display: flex; gap: 12px; }
    
    /* Buttons (Large & Flat) */
    .btn-lg {
        height: 48px; padding: 0 24px; font-size: 15px; font-weight: 600;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 8px; cursor: pointer; transition: all 0.2s;
        text-decoration: none; border: none;
    }
    .btn-new { background-color: #111; color: #fff !important; min-width: 160px; }
    .btn-new:hover { background-color: #333; }
    
    .btn-del { background-color: #fff; color: #EF4444 !important; border: 1px solid #E5E7EB; }
    .btn-del:hover { background-color: #FFF5F5; border-color: #FECACA; }
    
    .btn-save { 
        background-color: #2563EB; color: #fff !important; min-width: 160px;
    }
    .btn-save:hover { background-color: #1D4ED8; }

    /* Card List */
    .card-list { display: flex; flex-direction: column; gap: 16px; }
    
    .slide-card {
        background: #fff; border-radius: var(--radius);
        border: 1px solid #E5E7EB;
        padding: 0; display: flex; overflow: hidden;
        transition: border-color 0.2s;
        margin-bottom: 16px;
    }
    .slide-card:hover { border-color: #9CA3AF; }

    /* Drag Handle */
    .card-handle {
        width: 44px; background-color: #F9FAFB; border-right: 1px solid #E5E7EB;
        cursor: grab; display: flex; flex-direction: column; align-items: center; justify-content: center;
        color: #9CA3AF; gap: 4px; flex-shrink: 0;
    }
    .card-handle:active { cursor: grabbing; background-color: #F3F4F6; }
    .seq-num { font-size: 12px; font-weight: 700; color: #6B7280; }

    /* Content Area */
    .card-content { flex: 1; padding: 24px; display: flex; flex-direction: column; gap: 24px; }

    /* Top Controls Row */
    .top-controls {
        display: flex; align-items: center; gap: 24px;
        padding-bottom: 20px; border-bottom: 1px solid #F3F4F6;
        border-bottom-style: dashed;
        flex-wrap: wrap;
    }
    .control-item { display: flex; align-items: center; gap: 10px; }
    .control-label { font-size: 13px; font-weight: 600; color: var(--text-sub); flex-shrink: 0; }

    /* Modern Flat Inputs */
    .vm-input {
        background-color: #F9FAFB;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 0 16px;
        height: 40px;
        font-size: 14px;
        color: var(--text-main);
        outline: none; transition: all 0.2s;
        width: 100%; font-weight: 400;
    }
    .vm-input:focus {
        background-color: #fff;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1);
    }
    .input-with-btn { display: flex; gap: 8px; width: 100%; position: relative; }
    
    /* Upload Button - Styled */
    .btn-upload {
        height: 40px; width: 40px;
        display: flex; align-items: center; justify-content: center;
        background-color: #707070; border: none;
        border-radius: 8px; cursor: pointer; color: #fff;
        transition: all 0.2s; flex-shrink: 0;
    }
    .btn-upload:hover { background-color: #505050; transform: scale(1.05); }

    /* Toggle Switch (Restored) */
    .toggle-switch {
        position: relative; display: inline-block; width: 44px; height: 24px;
    }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .slider {
        position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
        background-color: #E5E7EB; transition: .2s; border-radius: 34px;
    }
    .slider:before {
        position: absolute; content: ""; height: 18px; width: 18px;
        left: 3px; bottom: 3px; background-color: white;
        transition: .2s; border-radius: 50%; box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    input:checked + .slider { background-color: var(--primary-color); }
    input:checked + .slider:before { transform: translateX(20px); }

    /* Media Grid */
    .media-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    .input-block { display: flex; flex-direction: column; gap: 8px; }
    .input-block label { font-size: 13px; font-weight: 600; color: #374151; }

    /* Preview Area */
    .preview-area {
        height: 180px; background-color: #333;
        border-radius: 8px; border: 1px solid #444;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden; position: relative;
    }
    .preview-area video { width: 100%; height: 100%; object-fit: contain; }
    
    /* Video Controls Overlay */
    .video-overlay {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        display: flex; align-items: center; justify-content: center;
        background: rgba(0,0,0,0.3); opacity: 0; transition: opacity 0.2s;
        pointer-events: none;
    }
    .preview-area:hover .video-overlay { opacity: 1; pointer-events: auto; }
    .vid-ctrl-btn {
        width: 44px; height: 44px; border-radius: 50%; background: rgba(255,255,255,0.9);
        color: #333; display: flex; align-items: center; justify-content: center;
        cursor: pointer; border: none; font-size: 18px; transition: all 0.2s;
    }
    .vid-ctrl-btn:hover { transform: scale(1.1); background: #fff; }

    /* Delete Action */
    .card-actions {
        width: 60px; display: flex; align-items: center; justify-content: center;
        border-left: 1px solid #E5E7EB; background-color: #fff;
    }
    .btn-icon {
        width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center;
        background-color: #FEF2F2; color: #EF4444; transition: all 0.2s; border: 1px solid #FEE2E2;
    }
    .btn-icon:hover { background-color: #EF4444; color: #fff; border-color: #EF4444; transform: scale(1.1); }
    
    /* Sortable Placeholder */
    .ui-sortable-placeholder { 
        height: 380px; 
        background: #F3F4F6 !important; 
        border: 2px dashed #E5E7EB !important; 
        border-radius: var(--radius);
        visibility: visible !important; 
        margin-bottom: 16px;
    }
    .ui-sortable-helper {
         opacity: 0.95;
    }

    @media (max-width: 1024px) {
        .media-grid, .link-area { grid-template-columns: 1fr; }
        .list-header { display: none; }
        .slide-card { grid-template-columns: 50px 1fr 50px; }
        .options-row { flex-direction: column; align-items: flex-start; gap: 12px; }
    }

    .figma-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background-color: #F3F4F6;
        border: 1px solid #E5E7EB;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #4B5563;
        transition: all 0.2s;
        margin-left: 10px;
    }
    .figma-btn:hover {
        background-color: #E5E7EB;
        color: #111827;
        transform: translateY(-1px);
    }
    .figma-btn i { font-size: 14px; }
</style>

<div class="vm-container">
    <header class="page-header">
        <h1 class="page-title">메인 비주얼 슬라이드 관리</h1>
        <p class="page-desc" style="display:flex; align-items:center;">
            피그마 매뉴얼, 수정 템플릿
            <a href="https://www.figma.com/design/lLrbrV902HBkuloKeu4v8a/%EC%97%90%ED%94%BD%EB%9D%BC%EC%9A%B4%EC%A7%80_%EC%9C%A0%EC%A7%80%EB%B3%B4%EC%88%98?node-id=14-100&t=GVwP4AitKWF1ogFy-1" target="_blank" class="figma-btn" title="피그마에서 보기">
                <i class="fa-regular fa-clone"></i> 복사하기
            </a>
        </p>
        
        <div class="summary-box">
            <div class="summary-item">
                <span class="summary-label">등록된 비주얼</span>
                <span class="summary-value"><?php echo number_format($cnt_total); ?>개</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">라이브(노출) 비주얼</span>
                <span class="summary-value text-primary"><?php echo number_format($cnt_show); ?>개</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">숨김 비주얼</span>
                <span class="summary-value text-gray"><?php echo number_format($cnt_hide); ?>개</span>
            </div>
        </div>

        <div class="action-bar">
             <div class="action-left">
                 <button type="button" class="btn-lg btn-new" onclick="add_new_slide()">+ 새 슬라이드 추가</button>
                 <button type="button" class="btn-lg btn-del" onclick="if(confirm('선택한 항목을 삭제하시겠습니까?')) { document.getElementById('act_button').value = '선택삭제'; document.fvisualmain.submit(); }">선택 삭제</button>
             </div>
             <button type="submit" form="fvisualmain" class="btn-lg btn-save">변경사항 저장</button>
        </div>
    </header>

    <form name="fvisualmain" id="fvisualmain" action="./visual_main.php" onsubmit="return fvisualmain_submit(this);" method="post">
    <input type="hidden" name="w" value="u">
    <input type="hidden" name="act_button" id="act_button" value="">

    <div id="vm-list">
    <?php
    for ($i = 0; $row = sql_fetch_array($result); $i++) {
        $duration_sec = $row['vm_duration'] / 1000;
        $is_checked = ($row['vm_display'] == 1) ? 'checked' : '';
        ?>
        <div class="slide-card">
            <input type="hidden" name="vm_id[<?php echo $i; ?>]" value="<?php echo $row['vm_id']; ?>">
            
            <!-- 드래그 핸들 (순서) -->
            <div class="card-handle">
                <input type="checkbox" name="chk[]" value="<?php echo $i; ?>" class="chk-box">
                <div style="flex:1;"></div>
                <i class="fa-solid fa-grip-vertical drag-icon"></i>
                <span class="seq-num"><?php echo $i + 1; ?></span>
                <div style="flex:1;"></div>
                <input type="hidden" name="vm_order[<?php echo $i; ?>]" value="<?php echo $row['vm_order']; ?>">
            </div>

            <!-- 컨텐츠 영역 -->
            <div class="card-content">
                <!-- 상단 컨트롤: 노출, 시간, 버튼텍스트, 링크 -->
                <div class="top-controls">
                    <!-- 노출 상태 -->
                    <div class="control-item">
                        <span class="control-label">노출</span>
                        <label class="toggle-switch">
                            <input type="hidden" name="vm_display[<?php echo $i; ?>]" value="<?php echo $row['vm_display']; ?>">
                            <input type="checkbox" <?php echo $is_checked; ?> onchange="this.previousElementSibling.value = this.checked ? 1 : 0;">
                            <span class="slider"></span>
                        </label>
                    </div>

                    <!-- 시간 -->
                    <div class="control-item">
                        <span class="control-label">시간(초)</span>
                        <input type="number" name="vm_duration[<?php echo $i; ?>]" value="<?php echo $duration_sec; ?>" class="vm-input" style="width: 70px;">
                    </div>

                    <!-- 버튼 텍스트 -->
                    <div class="control-item" style="flex:1; min-width:150px;">
                        <span class="control-label">버튼명</span>
                        <input type="text" name="vm_btn_text[<?php echo $i; ?>]" value="<?php echo $row['vm_btn_text']; ?>" class="vm-input full-width" placeholder="예: 자세히 보기">
                    </div>

                    <!-- 링크 URL -->
                    <div class="control-item" style="flex:2; min-width:200px;">
                        <span class="control-label">링크 URL</span>
                        <input type="text" name="vm_link_url[<?php echo $i; ?>]" value="<?php echo $row['vm_link_url']; ?>" class="vm-input full-width" placeholder="https://...">
                    </div>

                    <!-- 타이틀 텍스트 -->
                    <div class="control-item" style="flex:2; min-width:200px;">
                        <span class="control-label">타이틀 텍스트</span>
                        <input type="text" name="vm_title_text[<?php echo $i; ?>]" value="<?php echo $row['vm_title_text']; ?>" class="vm-input full-width" placeholder="비주얼 타이틀 입력">
                    </div>
                    
                    <!-- 미디어 타입 (hidden) -->
                    <input type="hidden" name="vm_bg_type[<?php echo $i; ?>]" value="video">
                </div>

                <!-- 하단 미디어 그리드: 5:5 비율로 크게 -->
                <div class="media-grid">
                    <!-- 배경 미디어 -->
                    <div class="input-block">
                        <label>배경 미디어 (Cloudflare R2)</label>
                        <div class="input-with-btn">
                            <input type="text" name="vm_bg_url[<?php echo $i; ?>]" value="<?php echo $row['vm_bg_url']; ?>" class="vm-input full-width" placeholder="URL을 입력하거나 파일을 업로드하세요">
                            <button type="button" class="btn-upload" title="파일 업로드" onclick="upload_r2(this)">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </button>
                        </div>
                        <?php if ($row['vm_bg_url']) { ?>
                             <div class="preview-area">
                                <?php if (preg_match('/\.(mp4|webm|ogg)$/i', $row['vm_bg_url'])) { ?>
                                    <video id="vid_<?php echo $i; ?>" src="<?php echo $row['vm_bg_url']; ?>" class="preview-thumb" muted loop preload="none" playsinline></video>
                                    <div class="video-overlay" style="opacity: 1; pointer-events: auto; background: rgba(0,0,0,0.1);">
                                        <button type="button" class="vid-ctrl-btn" onclick="toggle_vid('vid_<?php echo $i; ?>', this)">
                                            <i class="fa-solid fa-play"></i>
                                        </button>
                                    </div>
                                <?php } else { ?>
                                    <img src="<?php echo $row['vm_bg_url']; ?>" class="preview-thumb">
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                             <!-- 비디오일 경우 썸네일 표시가 어려우므로 패스하거나 아이콘 표시 -->
                             <div class="preview-area" style="background:#333; color:#666; font-size:12px; flex-direction:column; border:1px solid #444;">
                                <i class="fa-solid fa-play-circle" style="font-size:32px; margin-bottom:10px;"></i>
                                <span>No Media Asset</span>
                             </div>
                        <?php } ?>
                    </div>

                    <!-- 타이틀 이미지 -->
                    <div class="input-block">
                        <label>타이틀 이미지 (Overlay)</label>
                        <div class="input-with-btn">
                            <input type="text" name="vm_title_img[<?php echo $i; ?>]" value="<?php echo $row['vm_title_img']; ?>" class="vm-input full-width" placeholder="URL을 입력하세요">
                            <button type="button" class="btn-upload" title="파일 업로드" onclick="upload_r2(this)">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </button>
                        </div>
                        <?php if ($row['vm_title_img']) { ?>
                        <div class="preview-area">
                            <img src="<?php echo $row['vm_title_img']; ?>" class="preview-thumb">
                        </div>
                        <?php } else { ?>
                             <div class="preview-area"><span class="preview-placeholder">No Image</span></div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- 액션 (개별 삭제) -->
            <div class="card-actions">
                <a href="./visual_main.php?w=d&amp;vm_id=<?php echo $row['vm_id']; ?>" onclick="return confirm('정말 삭제하시겠습니까?');" class="btn-icon" title="삭제">
                    <i class="fa-solid fa-trash-can"></i>
                </a>
            </div>
        </div>
    <?php } ?>
    
    <?php if ($i == 0) { ?>
        <div style="padding:100px 0; text-align:center; color:#999;">등록된 슬라이드가 없습니다. 추가 버튼을 눌러보세요!</div>
    <?php } ?>
    </div>

    </form>
    
    <!-- R2 업로드를 위한 히든 파일 인풋 -->
    <input type="file" id="r2_file_input" style="display:none;" accept="image/*,video/*">
</div>

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(function() {
    // jQuery UI Sortable
    $("#vm-list").sortable({
        handle: ".card-handle",
        placeholder: "ui-sortable-placeholder",
        axis: "y",
        update: function(event, ui) {
            $("#vm-list .slide-card").each(function(index) {
                $(this).find(".seq-num").text(index + 1);
                $(this).find("input[name^='vm_order']").val(index + 1);
            });
        }
    });

    // 파일 선택 이벤트
    $('#r2_file_input').on('change', function() {
        var file = this.files[0];
        if (!file) return;

        var targetInput = $(this).data('target');
        var formData = new FormData();
        formData.append('file', file);

        var $btn = $(this).data('btn');
        var originalHtml = $btn.html();
        
        // 로딩 표시
        $btn.html('<i class="fa-solid fa-spinner fa-spin"></i>').prop('disabled', true);

        $.ajax({
            url: './r2_uploader.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                try {
                    var data = JSON.parse(res);
                    if (data.url) {
                        // URL 입력
                        $(targetInput).val(data.url);

                        // 성공 메시지
                        alert('업로드 완료!\n\nURL: ' + data.url + '\n\n변경사항을 저장하려면 "변경사항 저장" 버튼을 클릭하세요.');

                        // 페이지 리로드 대신 입력창 하이라이트
                        $(targetInput).css('background-color', '#d4edda').css('border-color', '#28a745');
                        setTimeout(function() {
                            $(targetInput).css('background-color', '').css('border-color', '');
                        }, 2000);

                        // 미리보기 동적 업데이트
                        var $card = $('#r2_file_input').data('card');
                        update_preview($card, data.url);
                    } else if (data.error) {
                        alert('에러: ' + data.error);
                    }
                } catch (e) {
                    alert('응답 파싱 에러: ' + e.message + '\n\n응답: ' + res);
                }
            },
            error: function() {
                alert('업로드 중 통신 에러가 발생했습니다.');
            },
            complete: function() {
                $btn.html(originalHtml).prop('disabled', false);
            }
        });
    });
});

// 새 슬라이드 추가 (AJAX)
function add_new_slide() {
    if (!confirm('새 슬라이드를 추가하시겠습니까?')) return;

    $.ajax({
        url: './visual_main_process.php',
        type: 'POST',
        data: { action: 'add_slide' },
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                alert(res.message);
                // 페이지 리로드 (새 슬라이드를 보여주기 위함)
                location.reload();
            } else {
                alert('오류: ' + res.message);
            }
        },
        error: function() {
            alert('슬라이드 추가 중 통신 오류가 발생했습니다.');
        }
    });
}

function upload_r2(btn) {
    var $btn = $(btn);
    var $input = $btn.closest('.input-with-btn').find('input');
    var $card = $btn.closest('.slide-card');

    $('#r2_file_input').data('target', $input).data('btn', $btn).data('card', $card).click();
}

/**
 * 미리보기 동적 업데이트
 */
function update_preview($card, url) {
    if (!$card || !url) return;

    // 파일 확장자로 타입 판단
    var ext = url.split('.').pop().toLowerCase();
    var isVideo = ['mp4', 'webm', 'ogg'].indexOf(ext) !== -1;
    var isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].indexOf(ext) !== -1;

    // input의 name으로 어느 미리보기 영역인지 판단
    var inputName = $('#r2_file_input').data('target').attr('name');
    var $previewArea;

    if (inputName && inputName.indexOf('vm_bg_url') !== -1) {
        // 배경 미디어 미리보기
        $previewArea = $card.find('.media-grid .input-block:first .preview-area');
    } else if (inputName && inputName.indexOf('vm_title_img') !== -1) {
        // 타이틀 이미지 미리보기
        $previewArea = $card.find('.media-grid .input-block:last .preview-area');
    }

    if (!$previewArea || $previewArea.length === 0) return;

    // 기존 내용 제거
    $previewArea.empty();

    // 새 미리보기 추가
    if (isVideo) {
        var videoId = 'vid_' + Date.now();
        var videoHtml = '<video id="' + videoId + '" src="' + url + '" class="preview-thumb" muted loop preload="none" playsinline></video>';
        videoHtml += '<div class="video-overlay" style="opacity: 1; pointer-events: auto; background: rgba(0,0,0,0.1);">';
        videoHtml += '<button type="button" class="vid-ctrl-btn" onclick="toggle_vid(\'' + videoId + '\', this)">';
        videoHtml += '<i class="fa-solid fa-play"></i>';
        videoHtml += '</button></div>';
        $previewArea.html(videoHtml);
    } else if (isImage) {
        $previewArea.html('<img src="' + url + '" class="preview-thumb">');
    } else {
        $previewArea.html('<span class="preview-placeholder">미리보기 불가</span>');
    }

    // 스타일 초기화 (No Media Asset 스타일 제거)
    $previewArea.css({
        'background': '',
        'color': '',
        'font-size': '',
        'flex-direction': '',
        'border': ''
    });
}

/**
 * 비디오 재생/일시정지 토글
 */
function toggle_vid(vid_id, btn) {
    var vid = document.getElementById(vid_id);
    var $icon = $(btn).find('i');
    if (vid.paused) {
        vid.play();
        $icon.removeClass('fa-play').addClass('fa-pause');
    } else {
        vid.pause();
        $icon.removeClass('fa-pause').addClass('fa-play');
    }
}

function fvisualmain_submit(f) {
    // 디버깅: 폼 데이터 확인
    console.log('폼 제출:', f);
    console.log('w 값:', f.w.value);

    // act_button이 설정되지 않았다면 일반 저장
    if (!f.act_button.value) {
        f.act_button.value = '';
    }

    return true;
}
</script>

<?php
include_once (G5_ADMIN_PATH . '/admin.tail.php');
?>
