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
                <th scope="col">TID</th>
                <th scope="col">MTID</th>
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
            $sql = "select * from 2024_event_log where (log_text like '%\"resultCode\": \"0000\"%'  or log_text like '%P_STATUS=00%') and log_text not like '%cancelDate%'  order by log_idx desc";
            $result = sql_query($sql);
            for ($i=1; $row=sql_fetch_array($result); ) {

                $bg = 'bg'.($i%2);

                $log_text = $row["log_text"];
                $p_name = "";
                $buyerTel = "";
                $tid = "";
                $MOID = "";
                $data = array();

                if (preg_match('/P_TID=([^&]*)/', $log_text, $matches)) {
                    $tid = $matches[1];
                    continue;

                    //$data["buyerTel"] = preg_match('/P_TID=([^&]*)/', $log_text, $matches);
                    $data["buyerName"] = preg_match('/P_UNAME=([^&]*)/', $log_text, $matches);
                    $data["buyerName"] = $matches[1];
                    //$data["buyerEmail"] = preg_match('/P_TID=([^&]*)/', $log_text, $matches);
                } else {

                    $data = json_decode($log_text, true); // JSON 문자열을 배열로 변환

                    if (isset($data['tid'])) {
                        $tid = $data['tid'];
                    }
                    if (isset($data['MOID'])) {
                        $MOID = $data['MOID'];
                    }
                }
                $sql_user = sql_fetch("select count(*) cnt from cb_unreal_2024_event2_apply where mpay_tid = '".$tid."' or pay_tid = '".$tid."' ");
                //$sql_user = sql_fetch("select count(*) cnt from cb_unreal_2024_event2_apply where mpay_tid = '".$tid."' or pay_moid = '".$MOID."' ");
                    if($sql_user["cnt"] == 0
                        && $data['buyerTel'] != "01029959522"
                        && $data['buyerTel'] != "01025752245"
                        && $data['buyerTel'] != "01085657487"
                        && $data['buyerTel'] != "01033299566"
                        && $data['buyerTel'] != "01025510017"
                        && $data['buyerTel'] != "01025510017"
                        && $data['buyerName'] != "박경덕"){
                        $sOrignText = md5(str_replace("'","\'",$data['buyerEmail']));
                        ?>
                        <tr class="<?php echo $bg; ?>" onclick="$(this).next().toggle();">
                            <td class="td_chk">
                                <?php echo $i; ?>
                            </td>
                            <td><?=$data['buyerEmail']?></td>
                            <td><?=$data['buyerName']?></td>
                            <td><?=$data['buyerTel']?></td>
                            <td><?=$tid?></td>
                            <td><?=$sOrignText?></td>
                            <td>--</td>
                            <td>--</td>
                            <td>--</td>
                            <td></td>
                            <td><?=$data["goodName"]?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr style="display:none;">
                            <td colspan="15" >
                                <?=$log_text?>
                            </td>
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
include_once('./admin.tail.php');