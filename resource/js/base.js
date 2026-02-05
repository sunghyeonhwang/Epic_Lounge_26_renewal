		/* 글자수 체크 */
		$('.textSize').keyup(function (e){
		    var content = $(this).val();
		    $(this).parents("td").find(".typingSize").html(content.length);    //글자수 실시간 카운팅
		});
		
		/* 초기 셋팅 */
		$(function() {
			$('.textSize').each(function (e){
			    var content = $(this).val();
			    $(this).parents("td").find(".typingSize").html(content.length);    //글자수 실시간 카운팅
			});
		});
		
		function chk_file(str) {
			if (/(\.gif|\.jpg|\.jpeg|\.png)$/i.test(str) == false) {
				alert("이미지 형식의 파일을 선택하십시오");
				return false;
			}
			return true
		}
		function fn_paging(page) {
			$('#curPage').val(page);
			$('#search_form').submit();
		}
		
		var regExp = /^[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_.]?[0-9a-zA-Z])*.[a-zA-Z]{2,3}$/i;
		var regTypeId = /^[a-z0-9+]*$/;
		var regTypeId1 = /^[a-z+]*$/;
		var regTypeId2 = /^[0-9+]*$/;				