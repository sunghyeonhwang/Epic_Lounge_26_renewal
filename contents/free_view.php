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
	$RData = sql_fetch("select * from v3_rsc_free_bbs where rsc_bbs_idx = {$idx}");
	if($RData["display_yn"] != "Y" && 10 != $member["mb_level"]) alert("게시물이 존재하지 않습니다.");
	$cate = "";
	$cate .= $RData["cate_industry"] . " , ";
	$cate .= $RData["cate_product"]. " , ";
	$cate .= $RData["cate_subject"]. " ";
	$cate .= $RData["cate_engine"]. " ";
	
	$cate = str_replace('|' , ',', $cate);
	$cate = str_replace(', ,' , ',', $cate);
	$cate = str_replace(', ,' , ',', $cate);
	$cate = str_replace(', ,' , ',', $cate);
	$cate = str_replace(', ,' , ',', $cate);

	$arr_cate = explode(",",$cate);
	$arr_save_cate = array();
	foreach($arr_cate as $row_cate ){
		if(trim($row_cate)){
			$arr_save_cate[] = $row_cate;
		}
	}
	$str_cate = implode(",",$arr_save_cate);

	$youtube_url = $RData["youtube_url"];
	$youtube_url = str_replace("http://youtu.be/","",$youtube_url);
	$youtube_url = str_replace("https://youtu.be/","",$youtube_url);
	$youtube_url = str_replace("https://www.youtube.com/watch?v=","",$youtube_url);
	$youtube_url = str_replace("http://www.youtube.com/watch?v=","",$youtube_url);
	$youtube_url = str_replace("http://www.youtube.com/watch?v=","",$youtube_url);

	if (!(strpos($youtube_url,"alert") === false)) {
		$youtube_url = "";
	}
	

	$back_image = "/v3/resource/images/sub/resource_view_bg.jpg";
	$bimg = G5_DATA_PATH."/rsc/{$RData['top_bbs_img']}";
	if (file_exists($bimg)  && $RData['top_bbs_img']) {
		$back_image = G5_DATA_URL.'/rsc/'.$RData['top_bbs_img'];
	}
?>

<div id="quick_banner">
	<ul>
		<li><a href="https://twitter.com/intent/tweet?text=<?=$RData["title"]?>&url=<?=$url?>" title="새창" target="_blank"><img src="/v3/resource/images/event/quick_sns_1.png" /></a></li>
		<li><a href="https://www.facebook.com/sharer/sharer.php?u=<?=$url?>" title="새창" target="_blank"><img src="/v3/resource/images/event/quick_sns_2.png" /></a></li>
		<li><a href="#n" onclick="clip('<?=$url?>'); return false;" title="새창" ><img src="/v3/resource/images/event/quick_sns_3.png" /></a></li>
	</ul>
	<a href="#event_main_sec_1" class="top_btn" onclick="move_top()"><img src="/v3/resource/images/event/arrow_top_btn.png" /></a>
</div>
<div class="resource_view_top" style="background-image:url('<?=$back_image?>')">
</div>
<div class="container"> 
	<div class="wrap">
	<div class="board_view resource_view free_view"> <!-- 뉴스면 뉴스 리소스면 리소스 이벤트면 이벤트라고 클래스 --> 
		
		<div class="board_top_text">
			<span class="board_top_cate"><?=$RData["category"]?></span>
			<span class="board_top_title"><?=$RData["title"]?></span>
			<?
				if($RData["site_url"]){
			?>			<a href="<?=$RData["site_url"]?>" class="board_link_btn" title="새창" target="_blank"><span>바로가기</span></a><?
				}
			?>
		</div>
		<div class="board_cont_box">
		<?
			if($youtube_url){
				?><iframe id="ytplayer" type="text/html" width="800" height="600"
  src="https://www.youtube.com/embed/<?=$youtube_url?>?autoplay=1&origin=https://epiclounge.co.kr/"
  frameborder="0"></iframe><br /><?
			}
		?>

			<?=$RData["contents"]?>	
		</div>
		<div class="board_file_box">
			<a href="<?=$RData["site_url"] ? $RData["site_url"]:"#"?>" target="_blank" title="페이지이동"><span class="file_text"><?=$str_cate?></span><span class="file_icon"><em><?=$RData["site_url"] ? "페이지이동":""?></em></span></a>
		</div>

		<div class="board_ot_list">
			<h3>다른리소스</h3>
			<ul>
			<?
			$other_result = sql_query("select * from v3_rsc_free_bbs  where rsc_bbs_idx<>{$idx} order by abs({$idx} - rsc_bbs_idx) asc,rsc_bbs_idx asc limit 3 ");
				foreach($other_result as $other){
					if($other["thumb_img_url"]){
						$bimg_str = $other["thumb_img_url"];
					}else{
						
						$bimg_str = "";
						$bimg = G5_DATA_PATH."/rsc/{$other['thumb_img']}";
						if (file_exists($bimg)  && $other['thumb_img'] ) {
							$size = @getimagesize($bimg);
							if($size[0] && $size[0] > 750)
								$width = 750;
							else
								$width = $size[0];

							$bimg_str = G5_DATA_URL.'/rsc/'.$other['thumb_img'];
						}
						if (!$bimg_str) {
							$bimg_str = "/v3/resource/images/sub/no_img.jpg";
						}

					}
			?>
				<li>
					<a href="./free_view.php?idx=<?=$other["rsc_bbs_idx"]?>">
						<span class="img_box"><img src="<?=$bimg_str?>" width="335" height="208" /></span>	
						<span class="text_box">
							<span class="title"><?=$other["title"]?></span>
							<span class="cate"><?=$other["category"]?></span>
						</span>
					</a>
				</li>
			<?
				}
			?>
			</ul>
		</div>
		<div class="board_btn_box">
			<a href="./free.php" ><span class="text">목록으로</span><span class="icon"></span></a>
		</div>
	</div>
	</div>
</div>


<?php include '../inc/common_footer.php'; ?>
</body>
</html>