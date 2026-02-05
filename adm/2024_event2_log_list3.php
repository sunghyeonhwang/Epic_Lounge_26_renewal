<?php
$sub_menu = "700610";
include_once('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

$g5['title'] = '이벤트로그조회';
include_once('./admin.head.php');

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
                <th scope="col">수신동의</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $data_all = array();
            $sql = "select * from cb_unreal_2024_event2_apply_temp where apply_no in (7674,7836,7842,7860,7970,7984,8002,8023,8071,8117,8215,8249,8263,8280,8320,8349,8538,8541,8551,8587,8589,8608,8626,8630,8637,8665,8674,8690,8698,8747,8884,8898,8926,8944,8957,8960,8961,8962,8994,9000,9008,9016,9116,9166,9176,9191,9202,9246,9275,9279,9282,9296,9320,9369,9371,9378,9379,9385,9394,9408,9411,9413,9443,9475,9485,9509,9591,9609,9616,9622,9627,9662,9676,9716,9718,9760,9767,9814,9825,9843,9859,9873,9874,9875,9906,9942,10015,10016,10036,10046,10053,10058,10134,10144,10173,10192,10293,10484,10537,10975,11101,11379,11506,11511,11554,11589,11706,11786,11834,11842,11917,11922,11933,11949,11971,11993,12047,12135,12150,12156,12169,12229,12230,12232,12341,12367,12398,12403,12558,12570,12592,12617,12618,12697,12706,12757,12761,12763,12785,12792,12797,12810,12883,12888,12897,12909,12917,12918,12919,12920,13004,13008,13014,13079,13101,13145,13153,13155,13156,13157,13159,13170,13177,13193,13200,13211,13215,13221,13226,13247,13248,13250,13260,13268,13291,13293,13298,13302,13312,13313,13315,13317,13359,13363,13367,13368,13377,13386,13389,13390,13392,13397,13398,13405,13415,13429,13433,13438,13439,13448,13453,13454,13457,13460,13462,13464,13465,13469,13471,13475,13476,13482,13487,13491,13494,13502,13509,13510,13515,13516,13518,13523,13529,13533,13534,13536,13540,13541,13546,13556,13559,13565,13582,13591,13592,13600,13610,13633,13638,13656,13785,13824,13923,13945,13951,13952,14089,14298,14332,14394,14649,15715,16415,17473) ";
            $result = sql_query($sql);
            for ($i=1; $row2=sql_fetch_array($result);$i++ ) {
                $bg = 'bg'.($i%2);

                $row = sql_fetch("select * from cb_unreal_2024_event2_apply where  apply_user_phone = '".$row2['apply_user_phone']."' ");
                if( $row["apply_user_phone"] != "01029959522"
                    && $row["apply_user_phone"] != "01025752245"
                    && $row["apply_user_phone"] != "01085657487"
                    && $row["apply_user_phone"] != "01033299566"
                    && $row["apply_user_phone"] != "01025510017"
                    && $row["apply_user_phone"] != "01025510017"
                ){

                    $data_all[] = $row["apply_no"];
                    ?>
                    <tr class="<?php echo $bg; ?>" >
                        <td class="td_chk">
                            <?php echo $i; ?>
                        </td>
                        <td><?=$row['apply_user_email']?></td>
                        <td><?=$row['apply_user_name']?></td>
                        <td><?=$row['apply_user_phone']?></td>
                        <td><?=$row['apply_track']?></td>
                        <td><?=$row['apply_user_company']?></td>
                        <td><?=$row['apply_user_depart']?></td>
                        <td><?=$row['apply_user_grade']?></td>
                        <td><?=$row['apply_user_ex1']?></td>
                        <td></td>
                        <td><?php
                            if($row['free_yn'] == 'Y'){
                                echo "온라인";
                            }else if($row['apply_product_code'] == "NORMAL_ALL"){
                                echo "양일권";
                            }else if($row['apply_product_code'] == "NORMAL_28"){
                                echo "28일권";
                            }else if($row['apply_product_code'] == "NORMAL_29"){
                                echo "29일권";
                            }else if($row['apply_product_code'] == "STD_ALL"){
                                echo "학생 양일권";
                            }else if($row['apply_product_code'] == "STD_28"){
                                echo "학생 28일권";
                            }else if($row['apply_product_code'] == "STD_29"){
                                echo "학생 29일권";
                            }
                            ?></td>
                        <td><?=$row['apply_reg_datetime']?></td>
                        <td><?=$row['apply_user_event_agree']  =="1" ? "동의":"미동의"?></td>
                    </tr>
                    <?php
                }else{

                }
            }
            ?>
            </tbody>
        </table>
    </div>
    </form>


<?php

echo implode(",",$data_all);
include_once('./admin.tail.php');