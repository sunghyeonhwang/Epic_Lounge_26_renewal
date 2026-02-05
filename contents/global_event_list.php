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

<div id="quick_banner">
	<a href="#event_main_sec_1" class="top_btn" onclick="move_top()"><img src="/v3/resource/images/event/arrow_top_btn.png" /></a>
</div>

<div id="sub_visual" class="event_vi">
	<h2>글로벌 이벤트</h2>
	<p>글로벌에서 진행되는 언리얼 엔진 이벤트를 만나보세요.</p>
</div>
	<div class="wrap">
	<!--
		<div class="board_sel_box news_sel">
			<select onchange="change_category()" id="cate_sel">
				<option value="">전체보기</option>
				<option value="진행중" <?=$_GET["status"] == "진행중" ? "selected='selected'":""?>>진행중</option>
				<option value="종료" <?=$_GET["status"] == "종료" ? "selected='selected'":""?>>종료</option>
				<option value="결과발표" <?=$_GET["status"] == "결과발표" ? "selected='selected'":""?>>결과발표</option>
			</select>
		</div>
		-->
		<div class="event_top_cate_list">
			<ul>
				<li class="<?=empty($_GET["status"])? "on":""?>"><a href="/v3/contents/global_event_list.php?category=글로벌%20이벤트">전체</a></li>
				<li class="<?=$_GET["status"] == "진행중" ? "on":""?>"><a href="/v3/contents/global_event_list.php?status=진행중&category=글로벌%20이벤트">진행중</a></li>
				<li class="<?=$_GET["status"] == "종료" ? "on":""?>"><a href="/v3/contents/global_event_list.php?status=종료&category=글로벌%20이벤트">종료</a></li>
				<li class="<?=$_GET["status"] == "결과발표" ? "on":""?>"><a href="/v3/contents/global_event_list.php?status=결과발표&category=글로벌%20이벤트">결과발표</a></li>
			</ul>
		</div>
	</div>
	<script>
	
		function change_category(){
			if($("#cate_sel").val() == ""){
				location.href="?status=&category=<?=$_GET[category]?>";
			}else if($("#cate_sel").val() == "진행중"){
				location.href="?status=진행중&category=<?=$_GET[category]?>";
			}else if($("#cate_sel").val() == "종료"){
				location.href="?status=종료&category=<?=$_GET[category]?>";
			}else if($("#cate_sel").val() == "결과발표"){
				location.href="?status=결과발표&category=<?=$_GET[category]?>";
			}

		}
	</script>

<div class="event_list">
	<div class="wrap">
<div class="event_list_wrap">
<?

	$where = "";
	if($_GET[status]){
		$where .= " and  status = '{$_GET[status]}'";
	}
	if($_GET[category]){
		$where .= " and  category = '{$_GET[category]}'";
	}
	$sql_list = 
		" select * from v3_rsc_global_event_bbs 
		  where 1=1 and display_yn='Y' " 
		  . $where . "
		  order by ordr desc, rsc_bbs_idx desc ";
	$result2 = sql_query($sql_list);
    for ($j=0; $row_list=sql_fetch_array($result2); $j++) {
?>
	<div class="event_list_box">
		<a href="./global_event_view.php?rsc_bbs_idx=<?=$row_list["rsc_bbs_idx"]?>&status=<?=$_GET[status]?>&category=<?=$_GET[category]?>">
			
			<span class="event_img">
				<?php
				$bimg_str = "";
				$bimg = G5_DATA_PATH."/event/{$row_list['thumb_img']}";
				if (file_exists($bimg)  && $row_list['thumb_img'] ) {
					$size = @getimagesize($bimg);
					if($size[0] && $size[0] > 750)
						$width = 750;
					else
						$width = $size[0];

					$bimg_str = G5_DATA_URL.'/event/'.$row_list['thumb_img'];
				}
				if ($bimg_str) {
					?><img src="<?=$bimg_str?>" width="1150" /><?
				}else{
					?><img src="/v3/resource/images/sub/event_list_img.jpg" width="1150"/><?
				}
				?>
				
			</span>
			<span class="event_title">
				<?=$row_list["title"]?>
			</span>
			<span class="event_time">
				<span class="time_text"><?=str_replace("-",".",$row_list["sdate"])?> - <?=str_replace("-",".",$row_list["edate"])?></span>
			</span>
				 <!-- 진행중이면 텍스트 바뀜 -->
		<?
			$str_status = "";
			if($row_list["status"] == "진행중"){
				$str_status = "type1";
			}else if($row_list["status"] == "종료"){
				$str_status = "type2";
			}else if($row_list["status"] == "결과발표"){
				$str_status = "type3";
			}

		?>
			<span class="event_time_cate <?=$str_status?>"><?=$row_list["status"]?></span>
		</a>
	</div><!-- event_list_box -->

<?
	}
?>
	</div><!-- wrap -->
	</div><!-- wrap -->
	
</div><!-- event_list -->
<?php include '../inc/common_footer.php'; ?>
</body>
</html>