<div class="snsbox">
			<button type="button" class="sns_btn" title="sns 공유 열기">sns 공유 열기</button>
			<div class="layer" style="display: none;">
				<ul class="clearfix">
					<li class="facebook"><a href="#" onclick="facebookSns('한국치산기술협회', location.href);" title="새창">페이스북 공유하기</a></li>
					<li class="blog"><a href="#" onclick="naverSns('한국치산기술협회', location.href);" title="새창">블로그 공유하기</a></li>
				</ul>
				<button type="button" class="sns_close">SNS 공유 닫기</button>
			</div>
		</div>
		<a href="javascript:printIt(document.getElementById('contents').innerHTML)" class="print_btn">Print</a>
<script type="text/javascript">
  var win=null;
  function printIt(printThis)  {
    win = window.open();
    self.focus();
    win.document.open();
    win.document.write('<'+'html'+'><'+'head'+'><'+'style'+'>');
    win.document.write('body, td { font-family: Verdana; font-size: 10pt;}');
    win.document.write('<'+'/'+'style'+'><'+'/'+'head'+'><'+'body'+'>');
    win.document.write(printThis);
    win.document.write('<'+'/'+'body'+'><'+'/'+'html'+'>');
    win.document.close();
    win.print();
    win.close();
  }
</script>