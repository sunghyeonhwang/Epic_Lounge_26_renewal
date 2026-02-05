<?php 
include_once('../common.php');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta property="og:title" content="언리얼 엔진 5" />
  <meta property="og:type" content="website" />
  <meta property="fb:589663484560989"/>
  <meta name="keywords" content="언리얼 엔진 5 "/>
  <meta name="description" content="언리얼 엔진 5">
  <meta property="og:url" content="https://www.epiclounge.co.kr" />
  <meta property="og:image" content="https://unrealsummit16.cafe24.com/og/ue5.jpg" />
  <meta property="og:image:height" content="630px" />
  <meta property="og:image:width" content="1200px" />
  <meta property="og:description" content="언리얼 엔진 5는 모든 산업 전반의 크리에이터가 멋진 리얼타임 콘텐츠와 경험을 제공할 수 있도록 돕습니다." />
  <meta name="naver-site-verification" content="374c7b8b38a950b57cfd67d5e14696efd33bc057" />
    <title>에픽 라운지 | 다시보기</title>
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

<?

	$where = "";

	$page = $_GET["page"];
	if(empty($page)) $page = 1;

	$pageunit = $_GET["pageunit"];
	if(empty($pageunit)) $pageunit  = 9999;

	$page_limit = $pageunit * $page;
	$start = ($page-1) * $pageunit;
	
	$sql_list_cnt = 
		" select count(*) cnt from v3_rsc_review_bbs 
		  where 1=1 " 
		  . $where . " ";
	$cnt= sql_fetch($sql_list_cnt);

	$sql_list = 
		" select * from v3_rsc_review_bbs 
		  where 1=1 " 
		  . $where . "
		  order by ordr desc, rsc_bbs_idx desc ";
	$sql_list .= " limit {$start}, ".$pageunit;
	$result2 = sql_query($sql_list);
	$total_page  = ceil($cnt["cnt"] / $pageunit);  // 전체 페이지 계산
	$qstr = "pageunit=".$pageunit."&sc_text=".$_GET["sc_text"];

?>
<div id="sub_visual" class="resource_vi">
	<h2>리소스</h2>
	<p>언리얼 서밋, 웨비나, 테크토크, 무료 콘텐츠, 백서 등의 다양한 리소스를 활용해 보세요.</p>
</div>
<div class="container resource_replay"> 
<div id="resource_search">
	<form action="#n"  onsubmit="return keyword_sc()" name="" id="">
		<fieldset>
			<div class="serach_in_box">
				<input type="text" class="in_text" id="sc_keyword" name="keyword" value="" />
				<input type="image" class="in_img" src="/v3/resource/images/sub/replay/search_btn.png" />
			</div>
			<div class="search_word_box">
			<?
				$RData = sql_fetch("select 
							(select count(*) from v3_rsc_book_bbs where  display_yn='Y') as book_cnt,
							(select count(*) from v3_rsc_review_bbs where  display_yn='Y') as replay_cnt,
							(select count(*) from v3_rsc_free_bbs where  display_yn='Y') as free_cnt
							from dual"
						);
			?>
				<ul>
					<li class="on"><a href="./replay.php"><span class="text_box">다시보기</span><span class="num_box"><?=$RData["replay_cnt"]?></span></a></li>
					<li><a href="./free.php"><span class="text_box">무료 콘텐츠</span><span class="num_box"><?=$RData["free_cnt"]?></span></a></li>
					<li><a href="./book.php"><span class="text_box">백서</span><span class="num_box"><?=$RData["book_cnt"]?></span></a></li>
				</ul>
			</div>
		</fieldset>

	</form>
</div>
<div class="resource_wrap wrap">
<div class="side">
<form id="frm_side" name="frm_side">
	<input type="hidden" name="keyword" id="frm_keyword" value="" />
    <button type="button" class="reset">리셋</button>
    <section class="part">
        <h3>산업분야</h3>
        <button class="acc_toggle" type="button">닫기</button>
        <div class="part_con">
<?

	$sql = " select * from v3_rsc_review_category where rsc_type='산업분야' order by sort asc ";
	$result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
            <div class="check_wrap">
                <input name="cate_industry[]" id="check1-<?=$i+1?>" type="checkbox" value="<?=$row["rsc_name"]?>" onchange="refresh_serach()">
                <label for="check1-<?=$i+1?>" class="label"><?=$row["rsc_name"]?></label>
            </div>
<?
	}
?>
        </div>
    </section>
    <section class="part">
        <h3>제품군</h3>
        <button type="button" class="acc_toggle">닫기</button>
        <div class="part_con">
<?

	$sql = " select * from v3_rsc_review_category where rsc_type='제품군' order by sort asc ";
	$result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
            <div class="check_wrap">
                <input name="cate_product[]" id="check2-<?=$i+1?>" type="checkbox" value="<?=$row["rsc_name"]?>" onchange="refresh_serach()">
                <label for="check2-<?=$i+1?>" class="label"><?=$row["rsc_name"]?></label>
            </div>
