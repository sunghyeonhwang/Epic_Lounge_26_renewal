<?php
$sub_menu = "600200";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

$g5['title'] = '리소스 쓰기';
include_once('./admin.head.php');
auth_check_menu($auth, $sub_menu, 'r');

$idx = $_GET["rsc_bbs_idx"]; 

$RData = sql_fetch("select * from v3_rsc_review_bbs where rsc_bbs_idx = '{$idx}'");

?>


<form name="frm_bbs" id="frm_bbs" action="./rsc_review_modify_proc.php" enctype="multipart/form-data" onsubmit="return submit_frm()" method="post">
<input type="hidden" name="rsc_bbs_idx" value="<?=$idx?>" />
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
            <th scope="row"><label for="category1">카테고리 1</label></th>
            <td colspan="2">
				<select id="category1" name="category1">
					<option value="전체" <?=$RData["category"] == "전체" ? "selected='selected'" : ""?>>전체</option>
					<option value="M&E" <?=$RData["category"] == "M&E" ? "selected='selected'" : ""?>>M&E</option>
					<option value="VR/AR" <?=$RData["category"] == "VR/AR" ? "selected='selected'" : ""?>>VR/AR</option>
					<option value="건축" <?=$RData["category"] == "건축" ? "selected='selected'" : ""?>>건축</option>
					<option value="기타" <?=$RData["category"] == "기타" ? "selected='selected'" : ""?>>기타</option>
					<option value="기획" <?=$RData["category"] == "기획" ? "selected='selected'" : ""?>>기획</option>
					<option value="비주얼아트" <?=$RData["category"] == "비주얼아트" ? "selected='selected'" : ""?>>비주얼아트</option>
					<option value="프로그래밍" <?=$RData["category"] == "프로그래밍" ? "selected='selected'" : ""?>>프로그래밍</option>
					<option value="프로덕션" <?=$RData["category"] == "프로덕션" ? "selected='selected'" : ""?>>프로덕션</option>
				</select>
			</td>
        </tr>
        <tr>
            <th scope="row"><label for="category2">카테고리 2</label></th>
            <td colspan="2">
				<select id="category2" name="category2">
					<option value="전체" <?=$RData["category"] == "전체" ? "selected='selected'" : ""?>>전체</option>
					<option value="M&E" <?=$RData["category"] == "M&E" ? "selected='selected'" : ""?>>M&E</option>
					<option value="건축" <?=$RData["category"] == "건축" ? "selected='selected'" : ""?>>건축</option>
					<option value="게임" <?=$RData["category"] == "게임" ? "selected='selected'" : ""?>>게임</option>
					<option value="교육" <?=$RData["category"] == "교육" ? "selected='selected'" : ""?>>교육</option>
					<option value="기타" <?=$RData["category"] == "기타" ? "selected='selected'" : ""?>>기타</option>
					<option value="산업" <?=$RData["category"] == "산업" ? "selected='selected'" : ""?>>산업</option>
					<option value="자동차" <?=$RData["category"] == "자동차" ? "selected='selected'" : ""?>>자동차</option>
				</select>
			</td>
        </tr>
        <tr>
            <th scope="row"><label for="cate_industry[]">산업분야</label></th>
            <td colspan="2">
				<ul class="button_ul">

<?

	$sql = " select * from v3_rsc_review_category where rsc_type='산업분야' order by rsc_idx desc ";
	$result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
					<li><input type="checkbox" name="cate_industry[]" id="rsc_<?=$row["rsc_idx"]?>" value="<?=$row["rsc_name"]?>" <?=strpos($RData["cate_industry"], $row["rsc_name"]) === false ? "":"checked='checked'"?> /> <label for="rsc_<?=$row["rsc_idx"]?>"><?=$row["rsc_name"]?> </label></li>

<?
	}
?>
				</ul>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="">제품군</label></th>
            <td colspan="2">
				<ul class="button_ul">
