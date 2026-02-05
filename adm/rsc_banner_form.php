<?php
$sub_menu = '600900';
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

$bn_id = preg_replace('/[^0-9]/', '', $bn_id);

$html_title = '배너';
$g5['title'] = $html_title.'관리';

if ($w=="u")
{
    $html_title .= ' 수정';
    $sql = " select * from v3_shop_banner where bn_id = '$bn_id' ";
    $bn = sql_fetch($sql);
}
else
{
    $html_title .= ' 입력';
    $bn['bn_url']        = "http://";
    $bn['bn_begin_time'] = date("Y-m-d 00:00:00", time());
    $bn['bn_end_time']   = date("Y-m-d 00:00:00", time()+(60*60*24*31));
}

// 접속기기 필드 추가
if(!sql_query(" select bn_device from v3_shop_banner limit 0, 1 ")) {
    sql_query(" ALTER TABLE `v3_shop_banner`
                    ADD `bn_device` varchar(10) not null default '' AFTER `bn_url` ", true);
    sql_query(" update v3_shop_banner set bn_device = 'pc' ", true);
}

include_once (G5_ADMIN_PATH.'/admin.head.php');
?>

<form name="fbanner" action="./rsc_banner_mng_proc.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="w" value="<?php echo $w; ?>">
<input type="hidden" name="bn_id" value="<?php echo $bn_id; ?>">

<div class="tbl_frm01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?></caption>
    <colgroup>
        <col class="grid_4">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <th scope="row">이미지</th>
        <td>
            <input type="file" name="bn_bimg">
            <?php
            $bimg_str = "";
            $bimg = G5_DATA_PATH."/banner/{$bn['bn_id']}";
            if (file_exists($bimg) && $bn['bn_id']) {
                $size = @getimagesize($bimg);
                if($size[0] && $size[0] > 750)
                    $width = 750;
                else
                    $width = $size[0];

                echo '<input type="checkbox" name="bn_bimg_del" value="1" id="bn_bimg_del"> <label for="bn_bimg_del">삭제</label>';
                $bimg_str = '<img src="'.G5_DATA_URL.'/banner/'.$bn['bn_id'].'" width="'.$width.'">';
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
        <th scope="row"><label for="bn_alt">이미지 설명</label></th>
        <td>
            <?php echo help("img 태그의 alt, title 에 해당되는 내용입니다.\n배너에 마우스를 오버하면 이미지의 설명이 나옵니다."); ?>
            <input type="text" name="bn_alt" value="<?php echo get_text($bn['bn_alt']); ?>" id="bn_alt" class="frm_input" size="80">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="bn_url">링크</label></th>
        <td>
            <?php echo help("배너클릭시 이동하는 주소입니다."); ?>
            <input type="text" name="bn_url" size="80" value="<?php echo $bn['bn_url']; ?>" id="bn_url" class="frm_input">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="bn_position">배너종류</label></th>
        <td>
            <select name="bn_position" id="bn_position">
                <option value="다시보기" <?php echo get_selected($bn['bn_position'], '다시보기'); ?>>다시보기</option>
                <option value="무료콘텐츠" <?php echo get_selected($bn['bn_position'], '무료콘텐츠'); ?>>무료콘텐츠</option>
                <option value="백서" <?php echo get_selected($bn['bn_position'], '백서'); ?>>백서</option>
                <option value="상단-새소식" <?php echo get_selected($bn['bn_position'], '상단-새소식'); ?>>상단-새소식</option>
                <option value="상단-이벤트" <?php echo get_selected($bn['bn_position'], '상단-이벤트'); ?>>상단-이벤트</option>
                <option value="상단-리소스" <?php echo get_selected($bn['bn_position'], '상단-리소스'); ?>>상단-리소스</option>
        </select>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="bn_new_win">새창</label></th>
        <td>
            <?php echo help("배너클릭시 새창을 띄울지를 설정합니다.", 50); ?>
            <select name="bn_new_win" id="bn_new_win">
                <option value="0" <?php echo get_selected($bn['bn_new_win'], 0); ?>>사용안함</option>
                <option value="1" <?php echo get_selected($bn['bn_new_win'], 1); ?>>사용</option>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="bn_order">출력 순서</label></th>
        <td>
           <?php echo help("배너를 출력할 때 순서를 정합니다. 숫자가 작을수록 먼저 출력됩니다."); ?>
           <?php echo order_select("bn_order", $bn['bn_order']); ?>
        </td>
    </tr>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">
    <a href="./rsc_banner_mng.php" class="btn_02 btn">목록</a>
    <input type="submit" value="확인" class="btn_submit btn" accesskey="s">
</div>

</form>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