<?
	}
?>
        </div>
    </section>
    <section class="part subject">
        <h3>주제</h3>
        <button type="button" class="acc_toggle">닫기</button>
        <div class="part_con">
<?

	$sql = " select * from v3_rsc_review_category where rsc_type='주제' order by sort asc ";
	$result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
            <div class="check_wrap type2">
                <input name="cate_subject[]" id="check3-<?=$i+1?>" type="checkbox" value="<?=$row["rsc_name"]?>" onchange="refresh_serach()">
                <label for="check3-<?=$i+1?>" class="label"><?=$row["rsc_name"]?></label>
            </div>
<?
	}
?>
        </div>
    </section>
    <section class="part level">
        <h3>난이도</h3>
        <button  type="button" class="acc_toggle">닫기</button>
        <div class="part_con">
<?

	$sql = " select * from v3_rsc_review_category where rsc_type='난이도' order by sort asc ";
	$result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
            <div class="check_wrap type2">
                <input name="cate_difficult[]" id="check4-<?=$i+1?>" type="checkbox" value="<?=$row["rsc_name"]?>" onchange="refresh_serach()">
                <label for="check4-<?=$i+1?>" class="label"><?=$row["rsc_name"]?></label>
            </div>
<?
	}
?>
        </div>
    </section>
	</form>
</div>
<div class="content">
    <div class="summary">
        <p>총 <span class="cnt_div"><?=$cnt["cnt"]?></span>의 검색결과가 있습니다.</p>
        <div class="summary_btn_wrap">
            <button class="summary_btn list ">리스트 타입</button>
            <button class="summary_btn gallery active">갤러리 타입</button>
        </div>
    </div>
    <div class="sub_visual">
        <div class="visual_slide">
		<?
			$rsc_banner_result = sql_query("select * from v3_shop_banner where bn_position = '다시보기'");
			foreach($rsc_banner_result as $banner_row){
				$bn_img = G5_DATA_URL.'/banner/'.$banner_row['bn_id'];
		?>
			<a href="<?=$banner_row["bn_url"]?>" target="_blank"><div class="visual_item" style="background-image:url(<?=$bn_img?>)"><?=$banner_row["bn_id"]?></div></a>
		<?
			}
		?>
        </div>
		<!--
        <div class="visual_ctrl">
            <button type="button" class="prev">이전</button>
            <button type="button" class="next">다음</button>
        </div>-->
    </div>
    <ul class="skip">
    </ul>
    <ul class="board_list list_type">
    </ul>
    <div class="more_wrap">
        <button type="button" class="more"><span>더보기</span></button>
    </div>
