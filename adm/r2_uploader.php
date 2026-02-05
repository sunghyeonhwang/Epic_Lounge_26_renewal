<?php
include_once ('./_common.php');
include_once ('./r2_config.php');

if (!defined('R2_ACCESS_KEY_ID') || !defined('R2_ACCOUNT_ID') || !defined('R2_SECRET_KEY') || !defined('R2_BUCKET')) {
    $missing = [];
    if (!defined('R2_ACCOUNT_ID')) $missing[] = 'ACCOUNT_ID';
    if (!defined('R2_ACCESS_KEY_ID')) $missing[] = 'ACCESS_KEY_ID';
    if (!defined('R2_SECRET_KEY')) $missing[] = 'SECRET_KEY';
    if (!defined('R2_BUCKET')) $missing[] = 'BUCKET';
    
    die(json_encode(['error' => 'R2 설정 상수가 누락되었습니다: ' . implode(', ', $missing)]));
}

if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    die(json_encode(['error' => '파일 업로드 에러: ' . $_FILES['file']['error']]));
}

$file = $_FILES['file'];
$content_type = $file['type'];

// 실제 MIME 타입 검증 (서버 측)
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$detected_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

// 파일 크기 검증 (100MB 제한)
if ($file['size'] > 100 * 1024 * 1024) {
    die(json_encode(['error' => '파일 크기는 100MB 이하여야 합니다.']));
}

// 허용된 MIME 타입 검증 (더 넓은 범위)
$allowed_types = [
    'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
    'video/mp4', 'video/webm', 'video/quicktime', 'video/x-msvideo', 'video/mpeg'
];

// MIME 타입이 정확히 일치하지 않으면 파일 확장자로도 체크
$file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'mp4', 'webm', 'mov', 'avi', 'mpeg'];

$is_valid = in_array($detected_type, $allowed_types) || in_array($file_ext, $allowed_extensions);

if (!$is_valid) {
    error_log('R2 Upload - Invalid file type. Detected: ' . $detected_type . ', Extension: ' . $file_ext);
    die(json_encode(['error' => '허용되지 않는 파일 형식입니다. (감지된 타입: ' . $detected_type . ')']));
}

// 경로 분류 (이미지 vs 영상) - R2_BASE_PATH는 r2_upload_file 함수에서 자동 추가됨
$sub_path = 'others/';  // 기본값
if (strpos($detected_type, 'image') !== false || in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
    $sub_path = 'main_image/';
} else if (strpos($detected_type, 'video') !== false || in_array($file_ext, ['mp4', 'webm', 'mov', 'avi', 'mpeg'])) {
    $sub_path = 'main_movie/';
}

$filename = $sub_path . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($file['name']));
$file_path = $file['tmp_name'];

// SVG 파일의 경우 명시적으로 Content-Type 설정
$upload_content_type = $detected_type;
if ($file_ext === 'svg') {
    $upload_content_type = 'image/svg+xml';
}

// R2 Upload logic (S3 API compatible)
try {
    $result_url = r2_upload_file($file_path, $filename, $upload_content_type);
    echo json_encode(['url' => $result_url]);
} catch (Exception $e) {
    // 디버깅을 위해 임시로 에러 메시지 노출
    error_log('R2 Upload Error: ' . $e->getMessage());
    echo json_encode(['error' => 'R2 업로드 실패: ' . $e->getMessage()]);
}

/**
 * R2 S3 API 업로드 함수 (CURL 기반)
 */
function r2_upload_file($source_file, $dest_name, $content_type)
{
    $bucket = trim(R2_BUCKET);
    $access_key = trim(R2_ACCESS_KEY_ID);
    $secret_key = trim(R2_SECRET_KEY);
    $account_id = trim(R2_ACCOUNT_ID);
    $region = 'auto';

    // S3 호환 엔드포인트 (버킷 이름은 호스트의 서브도메인으로)
    $host = $bucket . '.' . $account_id . '.r2.cloudflarestorage.com';

    // 전체 경로 구성 (BASE_PATH 포함)
    $full_path = R2_BASE_PATH . '/' . $dest_name;

    // 엔드포인트 URL (객체 키만 경로로)
    $endpoint = "https://$host/" . $full_path;

    $timestamp = gmdate('Ymd\THis\Z');
    $date = gmdate('Ymd');

    $content = file_get_contents($source_file);
    if ($content === false) {
        throw new Exception("파일을 읽을 수 없습니다: $source_file");
    }

    $content_hash = hash('sha256', $content);

    // 1. Canonical Request (객체 키만 포함, 버킷은 호스트에 있음)
    $canonical_uri = '/' . $full_path;
    $canonical_headers = "host:$host\nx-amz-content-sha256:$content_hash\nx-amz-date:$timestamp\n";
    $signed_headers = 'host;x-amz-content-sha256;x-amz-date';
    $canonical_request = "PUT\n$canonical_uri\n\n$canonical_headers\n$signed_headers\n$content_hash";

    // 2. String to Sign
    $scope = "$date/$region/s3/aws4_request";
    $string_to_sign = "AWS4-HMAC-SHA256\n$timestamp\n$scope\n" . hash('sha256', $canonical_request);

    // 3. Signature
    $k_date = hash_hmac('sha256', $date, "AWS4$secret_key", true);
    $k_region = hash_hmac('sha256', $region, $k_date, true);
    $k_service = hash_hmac('sha256', 's3', $k_region, true);
    $k_signing = hash_hmac('sha256', 'aws4_request', $k_service, true);
    $signature = hash_hmac('sha256', $string_to_sign, $k_signing);

    $authorization = "AWS4-HMAC-SHA256 Credential=$access_key/$scope, SignedHeaders=$signed_headers, Signature=$signature";

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: $authorization",
        "x-amz-date: $timestamp",
        "x-amz-content-sha256: $content_hash",
        "Content-Type: $content_type"
    ]);

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status === 200 || $status === 204) {
        // R2에 업로드된 파일의 공개 URL 반환
        return 'https://' . R2_CUSTOM_DOMAIN . '/' . $full_path;
    } else {
        throw new Exception("R2 업로드 실패 (Status: $status): " . $response);
    }
}
?>
