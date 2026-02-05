<?php
include_once('./_common.php');

if ($is_admin != 'super' && !auth_check($auth[$sub_menu], 'r', true)) {
    die(json_encode(array('success' => false, 'error' => '권한이 없습니다.')));
}

$mode = $_POST['mode'];

// 삭제 모드
if ($mode == 'delete') {
    $bl_id = (int)$_POST['bl_id'];
    if (!$bl_id) {
        die(json_encode(array('success' => false, 'error' => '삭제할 대상이 없습니다.')));
    }

    $table_name = G5_TABLE_PREFIX . 'bitly_logs';
    $sql = " delete from $table_name where bl_id = '$bl_id' ";
    if (sql_query($sql)) {
        echo json_encode(array('success' => true));
    } else {
        echo json_encode(array('success' => false, 'error' => 'DB 삭제 중 오류가 발생했습니다.'));
    }
    exit;
}

// 변환 모드 (기존 로직)
$long_url = trim($_POST['long_url']);
$domain   = trim($_POST['domain']);
$memo     = trim($_POST['memo']);

if (!$domain) $domain = 'bit.ly';

if (!$long_url) {
    die(json_encode(array('success' => false, 'error' => 'URL을 입력해주세요.')));
}

// Bitly Access Token
$access_token = '625f1a9db7f70628960d953cebf68d24c754e5b6'; 

if (!$access_token) {
    die(json_encode(array('success' => false, 'error' => 'Bitly Access Token이 설정되지 않았습니다. 관리자에게 문의하세요.')));
}

// DB 테이블 생성 (없을 경우)
$table_name = G5_TABLE_PREFIX . 'bitly_logs';
$table_exists = sql_query(" select 1 from $table_name limit 1 ", false);
if(!$table_exists) {
    sql_query(" CREATE TABLE IF NOT EXISTS `$table_name` (
        `bl_id` int(11) NOT NULL AUTO_INCREMENT,
        `bl_long_url` text NOT NULL,
        `bl_short_url` varchar(255) NOT NULL,
        `bl_memo` text NOT NULL,
        `bl_datetime` datetime NOT NULL,
        PRIMARY KEY (`bl_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ", true);
}

$api_url = 'https://api-ssl.bitly.com/v4/shorten';

$data = array(
    'long_url' => $long_url,
    'domain'   => $domain
);

// 커스텀 도메인 사용 시 group_guid가 필요하므로, 
// 현재 계정의 그룹 목록을 조회합니다.
$group_guid = "";
if ($domain != 'bit.ly') {
    $group_ch = curl_init('https://api-ssl.bitly.com/v4/groups');
    curl_setopt($group_ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($group_ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $access_token
    ));
    $group_response = curl_exec($group_ch);
    $groups_data = json_decode($group_response, true);
    curl_close($group_ch);

    if (isset($groups_data['groups']) && is_array($groups_data['groups'])) {
        // 모든 그룹을 돌며 해당 도메인을 소유한 그룹을 찾습니다.
        foreach ($groups_data['groups'] as $group) {
            if (isset($group['bsds']) && is_array($group['bsds'])) {
                if (in_array($domain, $group['bsds'])) {
                    $group_guid = $group['guid'];
                    break;
                }
            }
        }
        
        // 만약 도메인 매칭되는 그룹을 못 찾았다면, 
        // 차선책으로 첫 번째 그룹을 사용합니다.
        if (!$group_guid) {
            $group_guid = $groups_data['groups'][0]['guid'];
        }
        
        $data['group_guid'] = $group_guid;
    }
}

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $access_token,
    'Content-Type: application/json'
));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$res_data = json_decode($response, true);

if ($http_code == 200 || $http_code == 201) {
    if (isset($res_data['link'])) {
        $short_url = $res_data['link'];

        // DB에 로그 저장
        $sql = " insert into $table_name
                    set bl_long_url = '".sql_real_escape_string($long_url)."',
                        bl_short_url = '".sql_real_escape_string($short_url)."',
                        bl_memo = '".sql_real_escape_string($memo)."',
                        bl_datetime = '".G5_TIME_YMDHIS."' ";
        sql_query($sql);
        $new_id = sql_insert_id();

        echo json_encode(array(
            'success' => true,
            'short_url' => $short_url,
            'bl_id' => $new_id
        ));
    } else {
        echo json_encode(array(
            'success' => false,
            'error' => 'API 응답 형식 오류 (link 필드 없음)'
        ));
    }
} else {
    $error_msg = isset($res_data['message']) ? $res_data['message'] : '알 수 없는 에러가 발생했습니다.';
    $description = isset($res_data['description']) ? ' (' . $res_data['description'] . ')' : '';
    
    // 상세 에러 메시지 구성
    $full_error = $error_msg . $description . ' [HTTP Code: ' . $http_code . ']';
    
    // 만약 INVALID_ARG_DOMAIN 에러가 나면 도메인 설정과 함께 group_guid도 확인
    if ($error_msg == 'INVALID_ARG_DOMAIN') {
        $full_error .= "\n\n사용된 Group GUID: " . ($group_guid ? $group_guid : "없음");
        $full_error .= "\n\n현재 Bitly 계정에 '" . $domain . "' 도메인이 등록되어 있으나, 위 그룹 ID가 해당 도메인의 소유 그룹이 아닐 수 있습니다. Bitly 대시보드에서 도메인이 속한 Group을 확인해 주세요.";
    }

    echo json_encode(array(
        'success' => false,
        'error' => $full_error
    ));
}
?>
