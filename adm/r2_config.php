<?php
/**
 * Cloudflare R2 설정 파일
 * 이 파일은 보안을 위해 API 키 정보를 포함하고 있습니다.
 */

// Cloudflare 계정 정보
define('R2_ACCOUNT_ID', 'e1bf342e2435a04c2435feab6ad8e230');  // Cloudflare Account ID
define('R2_ACCESS_KEY_ID', 'f9e754b69546870227c899de7f17de85');  // R2 API Access Key ID
define('R2_SECRET_KEY', '1c8ee31f494bc6dce812a0e512cadb4f4dc4621c10cfa1b8de84be414ffdcc05');  // R2 API Secret Access Key
define('R2_BUCKET', 'epiclounge-assets');  // R2 Bucket Name

// 도메인 설정
define('R2_CUSTOM_DOMAIN', 'files.epiclounge.co.kr');  // 커스텀 도메인 (https:// 제외, 경로 제외)
define('R2_BASE_PATH', 'mainvisual');  // 버킷 내 기본 경로
define('R2_URL', 'https://' . R2_CUSTOM_DOMAIN . '/' . R2_BASE_PATH);

// S3 호환 엔드포인트 URL
define('R2_ENDPOINT', 'https://' . R2_ACCOUNT_ID . '.r2.cloudflarestorage.com');
?>
