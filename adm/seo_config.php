<?php
$sub_menu = '900200';
include_once ('./_common.php');
include_once (G5_LIB_PATH . '/r2.lib.php');
include_once ('./r2_config.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = 'SEO & 마케팅 설정 관리';
include_once (G5_ADMIN_PATH . '/admin.head.php');

// DB 테이블 컬럼 추가 (존재하지 않을 경우)
$table_exists = sql_query(" SHOW TABLES LIKE 'v3_seo_config' ");
if (sql_num_rows($table_exists) == 0) {
    $sql = " CREATE TABLE IF NOT EXISTS `v3_seo_config` (
      `seo_id` int(11) NOT NULL AUTO_INCREMENT,
      `seo_page` varchar(50) NOT NULL DEFAULT 'default',
      `seo_title` varchar(255) NOT NULL,
      `seo_description` text NOT NULL,
      `seo_keywords` text NOT NULL,
      `seo_og_image` varchar(255) NOT NULL,
      `seo_ga_id` varchar(50) NOT NULL,
      `seo_gtm_id` varchar(50) NOT NULL,
      `seo_pixel_id` varchar(50) NOT NULL,
      `seo_kakao_pixel_id` varchar(50) NOT NULL,
      `seo_naver_verif` varchar(255) NOT NULL,
      `seo_google_verif` varchar(255) NOT NULL,
      `seo_extra_head` text NOT NULL,
      `seo_extra_body` text NOT NULL,
      PRIMARY KEY (`seo_id`),
      UNIQUE KEY `seo_page` (`seo_page`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ";
    sql_query($sql);

    // 기본 데이터 삽입
    $sql = " INSERT INTO `v3_seo_config` (`seo_page`, `seo_title`, `seo_description`, `seo_keywords`, `seo_og_image`) VALUES
    ('default', 'EPIC LOUNGE 2025', '언리얼 엔진 개발자들을 위한 에픽 라운지입니다.', 'Unreal Engine, Epic Games, 언리얼 서밋', 'https://epiclounge.co.kr/img/og_image.png') ";
    sql_query($sql);
} else {
    // 기존 테이블에 컬럼 추가 (이미 존재하지 않는 경우만)
    $columns = array(
        'seo_ga_id' => "varchar(50) NOT NULL AFTER seo_og_image",
        'seo_gtm_id' => "varchar(50) NOT NULL AFTER seo_ga_id",
        'seo_pixel_id' => "varchar(50) NOT NULL AFTER seo_gtm_id",
        'seo_kakao_pixel_id' => "varchar(50) NOT NULL AFTER seo_pixel_id",
        'seo_naver_verif' => "varchar(255) NOT NULL AFTER seo_kakao_pixel_id",
        'seo_google_verif' => "varchar(255) NOT NULL AFTER seo_naver_verif",
        'seo_extra_head' => "text NOT NULL AFTER seo_google_verif",
        'seo_extra_body' => "text NOT NULL AFTER seo_extra_head"
    );
    
    foreach($columns as $col => $type) {
        $check = sql_query(" SHOW COLUMNS FROM `v3_seo_config` LIKE '$col' ");
        if(sql_num_rows($check) == 0) {
            sql_query(" ALTER TABLE `v3_seo_config` ADD `$col` $type ");
        }
    }
}

// 'default' 레코드가 있는지 최종 확인
$seo_check = sql_fetch(" SELECT COUNT(*) as cnt FROM v3_seo_config WHERE seo_page = 'default' ");
if($seo_check['cnt'] == 0) {
    sql_query(" INSERT INTO `v3_seo_config` SET seo_page = 'default', seo_title = 'EPIC LOUNGE 2025' ");
}

// 폼 처리
if (isset($_POST['w']) && $_POST['w'] == 'u') {
    $seo_id = $_POST['seo_id'];
    $seo_title = sql_real_escape_string($_POST['seo_title']);
    $seo_description = sql_real_escape_string($_POST['seo_description']);
    $seo_keywords = sql_real_escape_string($_POST['seo_keywords']);
    $seo_og_image = sql_real_escape_string($_POST['seo_og_image']);
    $seo_ga_id = sql_real_escape_string($_POST['seo_ga_id']);
    $seo_gtm_id = sql_real_escape_string($_POST['seo_gtm_id']);
    $seo_pixel_id = sql_real_escape_string($_POST['seo_pixel_id']);
    $seo_kakao_pixel_id = sql_real_escape_string($_POST['seo_kakao_pixel_id']);
    $seo_naver_verif = sql_real_escape_string($_POST['seo_naver_verif']);
    $seo_google_verif = sql_real_escape_string($_POST['seo_google_verif']);
    $seo_extra_head = sql_real_escape_string($_POST['seo_extra_head']);
    $seo_extra_body = sql_real_escape_string($_POST['seo_extra_body']);

    // OG 이미지 파일 업로드 처리 (R2)
    if (isset($_FILES['seo_og_image_file']) && $_FILES['seo_og_image_file']['name']) {
        $file = $_FILES['seo_og_image_file'];
        if ($file['error'] == UPLOAD_ERR_OK) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = 'og_image/' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($file['name']));
            
            try {
                $r2_url = r2_upload_file($file['tmp_name'], $filename, $mime_type);
                $seo_og_image = sql_real_escape_string($r2_url);
            } catch (Exception $e) {
                alert('R2 업로드 실패: ' . $e->getMessage());
            }
        }
    }

    $sql = " UPDATE `v3_seo_config`
                SET seo_title = '{$seo_title}',
                    seo_description = '{$seo_description}',
                    seo_keywords = '{$seo_keywords}',
                    seo_og_image = '{$seo_og_image}',
                    seo_ga_id = '{$seo_ga_id}',
                    seo_gtm_id = '{$seo_gtm_id}',
                    seo_pixel_id = '{$seo_pixel_id}',
                    seo_kakao_pixel_id = '{$seo_kakao_pixel_id}',
                    seo_naver_verif = '{$seo_naver_verif}',
                    seo_google_verif = '{$seo_google_verif}',
                    seo_extra_head = '{$seo_extra_head}',
                    seo_extra_body = '{$seo_extra_body}'
              WHERE seo_id = '{$seo_id}' ";
    sql_query($sql);
    alert('SEO 및 마케팅 설정이 저장되었습니다.', './seo_config.php');
}

