<?php
if (!defined('_GNUBOARD_')) exit; 

// SEO 설정 가져오기
$v3_seo = sql_fetch(" SELECT * FROM v3_seo_config WHERE seo_page = 'default' ");
$seo_title = ($v3_seo['seo_title']) ? $v3_seo['seo_title'] : $config['cf_title'];
if(!$seo_title) $seo_title = "에픽 라운지";
$seo_description = ($v3_seo['seo_description']) ? $v3_seo['seo_description'] : "";
$seo_keywords = ($v3_seo['seo_keywords']) ? $v3_seo['seo_keywords'] : "";
$seo_og_image = ($v3_seo['seo_og_image']) ? $v3_seo['seo_og_image'] : "";

// 추가 마케팅 필드
$seo_ga_id = trim($v3_seo['seo_ga_id']);
$seo_gtm_id = trim($v3_seo['seo_gtm_id']);
$seo_pixel_id = trim($v3_seo['seo_pixel_id']);
$seo_kakao_pixel_id = trim($v3_seo['seo_kakao_pixel_id']);
$seo_naver_verif = trim($v3_seo['seo_naver_verif']);
$seo_google_verif = trim($v3_seo['seo_google_verif']);
$seo_extra_head = $v3_seo['seo_extra_head'];

if($seo_description) echo '<meta name="description" content="'.get_text($seo_description).'">'.PHP_EOL;
if($seo_keywords) echo '<meta name="keywords" content="'.get_text($seo_keywords).'">'.PHP_EOL;
echo '<meta property="og:title" content="'.get_text($seo_title).'">'.PHP_EOL;
if($seo_description) echo '<meta property="og:description" content="'.get_text($seo_description).'">'.PHP_EOL;
if($seo_og_image) echo '<meta property="og:image" content="'.get_text($seo_og_image).'">'.PHP_EOL;
echo '<meta property="og:type" content="website">'.PHP_EOL;
echo '<meta property="og:url" content="https://epiclounge.co.kr'.$_SERVER['REQUEST_URI'].'">'.PHP_EOL;

// 소유권 인증
if($seo_naver_verif) echo '<meta name="naver-site-verification" content="'.$seo_naver_verif.'">'.PHP_EOL;
if($seo_google_verif) echo '<meta name="google-site-verification" content="'.$seo_google_verif.'">'.PHP_EOL;

// 1. Google Analytics 4
if($seo_ga_id && !defined('G5_IS_ADMIN')) { ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $seo_ga_id; ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '<?php echo $seo_ga_id; ?>');
</script>
<?php }

// 2. Google Tag Manager
if($seo_gtm_id && !defined('G5_IS_ADMIN')) { ?>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo $seo_gtm_id; ?>');</script>
<?php }

// 3. Meta Pixel
if($seo_pixel_id && !defined('G5_IS_ADMIN')) { ?>
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '<?php echo $seo_pixel_id; ?>');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=<?php echo $seo_pixel_id; ?>&ev=PageView&noscript=1"
/></noscript>
<?php }

// 4. Kakao Pixel
if($seo_kakao_pixel_id && !defined('G5_IS_ADMIN')) { ?>
<script type="text/javascript" charset="UTF-8" src="//t1.daumcdn.net/adfit/static/kp.js"></script>
<script type="text/javascript">
      kakaoPixel('<?php echo $seo_kakao_pixel_id; ?>').pageView();
</script>
<?php }

// Extra Head Script
if($seo_extra_head && !defined('G5_IS_ADMIN')) echo $seo_extra_head.PHP_EOL;
?>
