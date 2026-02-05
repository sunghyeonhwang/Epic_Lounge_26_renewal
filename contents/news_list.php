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
    <meta property="og:title" content="에픽 라운지 | 새소식">
    <meta property="og:description" content="에픽 라운지 | 새소식">
    <meta property="og:image" content="">
    <meta property="og:url" content="">
    <title>에픽 라운지 | 새소식</title>
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

<div id="sub_visual" class="news_vi">
	<h2>새소식</h2>
	<p>언리얼 엔진 뉴스, 이벤트 그리고 영감을 주는 사례를 확인해 보세요.</p>
</div>

<div class="wrap">
	<div class="board_sel_box news_sel">
		<select onchange="change_category()" id="cate_sel">
			<option value="">전체보기</option>
			<option value="뉴스" <?=$_GET["category"] == "뉴스" ? "selected='selected'":""?>>뉴스</option>
			<option value="업데이트/출시"  <?=$_GET["category"] == "업데이트/출시" ? "selected='selected'":""?>>업데이트 / 출시</option>
			<option value="블로그"  <?=$_GET["category"] == "블로그" ? "selected='selected'":""?>>블로그</option>
		</select>
	</div>
	<div class="news_list">
				
	</div><!-- wrap -->
	<div class="board_btn_box more margin_t_0 margin_b_100">
		<a href="#n" class="more_btn"><span class="text">더보기</span><span class="icon"></span></a>
	</div>
</div><!-- event_list -->



<script>

    //리스트 데이터
    var boardData = []

    var $summaryBtn = $('.summary_btn_wrap .summary_btn'),
        $boardList = $('.news_list'),
        $moreWrap = $('.board_btn_box'),
        $moreBtn = $moreWrap.find('.more_btn'),
        listCount = 9,
        elem = '';

    function createElem(thisData){
        elem = $('<div class="news_list_box">\n' +
            '    <a href="'+thisData.link+'" class="list_anchor">\n' +
            '        <span class="news_img"></span>\n' +
            '        <span class="news_cate ">뉴스</span>\n' +
            '        <span class="news_title"></span>\n' +
            '        <span class="news_time">\n' +
            '			2022-02-14\n' +
            '        </span>\n' +
            '    </a>\n' +
            '</div>');
        elem.addClass('list'+thisData.id);
        elem.find('.list_anchor').attr('href', thisData.anchor);
        elem.find('.list_anchor').attr('target', thisData.blank);
		
        elem.find('.news_img').html('<img src="'+thisData.image+'" />');
        elem.find('.news_cate').html(thisData.category);
		if(thisData.category == "뉴스"){
			elem.find('.news_cate').addClass("cate_1");
		}else if(thisData.category == "업데이트/출시"){
			elem.find('.news_cate').addClass("cate_2");
		}else if(thisData.category == "블로그"){
			elem.find('.news_cate').addClass("cate_3");
		}
        elem.find('.news_title').text(thisData.title);
        elem.find('.news_time').text(thisData.news_time);
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

    $moreBtn.on('click', function(){
        var currentCount = listCount;
        listCount += 9;
        mountElem(currentCount,listCount);

        if(boardData.length < listCount-1){
            $moreWrap.hide();
        }
    })
           
	$( document ).ready(function() {
		$('html').attr('data-board-type','list');
		$boardList.removeClass('gallery_type').addClass('list_type').html('');
		refresh_serach();
	});
	function refresh_serach(){
		$.ajax({
			type : 'post',
			url : "./news_ajax.php",
            type : 'POST', 
            data : {"category":$("#cate_sel").val()}, 
			success : function(json) {
				boardData = JSON.parse(json);
				boardData = boardData.data ;
				
				$moreWrap.show();
				$boardList.removeClass('gallery_type').addClass('list_type').html('');
				mountElem(0,9);
				listCount = 9;
				if(boardData.length <= listCount){
					$moreWrap.hide();
				}

				$(".cnt_div").html(boardData.length);
			},error : function(e) {
				console.log(e);
			}
		});

	}

	
function change_category(){
	//cate_sel
	refresh_serach();
}
</script>

<?php include '../inc/common_footer.php'; ?>
</body>
</html>