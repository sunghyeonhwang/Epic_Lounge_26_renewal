<?php
$sub_menu = "700520";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

$sql_common = " from cb_unreal_2024_speaker_apply a ";
$sql_search = " where 1=1  ";

if ($stx) {
    $sql_search .= " and ( ";
    if ($sfl) {
        $sql_search .= " ($sfl like '%$stx%') ";
    }
    $sql_search .= " ) ";
}

if ($apply_user_job) {
    $sql_search .= "and apply_user_job = '$apply_user_job'";
}

if ($apply_sector) {
    $sql_search .= "and apply_sector = '$apply_sector'";
}

if ($start_date) {
    $sql_search .= "and apply_reg_datetime > '$start_date'";
}
if ($end_date) {
    $sql_search .= "and apply_reg_datetime < '$end_date 23:59:59'";
}
// sfl apply_user_job apply_sector apply_user_ticket
//$qstr .= "&amp;sfl=".
//sst=&sod=&sfl=apply_user_name&stx=&page=4
$qstr .= "&apply_user_job=".$_REQUEST["apply_user_job"]."&apply_sector=".$_REQUEST["apply_sector"]."&apply_user_ticket=".$_REQUEST["apply_user_ticket"];

$sst  = "a.apply_no";
$sod = "desc";
$sql_order = " order by $sst $sod ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

if (!$rows) {
    $rows = 51;
}
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);


$g5['title'] = '이벤트신청리스트';
include_once('./admin.head.php');

$colspan = 15;
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

	<div class="local_ov01 local_ov">
    <a href="2024_event_list.php" class="btn btn_03 ft_11">전체 <?=$row_all_cnt["cnt"]?>명</a>
