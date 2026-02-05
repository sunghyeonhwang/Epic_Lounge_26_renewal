<?php
$sub_menu = '600950';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$where = ' where ';
$sql_search = '';

$g5['title'] = '메인 비쥬얼 관리';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$RData = sql_fetch("select * from v3_main_banner where bn_id = '{$bn_id}'");
?>


<form name="frm" id="frm" action="./rsc_main_banner_mng_proc.php" onsubmit="return submit_frm()" method="post" enctype="multipart/form-data">
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
            <th scope="row"><label for="file">배경이미지</label></th>
            <td colspan="2">
                <input type="file" name="bn_bimg" id="file" >
            </td>
        </tr>
        
        <tr>
            <th scope="row"><label for="bn_btn_txt">버튼텍스트</label></th>
            <td colspan="2">
				  <input type="text" name="bn_btn_txt" id="bn_btn_txt" value="<?=$RData["bn_btn_txt"]?>" class="frm_input" size="40" >
			</td>
        </tr>
        <tr>
            <th scope="row"><label for="bn_txt_top">제목</label></th>
            <td colspan="2">
				  <input type="text" name="bn_txt_top" id="bn_txt_top" value="<?=$RData["bn_txt_top"]?>" class="frm_input" size="40" >
			</td>
        </tr>
        <tr>
            <th scope="row"><label for="bn_txt_bot_color_btn">내용</label></th>
            <td colspan="2">
				  <input type="text" name="bn_txt_bot_color_btn" id="bn_txt_bot_color_btn" value="<?=$RData["bn_txt_bot_color_btn"]?>" class="frm_input" size="120" style="width:600px;">
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
					<option <?=$RData["bn_use_at"] == "Y"?"selected='selected'":""?>>Y</option>
					<option <?=$RData["bn_use_at"] == "N"?"selected='selected'":""?>>N</option>

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