<?php
$sub_menu = '900210';
include_once ('./_common.php');

auth_check_menu($auth, $sub_menu, 'r');

$g5['title'] = 'Bit.ly 주소 변환 관리';
include_once ('./admin.head.php');

// DB 테이블 생성
$table_name = G5_TABLE_PREFIX . 'bitly_logs';
$table_exists = sql_query(" select 1 from $table_name limit 1 ", false);
if (!$table_exists) {
    sql_query(" CREATE TABLE IF NOT EXISTS `$table_name` (
        `bl_id` int(11) NOT NULL AUTO_INCREMENT,
        `bl_long_url` text NOT NULL,
        `bl_short_url` varchar(255) NOT NULL,
        `bl_memo` text NOT NULL,
        `bl_datetime` datetime NOT NULL,
        PRIMARY KEY (`bl_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ", true);
}

// 페이징 설정
$sql_common = " from $table_name ";
$row = sql_fetch(" select count(*) as cnt $sql_common ");
$total_count = isset($row['cnt']) ? $row['cnt'] : 0;

$rows = 25;
$total_page = ceil($total_count / $rows);
if ($page < 1) {
    $page = 1;
}
$from_record = ($page - 1) * $rows;

if ($total_count > 0) {
    $sql = " select * $sql_common order by bl_id desc limit $from_record, $rows ";
    $result = sql_query($sql);
}

// 순번 계산용
$num = $total_count - ($page - 1) * $rows;
?>

<div class="local_desc01 local_desc">
    <p>
        긴 URL 주소를 Bit.ly를 이용해 짧은 주소로 변환하고 내역을 관리합니다.
    </p>
</div>

<div class="config_contents" style="margin-bottom:30px;">
    <form name="fbitly" id="fbitly">
        <div class="tbl_frm01 tbl_wrap">
            <table>
                <caption>Bit.ly 주소 변환</caption>
                <colgroup>
                    <col class="grid_4">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th scope="row"><label for="long_url">원본 URL</label></th>
                    <td>
                        <input type="text" name="long_url" id="long_url" required class="required frm_input" size="80" placeholder="https://example.com/very/long/url/path">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="memo">내용(메모)</label></th>
                    <td>
                        <textarea name="memo" id="memo" class="frm_input" style="width:100%; height:60px;" placeholder="URL에 대한 설명을 입력하세요."></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row">단축 도메인</th>
                    <td>
                        <select name="domain" id="domain" class="frm_input">
                            <option value="bit.ly">bit.ly (기본)</option>
                            <option value="link.epiclounge.co.kr" selected>link.epiclounge.co.kr (커스텀)</option>
                        </select>
                    </td>
                </tr>
                <tr id="tr_result" style="display:none; background-color: #f8faff; border:2px solid #0055ff;">
                    <th scope="row" style="color:#0055ff;">변환 성공!</th>
                    <td>
                        <span style="font-weight:bold; color:#333;">복사해서 사용하세요</span>
                        <span style="margin:0 15px; color:#ccc;">|</span>
                        <span id="bitly_result" style="font-weight:bold; color:#0055ff; font-size:1.2em; vertical-align: middle;"></span>
                        <span style="margin:0 15px; color:#ccc;">|</span>
                        <button type="button" id="btn_copy_main" class="btn_02" style="vertical-align: middle;">복사하기</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="btn_confirm01 btn_confirm">
            <input type="submit" value="주소 변환하기" class="btn_submit" accesskey="s">
        </div>
    </form>
</div>

<h2 class="h2_frm">변환 내역 리스트</h2>
<div class="tbl_head01 tbl_wrap">
    <table>
        <caption>변환 내역 리스트</caption>
        <thead>
        <tr>
            <th scope="col">No</th>
            <th scope="col">변환 날짜/시간</th>
            <th scope="col">변환 주소</th>
            <th scope="col">주소복사</th>
            <th scope="col">내용(메모)</th>
            <th scope="col">원본 긴 주소</th>
            <th scope="col">삭제</th>
        </tr>
        </thead>
        <tbody id="bitly_log_list">
        <?php
        $i = 0;
        if (isset($result) && $result) {
            for ($i = 0; $row = sql_fetch_array($result); $i++) {
                $bg = 'bg' . ($i % 2);
                ?>
        <tr class="<?php echo $bg; ?>" data-id="<?php echo $row['bl_id']; ?>">
            <td class="td_num"><?php echo $num--; ?></td>
            <td class="td_datetime"><?php echo $row['bl_datetime']; ?></td>
            <td class="td_url">
                <a href="<?php echo $row['bl_short_url']; ?>" target="_blank" class="short_url"><?php echo $row['bl_short_url']; ?></a>
            </td>
            <td class="td_mng td_show" style="text-align:center;">
                <button type="button" class="btn_03 btn_copy_list" data-url="<?php echo $row['bl_short_url']; ?>">복사</button>
            </td>
            <td style="padding-left:10px;"><?php echo get_text($row['bl_memo']); ?></td>
            <td style="padding-left:10px; font-size:0.9em; color:#666; max-width:250px; word-wrap:break-word; word-break:break-all;"><?php echo get_text($row['bl_long_url']); ?></td>
            <td class="td_mng td_show" style="text-align:center;">
                <button type="button" class="btn_02 btn_del_list" data-id="<?php echo $row['bl_id']; ?>" style="background:#ff4747; color:#fff; border:none;">삭제</button>
            </td>
        </tr>
        <?php
            }
        }

        if ($i == 0)
            echo '<tr><td colspan="7" class="empty_table">내역이 없습니다.</td></tr>';
        ?>
        </tbody>
    </table>
</div>

<?php
include_once (G5_LIB_PATH . '/common.lib.php');
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'] . '?page=');
?>

<script>
$(function() {
    // 폼 제출 (변환)
    $("#fbitly").on("submit", function(e) {
        e.preventDefault();

        var long_url = $("#long_url").val().trim();
        var domain = $("#domain").val();
        var memo = $("#memo").val();
        
        if(!long_url) {
            alert("URL을 입력해주세요.");
            return false;
        }

        if(!long_url.match(/^(http|https):\/\//i)) {
            if(confirm("입력하신 URL에 http:// 또는 https:// 가 빠져있습니다.\nhttps:// 를 자동으로 붙여서 변환할까요?")) {
                long_url = "https://" + long_url;
                $("#long_url").val(long_url);
            } else {
                return false;
            }
        }

        $("#bitly_result").text("변환 중...").css("color", "#666");
        $("#tr_result").hide();

        $.ajax({
            url: "./service_bitly_ajax.php",
            type: "POST",
            data: { 
                long_url: long_url,
                domain: domain,
                memo: memo
            },
            dataType: "json",
            success: function(data) {
                if(data.success) {
                    $("#bitly_result").text(data.short_url);
                    $("#btn_copy_main").attr("data-url", data.short_url);
                    $("#tr_result").fadeIn();
                    
                    // 리스트 상단에 즉시 추가
                    var now = new Date();
                    var datetime = now.getFullYear() + "-" + ("0" + (now.getMonth() + 1)).slice(-2) + "-" + ("0" + now.getDate()).slice(-2) + " " + ("0" + now.getHours()).slice(-2) + ":" + ("0" + now.getMinutes()).slice(-2) + ":" + ("0" + now.getSeconds()).slice(-2);
                    
                    var new_row = '<tr class="bg0" data-id="' + data.bl_id + '">' +
                        '<td class="td_num">New</td>' +
                        '<td class="td_datetime">' + datetime + '</td>' +
                        '<td class="td_url"><a href="' + data.short_url + '" target="_blank" class="short_url">' + data.short_url + '</a></td>' +
                        '<td class="td_mng td_show" style="text-align:center;"><button type="button" class="btn_03 btn_copy_list" data-url="' + data.short_url + '">복사</button></td>' +
                        '<td style="padding-left:10px;">' + memo + '</td>' +
                        '<td style="padding-left:10px; font-size:0.9em; color:#666; max-width:250px; word-wrap:break-word; word-break:break-all;">' + long_url + '</td>' +
                        '<td class="td_mng td_show" style="text-align:center;">' + 
                        '<button type="button" class="btn_02 btn_del_list" data-id="' + data.bl_id + '" style="background:#ff4747; color:#fff; border:none;">삭제</button>' +
                        '</td>' +
                        '</tr>';
                    
                    $("#bitly_log_list").prepend(new_row);
                    $(".empty_table").closest("tr").remove();
                } else {
                    alert("에러: " + data.error);
                }
            },
            error: function() {
                alert("서버 통신 에러가 발생했습니다.");
            }
        });
    });

    // 삭제 버튼 클릭
    $(document).on("click", ".btn_del_list", function() {
        if(!confirm("정말 이 내역을 삭제하시겠습니까?")) return;

        var bl_id = $(this).data("id");
        var $tr = $(this).closest("tr");

        $.ajax({
            url: "./service_bitly_ajax.php",
            type: "POST",
            data: { mode: "delete", bl_id: bl_id },
            dataType: "json",
            success: function(data) {
                if(data.success) {
                    $tr.fadeOut(function() { $(this).remove(); });
                } else {
                    alert("에러: " + data.error);
                }
            },
            error: function() {
                alert("서버 통신 중 에러가 발생했습니다.");
            }
        });
    });

    // 복사 버튼 이벤트
    $(document).on("click", "#btn_copy_main, .btn_copy_list", function() {
        var text = $(this).attr("data-url") || $("#bitly_result").text();
        if(!text || text.indexOf("http") !== 0) return;

        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(text).select();
        document.execCommand("copy");
        $temp.remove();
        alert("주소가 복사되었습니다: " + text);
    });
});
</script>

<?php
include_once ('./admin.tail.php');
?>
