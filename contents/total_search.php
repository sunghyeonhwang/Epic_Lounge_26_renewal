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
<body>
<?php include '../inc/common_header.php'; ?>


<div id="sub_visual" class="search_vi">
	<h2>검색</h2>
	<p>키워드를 검색하세요</p>
</div>
<div class="total_search_box">
		<div id="resource_search" class="total_search_text_box">
			<form action="./total_search.php" name="" id="">
				<fieldset>
					<div class="serach_in_box"><input type="text" name="keyword" class="in_text" value="<?=$_GET["keyword"]?>" id="" /><input type="image" class="in_img" src="/v3/resource/images/sub/replay/search_btn.png" /></div>
				</fieldset>
			</form>
			<div class="total_search_word_num">
				<em>"<?=$_GET["keyword"]?>"</em>에 대한 검색 결과 <span class="cnt_div">0</span>건
			</div>
		</div>
	<div class="wrap">

		<div class="total_search_news">
			<h3>새소식</h3>
			<ul class=" news_total_list total_search_list">
				<li>
					<a href="#n">
						<span class="title">제목<em class="sch_wr">부분</em>영역</span>
						<span class="cate_text">카테고리 l 태그</span>
						<span class="text">이번 에픽 라이브에서는 아직 언리얼 엔진을 접해보시지 않았거나, 언리얼 엔진 사용을 고민하고 계신 분들을 위해 러셀님이 언리얼 엔진의 매력을 소개합니다. 러셀님은 언리얼 엔진을 접한 후 그 강점 하나만으로 영상 제작 업계에서 게임 업계로 진출한 아티스트로서, 언리얼 엔진 크리에이터이자 유튜브 크리에이터로 활동하고 있습니다. 이번 영상에서는 언리얼 엔진 사용을 고민하시거나 처음 접하시는 분들을 위해 러셀님이 느끼신 언리얼 엔진의 매력과 강점을 풀어봅니다.</span>
						<span class="date">2022-01-24</span>
					</a>
				</li>
			</ul>
		</div>
	<div class="board_btn_box news_btn_box more">
		<a href="#n" class=" more_btn"><span class="text">더보기</span><span class="icon"></span></a>
	</div>


		<div class="total_search_event total_search_list">
			<h3>이벤트</h3>
			<ul class=" event_total_list">
				<li>
					<a href="#n">
						<span class="title">제목<em class="sch_wr">부분</em>영역</span>
						<span class="cate_text">커뮤니티 이벤트 l 상태값(예고,종료)</span>
						<span class="text">이번 에픽 라이브에서는 아직 언리얼 엔진을 접해보시지 않았거나, 언리얼 엔진 사용을 고민하고 계신 분들을 위해 러셀님이 언리얼 엔진의 매력을 소개합니다. 러셀님은 언리얼 엔진을 접한 후 그 강점 하나만으로 영상 제작 업계에서 게임 업계로 진출한 아티스트로서, 언리얼 엔진 크리에이터이자 유튜브 크리에이터로 활동하고 있습니다. 이번 영상에서는 언리얼 엔진 사용을 고민하시거나 처음 접하시는 분들을 위해 러셀님이 느끼신 언리얼 엔진의 매력과 강점을 풀어봅니다.</span>
						<span class="date">2022-04-05 ~ 2022-04-28 (일정)</span>
					</a>
				</li>
			</ul>
		</div>
	<div class="board_btn_box event_btn_box more">
		<a href="#n" class=" more_btn"><span class="text ">더보기</span><span class="icon"></span></a>
	</div>

		<div class="total_search_res total_search_list">
			<h3>리소스</h3>
			<ul class=" res_list">
				<li>
					<a href="#n">
						<span class="title">제목<em class="sch_wr">부분</em>영역</span>
						<span class="cate_text">커뮤니티 이벤트 l 상태값(예고,종료)</span>
						<span class="text">이번 에픽 라이브에서는 아직 언리얼 엔진을 접해보시지 않았거나, 언리얼 엔진 사용을 고민하고 계신 분들을 위해 러셀님이 언리얼 엔진의 매력을 소개합니다. 러셀님은 언리얼 엔진을 접한 후 그 강점 하나만으로 영상 제작 업계에서 게임 업계로 진출한 아티스트로서, 언리얼 엔진 크리에이터이자 유튜브 크리에이터로 활동하고 있습니다. 이번 영상에서는 언리얼 엔진 사용을 고민하시거나 처음 접하시는 분들을 위해 러셀님이 느끼신 언리얼 엔진의 매력과 강점을 풀어봅니다.</span>
						<span class="date">2022-04-05</span>
					</a>
				</li>
			</ul>
		</div>
	<div class="board_btn_box res_btn_box more">
		<a href="#n" class=" more_btn"><span class="text">더보기</span><span class="icon"></span></a>
	</div>


	</div>
