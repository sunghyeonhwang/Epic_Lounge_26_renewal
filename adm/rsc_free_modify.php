<?php
$sub_menu = "600400";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

$g5['title'] = '리소스 쓰기';
include_once('./admin.head.php');
auth_check_menu($auth, $sub_menu, 'r');

$idx = $_GET["rsc_bbs_idx"]; 

$RData = sql_fetch("select * from v3_rsc_free_bbs where rsc_bbs_idx = '{$idx}'");

?>


<form name="frm_bbs" id="frm_bbs" action="./rsc_free_modify_proc.php" enctype="multipart/form-data" onsubmit="return submit_frm()" method="post">
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
				<select id="category" name="category"><?

	$sql = " select * from v3_rsc_free_category where rsc_type='카테고리' order by sort asc ";
	$result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
					<option value="<?=$row["rsc_name"]?>" <?=$row_list["category"] == $row["rsc_name"] ? "selected='selected'" : ""?>><?=$row["rsc_name"]?></option>
<?
	}
?>
				</select>
			</td>
        </tr>
        <tr>
            <th scope="row"><label for="cate_industry[]">산업분야</label></th>
            <td colspan="2">
				<ul class="button_ul">

<?

	$sql = " select * from v3_rsc_free_category where rsc_type='산업분야' order by rsc_idx desc ";
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
            <th scope="row"><label for="">엔진버전</label></th>
            <td colspan="2">
				<ul class="button_ul">
<?

	$sql = " select * from v3_rsc_free_category where rsc_type='엔진버전' order by rsc_idx desc ";
	$result = sql_query($sql);
	$arr_engine = explode("|",$RData["cate_engine"]);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
		$checked = false;
		foreach($arr_engine as $val){
			if(trim($val) == trim($row["rsc_name"])) $checked = true;
		}
?>
					<li><input type="checkbox" name="cate_engine[]" id="rsc_<?=$row["rsc_idx"]?>" value="<?=$row["rsc_name"]?>" <?=!$checked ? "":"checked='checked'"?> /> <label for="rsc_<?=$row["rsc_idx"]?>"><?=$row["rsc_name"]?> </label></li>
<?
	}
?>
				</ul>
            </td>
        </tr>
		<tr>
            <th scope="row"><label for="contents">내용</label></th>
            <td colspan="2">
                <?php echo editor_html("contents", get_text(html_purifier($RData['contents']), 0)); ?>
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
				<option value="N">N</option>
				<option value="Y">Y</option>
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