</div>
<script>
    // reset
    var $html = $('html'),
        $side = $('.side'),
        $sideCheckbox = $side.find('input[type=checkbox]'),
        $sideResetBtn = $side.find('.reset');
    $sideResetBtn.on('click', function(){
        $sideCheckbox.each(function(){
            $(this).prop('checked',false);
        })
    })

    //visual
    var $subVisual = $('.sub_visual'),
        $subVisualSlide = $subVisual.find('.visual_slide'),
        $subVisualCtrl = $subVisual.find('.visual_ctrl'),
        $subVisualPrev = $subVisualCtrl.find('.prev'),
        $subVisualNext = $subVisualCtrl.find('.next');
    $subVisualSlide.slick({
        draggable: false,
        swipe: false,
        touchMove: false,
		dots: true, 
        speed: 450,
  autoplaySpeed: 15000,
        slidesToShow: 1,
        slidesToScroll: 1,
		
        prevArrow: $subVisualPrev,
        nextArrow: $subVisualNext,
        pauseOnArrowClick: true,
        pauseOnDirectionKeyPush: true,
        pauseOnSwipe: true,
        autoplay: true,
        playText: "재생",
        pauseText: "정지",
        infinite: false,
        responsive: [
            {
                breakpoint: 1001,
                settings: {
                    draggable: true,
                    swipe: true,
                    touchMove: true,
                    swipeToSlide: true,
                },
            },
        ],
    });

    //리스트 데이터
    var boardData = []

    var $summaryBtn = $('.summary_btn_wrap .summary_btn'),
        $boardList = $('.board_list'),
        $moreWrap = $('.more_wrap'),
        $moreBtn = $moreWrap.find('.more'),
        listCount = <?=$cnt["cnt"]?>,
        elem = '';

    function createElem(thisData){
        elem = $('<li class="list_item">\n' +
            '    <a href="'+thisData.link+'" class="list_anchor" target="'+thisData.target+'">\n' +
            '        <div class="img"></div>\n' +
            '        <div class="con_wrap">\n' +
            '            <div class="title"></div>\n' +
            '            <div class="con">\n' +
            '                 <span class="label"><em>다시보기</em></span>\n' +
            '                <ul class="category"></ul>\n' +
            '            </div>\n' +
            '        </div>\n' +
            '    </a>\n' +
            '</li>');
        elem.addClass('list'+thisData.id);
        elem.find('.list_anchor').attr('href', thisData.anchor);
        elem.find('.img').css('backgroundImage','url('+thisData.image+')');
        elem.find('.img').text(thisData.title + ' 이미지');
        elem.find('.title').text(thisData.title);
        thisData.category.map(function(thisElem){
            return elem.find('.category').append('<li>'+thisElem+'<li>');
        });
    }
    function mountElem(startCount,endCount){
        for(var i = startCount+1;i <= endCount;i++){
            var thisData = boardData.find(function(eachData,eachCount){
                return eachData.id === i;
            });
            if(thisData){
                createElem(thisData);
                $boardList.append(elem);
            } else {
                return;
            }
        }
    }

    // 리스트 형태 변화
    $summaryBtn.on('click', function(){
        var $this = $(this);
        $summaryBtn.removeClass('active');
        $moreWrap.show();
        if($this.hasClass('list')){
            $this.addClass('active');
            $('html').attr('data-board-type','list');
            $boardList.removeClass('gallery_type').addClass('list_type').html('');
            
            refresh_serach();
        } else if($this.hasClass('gallery')){
            $this.addClass('active');
            $boardList.removeClass('list_type').addClass('gallery_type').html('');
            $('html').attr('data-board-type','gallery');
            
            refresh_serach();
        }
    });
    $moreBtn.on('click', function(){
		var hold_top = $("#frm_side").offset().top;
		var hold_left = $("#frm_side").offset().left;
		console.log(hold_top);
		console.log(hold_left);
        var currentCount = listCount;
        if($html.attr('data-board-type') === 'list'){
            listCount += 8;
        } else if($html.attr('data-board-type') === 'gallery'){
            listCount += 9;
        }
        mountElem(currentCount,listCount);

        if(boardData.length < listCount-1){
            $moreWrap.hide();
        }
		$("#frm_side").removeClass("fixed_footer");
    })
	$( document ).ready(function() {
            $('html').attr('data-board-type','gallery');
            $boardList.removeClass('list_type').addClass('gallery_type').html('');
			refresh_serach();
	});
	function refresh_serach(){
        var formData = $("#frm_side").serialize();
		$.ajax({
			type : 'post',
			url : "./replay_ajax.php",
            type : 'POST', 
            data : formData, 
			success : function(json) {
				boardData = JSON.parse(json);
				boardData = boardData.data ;
				
				$moreWrap.show();
				if($('html').attr('data-board-type') == 'list'){
					$boardList.removeClass('gallery_type').addClass('list_type').html('');
					mountElem(0,8);
					listCount = 8;
					if(boardData.length <= listCount){
						$moreWrap.hide();
					}
				} else{
					$boardList.removeClass('list_type').addClass('gallery_type').html('');
					mountElem(0,9);
					listCount = 9;
					if(boardData.length <= listCount){
						$moreWrap.hide();
					}
				}


				$(".cnt_div").html(boardData.length);
			},error : function(e) {
				console.log(e);
			}
		});

	}
	function keyword_sc(){
		var keyword = $("#sc_keyword").val();
		$("#frm_keyword").val(keyword);
		refresh_serach();
		return false;
	}
</script>
    </div>
</div>
<script>
$(function() {
	$.fn.Scrolling = function(obj, tar) {
		var _this = this;
		var filter = "win16|win32|win64|mac";
		$(window).scroll(function(e) {
			if(navigator.platform){
			
					var end = obj + tar;
					// 현재상태 고정해제 없음

					//end  = $("#footer").offset().top - $(window).height();
					//푸터가 화면에 보일대 스크롤고정 해제
					if($(window).scrollTop() >= obj)
					{
						_this.addClass("fixed");
						$(".fixed").css("left",$(".side").offset().left );
						$(".fixed").css("width",$(".side").width() );
					}else{
						_this.removeClass("fixed");
						_this.css("left",0);
					}
					var bottom_distance = 0;
					var end_point = ($("#footer").offset().top+100 - ($(window).height() + bottom_distance));
	
					if($(window).scrollTop() >= end_point) {
						_this.addClass("fixed_footer");
					}else{
						_this.removeClass("fixed_footer");
					}
					/*if($(window).scrollTop() >= end) {
						_this.removeClass("fixed");
					}*/
			}
		});
	};
	
	$("#frm_side").Scrolling($("#frm_side").offset().top-100, 999999999);
});
</script>
<?php include '../inc/common_footer.php'; ?>
</body>
</html>