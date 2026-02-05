<?php
$sub_menu = "700710";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

$sql_common = " from cb_unreal_2025_event2_apply a ";
$sql_search = " where (apply_pay_status = 10 or apply_pay_status = 1) ";

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
    $sql_search .= "and apply_track like '%$strack%'";
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


$sql_all_cnt = " select count(*) as cnt {$sql_common} where (apply_pay_status = 10 or apply_pay_status = 1) and apply_temp_yn = 'N'    {$sql_order} ";
$row_all_cnt = sql_fetch($sql_all_cnt);


$sql_free_cnt = " select count(*) as cnt {$sql_common} where (apply_pay_status = 10 or apply_pay_status = 1) and apply_temp_yn = 'N' and free_yn='Y'   ";
$row_free_cnt = sql_fetch($sql_free_cnt);

$sql_pay_cnt = " select count(*) as cnt {$sql_common} where (apply_pay_status = 10 or apply_pay_status = 1) and apply_temp_yn = 'N' and free_yn='N'  ";
$row_pay_cnt = sql_fetch($sql_pay_cnt);

$sql_refund_cnt = " select count(*) as cnt {$sql_common} where IFNULL(refund_date,'') <> '' ";
$row_refund_cnt = sql_fetch($sql_refund_cnt);


$sql_wait_cnt = " select count(*) as cnt from cb_unreal_2025_event2_apply_inquire ";
$row_wait_cnt = sql_fetch($sql_wait_cnt);



$sql_group_cnt = " select count(*) as cnt {$sql_common} where (apply_pay_status = 10 or apply_pay_status = 1)  and team_yn='Y' {$sql_order} ";
$row_group_cnt = sql_fetch($sql_group_cnt);

$sql_fr_cnt = " select count(*) as cnt {$sql_common} where apply_user_job = '외부등록' ";
$row_fr_cnt = sql_fetch($sql_fr_cnt);



$sql = "select count(*) as cnt from cb_unreal_2025_event2_apply where apply_track <> '쿠폰' and apply_pay_status <> 0 and apply_temp_yn = 'N' and apply_product_code like 'NORMAL%'  and (apply_product_code like '%ALL') and apply_coupon_no < 10000 ";
$track_nor_all = sql_fetch($sql);


$sql = "select count(*) as cnt from cb_unreal_2025_event2_apply where apply_track <> '쿠폰' and apply_pay_status <> 0 and apply_temp_yn = 'N' and apply_product_code like 'NORMAL%'  and (apply_product_code like '%25') and apply_coupon_no < 10000 ";
$track_nor_25 = sql_fetch($sql);


$sql = "select count(*) as cnt from cb_unreal_2025_event2_apply where apply_track <> '쿠폰' and apply_pay_status <> 0 and apply_temp_yn = 'N' and apply_product_code like 'NORMAL%'  and (apply_product_code like '%26') and apply_coupon_no < 10000 ";
$track_nor_26 = sql_fetch($sql);


$row_ticket = sql_fetch(" SELECT SUM(CASE WHEN name LIKE '%DAY1%' THEN date1 ELSE 0 END) AS date1,  SUM(CASE WHEN name LIKE '%DAY2%' THEN date2 ELSE 0 END) AS date2 FROM 2025_event_ticket WHERE name <> '쿠폰' ");
//print_r($row_ticket);


$g5['title'] = '이벤트신청리스트';
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

        <?
        $offline_txt = "오프라인 ".$row_pay_cnt["cnt"]." (";
        $offline_txt .= "양일권 ".$track_nor_all["cnt"]." / ";
        $offline_txt .= "25 일권 ".$track_nor_25["cnt"]." / ";
        $offline_txt .= "26 일권 ".$track_nor_26["cnt"]." / ";
        ?>
	<div class="local_ov01 local_ov">
        <span style="font-size: 18px">
        <strong>오프라인</strong>:
        <br>25일권 <strong><?=($track_nor_25["cnt"] + $track_nor_all["cnt"] + $track_std_25["cnt"] + $track_std_all["cnt"])?>명</strong> / <?=number_format($row_ticket["date1"])?>명 (일반:<?=$track_nor_25["cnt"] + $track_nor_all["cnt"]?>명) | <span style="color: #535353">D1. 잔여석: <?=$row_ticket["date1"]-($track_nor_25["cnt"] + $track_nor_all["cnt"] + $track_std_25["cnt"] + $track_std_all["cnt"])?>석</span>

        <br>26일권 <strong><?=($track_nor_26["cnt"] + $track_nor_all["cnt"] + $track_std_26["cnt"] + $track_std_all["cnt"])?>명</strong> / <?=number_format($row_ticket["date2"])?>명 (일반:<?=$track_nor_26["cnt"] + $track_nor_all["cnt"]?>명) | <span style="color: #535353">D2. 잔여석:  <?=$row_ticket["date2"] - ($track_nor_26["cnt"] + $track_nor_all["cnt"] + $track_std_26["cnt"] + $track_std_all["cnt"])?>석</span>
        <br><br></span>

        <a href="2025_event2_list.php" class="btn btn_03 ft_11">전체 <?=$row_pay_cnt["cnt"] + $row_free_cnt["cnt"]?>명</a>
        <a href="2025_event2_list.php?sc_md=pay" class="btn <?=$sc_md == "pay" ? "btn_03":"btn_02"?> ft_11">오프라인 <?=$row_pay_cnt["cnt"]?>명</a>
        <a href="2025_event2_list.php?scode=NORMAL_ALL" class="btn  <?=$scode == "NORMAL_ALL" ? "btn_03":"btn_02"?>  ft_11"><?="양일권 ".$track_nor_all["cnt"]?>명</a>
        <a href="2025_event2_list.php?scode=NORMAL_25" class="btn  <?=$scode == "NORMAL_25" ? "btn_03":"btn_02"?>  ft_11"><?="25 일권 ".$track_nor_25["cnt"]?>명</a>
        <a href="2025_event2_list.php?scode=NORMAL_26" class="btn  <?=$scode == "NORMAL_26" ? "btn_03":"btn_02"?>  ft_11"><?="26 일권 ".$track_nor_26["cnt"]?>명</a>
        &nbsp;
        &nbsp;<a href="2025_event2_list.php?sc_md=free" class="btn <?=$sc_md == "free" ? "btn_03":"btn_02"?> ft_11">온라인   <?=$row_free_cnt["cnt"]?>명</a>
        <!-- <a href="2025_event2_wait_list.php" class="btn btn_02 ft_11">대기   <?=$row_wait_cnt["cnt"]?>명</a>-->


