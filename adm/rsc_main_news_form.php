<?php
$sub_menu = '600960';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$where = ' where ';
$sql_search = '';

$g5['title'] = '메인 새소식관리';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$RData = sql_fetch("select * from v3_main_banner_news where bn_id = '{$bn_id}'");
?>


<form name="frm" id="frm" action="./rsc_main_news_mng_proc.php" onsubmit="return submit_frm()" method="post" enctype="multipart/form-data">
<input type="hidden" name="bn_id" value="<?=$RData["bn_id"]?>" />
<input type="hidden" name="w" value="u" />
<div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>게시판 기본 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="file">이미지</label></th>
            <td colspan="2">
            <?php
            $bimg_str = "";
            $bimg = G5_DATA_PATH."/main_news/{$bn['bn_id']}";
            if (file_exists($bimg) && $RData['bn_id']) {
                $size = @getimagesize($bimg);
                if($size[0] && $size[0] > 750)
                    $width = 750;
                else
                    $width = $size[0];
                $bimg_str = '<img src="'.G5_DATA_URL.'/main_news/'.$RData['bn_id'].'" width="'.$width.'">';
            }
            if ($bimg_str) {
                echo '<div class="banner_or_img">';
                echo $bimg_str;
                echo '</div>';
            }
            ?>
                <input type="file" name="bn_bimg" id="file" >
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="bn_info">설명글</label></th>
            <td colspan="2">
				  <input type="text" name="bn_info" id="bn_info" value="<?=$RData["bn_info"]?>" class="frm_input frm_input_full" >
			</td>
        </tr>
        <tr>
            <th scope="row"><label for="bn_title">타이틀</label></th>
            <td colspan="2">
				  <input type="text" name="bn_title" id="bn_title" value="<?=$RData["bn_title"]?>" class="frm_input frm_input_full" >
			</td>
        </tr>
        <tr>
            <th scope="row"><label for="bn_url">링크</label></th>
            <td colspan="2">
				  <input type="text" name="bn_url" id="bn_url" value="<?=$RData["bn_url"]?>" class="frm_input frm_input_full" >
			</td>
        </tr>
        <tr>
            <th scope="row"><label for="bn_use_at">사용</label></th>
            <td colspan="2">
				 
				<select id="bn_use_at" name="bn_use_at">
					<option <?=$RData["bn_use_at"] == "N"?"selected='selected'":""?>>N</option>
					<option <?=$RData["bn_use_at"] == "Y"?"selected='selected'":""?>>Y</option>

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
<?php
include_once ('./admin.tail.php');