// 데이터 조회 (기본값)
$seo = sql_fetch(" SELECT * FROM v3_seo_config WHERE seo_page = 'default' ");
?>

<style>
/* Modern SEO Config Styles */
.seo_config_container { margin: 20px 0; font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, system-ui, Roboto, sans-serif; }
.seo_card { background: #fff; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); padding: 30px; margin-bottom: 30px; border: 1px solid #edf2f7; }
.seo_card_title { font-size: 18px; font-weight: 700; color: #1a202c; margin-bottom: 25px; display: flex; align-items: center; border-bottom: 2px solid #3498db; padding-bottom: 10px; width: fit-content; }
.seo_card_title i { margin-right: 10px; color: #3498db; }

.frm_table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
.frm_table th { text-align: left; padding: 10px 20px; color: #4a5568; font-weight: 600; width: 240px; vertical-align: top; padding-top: 15px; }
.frm_table td { padding: 5px 20px; }

.modern_input { width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; transition: all 0.2s; background: #f8fafc; }
.modern_input:focus { border-color: #3498db; background: #fff; box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1); outline: none; }
.modern_textarea { height: 80px; resize: vertical; }
.code_textarea { font-family: 'Consolas', 'Monaco', monospace; background: #2d3748; color: #a0aec0; height: 120px; }
.code_textarea:focus { background: #1a202c; color: #fff; }

.frm_info { display: block; margin-top: 8px; font-size: 12px; color: #718096; line-height: 1.4; }
.frm_info b { color: #3182ce; }

.preview_container { margin-top: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.preview_box { border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; background: #fdfdfd; }
.preview_label { font-size: 12px; font-weight: 800; color: #a0aec0; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; }
.preview_label i { margin-right: 6px; }

/* Grid Layout for Tracking IDs */
.tracking_grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.tracking_item { margin-bottom: 15px; }
.tracking_label { display: block; font-size: 13px; font-weight: 600; color: #4a5568; margin-bottom: 8px; }

/* Google Preview */
.google_preview { font-family: arial, sans-serif; background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #eee; }
.google_url { font-size: 14px; color: #202124; margin-bottom: 4px; }
.google_title { font-size: 20px; color: #1a0dab; text-decoration: none; display: block; margin-bottom: 3px; cursor: pointer; }
.google_desc { font-size: 14px; color: #4d5156; line-height: 1.5; }

/* SNS Preview */
.sns_preview { border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; background: #fff; max-width: 500px; }
.sns_img { width: 100%; height: 240px; background-size: cover; background-position: center; background-color: #f7fafc; }
.sns_content { padding: 15px; background: #f8fafc; border-top: 1px solid #edf2f7; }
.sns_domain { font-size: 11px; color: #718096; text-transform: uppercase; font-weight: 700; margin-bottom: 4px; }
.sns_title { font-size: 16px; font-weight: 700; color: #2d3748; margin-bottom: 6px; line-height: 1.3; }
.sns_desc { font-size: 14px; color: #718096; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

.btn_confirm { background: #3182ce; color: #fff; border: none; padding: 14px 40px; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 6px rgba(49, 130, 206, 0.2); }
.btn_confirm:hover { background: #2b6cb0; transform: translateY(-1px); box-shadow: 0 7px 14px rgba(49, 130, 206, 0.3); }

.local_desc01 { background: #ebf8ff; border: 1px solid #bee3f8; border-left: 5px solid #3182ce; padding: 18px 25px; border-radius: 8px; margin-bottom: 30px; }
.local_desc01 p { color: #2a4365; font-weight: 500; margin: 0; line-height: 1.6; }

.platform_icon { width: 24px; vertical-align: middle; margin-right: 8px; }
/* AI Analysis Styles */
.ai_pulse_btn { background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: #fff; border: none; padding: 10px 20px; border-radius: 50px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3); transition: all 0.3s; font-size: 14px; }
.ai_pulse_btn:hover { transform: translateY(-2px) scale(1.05); box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4); }
.ai_pulse_btn i { font-size: 16px; animation: pulse 2s infinite; }

@keyframes pulse {
    0% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.7; transform: scale(1.2); }
    100% { opacity: 1; transform: scale(1); }
}

.ai_report_modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; backdrop-filter: blur(5px); }
.ai_report_content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 600px; background: #fff; border-radius: 20px; padding: 40px; box-shadow: 0 20px 50px rgba(0,0,0,0.2); }
.ai_score_circle { width: 100px; height: 100px; border-radius: 50%; border: 8px solid #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 800; margin: 0 auto 20px; position: relative; }
.ai_score_circle.good { border-color: #10b981; color: #10b981; }
.ai_score_circle.warning { border-color: #f59e0b; color: #f59e0b; }
.ai_score_circle.bad { border-color: #ef4444; color: #ef4444; }

.ai_recommendation_list { margin-top: 25px; list-style: none; padding: 0; }
.ai_recommendation_item { display: flex; gap: 12px; margin-bottom: 12px; font-size: 14px; line-height: 1.5; color: #4b5563; align-items: flex-start; }
.ai_recommendation_item i { margin-top: 3px; font-size: 16px; }
.ai_recommendation_item.pass i { color: #10b981; }
.ai_recommendation_item.fail i { color: #ef4444; }
.ai_recommendation_item.tip i { color: #6366f1; }
</style>

<div class="seo_config_container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div class="local_desc01 local_desc" style="margin-bottom: 0; flex: 1; margin-right: 20px;">
            <p><i class="fa fa-rocket"></i> <b>마케팅 성과를 극대화하세요!</b><br>검색 엔진 노출 최적화뿐만 아니라 Google, Meta, Kakao 등 주요 매체의 트래킹 코드를 한곳에서 관리하여 광고 효율과 유입 분석을 최적화할 수 있습니다.</p>
        </div>
        <button type="button" class="ai_pulse_btn" onclick="runAIAnalysis()">
            <i class="fa fa-magic"></i> AI SEO 분석하기
        </button>
    </div>

    <form name="fseoconfig" id="fseoconfig" action="./seo_config.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="w" value="u">
    <input type="hidden" name="seo_id" value="<?php echo $seo['seo_id']; ?>">

    <!-- 1. 기본 SEO 설정 -->
    <div class="seo_card">
        <div class="seo_card_title"><i class="fa fa-search"></i> 콘텐츠 검색 최적화 (SEO)</div>
        
        <table class="frm_table">
        <tbody>
        <tr>
            <th scope="row"><label for="seo_title">사이트 타이틀 (Title)</label></th>
            <td>
                <input type="text" name="seo_title" value="<?php echo get_text($seo['seo_title']); ?>" id="seo_title" class="modern_input" placeholder="브라우저 탭에 표시되는 제목">
                <span class="frm_info">중요 키워드를 앞쪽에 배치하세요. 권장 길이: <b>50-60자</b> 이내.</span>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="seo_description">검색 요약 설명 (Description)</label></th>
            <td>
                <textarea name="seo_description" id="seo_description" class="modern_input modern_textarea" placeholder="검색 결과에 노출되는 사이트 요약 설명"><?php echo get_text($seo['seo_description']); ?></textarea>
                 <span class="frm_info">클릭을 유도하는 매력적인 요약글을 작성하세요. 권장 길이: <b>한글 140자</b> 내외.</span>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="seo_keywords">메타 키워드 (Keywords)</label></th>
            <td>
                <input type="text" name="seo_keywords" value="<?php echo get_text($seo['seo_keywords']); ?>" id="seo_keywords" class="modern_input" placeholder="콤마(,)로 구분한 키워드">
                <span class="frm_info">최근 검색엔진에서는 중요도가 낮아졌으나, 보조 지표로 활용됩니다. 10개 내외를 권장합니다.</span>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="seo_og_image">공유 썸네일 (OG Image)</label></th>
            <td>
                <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 10px;">
                    <input type="text" name="seo_og_image" value="<?php echo get_text($seo['seo_og_image']); ?>" id="seo_og_image" class="modern_input" placeholder="https://files.epiclounge.co.kr/...">
                </div>
                <div style="background: #f1f5f9; padding: 15px; border-radius: 8px; border: 1px dashed #cbd5e1;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px; color: #475569;">R2 버킷에 직접 업로드</label>
                    <input type="file" name="seo_og_image_file" id="seo_og_image_file" class="modern_input" style="background: #fff;">
                    <span class="frm_info">파일을 선택하고 저장하면 자동으로 <b>Cloudflare R2 (files.epiclounge.co.kr)</b>에 업로드됩니다.</span>
                </div>
                <span class="frm_info">권장 규격: <b>1200x630px</b> (1.91:1 비율).</span>
            </td>
        </tr>
        </tbody>
        </table>

        <!-- 실시간 미리보기 -->
        <div class="preview_container">
            <div class="preview_box">
                <div class="preview_label"><i class="fa fa-google"></i> Google 검색 결과 미리보기</div>
                <div class="google_preview">
                    <div class="google_url">https://epiclounge.co.kr</div>
                    <a href="#" onclick="return false;" class="google_title" id="preview_title"><?php echo $seo['seo_title']; ?></a>
                    <div class="google_desc" id="preview_desc"><?php echo $seo['seo_description']; ?></div>
                </div>
            </div>
            <div class="preview_box">
                <div class="preview_label"><i class="fa fa-share-alt"></i> SNS 공유 미리보기 (미리보기 서비스)</div>
                <div class="sns_preview">
                    <div class="sns_img" id="preview_og_image" style="background-image: url('<?php echo $seo['seo_og_image']; ?>');"></div>
                    <div class="sns_content">
                        <div class="sns_domain">EPICLOUNGE.CO.KR</div>
                        <div class="sns_title" id="preview_sns_title"><?php echo $seo['seo_title']; ?></div>
                        <div class="sns_desc" id="preview_sns_desc"><?php echo $seo['seo_description']; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. 분석 및 광고 트래킹 (Marketer Essentials) -->
    <div class="seo_card">
        <div class="seo_card_title"><i class="fa fa-line-chart"></i> 마케팅 분석 및 트래킹</div>
        
        <div class="tracking_grid">
            <div class="tracking_item">
                <label class="tracking_label"><i class="fa fa-google"></i> Google Analytics 4 (GA4) 측정 ID</label>
                <input type="text" name="seo_ga_id" value="<?php echo get_text($seo['seo_ga_id']); ?>" class="modern_input" placeholder="G-XXXXXXXXXX">
                <span class="frm_info">사이트 유입 분석을 위한 GA4 측정 ID를 입력하세요.</span>
            </div>
            <div class="tracking_item">
                <label class="tracking_label"><i class="fa fa-tags"></i> Google Tag Manager (GTM) ID</label>
                <input type="text" name="seo_gtm_id" value="<?php echo get_text($seo['seo_gtm_id']); ?>" class="modern_input" placeholder="GTM-XXXXXXX">
                <span class="frm_info">다양한 태그를 관리하기 위한 GTM 키를 입력하세요.</span>
            </div>
            <div class="tracking_item">
                <label class="tracking_label"><i class="fa fa-facebook-square"></i> Meta Pixel ID (Facebook)</label>
                <input type="text" name="seo_pixel_id" value="<?php echo get_text($seo['seo_pixel_id']); ?>" class="modern_input" placeholder="숫자 ID 입력">
                <span class="frm_info">페이스북/인스타그램 광고 성과 측정을 위한 픽셀 ID입니다.</span>
            </div>
            <div class="tracking_item">
                <label class="tracking_label"><i class="fa fa-comment"></i> Kakao Pixel ID</label>
                <input type="text" name="seo_kakao_pixel_id" value="<?php echo get_text($seo['seo_kakao_pixel_id']); ?>" class="modern_input" placeholder="숫자 ID 입력">
                <span class="frm_info">카카오 광고 최적화를 위한 픽셀 ID를 입력하세요.</span>
            </div>
        </div>
    </div>

    <!-- 3. 검색 플랫폼 소유권 인증 -->
    <div class="seo_card">
        <div class="seo_card_title"><i class="fa fa-check-circle"></i> 검색 플랫폼 소유권 인증</div>
        
        <div class="tracking_grid">
            <div class="tracking_item">
                <label class="tracking_label"><img src="https://searchadvisor.naver.com/favicon.ico" class="platform_icon"> 네이버 서치어드바이저 (Verif Tag)</label>
                <input type="text" name="seo_naver_verif" value="<?php echo get_text($seo['seo_naver_verif']); ?>" class="modern_input" placeholder="content 값만 입력">
                <span class="frm_info">인증용 메타태그의 <b>content="..."</b> 부분의 값을 입력하세요.</span>
            </div>
            <div class="tracking_item">
                <label class="tracking_label"><img src="https://www.google.com/s2/favicons?domain=search.google.com" class="platform_icon"> Google Search Console (Verif Tag)</label>
                <input type="text" name="seo_google_verif" value="<?php echo get_text($seo['seo_google_verif']); ?>" class="modern_input" placeholder="content 값만 입력">
                <span class="frm_info">구글 서치콘솔 HTML 태그 인증용 content 값을 입력하세요.</span>
            </div>
        </div>
    </div>

    <!-- 4. 추가 스크립트 (Marketer Power User) -->
    <div class="seo_card">
        <div class="seo_card_title"><i class="fa fa-code"></i> 고급 설정 (추가 스크립트)</div>
        <p class="frm_info" style="margin-bottom: 20px;">위에 제공되지 않는 추가 메타태그나 분석 스크립트(핫자, 채널톡 등)를 직접 삽입할 수 있습니다.</p>
        
        <table class="frm_table">
        <tbody>
        <tr>
            <th scope="row"><label for="seo_extra_head">Head 내부 (&lt;/head&gt; 직전)</label></th>
            <td>
                <textarea name="seo_extra_head" id="seo_extra_head" class="modern_input code_textarea" placeholder="<script>...</script> 또는 <meta>..."><?php echo get_text($seo['seo_extra_head']); ?></textarea>
                <span class="frm_info">모든 페이지의 &lt;head&gt; 섹션에 포함됩니다. 주로 트래킹 기본 코드에 사용됩니다.</span>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="seo_extra_body">Body 시작 (&lt;body&gt; 직후)</label></th>
            <td>
                <textarea name="seo_extra_body" id="seo_extra_body" class="modern_input code_textarea" placeholder="<noscript>...</noscript>"><?php echo get_text($seo['seo_extra_body']); ?></textarea>
                <span class="frm_info">GTM Noscript 코드나 레이아웃 직후에 실행되어야 하는 스크립트에 사용됩니다.</span>
            </td>
        </tr>
        </tbody>
        </table>
    </div>

    <div class="btn_fixed_top" style="z-index: 100;">
        <input type="submit" value="마케팅 설정 저장하기" class="btn_confirm">
    </div>

    </form>
</div>

<!-- AI 분석 모달 -->
<div id="aiModal" class="ai_report_modal">
    <div class="ai_report_content">
        <button type="button" onclick="closeAIModal()" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 24px; cursor: pointer; color: #9ca3af;">&times;</button>
        <div style="text-align: center;">
            <div id="aiScore" class="ai_score_circle">0</div>
            <h3 id="aiStatus" style="font-size: 20px; font-weight: 700; color: #1f2937; margin-bottom: 5px;">현황 분석 중...</h3>
            <p style="font-size: 14px; color: #6b7280;">실시간 AI SEO 진단 결과 보고서</p>
        </div>
        
        <div id="aiResults" class="ai_recommendation_list">
            <!-- 결과가 여기에 동적으로 삽입됨 -->
        </div>

        <div style="margin-top: 30px; padding: 15px; background: #fdf2f2; border-radius: 12px; display: none;" id="aiCriticalError">
            <p style="font-size: 13px; color: #991b1b; font-weight: 600;"><i class="fa fa-exclamation-triangle"></i> 치명적 오류 발견: 검색엔진 수집이 원활하지 않을 수 있습니다.</p>
        </div>

        <div style="margin-top: 30px; text-align: center;">
            <button type="button" onclick="closeAIModal()" class="btn_confirm" style="width: 100%;">확인 및 반영하러 가기</button>
        </div>
    </div>
</div>

<script>
$(function() {
    // 실시간 미리보기 업데이트
    function updatePreviews() {
        var title = $('#seo_title').val() || '제목을 입력하세요';
        var desc = $('#seo_description').val() || '설명을 입력하세요';
        var og_img = $('#seo_og_image').val();

        $('#preview_title, #preview_sns_title').text(title);
        $('#preview_desc, #preview_sns_desc').text(desc);
        
        if(og_img) {
            $('#preview_og_image').css('background-image', 'url(' + og_img + ')');
        } else {
            $('#preview_og_image').css('background-image', 'none');
        }
    }

    $('#seo_title, #seo_description, #seo_og_image').on('input', updatePreviews);
});

function runAIAnalysis() {
    var title = $('#seo_title').val();
    var desc = $('#seo_description').val();
    var keywords = $('#seo_keywords').val();
    var og_img = $('#seo_og_image').val();
    var ga_id = $('input[name=seo_ga_id]').val();
    var verif = $('input[name=seo_naver_verif]').val() || $('input[name=seo_google_verif]').val();

    var score = 100;
    var listHtml = '';
    var hasCritical = false;

    // 1. 타이틀 진단
    if(title.length < 5) {
        score -= 20;
        listHtml += '<li class="ai_recommendation_item fail"><i class="fa fa-times-circle"></i> <b>타이틀 부족:</b> 제목이 너무 짧아 검색 신뢰도가 떨어집니다.</li>';
        hasCritical = true;
    } else if(title.length < 30) {
        score -= 5;
        listHtml += '<li class="ai_recommendation_item tip"><i class="fa fa-lightbulb-o"></i> <b>타이틀 팁:</b> 제목을 조금 더 길게(30-50자) 작성하여 정보를 보강하세요.</li>';
    } else if(title.length > 70) {
        score -= 5;
        listHtml += '<li class="ai_recommendation_item tip"><i class="fa fa-lightbulb-o"></i> <b>타이틀 초과:</b> 제목이 너무 길어 검색 결과에서 잘릴 수 있습니다.</li>';
    } else {
        listHtml += '<li class="ai_recommendation_item pass"><i class="fa fa-check-circle"></i> 타이틀 길이가 최적화 범주에 속합니다.</li>';
    }

    // 2. 설명 진단
    if(desc.length < 10) {
        score -= 20;
        listHtml += '<li class="ai_recommendation_item fail"><i class="fa fa-times-circle"></i> <b>설명 부족:</b> 검색 요약 설명이 거의 없어 클릭률이 저하될 수 있습니다.</li>';
        hasCritical = true;
    } else if(desc.length < 80) {
        score -= 5;
        listHtml += '<li class="ai_recommendation_item tip"><i class="fa fa-lightbulb-o"></i> <b>설명 보완:</b> 90~150자 사이로 구체적인 혜택이나 정보를 담아주세요.</li>';
    } else {
        listHtml += '<li class="ai_recommendation_item pass"><i class="fa fa-check-circle"></i> 검색 요약(Description)이 충분히 작성되었습니다.</li>';
    }

    // 3. 키워드 진단
    if(!keywords) {
        score -= 5;
        listHtml += '<li class="ai_recommendation_item tip"><i class="fa fa-lightbulb-o"></i> <b>키워드 추천:</b> 관련 검색어를 입력하면 수집 성능 향상에 도움이 됩니다.</li>';
    }

    // 4. 이미지 진단
    if(!og_img) {
        score -= 15;
        listHtml += '<li class="ai_recommendation_item fail"><i class="fa fa-times-circle"></i> <b>공유 이미지 부재:</b> SNS 공유 시 사이트 이미지가 노출되지 않습니다.</li>';
    } else if(og_img.indexOf('files.epiclounge.co.kr') === -1) {
        listHtml += '<li class="ai_recommendation_item tip"><i class="fa fa-info-circle"></i> 공유 이미지가 외부 서버에 있습니다. R2 직접 업로드를 권장합니다.</li>';
    } else {
        listHtml += '<li class="ai_recommendation_item pass"><i class="fa fa-check-circle"></i> 고성능 R2 공유 썸네일 사용 중입니다.</li>';
    }

    // 5. 마케팅 도구 진단
    if(!ga_id || !verif) {
        score -= 10;
        listHtml += '<li class="ai_recommendation_item tip"><i class="fa fa-line-chart"></i> 분석 데이터 수집을 위해 GA4와 소유권 인증 태그를 연동하세요.</li>';
    } else {
        listHtml += '<li class="ai_recommendation_item pass"><i class="fa fa-check-circle"></i> 마케팅 분석 환경이 잘 갖춰져 있습니다.</li>';
    }

    // 최종 스코어링 클래스
    var scoreClass = 'good';
    var statusText = '최적화 상태: 우수';
    if(score < 60) { scoreClass = 'bad'; statusText = '최적화 상태: 위험'; }
    else if(score < 85) { scoreClass = 'warning'; statusText = '최적화 상태: 보완 필요'; }

    $('#aiScore').text(score).removeClass('good warning bad').addClass(scoreClass);
    $('#aiStatus').text(statusText);
    $('#aiResults').html(listHtml);
    $('#aiCriticalError').toggle(hasCritical);
    $('#aiModal').fadeIn();
}

function closeAIModal() {
    $('#aiModal').fadeOut();
}
</script>

<?php
include_once (G5_ADMIN_PATH . '/admin.tail.php');
?>