</div>

        <select class="" name="sfl">
            <option value="apply_user_name" <?php if($sfl == 'apply_user_name'){?>selected<?php } ?>>이름</option>
            <option value="apply_user_email" <?php if($sfl == 'apply_user_email'){?>selected<?php } ?>>이메일</option>
            <option value="apply_user_phone" <?php if($sfl == 'apply_user_phone'){?>selected<?php } ?>>전화번호</option>
        </select>
        <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
        <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
        <input type="submit" value="검색" class="btn_submit">
        <button type="button" class="btn btn-outline btn-success btn-sm" id="export_to_excel"><i class="fa fa-file-excel-o"></i> 엑셀 다운로드</button>
    </form>
    <p>검색된 리스트 : 총(<?=$total_count?>명)<!--, 수신동의(<?php /*=$total_count_apply_user_event_agree*/?>명), 수신동의 비율(<?php /*=round($total_count_apply_user_event_agree/$total_count*100,2)*/?>%)--></p>
    <form name="fboardlist" id="fboardlist" action="#b" onsubmit="return fboardlist_submit(this);" method="post">
        <input type="hidden" name="sst" value="<?php echo $sst ?>">
        <input type="hidden" name="sod" value="<?php echo $sod ?>">
        <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
        <input type="hidden" name="stx" value="<?php echo $stx ?>">
        <input type="hidden" name="page" value="<?php echo $page ?>">
        <!-- <input type="hidden" name="token" value="<?php echo isset($token) ? $token : ''; ?>"> -->

        <div class="tbl_head01 tbl_wrap">
            <table>
                <captio><?php echo $g5['title']; ?> 목록</captio>
                <colgroup>
                    <col style="width:70px">
                    <col style="width:70px">
                    <col style="width:110px">
                    <col style="width:70px">
                    <col style="width:100px">
                    <col style="width:300px">
                    <col style="width:70px">
                    <col style="width:70px">
                    <col style="width:200px">
                    <col style="width:200px">
                    <col style="width:70px">
                    <col style="width:70px">

                </colgroup>
                <thead>
                <tr>
                    <th scope="col" rowspan="3">번호</th>
                    <th scope="col">이름</th>
                    <th scope="col">이메일</th>
                    <th scope="col">연락처</th>
                    <th scope="col">회사</th>
                    <th scope="col">분야</th>
                    <th scope="col">세션시간</th>
                    <th scope="col">난이도</th>
                    <th scope="col">주제</th>
                    <th scope="col">플랫폼</th>
                    <th scope="col">가입일</th>
                    <th rowspan="3">삭제</th>
                </tr>
                <tr>
                    <th scope="col" colspan="2">연혁</th>
                    <th scope="col" colspan="2">세션제목</th>
                    <th scope="col" colspan="2">세션소개</th>
                    <th scope="col" colspan="2">요청사항</th>
                    <th scope="col" colspan="2">세션이미지</th>
                </tr>
                <tr>
                    <th scope="col" colspan="2">세션플랫폼</th>
                    <th scope="col" colspan="2">청강대상</th>
                    <th scope="col" colspan="2">발표자료PDF</th>
                    <th scope="col" colspan="2">발표영상</th>
                    <th scope="col" colspan="2">목차</th>
                </tr>
                </thead>
                <tbody>
                <?php
                for ($i=1; $row=sql_fetch_array($result); $i++) {
                    $bg = 'bg'.($i%2);
                    ?>

                    <tr class="<?php echo $bg; ?>">
                        <td class="td_chk" rowspan="3">
                            <?php echo $total_count+1-($i+(($page-1)*$rows)); ?>
                        </td>
                        <td><?=$row['apply_user_name']?></td>
                        <td><?=$row['apply_user_email']?></td>
                        <td><?=$row['apply_user_phone']?></td>
                        <td><?=$row['apply_user_company']?><?=$row["etc1"] ? "(".$row["etc1"].")":""?></td>
                        <td><?=$row['apply_pt_category']?></td>
                        <td><?=$row['apply_pt_runtime']?></td>
                        <td><?=$row['apply_pt_difficulty']?></td>
                        <td><?=$row['apply_pt_subject']?><?=$row["etc3"] ? "(".$row["etc3"].")":""?></td>
                        <td><?=$row['apply_pt_platform']?><?=$row["etc2"] ? "(".$row["etc2"].")":""?></td>
                        <td><?=$row['apply_reg_datetime']?></td>
                        <td rowspan="3">
                            <a style="font-weight: 900;" href="javascript:;" onclick="del(<?=$row['apply_no']?>)">
                                삭제
                            </a>
                        </td>
                    </tr>
                    <tr class="<?php echo $bg; ?>">
                        <td colspan="2"><?=$row['apply_user_history']?></td>
                        <td colspan="2"><?=$row['apply_pt_title']?></td>
                        <td colspan="2"><?=$row['apply_pt_intro']?></td>
                        <td colspan="2"><?=$row['apply_pt_request']?></td>
                        <td colspan="2"><? if(!empty($row['apply_photo_path'])){?><a href="/v3/data/file/speak/<?=$row['apply_photo_path']?>">세션이미지</a><?}?></td>
                    </tr>
                    <tr class="<?php echo $bg; ?>">
                        <td colspan="2"><?=$row['apply_add_field1']?><?=$row["etc4"] ? "(".$row["etc4"].")":""?></td>
                        <td colspan="2"><?=$row['apply_add_field2']?></td>
                        <td colspan="2"><?=$row['apply_add_field3']?></td>
                        <td colspan="2"><?=$row['apply_add_field4']?></td>
                        <td colspan="2"><?=$row['apply_add_field5']?></td>
                    </tr>
                    <?php
                }
                if ($i == 0)
                    echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
                ?>
                </tbody>
            </table>
        </div>
        <script>
            function pop_pay_log(num){
                //$("#pop_log_pan_"+num).text();
                $("#modal_log_textarea").text($("#pop_log_pan_"+num).text())
                $('#pay_log_modal').modal('toggle')
            }
        </script>
        <style>
            .pagination {width:100%; margin:auto; text-align:center}
            .pagination ul { width:520px; margin:auto; text-align:center}
            .pagination li {float:left; margin: 10px;}
            .pagination li .active{ font-weight: 800;}
        </style>
        <?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

    </form>
    <form class="form-inline" name="fsearch2" action="https://epiclounge.co.kr/v3/adm/2024_event_export_excel.php" method="get">
        <input type="hidden" name="export" value="">
        <input type="hidden" name="start_date" value="<?=$start_date?>">
        <input type="hidden" name="end_date" value="<?=$end_date?>">
        <input type="hidden" name="stx" value="<?=$stx?>">
        <input type="hidden" name="sfl" value="<?=$sfl?>">


        <input type="hidden" name="sc_md" value="<?=$sc_md?>">


        <input type="hidden" name="apply_user_job" id="ex_apply_user_job" value="<?=$apply_user_job?>">
        <input type="hidden" name="apply_sector" id="ex_apply_sector"  value="<?=$apply_sector?>">
    </form>
    <script type="text/javascript">
        $(document).on('click', '#export_to_excel', function() {
            var f = document.fsearch2;
            f.export.value = "excel";
            f.submit();
            f.export.value = "";
        });
        function del(val){
            if (confirm('삭제 하시겠습니까?')) {
                location.replace('2024_findApplyByEmail.php?mode2=del&no='+val);
            }
        }
    </script>

    <div class="modal fade" id="pay_log_modal" tabindex="-1" aria-labelledby="admin_modfy" aria-hidden="true">
        <div class="modal-dialog" style="width: 650px;">
            <div class="modal-content " style="max-height: 650px;overflow-y: scroll;">
                <div class="modal-body body_pop_bid">
                    <textarea style="width: 600px;height: 600px;" id="modal_log_textarea"></textarea>
                </div>
            </div>
        </div>
    </div>
<?php
include_once('./admin.tail.php');