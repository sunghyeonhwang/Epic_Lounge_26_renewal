<?php
$sub_menu = "700100";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

$sql_common = " from cb_unreal_2023_event_apply a ";
$sql_search = " where apply_pay_status = 10 ";

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
    $sql_search .= "and apply_reg_datetime < '$end_date'";
}

$sst  = "a.apply_no";
$sod = "desc";
$sql_order = " order by $sst $sod ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$sql_apply_user_event_agree = " select count(*) as cnt {$sql_common} {$sql_search} and apply_user_event_agree = '1' {$sql_order} ";
$row_apply_user_event_agree = sql_fetch($sql_apply_user_event_agree);
$total_count_apply_user_event_agree = $row_apply_user_event_agree['cnt'];

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
        기간 : <input type="text" class="frm_input datepicker " name="start_date" value="<?=$start_date?>" readonly="readonly"> - <input type="text" class="frm_input datepicker" name="end_date" value="<?=$end_date?>" readonly="readonly">

        <select name="apply_user_job" id="apply_user_job">
            <option value=''>--부문--</option>
            <option <?php if($apply_user_job == '직장인'){?>selected<?php } ?>>직장인</option>
            <option <?php if($apply_user_job == '학생'){?>selected<?php } ?>>학생</option>
            <option <?php if($apply_user_job == '교육자/교육기관'){?>selected<?php } ?>>교육자/교육기관</option>
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
    <p>검색된 리스트 : 총(<?=$total_count?>명), 수신동의(<?=$total_count_apply_user_event_agree?>명), 수신동의 비율(<?=round($total_count_apply_user_event_agree/$total_count*100,2)?>%)</p>
    <form name="fboardlist" id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
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
                    <!-- <th scope="col">네이버</th>
                     <th scope="col">상품</th> -->
                    <th scope="col">수신동의</th>
                    <th scope="col">가입일</th>
                    <th>삭제</th>
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
                        <td><?=$row['apply_user_job']?></td>
                        <td><?=$row['apply_user_company']?></td>
                        <td><?=$row['apply_user_depart']?></td>
                        <td><?=$row['apply_user_grade']?></td>
                        <td><?=$row['apply_sector']?></td>
                        <td>
                            <?
                            $arrayList = '';
                            if ($row['apply_user_ex1']) {
                                $arrayList[] = '9월 27일(화)-공통<br />';
                            }
                            if ($row['apply_user_ex2']) {
                                $arrayList[] = '9월 28일(수)-게임<br />';
                            }
                            if ($row['apply_user_ex3']) {
                                $arrayList[] = '9월 29일(목)-영화, TV &amp;<br />';
                            }
                            if ($row['apply_user_ex4']) {
                                $arrayList[] = '9월 30일(금)-건축&amp; 자동차<br />';
                            }
                            echo implode(', ', $arrayList);
                            ?>
                        </td>
                        <!--  <td><?=$row['apply_user_email2']?></td>
      <td><?=$row['apply_product_name']?></td> -->
                        <td><?=$row['apply_user_event_agree']?></td>
                        <td><?=$row['apply_reg_datetime']?></td>
                        <td>
                            <a style="font-weight: 900;" href="javascript:;" onclick="del(<?=$row['apply_no']?>)">
                                삭제
                            </a>
                        </td>
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
    <form class="form-inline" name="fsearch2" action="https://epiclounge.co.kr/v3/adm/2023_event_export_excel.php" method="get">
        <input type="hidden" name="export" value="">
        <input type="hidden" name="start_date" value="<?=$start_date?>">
        <input type="hidden" name="end_date" value="<?=$end_date?>">
        <input type="hidden" name="stx" value="<?=$stx?>">
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
                location.replace('/v3/2023_findApplyByEmail?mode2=del&no='+val);
            }
        }
    </script>
<?php
include_once('./admin.tail.php');