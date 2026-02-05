<?php
if (!defined('_GNUBOARD_')) exit; 

// SEO 설정 가져오기 (이미 정의되어 있을 것임)
$v3_seo_body = sql_fetch(" SELECT seo_gtm_id, seo_extra_body FROM v3_seo_config WHERE seo_page = 'default' ");
$seo_gtm_id = trim($v3_seo_body['seo_gtm_id']);
$seo_extra_body = $v3_seo_body['seo_extra_body'];

// Google Tag Manager (noscript)
if($seo_gtm_id && !defined('G5_IS_ADMIN')) { ?>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $seo_gtm_id; ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<?php }

// Extra Body Script
if($seo_extra_body && !defined('G5_IS_ADMIN')) echo $seo_extra_body.PHP_EOL;
?>
