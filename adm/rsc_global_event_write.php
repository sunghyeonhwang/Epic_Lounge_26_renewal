<?php
$sub_menu = "600990";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

$g5['title'] = '글로벌 이벤트 관리';
include_once('./admin.head.php');
auth_check_menu($auth, $sub_menu, 'r');
?>



<form name="frm_bbs" id="frm_bbs" action="./rsc_global_event_write_proc.php" enctype="multipart/form-data" onsubmit="return submit_frm()" method="post">
<div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>게시판 기본 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="title">제목</label></th>
            <td colspan="2">
                <input type="text" name="title" id="title" value="<?=$RData["title"]?>" class="frm_input  frm_input_full" required="required">
            </td>
        </tr>
		
      
        <tr>
            <th scope="row"><label for="category">카테고리</label></th>
            <td colspan="2">
             
				<select id="category" name="category">
					<option value="커뮤니티 이벤트" <?=$RData["category"] == "커뮤니티 이벤트" ? "selected='selected'" : ""?>>커뮤니티 이벤트</option>
					<option value="글로벌 이벤트"  <?=$RData["category"] == "글로벌 이벤트" ? "selected='selected'" : ""?>>글로벌 이벤트</option>
				</select>
			</td>
        </tr>
		  <tr>
            <th scope="row"><label for="status">상태</label></th>
            <td colspan="2">
				<select id="status" name="status" class="frm_input">
					<option <?=$RData["status"] == "전체" ? "selected='selected'":""?>>전체</option>
					<option <?=$RData["status"] == "진행중" ? "selected='selected'":""?>>진행중</option>
					<option <?=$RData["status"] == "예고" ? "selected='selected'":""?>>예고</option>
					<option <?=$RData["status"] == "종료" ? "selected='selected'":""?>>종료</option>
					<option <?=$RData["status"] == "결과발표" ? "selected='selected'":""?>>결과발표</option>
				</select>
			</td>
		</tr>
		<tr>
            <th scope="row"><label for="sdate">이벤트 일정</label></th>
            <td colspan="2">
                <input type="date" name="sdate" id="sdate" value="<?=$RData["sdate"]?>" class="frm_input"> ~ 
                <input type="date" name="edate" id="edate" value="<?=$RData["edate"]?>" class="frm_input">
            </td>

		</tr>
		<tr>
            <th scope="row"><label for="add_btn_yn">참가신청 버튼</label></th>
            <td colspan="2">
				<select id="add_btn_yn" name="add_btn_yn" class="frm_input">
					<option <?=$RData["add_btn_yn"] == "Y" ? "selected='selected'":""?> value="Y">사용</option>
					<option <?=$RData["add_btn_yn"] == "N" ? "selected='selected'":""?> value="N">미사용</option>
				</select>
				<input type="text" name="add_btn_url" id="add_btn_url" value="<?=$RData["add_btn_url"]?>" class="frm_input" size="100" >
            </td>

		</tr>
		<tr>
            <th scope="row"><label for="contents">내용</label></th>
            <td colspan="2">
			<?
				$pre_cnt = '<div class="event_edit_wrap"><div class="event_top_btn"><a href="#n" title="새창" target="_blank">등록하기</a></div><div class="event_edit_mid"><p class="text_1">트윈모션 2022.1이 출시되었습니다!</p><p class="text_2">트윈모션은 시각화 콘셉트부터 포토리얼한 경험에 이르기까지 전체 건축 시각화 파이프라인의 핵심 제품입니다. <br />트윈모션 2022.1에는 새로운 패스 트레이서, HDRI 스카이돔, CAD와 BIM 패키지에 대한 동시 동기화 그리고 모든 트윈모션 사용자를 위한 트윈모션 클라우드 얼리 액세스 등의 많은 기능이 포함되어 있어 건축, 건설, 도시 계획, 랜드스케이프 디자인을 위한 멋진 시각화와 몰입적인 경험을 훨씬 더 쉽게 만들고 공유할 수 있습니다.<br />이번 <트윈모션 2022.1 출시 웨비나>에서 신규 기능과 업데이트들을 시연과 함께 살펴보세요.</p></div><div class="event_edit_con_box"><ul><li><span class="title">일시</span><span class="text">2022년 1월 25일(화) 오후 2시 에픽 라운지</span></li><li><span class="title">채널</span><span class="text">언리얼 엔진 유튜브 채널</span></li><li><span class="title">대상</span><span class="text">관심 있는 사람, 누구나</span></li></ul></div><div class="event_edit_youtube"><iframe class="youtube_box" src="https://www.youtube.com/embed/eNWSO74pSaY" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div><div class="event_edit_wraing"><p class="title">안내사항</p><ul><li>무료로 진행되는 본 웨비나는 등록을 완료하셔야 시청하실 수 있습니다.</li><li>1월 25일 12시까지 등록하신 분들께는 시청 url을 등록하신 메일로 발송해 드립니다.</li><li>행사 당일 본 웹페이지 내 [시청하기] 버튼을 통해서 웨비나를 시청하실 수 있습니다.</li><li>방송 진행 중에도 입장하여 웨비나 시청이 가능합니다.</li><li>웨비나는 PC, 모바일, 태블릿 등에서 시청하실 수 있으나, PC 환경을 권장드립니다.</li><li>웨비나 시작 30분 전부터 참여하실 수 있습니다.</li><li>웨비나 등록 확인 및 수정은 여기에서 진행하실 수 있습니다.</li><li>문의 : 이메일 unrealsummit.korea@gmail.com 또는 전화 : 02-326-3701</li></ul></div></div>';
			?>
                <?php echo editor_html("contents", get_text(html_purifier($pre_cnt), 0)); ?>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="thumb_img">리스트 썸네일</label></th>
            <td colspan="2">
				<?php
				$bimg_str = "";
				$bimg = G5_DATA_PATH."/event/{$RData['thumb_img']}";
				if (file_exists($bimg)  && $RData['thumb_img'] ) {
					$size = @getimagesize($bimg);
					if($size[0] && $size[0] > 750)
						$width = 750;
					else
						$width = $size[0];

					$bimg_str = '<img src="'.G5_DATA_URL.'/rsc/'.$RData['thumb_img'].'" width="'.$width.'">';
				}
				if ($bimg_str) {
					echo '<div class="banner_or_img">';
					echo $bimg_str;
					echo '</div>';
				}
				?>
                <input type="file" name="thumb_img" id="thumb_img" class="" >
				<br />이미지 사이즈 1240 x 390
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="top_bbs_img">상세 상단 이미지</label></th>
            <td colspan="2">
				<?php
				$bimg_str = "";
				$bimg = G5_DATA_PATH."/event/{$RData['top_bbs_img']}";
				if (file_exists($bimg)  && $RData['top_bbs_img'] ) {
					$size = @getimagesize($bimg);
					if($size[0] && $size[0] > 750)
						$width = 750;
					else
						$width = $size[0];

					$bimg_str = '<img src="'.G5_DATA_URL.'/rsc/'.$RData['top_bbs_img'].'" width="'.$width.'">';
				}
				if ($bimg_str) {
					echo '<div class="banner_or_img">';
					echo $bimg_str;
					echo '</div>';
				}
				?>
                <input type="file" name="top_bbs_img" id="top_bbs_img"  class="" >
				<br />이미지 사이즈 1920 x 520
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="doc_file">첨부파일</label></th>
            <td colspan="2">
                <input type="file" name="doc_file" id="doc_file" class="" >
            </td>
        </tr>
		
		<tr>
            <th scope="row"><label for="display_yn">출력여부</label></th>
            <td colspan="2">
            <select id="display_yn" name="display_yn">
				<option value="N" <?=$RData["display_yn"] == "N" ? "selected='selected'":""?>>N</option>
				<option value="Y" <?=$RData["display_yn"] == "Y" ? "selected='selected'":""?>>Y</option>
			</select>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
	

<div class="btn_fixed_top">
    <input type="submit" value="확인" class="btn_submi btn btn_01" accesskey="s">
</div>
</form>
<script>
function submit_frm(){

    <?php echo get_editor_js("contents"); ?>

	return true;
}
</script>
<?php
include_once ('./admin.tail.php');