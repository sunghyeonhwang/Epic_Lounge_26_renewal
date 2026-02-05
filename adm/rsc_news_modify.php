<?php
$sub_menu = "600700";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

$g5['title'] = '새소식 관리';
include_once('./admin.head.php');
auth_check_menu($auth, $sub_menu, 'r');

$idx = $_GET["rsc_bbs_idx"]; 

$RData = sql_fetch("select * from v3_rsc_news_bbs where rsc_bbs_idx = '{$idx}'");

?>


<form name="frm_bbs" id="frm_bbs" action="./rsc_news_modify_proc.php" enctype="multipart/form-data"  onsubmit="return submit_frm()" method="post">
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
            <th scope="row"><label for="category">카테고리</label></th>
            <td colspan="2">
             
				<select id="category" name="category">
					<option value="블로그" <?=$RData["category"] == "블로그" ? "selected='selected'" : ""?>>블로그</option>
					<option value="뉴스"  <?=$RData["category"] == "뉴스" ? "selected='selected'" : ""?>>뉴스</option>
					<option value="업데이트/출시"  <?=$RData["category"] == "업데이트/출시" ? "selected='selected'" : ""?>>업데이트/출시</option>
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
            <th scope="row"><label for="tag">태그</label></th>
            <td colspan="2">
                <input type="text" name="tag" id="tag" value="<?=$RData["tag"]?>" class="frm_input  frm_input_full" >
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="site_url">사이트 URL</label></th>
            <td colspan="2">
                <input type="text" name="site_url" id="site_url" value="<?=$RData["site_url"]?>" class="frm_input  frm_input_full" >
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
				$bimg = G5_DATA_PATH."/news/{$RData['thumb_img']}";
				if (file_exists($bimg)  && $RData['thumb_img'] ) {
					$size = @getimagesize($bimg);
					if($size[0] && $size[0] > 750)
						$width = 750;
					else
						$width = $size[0];

					$bimg_str = '<img src="'.G5_DATA_URL.'/news/'.$RData['thumb_img'].'" width="'.$width.'">';
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
            <th scope="row"><label for="top_bbs_img">상세 상단 이미지</label></th>
            <td colspan="2">
				<?php
				$bimg_str = "";
				$bimg = G5_DATA_PATH."/news/{$RData['top_bbs_img']}";
				if (file_exists($bimg)  && $RData['top_bbs_img'] ) {
					$size = @getimagesize($bimg);
					if($size[0] && $size[0] > 750)
						$width = 750;
					else
						$width = $size[0];

					$bimg_str = '<img src="'.G5_DATA_URL.'/news/'.$RData['top_bbs_img'].'" width="'.$width.'">';
				}
				if ($bimg_str) {
					echo '<div class="banner_or_img">';
					echo $bimg_str;
					echo '</div>';
				}
				?>
                <input type="file" name="top_bbs_img" id="top_bbs_img"  class="" >
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="doc_file">첨부파일</label></th>
            <td colspan="2"><?php
				$bimg_str = "";
				$bimg = G5_DATA_PATH."/news/{$RData['doc_file']}";
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