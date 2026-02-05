<?php
if (!defined('_GNUBOARD_')) exit;

/**
 * R2 S3 API 업로드 함수 (CURL 기반)
 */
function r2_upload_file($source_file, $dest_name, $content_type)
{
    if (!defined('R2_BUCKET') || !defined('R2_ACCESS_KEY_ID') || !defined('R2_SECRET_KEY') || !defined('R2_ACCOUNT_ID')) {
        throw new Exception("R2 설정 정보가 부족합니다.");
    }

    $bucket = trim(R2_BUCKET);
    $access_key = trim(R2_ACCESS_KEY_ID);
    $secret_key = trim(R2_SECRET_KEY);
    $account_id = trim(R2_ACCOUNT_ID);
    $region = 'auto';

    // S3 호환 엔드포인트 (버킷 이름은 호스트의 서브도메인으로)
    $host = $bucket . '.' . $account_id . '.r2.cloudflarestorage.com';

    // 전체 경로 구성 (BASE_PATH 포함)
    // R2_BASE_PATH 가 정의되어 있으면 앞에 붙여줌
    $base_path = defined('R2_BASE_PATH') ? trim(R2_BASE_PATH, '/') : '';
    $full_path = ($base_path ? $base_path . '/' : '') . ltrim($dest_name, '/');

    // 엔드포인트 URL (객체 키만 경로로)
    $endpoint = "https://$host/" . $full_path;

    $timestamp = gmdate('Ymd\THis\Z');
    $date = gmdate('Ymd');

    $content = file_get_contents($source_file);
    if ($content === false) {
        throw new Exception("파일을 읽을 수 없습니다: $source_file");
    }

    $content_hash = hash('sha256', $content);

    // 1. Canonical Request
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
