<?php
$sub_menu = "600990";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

$g5['title'] = '글로벌 이벤트 관리';
include_once('./admin.head.php');
auth_check_menu($auth, $sub_menu, 'r');


$RData = sql_fetch("select * from v3_rsc_global_event_bbs where rsc_bbs_idx = '{$rsc_bbs_idx}'");
?>



<form name="frm_bbs" id="frm_bbs" action="./rsc_global_event_modify_proc.php" enctype="multipart/form-data" onsubmit="return submit_frm()" method="post">
<input type="hidden" name="rsc_bbs_idx" value="<?=$RData["rsc_bbs_idx"]?>" />
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
                <?php echo editor_html("contents", get_text(html_purifier($RData['contents']), 0)); ?>
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

					$bimg_str = '<img src="'.G5_DATA_URL.'/event/'.$RData['thumb_img'].'" width="'.$width.'">';
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

					$bimg_str = '<img src="'.G5_DATA_URL.'/event/'.$RData['top_bbs_img'].'" width="'.$width.'">';
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
            <td colspan="2"><?php
				$bimg_str = "";
				$bimg = G5_DATA_PATH."/event/{$RData['doc_file']}";
				if (file_exists($bimg)  && $RData['doc_file'] ) {
					echo '<div class="banner_or_img">';
					echo "<a href= '".$bimg."'> 다운로드</a>";
					echo '</div>';
				}
				?>
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