</div>


        기간 : <input type="text" class="frm_input datepicker " name="start_date" value="<?=$start_date?>" readonly="readonly"> - <input type="text" class="frm_input datepicker" name="end_date" value="<?=$end_date?>" readonly="readonly">


	 <select name="sc_md" id="sc_md">
            <option value=''>--구분--</option>
            <option <?php if($sc_md == '전체'){?>selected<?php } ?> value="">전체</option>
            <option <?php if($sc_md == 'free'){?>selected<?php } ?> value="free">온라인</option>
            <option <?php if($sc_md == 'pay'){?>selected<?php } ?> value="pay">오프라인</option>
        </select>
        <select class="" name="strack">
            <option value=''>--트랙--</option>
            <option value="DAY1_TR1" <?php if($strack == 'DAY1_TR1'){?>selected<?php } ?>>25일 : 게임: 프로그래밍</option>
            <option value="DAY1_TR2" <?php if($strack == 'DAY1_TR2'){?>selected<?php } ?>>25일 : 미디어 & 엔터테인먼트</option>
            <option value="DAY1_TR3" <?php if($strack == 'DAY1_TR3'){?>selected<?php } ?>>25일 : 게임: 아트</option>
            <option value="DAY2_TR1" <?php if($strack == 'DAY2_TR1'){?>selected<?php } ?>>26일 : 게임</option>
            <option value="DAY2_TR2" <?php if($strack == 'DAY2_TR2'){?>selected<?php } ?>>26일 : 미디어 & 엔터테인먼트</option>
            <option value="DAY2_TR3" <?php if($strack == 'DAY2_TR3'){?>selected<?php } ?>>26일 : 제조 및 시뮬레이션</option>
        </select>
        <select class="" name="scode">
            <option value=''>--신청구분--</option>
            <option value="NORMAL_ALL" <?php if($scode == 'NORMAL_ALL'){?>selected<?php } ?>>일반양일</option>
            <option value="NORMAL_25" <?php if($scode == 'NORMAL_25'){?>selected<?php } ?>>일반25일</option>
            <option value="NORMAL_26" <?php if($scode == 'NORMAL_26'){?>selected<?php } ?>>일반26일</option>
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
                    <th scope="col">직업</th>
                    <th scope="col">회사/소속</th>
                    <!-- <th scope="col">부서,학과</th> -->
                    <th scope="col">직무</th>
                    <th scope="col">산업/관심분야</th>
                    <th scope="col">결제구분</th>
                     <th scope="col">신청구분</th>
                    <th scope="col">가입일</th>
                    <th>삭제</th>
                    <th scope="col">수신동의</th>
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

                    ?>

                    <tr class="<?php echo $bg; ?>">
                        <td class="td_chk">
                            <?php echo $total_count+1-($i+(($page-1)*$rows)); ?>
                        </td>
                        <td><?=$row['apply_user_email']?></td>
                        <td><?=$row['apply_user_name']?></td>
                        <td><?=$row['apply_user_phone']?></td>
                        <td><?=$row['apply_track']?></td>
                        <td><?=$row['apply_user_job']?></td>
                        <td><?=$row['apply_user_company']?></td>
                       <!-- <td><?=$row['apply_user_depart']?></td> -->
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
                        <td><?php
                                if($row['free_yn'] == 'Y'){
                                    echo "온라인";
                                }else if($row['apply_product_code'] == "NORMAL_ALL"){
                                    echo "양일권";
                                }else if($row['apply_product_code'] == "NORMAL_25"){
                                    echo "25일권";
                                }else if($row['apply_product_code'] == "NORMAL_26"){
                                    echo "26일권";
                                }
                            ?></td>
                        <td><?=$row['apply_reg_datetime']?></td>
                        <td>
                            <a style="font-weight: 900;" href="javascript:;" onclick="<?=!$str_pay_method ?"del":"del_pay"?>(<?=$row['apply_no']?>)">
                                삭제
                            </a>
                        </td>
                        <td><?=$row['apply_user_event_agree']  =="1" ? "동의":"미동의"?></td>
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
    <form class="form-inline" name="fsearch2" action="https://epiclounge.co.kr/v3/adm/2025_event2_export_excel.php" method="get">
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
                location.replace('2025_event2_findApplyByEmail.php?mode2=del&no='+val);
            }
        }
        function del_pay(val){
            if (confirm('삭제 및 환불처리를 진행 하시겠습니까?')) {
                location.replace('2025_event2_findApplyByEmail.php?mode2=del2&no='+val);
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