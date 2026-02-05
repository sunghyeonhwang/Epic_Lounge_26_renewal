<?php
$sub_menu = "700640";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

$sql_common = " from cb_unreal_2024_event2_apply a ";
$sql_search = " where (apply_pay_status = 10 or apply_pay_status = 1) and apply_track = '쿠폰' ";

if ($stx) {
    $sql_search .= " and ( ";
    if ($sfl) {
        $sql_search .= " ($sfl like '%$stx%') ";
    }
    $sql_search .= " ) ";
}

$sql_search .= " and apply_temp_yn = 'N' ";

if ($scode) {
    $sql_search .= "and apply_product_code = '$scode'";
    $qstr .= '&amp;scode=' . urlencode($scode);
}
if ($strack) {
    $sql_search .= "and apply_user_ex2 = '$strack'";
    $qstr .= '&amp;strack=' . urlencode($strack);
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
if($sc_md=="free"){
    $sql_search .= " and free_yn = 'Y' ";
    $qstr .= '&amp;sc_md=' . urlencode($sc_md);
}else if($sc_md=="pay"){
    $sql_search .= " and free_yn = 'N' ";
    $qstr .= '&amp;sc_md=' . urlencode($sc_md);
}else if($sc_md=="group"){
    $sql_search .= " and team_yn='Y' ";
    $qstr .= '&amp;sc_md=' . urlencode($sc_md);

}else if($sc_md=="fr"){
    $sql_search .= " and apply_user_job = '외부등록' ";
    $qstr .= '&amp;sc_md=' . urlencode($sc_md);

}
// sfl apply_user_job apply_sector apply_user_ticket
//$qstr .= "&amp;sfl=".
//sst=&sod=&sfl=apply_user_name&stx=&page=4
$qstr .= "&apply_track&=".$_REQUEST["apply_track"]."&apply_user_job=".$_REQUEST["apply_user_job"]."&apply_sector=".$_REQUEST["apply_sector"]."&apply_user_ticket=".$_REQUEST["apply_user_ticket"];

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


$sql_all_cnt = " select count(*) as cnt {$sql_common} where (apply_pay_status = 10 or apply_pay_status = 1) and apply_temp_yn = 'N'  {$sql_order} ";
$row_all_cnt = sql_fetch($sql_all_cnt);


$g5['title'] = '단체등록현황';
include_once('./admin.head.php');

$colspan = 12;
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

        기간 : <input type="text" class="frm_input datepicker " name="start_date" value="<?=$start_date?>" readonly="readonly"> - <input type="text" class="frm_input datepicker" name="end_date" value="<?=$end_date?>" readonly="readonly">


	 <select name="sc_md" id="sc_md">
            <option value=''>--구분--</option>
            <option <?php if($sc_md == '전체'){?>selected<?php } ?> value="">전체</option>
            <option <?php if($sc_md == 'free'){?>selected<?php } ?> value="free">온라인</option>
            <option <?php if($sc_md == 'pay'){?>selected<?php } ?> value="pay">오프라인</option>
        </select>
        <select class="" name="strack">
            <option value=''>--트랙--</option>
            <option value="게임:프로그래밍" <?php if($strack == '게임:프로그래밍'){?>selected<?php } ?>>게임:프로그래밍</option>
            <option value="게임:아트 및 공통" <?php if($strack == '게임:아트 및 공통'){?>selected<?php } ?>>게임:아트 및 공통</option>
            <option value="영화&TV, 애니메이션, 방송" <?php if($strack == '영화&TV, 애니메이션, 방송'){?>selected<?php } ?>>영화&TV, 애니메이션, 방송</option>
            <option value="건축 및 기타" <?php if($strack == '건축 및 기타'){?>selected<?php } ?>>건축 및 기타</option>
        </select>
        <select class="" name="scode">
            <option value=''>--신청구분--</option>
            <option value="STD_ALL" <?php if($scode == 'STD_ALL'){?>selected<?php } ?>>학생양일</option>
            <option value="STD_28" <?php if($scode == 'STD_28'){?>selected<?php } ?>>학생28일</option>
            <option value="STD_29" <?php if($scode == 'STD_29'){?>selected<?php } ?>>학생29일</option>
            <option value="NORMAL_ALL" <?php if($scode == 'NORMAL_ALL'){?>selected<?php } ?>>일반양일</option>
            <option value="NORMAL_28" <?php if($scode == 'NORMAL_28'){?>selected<?php } ?>>일반28일</option>
            <option value="NORMAL_29" <?php if($scode == 'NORMAL_29'){?>selected<?php } ?>>일반29일</option>
        </select>
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
    <form name="fboardlist" id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
        <input type="hidden" name="sst" value="<?php echo $sst ?>">
        <input type="hidden" name="sod" value="<?php echo $sod ?>">
        <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
        <input type="hidden" name="stx" value="<?php echo $stx ?>">
        <input type="hidden" name="scode" value="<?php echo $scode ?>">
        <input type="hidden" name="strack" value="<?php echo $strack ?>">
        <input type="hidden" name="page" value="<?php echo $page ?>">

        <div class="tbl_head01 tbl_wrap">
            <table>
                <caption><?php echo $g5['title']; ?> 목록</caption>
                <thead>
                <tr>
                    <th scope="col">번호</th>
                    <th scope="col">이메일</th>
                    <th scope="col">이름</th>
                    <th scope="col">연락처</th>
                    <th scope="col">트랙</th>
                    <th scope="col">회사,학교</th>
                    <th scope="col">부서,학과</th>
                    <th scope="col">직무,학년</th>
                    <th scope="col">산업/관심분야</th>
                    <th scope="col">결제구분</th>
                     <th scope="col">신청구분</th>
                    <th scope="col">가입일</th>
                    <th>삭제</th>
                    <th scope="col">수신동의</th>
                    <th scope="col">문자재발송</th>
                </tr>
                </thead>
                <tbody>
                <?php
                for ($i=1; $row=sql_fetch_array($result); $i++) {
                    $bg = 'bg'.($i%2);
                    $pay_method = $row["pay_paymethod"];
                    $mpay_type = $row["mpay_type"];
                    $str_pay_method = "";
                    if($pay_method == "Card"){
                        $str_pay_method = "신용카드";
                    }else if($pay_method == "VCard"){
                        $str_pay_method = "신용카드";
                    }else if($pay_method == "VBank"){
                        if($row["apply_pay_status"] == "1"){
                            $str_pay_method = "가상계좌[미입금]";
                        }else{
                            $str_pay_method = "가상계좌";
                        }

                    }else if($mpay_type == "CARD"){
                        $str_pay_method = "신용카드";

                    }else if($pay_method == "VCARD"){
                        $str_pay_method = "신용카드";
                    }else  if($mpay_type == "VBANK"){
                        if($row["apply_pay_status"] == "1"){
                            $str_pay_method = "가상계좌[미입금]";
                        }else{
                            $str_pay_method = "가상계좌";
                        }

                    }

                    $str_pay_method = "단체";

                    ?>

                    <tr class="<?php echo $bg; ?>">
                        <td class="td_chk">
                            <?php echo $total_count+1-($i+(($page-1)*$rows)); ?>
                        </td>
                        <td><?=$row['apply_user_email']?></td>
                        <td><?=$row['apply_user_name']?></td>
                        <td><?=$row['apply_user_phone']?></td>
                        <td><select name="apply_user_ex2" id="apply_user_ex2_<?=$row['apply_no']?>" onchange="change_product('<?=$row['apply_no']?>')">
                                <option value="" <?php if($row['apply_user_ex2'] == ''){?>selected<?php } ?>>==트랙선택==</option>
                                <option value="게임:프로그래밍" <?php if($row['apply_user_ex2'] == '게임:프로그래밍'){?>selected<?php } ?>>게임:프로그래밍</option>
                                <option value="게임:아트 및 공통" <?php if($row['apply_user_ex2'] == '게임:아트 및 공통'){?>selected<?php } ?>>게임:아트 및 공통</option>
                                <option value="영화&TV, 애니메이션, 방송" <?php if($row['apply_user_ex2'] == '영화&TV, 애니메이션, 방송'){?>selected<?php } ?>>영화&TV, 애니메이션, 방송</option>
                                <option value="건축 및 기타" <?php if($row['apply_user_ex2'] == '건축 및 기타'){?>selected<?php } ?>>건축 및 기타</option>
                            </select>
                        </td>
                        <td><?=$row['apply_user_company']?></td>
                        <td><?=$row['apply_user_depart']?></td>
                        <td><?=$row['apply_user_grade']?></td>
                        <td><?=$row['apply_user_ex1']?></td>
                       <td>
                            <?=$str_pay_method?>
                            <?
                                if($str_pay_method){
                                    ?><br />
                                    <!--<a href="#n" class="btn btn_02" onclick="pop_pay_log('<?=$i?>')" >로그보기</a>-->
                                    <div style="display: none;" id="pop_log_pan_<?=$i?>"><?=$row["pay_result_map"]?><?=str_replace("&","\n",$row["mpay_resultmap"])?></div>
                                    <?
                                }
                            ?>

                        </td>
                        <td><select name="apply_product_code" id="apply_product_code_<?=$row['apply_no']?>" onchange="change_product('<?=$row['apply_no']?>')">
                                <option value="NORMAL_ALL" <?php if($row['apply_product_code'] == 'NORMAL_ALL'){?>selected<?php } ?>>양일권</option>
                                <option value="NORMAL_28" <?php if($row['apply_product_code'] == 'NORMAL_28'){?>selected<?php } ?>>28일권</option>
                                <option value="NORMAL_29" <?php if($row['apply_product_code'] == 'NORMAL_29'){?>selected<?php } ?>>29일권</option>
                            </select></td>
                        <td><?=$row['apply_reg_datetime']?></td>
                        <td>
                            <a style="font-weight: 900;" href="javascript:;" onclick="<?=!$str_pay_method ?"del":"del_pay"?>(<?=$row['apply_no']?>)">
                                삭제
                            </a>
                        </td>
                        <td><?=$row['apply_user_event_agree']  =="1" ? "동의":"미동의"?></td>
                        <td><a style="font-weight: 900;" href="#n" onclick="sms_send(<?=$row['apply_no']?>)">문자재발송</a></td>
                    </tr>
                    <?php
                }
                if ($i == 0)
                    echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
                ?>
                </tbody>
            </table>
        </div>
        <link rel="stylesheet" href="//rawgit.com/Soldier-B/jquery.toast/master/jquery.toast/jquery.toast.min.css" />
        <script src="//rawgit.com/Soldier-B/jquery.toast/master/jquery.toast/jquery.toast.min.js"></script>
        <script>
            function sms_send(apply_no){
                if(confirm("문자재발송 하시겠습니까?") == false) return false;
                $.ajax({
                    type: "POST",
                    url: "2024_event2_list_group_sms_ajax.php",
                    data: {apply_no:apply_no,sms_send:"Y"},
                    success: function(data){

                        // 공백 제거
                        var trimmedData = $.trim(data);
                        // JSON 파싱
                        var parsedData = JSON.parse(trimmedData);
                        // msg 값 가져오기
                        var message = parsedData.msg;
                        // alert으로 msg 표시
                        $.toast(message, {
                            sticky: false,
                            duration: 2000,
                            type: 'success'
                        });
                    }
                });
            }
            function change_product(apply_no){
                var apply_product_code = $("#apply_product_code_"+apply_no).val();
                var apply_user_ex2 = $("#apply_user_ex2_"+apply_no).val();
                $.ajax({
                    type: "POST",
                    url: "2024_event2_list_group_update_ajax.php",
                    data: {apply_no:apply_no,apply_product_code:apply_product_code,apply_user_ex2:apply_user_ex2},
                    success: function(data){
                        $.toast('수정되었습니다.', {
                            sticky: false,
                            duration: 2000,
                            type: 'success'
                        });
                    }
                });

            }
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
    <form class="form-inline" name="fsearch2" action="https://epiclounge.co.kr/v3/adm/2024_event2_list_group_excel.php" method="get">
        <input type="hidden" name="export" value="">
        <input type="hidden" name="start_date" value="<?=$start_date?>">
        <input type="hidden" name="end_date" value="<?=$end_date?>">
        <input type="hidden" name="stx" value="<?=$stx?>">
        <input type="hidden" name="sfl" value="<?=$sfl?>">
        <input type="hidden" name="scode" value="<?php echo $scode ?>">
        <input type="hidden" name="strack" value="<?php echo $strack ?>">


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
                location.replace('2024_event2_findApplyByEmail.php?mode2=del&no='+val);
            }
        }
        function del_pay(val){
            if (confirm('삭제 및 환불처리를 진행 하시겠습니까?')) {
                location.replace('2024_event2_findApplyByEmail.php?mode2=del2&no='+val);
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