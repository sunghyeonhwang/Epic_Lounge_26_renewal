</div></div></div>
<!-- //container -->
<!-- footer -->
    
<div id="footer">
	<div class="wrap">
		<div class="logo">
			<img src="/images/common/logo_wh.png" />
		</div>
		<div class="f_info">
			<ul>
				<li><a href="/../../sub_8_1.php">개인정보처리지침</a></li>
				<li><a href="/../../sub_8_2.php">영상정보처리기기(CCTV) 운영·관리방침</a></li>
			</ul>
			<address>[28165] 충청북도 청주시 흥덕구 오송읍 오송생명3로 150 (연제리 832) 7층<br />(대표) TEL : 043-279-5305 ㅣ FAX : 043-238-8183<br />(산지조사실) TEL : 043-279-5332~5334</address>
		</div>
		<p class="copy">COPYRIGHT(C) 2021 KAFET ALL RIGHTS RESERVED</p>

	</div>
	<!-- f_info -->
</div>


<!-- //wrap -->
<script type="text/javascript">
	document.addEventListener('DOMContentLoaded', function() {
		var trigger = new ScrollTrigger({

			offset: {
				x: 0,
				y: 50
			},
			addHeight: true
		}, document.body, window);
	});



	$(function(){
		$("a").on("click", function(){
			var divName = $(this).attr("id"), 
				topPosition = $("."+ divName).offset().top;
			$('html, body').animate({
				scrollTop: topPosition - 0
			}, 500);
			return false; //리턴펄스로 스크롤이 최상위로 갔다가 돌아오는 현상 없어짐
		});
	});
</script>