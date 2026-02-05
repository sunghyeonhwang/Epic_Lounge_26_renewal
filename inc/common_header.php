<?
$http_host = $_SERVER['HTTP_HOST'];
$request_uri = $_SERVER['REQUEST_URI'];
$url = 'http://' . $http_host . $request_uri;
?>
<div class="accessibility"><a href="#n">본문 바로가기</a></div>
<header id="header">
	<div class="wrap">
		<h1 class="logo"><a class="logo_img" href="/index.php"><img src="/resource/images/common/logo.svg" /></a></h1>
		
		<div class="top_search_btn_box">
			<form action="/contents/total_search.php" name="frm_total_sc" id="frm_total_sc">
				<fieldset>
					<div class="top_search_box">
						<input type="text" name="keyword" id="keyword" />
						<button type="submit">
							<img src="/resource/images/common/top_search_btn.svg" />
						</button>
					</div>
				</fieldset>
			</form>
			<a href="https://epiclounge.co.kr/start_unrealengine.php" title="새창" target="_self" class="top_btn2"><strong>시작해요 UE5</strong></a>
		</div>
	</div>
<nav>

<h2 class="skip">주메뉴</h2>
	<div class="lnb_m_nav">
		<button type="button" class="open"><span class="line"></span><span class="line"></span><span class="line"></span></button>
	</div>

<div id="lnb">
	
		<h1 class="m_logo"><a class="logo_img" href="/index.php"><img src="/resource/images/common/foot_logo.svg" /></a></h1>
	
	<div class="m_top_search_btn_box">
		<form action="/contents/total_search.php" name="frm_total_sc" id="frm_total_sc">
			<fieldset>
				<div class="top_search_box">
					<input type="text" name="keyword" id="keyword" />
					<button type="submit">
						<img src="/resource/images/common/top_search_btn.svg" />
					</button>
				</div>
			</fieldset>
		</form>
		<a href="https://epiclounge.co.kr/start_unrealengine.php" title="새창" target="_self" class="top_btn2" style="color:#fff">시작해요 UE5</a>
	</div>



  <ul class="top1menu clearfix">
	
    <li class="depth1"><a href="/contents/news_list.php" target="_self" class="tit depth1_ti"><span>새소식</span></a><div>
            <div class="menu_bg clearfix">   
				 
					<div class="lnb_title_box">
						<span class="title">새소식</span>
						<span class="text">언리얼 엔진 뉴스, 이벤트<br />그리고 영감을 주는 사례를 확인해 보세요.</span>
					</div>
					<div class="lnb_menu_box">
						<ul class="depth2">
							<li class="tit02_1_1"><a href="/contents/news_list.php?category=뉴스" target="_self" class="tit02">뉴스</a></li>
							<li class="tit02_1_2"><a href="/contents/news_list.php?category=업데이트/출시" target="_self" class="tit02">출시 &#38; 업데이트</a></li>
							<li class="tit02_1_3"><a href="/contents/news_list.php?category=블로그" target="_self" class="tit02">블로그</a></li>
						</ul>
					</div>

					<div class="lnb_banner">
		<?
			$rsc_banner_result = sql_query("select * from v3_shop_banner where bn_position = '상단-새소식' order by bn_id desc limit 1");
			foreach($rsc_banner_result as $banner_row){
				$bn_img = G5_DATA_URL.'/banner/'.$banner_row['bn_id'];
		?>
							<a href="<?=$banner_row["bn_url"]?>"><img src="<?=$bn_img?>" width="500" height="170" /></a>
		<?
			}
		?>
					</div>
				</div>
        </div>
    </li>
    <li class="depth1"> <a href="/contents/event_list.php?category=커뮤니티 이벤트" target="_self" class="tit depth1_ti"><span>이벤트</span></a><div>
            <div class="menu_bg clearfix"> 
					<div class="lnb_title_box">
						<span class="title">이벤트</span>
						<span class="text">웨비나, 테크토크, 챌린지와 같은<br />온*오프라인 이벤트를 모두 만나보세요.</span>
					</div>
					
					<div class="lnb_menu_box">
					<!-- <strong><a href="https://epiclounge.co.kr/unrealfest24.php" target="_blank" class="tit02" style="color:#00C2E8; font-weight: 800;">언리얼 페스트 2024 서울</a></strong> -->
						<ul class="depth2">
							<!-- <li class="tit02_2_1"><a href="https://epiclounge.co.kr/unrealfest2025/" target="_blank" class="tit02" style="color:#00C2E8; font-weight: 800;">언리얼 페스트 서울 2025</a></li> -->
							<li class="tit02_2_1"><a href="/contents/event_list.php?category=커뮤니티 이벤트" target="_self" class="tit02">커뮤니티 이벤트</a></li>
							<li class="tit02_2_2"><a href="/contents/global_event_list.php" target="_blank" class="tit02">글로벌 이벤트</a></li>
						</ul>
					</div>
					<div class="lnb_banner">
		<?
			$rsc_banner_result = sql_query("select * from v3_shop_banner where bn_position = '상단-이벤트' order by bn_id desc limit 1");
			foreach($rsc_banner_result as $banner_row){
				$bn_img = G5_DATA_URL.'/banner/'.$banner_row['bn_id'];
		?>
							<a href="<?=$banner_row["bn_url"]?>"><img src="<?=$bn_img?>" width="500" height="170" /></a>
		<?
			}
		?>
					</div>
				</div>
        </div>
    </li>
    <li class="depth1"> <a href="/contents/replay.php" target="_self" class="tit depth1_ti"><span>리소스</span></a><div>
            <div class="menu_bg clearfix">  
					<div class="lnb_title_box">
						<span class="title">리소스</span>
						<span class="text">언리얼 서밋, 웨비나, 테크토크, 무료 콘텐츠, 백서 등<br> 다양한 리소스를 활용해 보세요.</span>
					</div>
					
					<div class="lnb_menu_box">
						<!-- <strong>리소스</strong> -->
						<ul class="depth2">
							<li class="tit02_3_1"><a href="/contents/replay.php" target="_self" class="tit02">다시보기</a></li>
							<li class="tit02_3_3"><a href="/contents/free.php" target="_self" class="tit02">무료 콘텐츠</a></li>
							<li class="tit02_3_2"><a href="/contents/book.php" target="_self" class="tit02">백서</a></li>
							<li class="tit02_3_3"><a href="https://www.unrealengine.com/ko/onlinelearning-courses" target="_blank" class="tit02">에픽 디벨로퍼 커뮤니티 ↗</a></li>
							<br  />
							<!--<li class="tit02_3_4"><a href="https://dev.epicgames.com/community/" target="_blank" class="tit02">DEV 커뮤니티</a><i class="fas fa-external-link-alt"></i></li>-->
						</ul>
					</div>
					<div class="lnb_banner">
		<?
			$rsc_banner_result = sql_query("select * from v3_shop_banner where bn_position = '상단-리소스' order by bn_id desc limit 1");
			foreach($rsc_banner_result as $banner_row){
				$bn_img = G5_DATA_URL.'/banner/'.$banner_row['bn_id'];
		?>
							<a href="<?=$banner_row["bn_url"]?>"><img src="<?=$bn_img?>" width="500" height="170" /></a>
		<?
			}
		?>
					</div>
				</div>
        </div>
    </li>

</ul>
</div>
</nav>
<div class="lnb_close">
	<button type="button" class="close">주메뉴 닫기</button>
</div>
<div class="mask">
</div>
</header>