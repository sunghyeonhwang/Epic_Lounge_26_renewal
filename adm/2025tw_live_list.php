<?php
$sub_menu = "700300";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');
$sql_common = " from cb_unreal_2023_event_apply_live a ";
$sql_search = " where 1 ";

if ($stx) {
    $sql_search .= " and ( ";
    if ($sfl) {
        $sql_search .= " ($sfl like '%$stx%') ";
    }
    $sql_search .= " ) ";
}

if ($apply_reg_datetime) {
    $sql_search .= "and apply_reg_datetime like '$apply_reg_datetime%'";
}

if ($track == 1) {
    $sql_search .= "and track1 = '$track'";
} else if ($track == 2) {
    $sql_search .= "and track2 = '$track'";
}

$sst  = "a.apply_no";
$sod = "desc";
$sql_order = " order by $sst $sod ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
if (!$rows) {
    $rows = 4000;
}
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);


$g5['title'] = '라이브 접속 리스트';
include_once('./admin.head.php');

$colspan = 15;

$offline_cnt = sql_fetch("select count(*) as cnt from (select count(*) as cnt from cb_unreal_2023_event_apply_live l,cb_unreal_2023_event_apply e where l.apply_user_email=e.apply_user_email and e.free_yn = 'N' and (e.apply_pay_status = 10 or e.apply_pay_status = 1)  group by l.apply_user_email)b");
$online_cnt = sql_fetch("select count(*) as cnt from (select count(*) as cnt from cb_unreal_2023_event_apply_live l,cb_unreal_2023_event_apply e where l.apply_user_email=e.apply_user_email and e.free_yn = 'Y'  group by l.apply_user_email)b");
?>

<link rel="stylesheet" type="text/css" href="https://epiclounge.co.kr/cib/assets/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="https://epiclounge.co.kr/cib/assets/css/bootstrap-theme.min.css" />
<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" href="https://epiclounge.co.kr/cib/assets/css/datepicker3.css" />
<link rel="stylesheet" type="text/css" href="https://epiclounge.co.kr/cib/views/admin/basic/css/style.css" />
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/earlyaccess/nanumgothic.css" />

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script type="text/javascript" src="https://epiclounge.co.kr/cib/assets/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="https://epiclounge.co.kr/cib/assets/js/bootstrap-datepicker.kr.js"></script>

<script type="text/javascript" src="https://epiclounge.co.kr/cib/assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://epiclounge.co.kr/cib/assets/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://epiclounge.co.kr/cib/assets/js/jquery.validate.extension.js"></script>
<script type="text/javascript" src="https://epiclounge.co.kr/cib/assets/js/common.js"></script>
<style>
    h1
    {
        font-weight: bold;    font-size: 1.5em; font-family: 'Malgun Gothic',"맑은 고딕",AppleGothic,Dotum,"돋움", sans-serif;
    }
    body table td
    {
        font-size: 12px;
    }
</style>
<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
   
기간 :
<select class="" name="apply_reg_datetime">
    <option value="" <?php if($apply_reg_datetime == ''){?>selected<?php } ?>>전체</option>
    <option value="2024-05-02" <?php if($apply_reg_datetime == '2024-05-02'){?>selected<?php } ?>>1W</option>
    <option value="2024-05-09" <?php if($apply_reg_datetime == '2024-05-09'){?>selected<?php } ?>>2W</option>
    <option value="2024-05-16" <?php if($apply_reg_datetime == '2024-05-16'){?>selected<?php } ?>>3W</option>
    <option value="2024-05-23" <?php if($apply_reg_datetime == '2024-05-23'){?>selected<?php } ?>>4W</option>
    <option value="2024-05-30" <?php if($apply_reg_datetime == '2024-05-30'){?>selected<?php } ?>>5W</option>


</select>
<!--트랙 :
<select class="" name="track">
    <option value="" <?php if($track == ''){?>selected<?php } ?>>전체</option>
    <option value="1" <?php if($track == '1'){?>selected<?php } ?>>1</option>
    <option value="2" <?php if($track == '2'){?>selected<?php } ?>>2</option>