<?

	$sql = " select * from v3_rsc_review_category where rsc_type='제품군' order by rsc_idx desc ";
	$result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
					<li><input type="checkbox" name="cate_product[]" id="rsc_<?=$row["rsc_idx"]?>" value="<?=$row["rsc_name"]?>" <?=strpos($RData["cate_product"], $row["rsc_name"]) === false ? "":"checked='checked'"?> /> <label for="rsc_<?=$row["rsc_idx"]?>"><?=$row["rsc_name"]?> </label></li>
<?
	}
?>
				</ul>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="">주제</label></th>
            <td colspan="2">
				<ul class="button_ul">
<?

	$sql = " select * from v3_rsc_review_category where rsc_type='주제' order by rsc_idx desc ";
	$result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
					<li><input type="checkbox" name="cate_subject[]" id="rsc_<?=$row["rsc_idx"]?>" value="<?=$row["rsc_name"]?>" <?=strpos($RData["cate_subject"], $row["rsc_name"]) === false ? "":"checked='checked'"?> /> <label for="rsc_<?=$row["rsc_idx"]?>"><?=$row["rsc_name"]?> </label></li>
<?
	}
?>
				</ul>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="gr_id">난이도</label></th>
            <td colspan="2">
             
				<select id="cate_difficult" name="cate_difficult">
					
<?

	$sql = " select * from v3_rsc_review_category where rsc_type='난이도' order by rsc_idx desc ";
	$result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
					<option value="<?=$row["rsc_name"]?>" <?=$RData["cate_difficult"] == $row["rsc_name"] ? "selected='selected'" : ""?>><?=$row["rsc_name"]?></option>
<?
	}
?>
				</select>
			</td>
        </tr>
		
		<tr>
            <th scope="row"><label for="contents">내용</label></th>
            <td colspan="2">
                <?php echo editor_html("contents", get_text(html_purifier($RData['contents']), 0)); ?>
            </td>
        </tr>




        <tr>
            <th scope="row"><label for="thumb_img_url">외부 썸네일(우선사용)</label></th>
            <td colspan="2">
			<img src="<?=$RData["thumb_img_url"]?>" width="400"/>
			<br />
                <input type="text" name="thumb_img_url" id="thumb_img_url" value="<?=$RData["thumb_img_url"]?>" class="frm_input  frm_input_full" >
            </td>
        </tr>
		
		
        <tr>
            <th scope="row"><label for="thumb_img">썸네일 업로드</label></th>
            <td colspan="2">
				<?php
				$bimg_str = "";
				$bimg = G5_DATA_PATH."/rsc/{$RData['thumb_img']}";
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
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="youtube_url">유튜브 URL</label></th>
            <td colspan="2">
                <input type="text" name="youtube_url" id="youtube_url" value="<?=$RData["youtube_url"]?>" class="frm_input  frm_input_full" >
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="event_title">이벤트명</label></th>
            <td colspan="2">
                <input type="text" name="event_title" id="event_title" value="<?=$RData["event_title"]?>" class="frm_input  frm_input_full" >
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="event_year">이벤트년도</label></th>
            <td colspan="2">
                <input type="text" name="event_year" id="event_year" value="<?=$RData["event_year"]?>" class="frm_input  frm_input_full" >
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="youtube_url">Speaker</label></th>
            <td colspan="2">
                <input type="text" name="speker" id="speker" value="<?=$RData["speker"]?>" class="frm_input  frm_input_full" >
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="pdf_url">PDF파일</label></th>
            <td colspan="2">
                <input type="text" name="pdf_url" id="pdf_url" value="<?=$RData["pdf_url"]?>" class="frm_input  frm_input_full" >
            </td>
        </tr>

		
		
		<tr>
			<th scope="row">상단이미지</th>
			<td>
				<input type="file" name="top_bbs_img">
				<?php
				$bimg_str = "";
				$bimg = G5_DATA_PATH."/rsc/{$RData['top_bbs_img']}";
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
			</td>
		</tr>
		<tr>
            <th scope="row"><label for="display_yn">출력여부</label></th>
            <td colspan="2">
            <select id="display_yn" name="display_yn">
				<option value="N" <?=$RData["display_yn"] == "N"? "selected='selected'":""?>>N</option>
				<option value="Y" <?=$RData["display_yn"] == "Y"? "selected='selected'":""?>>Y</option>
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