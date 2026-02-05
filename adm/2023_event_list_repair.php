<?php
$sub_menu = "700100";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

$sc_md = "pay";
$apply_user_job = "학생/교육기관";
$sql_common = " from cb_unreal_2023_event_apply a ";
$sql_search = " where (apply_pay_status = 10 or apply_pay_status = 1) ";

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
if($sc_md=="free"){
    $sql_search .= " and free_yn = 'Y' ";
    $qstr .= '&amp;sc_md=' . urlencode($sc_md);
}else if($sc_md=="pay"){
    $sql_search .= " and free_yn = 'N' ";
    $qstr .= '&amp;sc_md=' . urlencode($sc_md);
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


$sql_all_cnt = " select count(*) as cnt {$sql_common} where (apply_pay_status = 10 or apply_pay_status = 1)  {$sql_order} ";
$row_all_cnt = sql_fetch($sql_all_cnt);


$sql_free_cnt = " select count(*) as cnt {$sql_common} where (apply_pay_status = 10 or apply_pay_status = 1) and free_yn='Y' ";
$row_free_cnt = sql_fetch($sql_free_cnt);

$sql_pay_cnt = " select count(*) as cnt {$sql_common} where (apply_pay_status = 10 or apply_pay_status = 1) and free_yn='N' ";
$row_pay_cnt = sql_fetch($sql_pay_cnt);

$sql_refund_cnt = " select count(*) as cnt {$sql_common} where IFNULL(refund_date,'') <> '' ";
$row_refund_cnt = sql_fetch($sql_refund_cnt);


$sql_wait_cnt = " select count(*) as cnt from cb_unreal_2023_event_apply_inquire ";
$row_wait_cnt = sql_fetch($sql_wait_cnt);



$g5['title'] = '이벤트신청복구리스트';
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
            <a href="2023_event_list.php" class="btn btn_03 ft_11">전체 <?=$row_all_cnt["cnt"]?>명</a>
            <a href="2023_event_list.php?sc_md=pay" class="btn btn_02 ft_11">오프라인  <?=$row_pay_cnt["cnt"]?>명</a>
            <a href="2023_event_list.php?sc_md=free" class="btn btn_02 ft_11">온라인   <?=$row_free_cnt["cnt"]?>명</a>
            <a href="2023_event_wait_list.php" class="btn btn_02 ft_11">대기   <?=$row_wait_cnt["cnt"]?>명</a>
        </div>


        기간 : <input type="text" class="frm_input datepicker " name="start_date" value="<?=$start_date?>" readonly="readonly"> - <input type="text" class="frm_input datepicker" name="end_date" value="<?=$end_date?>" readonly="readonly">


        <select name="sc_md" id="sc_md">
            <option value=''>--구분--</option>
            <option <?php if($sc_md == '전체'){?>selected<?php } ?> value="">전체</option>
            <option <?php if($sc_md == 'free'){?>selected<?php } ?> value="free">온라인</option>
            <option <?php if($sc_md == 'pay'){?>selected<?php } ?> value="pay">오프라인</option>
        </select>
        <select name="apply_user_job" id="apply_user_job">
            <option value=''>--소속--</option>
            <option <?php if($apply_user_job == '직장인'){?>selected<?php } ?>>직장인</option>
            <!--<option <?php /*if($apply_user_job == '학생'){*/?>selected<?php /*} */?>>학생</option>-->
            <option <?php if($apply_user_job == '학생/교육기관'){?>selected<?php } ?>>학생/교육기관</option>
            <option <?php if($apply_user_job == '인디/프리랜서'){?>selected<?php } ?>>인디/프리랜서</option>
            <option <?php if($apply_user_job == '기타'){?>selected<?php } ?>>기타</option>
        </select>
        <select name="apply_sector" id="apply_sector">
            <option value=''>--산업/관심분야--</option>
            <option <?php if($apply_sector == '게임'){?>selected<?php } ?>>게임</option>
            <option <?php if($apply_sector == '건축'){?>selected<?php } ?>>건축</option>
            <option <?php if($apply_sector == '자동차&운송'){?>selected<?php } ?>>자동차&amp;운송</option>
            <option <?php if($apply_sector == '영화 & TV'){?>selected<?php } ?>>영화 &amp; TV</option>
            <option <?php if($apply_sector == '방송&라이브 이벤트'){?>selected<?php } ?>>방송&amp;라이브 이벤트</option>
            <option <?php if($apply_sector == '제조'){?>selected<?php } ?>>제조</option>
            <option <?php if($apply_sector == '소프트웨어&툴 개발'){?>selected<?php } ?>>소프트웨어&amp;툴 개발</option>
            <option <?php if($apply_sector == 'VR/AR'){?>selected<?php } ?>>VR/AR</option>
            <option <?php if($apply_sector == '교육'){?>selected<?php } ?>>교육</option>
            <option <?php if($apply_sector == '기타'){?>selected<?php } ?>>기타</option>
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

    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <!-- <input type="hidden" name="token" value="<?php echo isset($token) ? $token : ''; ?>"> -->

    <div class="tbl_head01 tbl_wrap">
        <table>
            <caption><?php echo $g5['title']; ?> 목록</caption>
            <thead>
            <tr>
                <th scope="col">번호</th>
                <th scope="col">이름</th>
                <th scope="col">이메일</th>
                <th scope="col">연락처</th>
                <th scope="col">직업</th>
                <th scope="col">회사,학교</th>
                <th scope="col">부서,학과</th>
                <th scope="col">직무,학년</th>
                <th scope="col">산업/관심분야</th>
                <th scope="col">선택세션</th>
                <!-- <th scope="col">결제구분</th>-->
                <!-- <th scope="col">네이버</th>
                 <th scope="col">상품</th> -->
                <th scope="col">구분</th>
                <th scope="col">가입일</th>
                <th>메모</th>
                <th>수정</th>
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
                    <form action="2023_repair.php?<?=$qstr?>" method="post" id="frm_<?=$row["apply_no"]?>">
                        <input type="hidden" name="apply_no" id="apply_no" value="<?=$row["apply_no"]?>">

                        <td class="td_chk">
                            <?php echo $total_count+1-($i+(($page-1)*$rows)); ?>
                        </td>
                        <td><?=$row['apply_user_name']?></td>
                        <td><?=$row['apply_user_email']?></td>
                        <td><?=substr($row['apply_user_phone'], 0, 3) . "-" . substr($row['apply_user_phone'], 3, 4) . "-" . substr($row['apply_user_phone'], 7, 4)?></td>
                        <td><?=$row['apply_user_job']?></td>
                        <td><input type="text" name="apply_user_company" value="<?=$row['apply_user_company']?>" /></td>
                        <td><input type="text" name="apply_user_depart" value="<?=$row['apply_user_depart']?>" /></td>
                        <td><input type="text" name="apply_user_grade" value="<?=$row['apply_user_grade']?>" /></td>
                        <td><?=$row['apply_sector']?></td>
                        <td>
                            <?
                            $arrayList = '';
                            if ($row['apply_user_ex1']) {
                                $arrayList[] = '8월29일(화)-공통<br />';
                            }
                            if ($row['apply_user_ex2']) {
                                $arrayList[] = '8월30일(수)-게임<br />';
                            }
                            if ($row['apply_user_ex3']) {
                                $arrayList[] = '8월31일(목)-영화&TV/라이브 이벤트/애니메이션<br />';
                            }
                            if ($row['apply_user_ex4']) {
                                $arrayList[] = '9월1일(금)-AEC/자동차/시뮬레이션<br />';
                            }
                            echo implode(', ', $arrayList);
                            ?>
                        </td>
                        <!--  <td>
                            <?=$str_pay_method?>
                            <?
                        if($str_pay_method){
                            ?><br />
                                    <a href="#n" class="btn btn_02" onclick="pop_pay_log('<?=$i?>')" >로그보기</a>
                                    <div style="display: none;" id="pop_log_pan_<?=$i?>"><?=$row["pay_result_map"]?><?=str_replace("&","\n",$row["mpay_resultmap"])?></div>
                                    <?
                        }
                        ?>

                        </td>-->
                        <!--  <td><?=$row['apply_user_email2']?></td>
      <td><?=$row['apply_product_name']?></td> -->
                        <td><?=$row['free_yn'] == "N"?"오프라인":"온라인"?></td>
                        <td><?=$row['apply_reg_datetime']?></td>
                        
                        <td><input type="text" name="repair_memo" value="<?=$row['repair_memo']?>" /></td>
                        <td>
                            <a style="font-weight: 900;" href="javascript:;" onclick="repair('<?=$row["apply_no"]?>')">
                                수정
                            </a>
                        </td>
                    </form>
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
        function repair(no){
            if(confirm("수정하시겠습니까?")){
                $.post("2023_repair_ajax.php",$("#frm_"+no).serialize(),function(data){
                    alert(data);
                });
                //$("#frm_"+no).submit();
            }
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


    <form class="form-inline" name="fsearch2" action="https://epiclounge.co.kr/v3/adm/2023_event_export_excel.php" method="get">
        <input type="hidden" name="export" value="">
        <input type="hidden" name="start_date" value="<?=$start_date?>">
        <input type="hidden" name="end_date" value="<?=$end_date?>">
        <input type="hidden" name="stx" value="<?=$stx?>">
        <input type="hidden" name="sfl" value="<?=$sfl?>">
        <input type="hidden" name="sc_md" value="<?=$sc_md?>">


        <input type="hidden" name="apply_user_job" id="ex_apply_user_job" value="<?=$apply_user_job?>">
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