</div>

<script>

    //리스트 데이터
    var boardDataRes = []
    var boardDataEvent = []
    var boardDataNews = []

    var listCountRes = 8, res_elem = '';
    var listCountEvent = 8, event_elem = '';
    var listCountNews = 8, news_elem = '';

    function createElemRes(thisData){
        res_elem = $('<li>\n' +
            '    <a href="'+thisData.link+'" class="list_anchor">\n' +
            '        <span class="title">'+thisData.title+'</span>\n' +
            '        <span class="cate_text">커뮤니티 이벤트 l 상태값(예고,종료)</span>\n' +
            '        <span class="text"></span>\n' +
            '        <span class="date">2022-04-05</span>\n' +
            '    </a>\n' +
            '</div>');
        res_elem.addClass('list'+thisData.id);
        res_elem.find('.list_anchor').attr('target', thisData.blank);
        res_elem.find('.cate_text').text(thisData.category);
        res_elem.find('.text').text(thisData.content);
        res_elem.find('.date').text(thisData.reg_date);
    }
    function mountElemRes(startCount,endCount){
        for(var i = startCount+1;i <= endCount;i++){
            var thisData = boardDataRes.find(function(eachData,eachCount){
                return eachData.id === i;
            });
            if(thisData){
                createElemRes(thisData);
                $('.res_list').append(res_elem);
            } else {
                return;
            }
        }
    }

    $('.res_btn_box').find('.more_btn').on('click', function(){
        var currentCount = listCountRes;
        listCountRes += 8;
        mountElemRes(currentCount,listCountRes);

        if(boardDataRes.length < listCountRes-1){
            $('.res_btn_box').hide();
        }
    })
           
	function refresh_rsc_serach(){
		$.ajax({
			type : 'post',
			url : "./total_search_ajax_rsc.php",
            type : 'POST', 
            data : {"keyword":"<?=$_GET["keyword"]?>"}, 
			success : function(json) {
				json = json.replace('{"id":"-1"},',"");
				json = json.replace('{"id":"-1"}',"");
				boardDataRes = JSON.parse(json);
				boardDataRes = boardDataRes.data ;
				
				$('.res_btn_box').show();
				$('.res_list').html('');
				mountElemRes(0,8);
				listCountRes = 8;
				if(boardDataRes.length <= listCountRes){
					$('.res_btn_box').hide();
				}
				if(boardDataRes.length == 0){
					$(".total_search_res").hide();
				}

				$(".cnt_div").html(boardDataRes.length + boardDataNews.length + boardDataEvent.length);
			},error : function(e) {
				console.log(e);
			}
		});

	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function createElemNews(thisData){
        news_elem = $('<li>\n' +
            '    <a href="'+thisData.link+'" class="list_anchor">\n' +
            '        <span class="title">'+thisData.title+'</span>\n' +
            '        <span class="cate_text">커뮤니티 이벤트 l 상태값(예고,종료)</span>\n' +
            '        <span class="text"></span>\n' +
            '        <span class="date">2022-04-05</span>\n' +
            '    </a>\n' +
            '</div>');
        news_elem.addClass('list'+thisData.id);
        news_elem.find('.list_anchor').attr('target', thisData.blank);
        news_elem.find('.cate_text').text(thisData.category);
        news_elem.find('.text').text(thisData.content);
        news_elem.find('.date').text(thisData.reg_date);
    }
    function mountElemNews(startCount,endCount){
        for(var i = startCount+1;i <= endCount;i++){
            var thisData = boardDataNews.find(function(eachData,eachCount){
                return eachData.id === i;
            });
            if(thisData){
                createElemNews(thisData);
                $('.news_total_list').append(news_elem);
            } else {
                return;
            }
        }
    }

    $('.news_btn_box').find('.more_btn').on('click', function(){
        var currentCount = listCountNews;
        listCountNews += 8;
        mountElemNews(currentCount,listCountNews);

        if(boardDataNews.length < listCountNews-1){
            $('.news_btn_box').hide();
        }
    })
           
	function refresh_news_serach(){
		$.ajax({
			type : 'post',
			url : "./total_search_ajax_news.php",
            type : 'POST', 
            data : {"keyword":"<?=$_GET["keyword"]?>"}, 
			success : function(json) {
				json = json.replace('{"id":"-1"},',"");
				json = json.replace('{"id":"-1"}',"");
				boardDataNews = JSON.parse(json);
				boardDataNews = boardDataNews.data ;
				
				$('.news_btn_box').show();
				$('.news_total_list').html('');
				mountElemNews(0,8);
				listCountNews = 8;
				if(boardDataNews.length <= listCountNews){
					$('.news_btn_box').hide();
				}
				if(boardDataNews.length == 0){
					$(".total_search_news").hide();
				}

				$(".cnt_div").html(boardDataNews.length + boardDataNews.length + boardDataEvent.length);
			},error : function(e) {
				console.log(e);
			}
		});

	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////


    function createElemEvent(thisData){
        event_elem = $('<li>\n' +
            '    <a href="'+thisData.link+'" class="list_anchor">\n' +
            '        <span class="title">'+thisData.title+'</span>\n' +
            '        <span class="cate_text">커뮤니티 이벤트 l 상태값(예고,종료)</span>\n' +
            '        <span class="text"></span>\n' +
            '        <span class="date">2022-04-05</span>\n' +
            '    </a>\n' +
            '</div>');
        event_elem.addClass('list'+thisData.id);
        event_elem.find('.list_anchor').attr('target', thisData.blank);
        event_elem.find('.cate_text').text(thisData.category);
        event_elem.find('.text').text(thisData.content);
        event_elem.find('.date').text(thisData.reg_date);
    }
    function mountElemEvent(startCount,endCount){
        for(var i = startCount+1;i <= endCount;i++){
            var thisData = boardDataEvent.find(function(eachData,eachCount){
                return eachData.id === i;
            });
            if(thisData){
                createElemEvent(thisData);
                $('.event_total_list').append(event_elem);
            } else {
                return;
            }
        }
    }

    $('.event_btn_box').find('.more_btn').on('click', function(){
        var currentCount = listCountEvent;
        listCountEvent += 8;
        mountElemEvent(currentCount,listCountEvent);

        if(boardDataEvent.length < listCountEvent-1){
            $('.event_btn_box').hide();
        }
    })
           
	function refresh_event_serach(){
		$.ajax({
			type : 'post',
			url : "./total_search_ajax_event.php",
            type : 'POST', 
            data : {"keyword":"<?=$_GET["keyword"]?>"}, 
			success : function(json) {
				json = json.replace('{"id":"-1"},',"");
				json = json.replace('{"id":"-1"}',"");
				console.log(json);
				boardDataEvent = JSON.parse(json);
				boardDataEvent = boardDataEvent.data ;
				
				$('.event_btn_box').show();
				$('.event_total_list').html('');
				mountElemEvent(0,8);
				listCountEvent = 8;
				if(boardDataEvent.length <= listCountEvent){
					$('.event_btn_box').hide();
				}
				if(boardDataEvent.length == 0){
					$(".total_search_event").hide();
				}
				$(".cnt_div").html(boardDataEvent.length + boardDataNews.length + boardDataEvent.length);
			},error : function(e) {
				console.log(e);
			}
		});

	}
	$( document ).ready(function() {
		$('html').attr('data-board-type','list');
		$('.res_list').html('');
		$('.event_total_list').html('');
		$('.news_total_list').html('');
		refresh_rsc_serach ();
		refresh_news_serach ();
		refresh_event_serach ();
	});
</script>
<?php include '../inc/common_footer.php'; ?>
</body>
</html>