</select>-->
<select class="" name="sfl">
    <option value="apply_user_name" <?php if($sfl == 'apply_user_name'){?>selected<?php } ?>>이름</option>
    <option value="apply_user_email" <?php if($sfl == 'apply_user_email'){?>selected<?php } ?>>이메일</option>
    <option value="apply_user_phone" <?php if($sfl == 'apply_user_phone'){?>selected<?php } ?>>전화번호</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
<input type="submit" value="검색" class="btn_submit">
    <button type="button" class="btn btn-outline btn-success btn-sm" id="export_to_excel"><i class="fa fa-file-excel-o"></i> 전체 리스트 다운로드</button>
    <button type="button" class="btn btn-outline btn-success btn-sm" id="export_to_excel15"><i class="fa fa-file-excel-o"></i> 60명 랜덤 리스트</button>
   <!-- <button type="button" class="btn btn-outline btn-success btn-sm" id="export_to_excel3"><i class="fa fa-file-excel-o"></i> 전 기간 중복 없이 시청</button>-->
   <!-- <button type="button" class="btn btn-outline btn-success btn-sm" id="export_to_exceldual"><i class="fa fa-file-excel-o"></i> 온오프 동시참가자</button>-->

    <!--<button type="button" class="btn btn-outline btn-primary  btn-sm" id="export_to_excel300"><i class="fa fa-file-excel-o"></i> 4일 시청 300명 </button>-->
    <!--<button type="button" class="btn btn-outline btn-primary btn-sm" id="">온라인 <?=$online_cnt["cnt"]?>명 </button>-->
    <!--<button type="button" class="btn btn-outline btn-primary btn-sm" id="">오프라인 <?=$offline_cnt["cnt"]?>명 </button>-->
</form>

<form name="fboardlist" id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="<?php echo isset($token) ? $token : ''; ?>">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col">번호</th>
        <th scope="col">이름</th>
        <th scope="col">이메일</th>
        <th scope="col">연락처</th>
        <th scope="col">최초접속시간</th>
        <th scope="col">최종접속시간</th>
        <!--<th scope="col">트랙1</th>
        <th scope="col">트랙2</th>-->
        <th scope="col">랜덤추출</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=1; $row=sql_fetch_array($result); $i++) {
        $bg = 'bg'.($i%2);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <?php echo $total_count+1-($i+(($page-1)*$rows)); ?>
        </td>
        <td><?=$row['apply_user_name']?></td>
        <td><?=$row['apply_user_email']?></td>
        <td><?=$row['apply_user_phone']?></td>
        <td><?=$row['apply_reg_datetime']?></td>
        <td><?=$row['apply_reg_datetime2']?></td>
       <!-- <td><?=$row['track1']?></td>
        <td><?=$row['track2']?></td>-->
        <td><?php if($row['down']){ echo '다운'; } else { echo '-'; }?></td>
    </tr>
    <?php
    }
    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
    ?>
    </tbody>
    </table>
</div>
<style>
    .pagination {width:100%; margin:auto; text-align:center}
    .pagination ul { width:520px; margin:auto; text-align:center}
    .pagination li {float:left; margin: 10px;}
    .pagination li .active{ font-weight: 800;}
    </style>
<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

</form>
<form class="form-inline" name="fsearch2" action="https://epiclounge.co.kr/v3/adm/2023_export_excel_live_0501.php" method="get">
<input type="hidden" name="export" value="">
<input type="hidden" name="apply_reg_datetime" value="<?=$apply_reg_datetime?>">
<input type="hidden" name="track" value="<?=$track?>">
<input type="hidden" name="stx" value="<?=$stx?>">
</form>
<script type="text/javascript">
    $(document).on('click', '#export_to_excel', function() {
        var f = document.fsearch2;
        f.export.value = "excel";
        f.submit();
        f.export.value = "";
    });
    $(document).on('click', '#export_to_exceldual', function() {
        var f = document.fsearch2;
        f.export.value = "dual";
        f.submit();
        f.export.value = "";
    });
    $(document).on('click', '#export_to_excel300', function() {
        var f = document.fsearch2;
        f.export.value = "rand300";
        f.submit();
        f.export.value = "";
    });
    $(document).on('click', '#export_to_excel3', function() {
        var f = document.fsearch2;
        f.export.value = "all";
        f.submit();
        f.export.value = "";
    });
</script>
<?php
include_once('./admin.tail.php');