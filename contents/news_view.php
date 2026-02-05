<?php 
include_once('../common.php');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta property="og:type" content="website">
    <meta property="og:title" content="에픽게임즈">
    <meta property="og:description" content="에픽게임즈">
    <meta property="og:image" content="">
    <meta property="og:url" content="">
    <title>에픽게임즈</title>
    <link rel="stylesheet" href="/v3/resource/css/sub.css">
    <script src="/v3/resource/js/jquery-3.4.1.min.js"></script>
    <script src="/v3/resource/js/slick.min.js"></script>
    <script src="/v3/resource/js/ScrollTrigger.min.js"></script>
    <script src="/v3/resource/js/jquery.menu.min.js"></script>
    <script src="/v3/resource/js/jquery.responsive.min.js"></script>
    <script src="/v3/resource/js/common.js"></script>
    <script src="/v3/resource/js/sub.js"></script>

	
</head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-174668456-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'UA-174668456-1');
</script>
<!-- Global site tag (gtag.js) - Google Ads: 760706945 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-760706945"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());
	gtag('config', 'AW-760706945');
</script>
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '413080733349618');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=413080733349618&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->
<body>
<?php include '../inc/common_header.php'; ?>
<?
	$RData = sql_fetch("select * from v3_rsc_news_bbs where rsc_bbs_idx = {$idx}");
	if($RData["display_yn"] != "Y" && 10 != $member["mb_level"]) alert("게시물이 존재하지 않습니다.");
	
?>

<div id="quick_banner">
	<ul>
		<li><a href="https://twitter.com/intent/tweet?text=<?=$RData["title"]?>&url=<?=$url?>" title="새창" target="_blank"><img src="/v3/resource/images/event/quick_sns_1.png" /></a></li>
		<li><a href="https://www.facebook.com/sharer/sharer.php?u=<?=$url?>" title="새창" target="_blank"><img src="/v3/resource/images/event/quick_sns_2.png" /></a></li>
		<li><a href="#n" onclick="clip('<?=$url?>'); return false;" title="새창" ><img src="/v3/resource/images/event/quick_sns_3.png" /></a></li>
	</ul>
	<a href="#event_main_sec_1" class="top_btn" onclick="move_top()"><img src="/v3/resource/images/event/arrow_top_btn.png" /></a>
</div>

<?

	$back_image = "/v3/resource/images/sub/resource_view_bg.jpg";
	$bimg = G5_DATA_PATH."/news/{$RData['top_bbs_img']}";
	if (file_exists($bimg) && $RData['top_bbs_img'] ) {
		$back_image = G5_DATA_URL.'/news/'.$RData['top_bbs_img'];
	}

	$tag = $RData["tag"];
	$arr_tag = explode(",",$tag);

	$cate_class = "1";
	switch($other["category"]){
		case "뉴스": $cate_class = "1"; break;
		case "업데이트/출시": $cate_class = "2"; break;
		case "블로그": $cate_class = "3"; break;
	}
?>
<div class="resource_view_top" style="background-image:url('<?=$back_image?>')">
</div>
<div class="container"> 
	<div class="wrap">
	<div class="board_view news_view"> <!-- 뉴스면 뉴스 리소스면 리소스 이벤트면 이벤트라고 클래스 --> 
		
		<div class="board_top_text">
			<span class="board_top_cate cate_<?=$cate_class?>"><?=$RData["category"]?></span>
			<span class="board_top_title"><?=$RData["title"]?></span>
			<span class="board_top_date"><?=str_replace("-",".",substr($RData["reg_date"],0,10))?></span>
		</div>
		<div class="board_cont_box">
			<?=$RData["contents"]?>	
		</div>
		<div class="board_tag_list bd_tag_list">
			<ul>
			<?
				foreach($arr_tag as $tag){
			?>
				<li><?=$tag?></li>
			<?
				}
			?>
			</ul>
		</div>
		

		<div class="board_ot_list">
			<h3>다른 새소식</h3>
			<ul>
			<?
			$other_result = sql_query("select * from v3_rsc_news_bbs  where rsc_bbs_idx<>{$idx} order by abs({$idx} - rsc_bbs_idx) asc ,rsc_bbs_idx asc limit 3 ");
				foreach($other_result as $other){
					$cate_class = "1";
					switch($other["category"]){
						case "뉴스": $cate_class = "1"; break;
						case "업데이트/출시": $cate_class = "2"; break;
						case "블로그": $cate_class = "3"; break;
					}
					
				$link = "";
				if($other["site_url"]){
					$link = $other["site_url"];
				}else{
					$link = "./news_view.php?idx=".$other["rsc_bbs_idx"];
				}

				if($other["thumb_img_url"]){
					$bimg_str = $other["thumb_img_url"];
				}else{
					
					$bimg_str = "";
					$bimg = G5_DATA_PATH."/news/{$other['thumb_img']}";
					if (file_exists($bimg)  && $other['thumb_img'] ) {
						$size = @getimagesize($bimg);
						if($size[0] && $size[0] > 750)
							$width = 750;
						else
							$width = $size[0];

						$bimg_str = G5_DATA_URL.'/news/'.$other['thumb_img'];
					}
					if (!$bimg_str) {
						$bimg_str = "/v3/resource/images/sub/no_img.jpg";
					}
				}

			?>
				<li>
					<a href="<?=$link?>" target="<?=$other["site_url"] ? "_blank":""?>">
						<span class="img_box"><img src="<?=$bimg_str?>" width="335" height="208" /></span>	
						<span class="text_box">
							<span class="title"><?=$other["title"]?></span>
							<span class="cate cate_<?=$cate_class?>"><?=$other["category"]?></span>
						</span>
					</a>
				</li>
			<?
				}
			?>
			</ul>
		</div>
		<div class="board_btn_box">
			<a href="./news_list.php" ><span class="text">목록으로</span><span class="icon"></span></a>
		</div>
	</div>
	</div>
</div>


<?php include '../inc/common_footer.php'; ?>
</body>
</html>