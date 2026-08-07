<?php
/* Unreal Fest Seoul 2026 — 관리자: 단체 등록 쿠폰 관리 (adm/2026_coupon.php)
 * 라이브(2026_coupon.php) 무수정 검증용. 신규 CSV 양식(11열) 대응:
 *   분류, 코드(선택), 업체/스피커, 받는사람, 이메일1, 참조이메일2(CC), 할인율, 만료일, 한도, 메모, 언어(ko/en)
 * 변경점: ① 새 11열 순서 파싱 ② 만료일 YYYY.M.D 허용 ③ 참조이메일=CC 발송 ④ 분류/업체 저장(분류→메일양식 분기용)
 * CC 발송은 공유 _resend.php 무수정 위해 로컬 발송기(cp_resend_cc) 사용.
 * cb_unreal_2026_coupon. PHP 7.0 호환.
 */
$sub_menu = '700366';
include_once('./_common.php');
if (!function_exists('is_admin') || !is_admin($member['mb_id'])) {
    alert('관리자 로그인이 필요합니다.', G5_ADMIN_URL);
}
$g5['title'] = '쿠폰 관리';

sql_query("CREATE TABLE IF NOT EXISTS cb_unreal_2026_coupon (
  cp_no INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cp_code VARCHAR(40) NOT NULL DEFAULT '',
  cp_percent TINYINT NOT NULL DEFAULT 0,
  cp_expire DATE DEFAULT NULL,
  cp_max INT NOT NULL DEFAULT 0,
  cp_used INT NOT NULL DEFAULT 0,
  cp_active CHAR(1) NOT NULL DEFAULT 'Y',
  cp_memo VARCHAR(200) NOT NULL DEFAULT '',
  cp_reg DATETIME DEFAULT NULL,
  PRIMARY KEY (cp_no), UNIQUE KEY uq_code (cp_code)
) DEFAULT CHARSET=utf8");
// 수신자/발송 이력 컬럼 보강 (쿠폰 메일 발송용)
@sql_query("ALTER TABLE cb_unreal_2026_coupon ADD COLUMN IF NOT EXISTS cp_recipient_name VARCHAR(100) NOT NULL DEFAULT ''");
@sql_query("ALTER TABLE cb_unreal_2026_coupon ADD COLUMN IF NOT EXISTS cp_recipient_email VARCHAR(200) NOT NULL DEFAULT ''");
@sql_query("ALTER TABLE cb_unreal_2026_coupon ADD COLUMN IF NOT EXISTS cp_sent_at DATETIME DEFAULT NULL");
@sql_query("ALTER TABLE cb_unreal_2026_coupon ADD COLUMN IF NOT EXISTS cp_status VARCHAR(20) NOT NULL DEFAULT ''");
@sql_query("ALTER TABLE cb_unreal_2026_coupon ADD COLUMN IF NOT EXISTS cp_lang VARCHAR(5) NOT NULL DEFAULT 'ko'");
// [TEST] 신규 양식 컬럼: 분류(스폰서/스피커)·업체명·참조이메일(CC)
@sql_query("ALTER TABLE cb_unreal_2026_coupon ADD COLUMN IF NOT EXISTS cp_category VARCHAR(20) NOT NULL DEFAULT ''");
@sql_query("ALTER TABLE cb_unreal_2026_coupon ADD COLUMN IF NOT EXISTS cp_company VARCHAR(120) NOT NULL DEFAULT ''");
@sql_query("ALTER TABLE cb_unreal_2026_coupon ADD COLUMN IF NOT EXISTS cp_cc_email VARCHAR(400) NOT NULL DEFAULT ''");
@sql_query("ALTER TABLE cb_unreal_2026_coupon MODIFY cp_cc_email VARCHAR(400) NOT NULL DEFAULT ''"); // 다중 CC 대비 폭 확장
@sql_query("ALTER TABLE cb_unreal_2026_coupon ADD COLUMN IF NOT EXISTS cp_scheduled_at DATETIME DEFAULT NULL"); // 예약 발송 시각
@sql_query("ALTER TABLE cb_unreal_2026_coupon ADD COLUMN IF NOT EXISTS cp_send_id VARCHAR(60) NOT NULL DEFAULT ''"); // Resend 메일 id(예약 취소용)

// 쿠폰 메일 발송 모듈 [TEST: 분류 분기 템플릿] + Resend
@include_once(__DIR__ . '/../unrealfest2026/_coupon_mail.php');
@include_once(__DIR__ . '/../unrealfest2026/_resend.php');

/* [TEST] CC 지원 Resend 발송기 — 공유 _resend.php 무수정. _resend.php 상수(FROM/REPLYTO/API_KEY) 재사용.
 * $cc: 문자열(단일) 또는 배열. 반환 array(ok, id|error). */
function cp_resend_cc($to, $subject, $html, $text = '', $cc = '', $scheduled_at = '') {
    if (!function_exists('curl_init')) return array('ok'=>false,'error'=>'no_curl');
    if (!defined('UFS_RESEND_API_KEY') || UFS_RESEND_API_KEY === '') return array('ok'=>false,'error'=>'no_api_key');
    $to = is_array($to) ? array_values(array_filter($to)) : array($to);
    if (!$to) return array('ok'=>false,'error'=>'no_recipient');
    $payload = array(
        'from'     => UFS_RESEND_FROM,
        'to'       => $to,
        'subject'  => (string)$subject,
        'html'     => (string)$html,
        'reply_to' => UFS_RESEND_REPLYTO,
    );
    if ($text !== '') $payload['text'] = (string)$text;
    $ccArr = is_array($cc) ? $cc : ($cc !== '' ? array($cc) : array());
    $ccArr = array_values(array_filter($ccArr, function($e){ return $e!=='' && filter_var($e, FILTER_VALIDATE_EMAIL); }));
    if ($ccArr) $payload['cc'] = $ccArr;
    if ($scheduled_at !== '') $payload['scheduled_at'] = (string)$scheduled_at; // Resend 예약 발송(ISO8601, 최대 ~72h)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.resend.com/emails');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . UFS_RESEND_API_KEY, 'Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    $res  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $cerr = curl_error($ch);
    curl_close($ch);
    if ($res === false) return array('ok'=>false,'error'=>'curl: '.$cerr);
    $d = json_decode($res, true);
    if ($code >= 200 && $code < 300 && isset($d['id'])) return array('ok'=>true,'id'=>$d['id']);
    return array('ok'=>false,'error'=>(isset($d['message']) ? $d['message'] : ('HTTP '.$code)));
}

/* [TEST] CC 문자열/배열 → 유효 이메일 배열(콤마·세미콜론·공백 구분, 중복 제거). 다중 CC 지원. */
function cp_split_emails($s) {
    $s = is_array($s) ? implode(',', $s) : (string)$s;
    $parts = preg_split('/[,;\s]+/', $s, -1, PREG_SPLIT_NO_EMPTY);
    $out = array();
    foreach ($parts as $p) { $p = trim($p); if ($p !== '' && filter_var($p, FILTER_VALIDATE_EMAIL)) $out[] = $p; }
    return array_values(array_unique($out));
}

/* 쿠폰 1건 메일 발송(+CC, +예약) + 발송상태 기록. 반환 array(ok,msg,to).
 * $scheduled_at: '' 즉시 / 'YYYY-MM-DDTHH:MM:SS+09:00' 예약(Resend, 최대 ~72h). */
function cp_send_mail($cp_no, $scheduled_at = '') {
    if (!function_exists('ufs_coupon_mail')) return array('ok'=>false,'msg'=>'메일 모듈 미로드');
    $r = sql_fetch("SELECT * FROM cb_unreal_2026_coupon WHERE cp_no=".(int)$cp_no);
    if (!$r) return array('ok'=>false,'msg'=>'쿠폰을 찾을 수 없습니다.');
    $to = trim($r['cp_recipient_email']);
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) return array('ok'=>false,'msg'=>'수신자 이메일이 없거나 형식이 올바르지 않습니다.');
    $ccArr = cp_split_emails(isset($r['cp_cc_email']) ? $r['cp_cc_email'] : '');
    // $r 전체 전달 → 메일 템플릿이 cp_category(스폰서/스피커/기타)로 양식 분기
    $m = ufs_coupon_mail($r, (isset($r['cp_lang']) && $r['cp_lang']==='en') ? 'en' : 'ko');
    $res = cp_resend_cc($to, $m['subject'], $m['html'], $m['text'], $ccArr, $scheduled_at);
    $ok = !empty($res['ok']);
    $sched = ($ok && $scheduled_at !== '');
    $status = $sched ? 'scheduled' : ($ok ? 'sent' : 'fail');
    $schSql = $sched ? "'".sql_real_escape_string($scheduled_at)."'" : "NULL";
    $sid = ($ok && isset($res['id'])) ? $res['id'] : '';
    sql_query("UPDATE cb_unreal_2026_coupon SET cp_sent_at=now(), cp_status='".$status."', cp_scheduled_at=".$schSql.", cp_send_id='".sql_real_escape_string($sid)."' WHERE cp_no=".(int)$cp_no);
    $ccMsg = ($ccArr ? ' · CC '.implode(', ', $ccArr) : '');
    if ($sched) return array('ok'=>true, 'to'=>$to, 'msg'=>('예약 완료 ('.$to.' · '.$scheduled_at.')'.$ccMsg));
    return array('ok'=>$ok, 'to'=>$to, 'msg'=>($ok ? ('발송 성공 ('.$to.')'.$ccMsg) : ('발송 실패: '.(isset($res['error'])?$res['error']:'오류'))));
}

/* [TEST] 예약 메일 취소 — Resend cancel API(POST /emails/{id}/cancel). 예약(scheduled) 상태만 유효. */
function cp_cancel_mail($cp_no) {
    $r = sql_fetch("SELECT * FROM cb_unreal_2026_coupon WHERE cp_no=".(int)$cp_no);
    if (!$r) return array('ok'=>false,'msg'=>'쿠폰을 찾을 수 없습니다.');
    if (($r['cp_status'] ?? '') !== 'scheduled') return array('ok'=>false,'msg'=>'예약 상태가 아닙니다.');
    $id = trim(isset($r['cp_send_id']) ? $r['cp_send_id'] : '');
    if ($id === '') return array('ok'=>false,'msg'=>'발송 id가 없어 취소할 수 없습니다.');
    if (!defined('UFS_RESEND_API_KEY') || UFS_RESEND_API_KEY === '') return array('ok'=>false,'msg'=>'API 키 없음');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.resend.com/emails/'.$id.'/cancel');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{}'); // 빈 JSON 바디(없으면 프록시 400)
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . UFS_RESEND_API_KEY, 'Content-Type: application/json'));
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $d = json_decode($res, true);
    if ($code >= 200 && $code < 300) {
        sql_query("UPDATE cb_unreal_2026_coupon SET cp_status='canceled' WHERE cp_no=".(int)$cp_no);
        return array('ok'=>true,'msg'=>'예약을 취소했습니다.');
    }
    // 발송전용(restricted) 키면 취소 불가 → 안내
    $emsg = isset($d['message']) ? $d['message'] : ('HTTP '.$code);
    if (isset($d['name']) && $d['name']==='restricted_api_key') $emsg = '현재 Resend 키가 발송전용이라 취소가 불가합니다. Resend 대시보드에서 취소하거나 전체권한 키가 필요합니다.';
    return array('ok'=>false,'msg'=>'취소 실패: '.$emsg);
}

/* [TEST] 예약(scheduled) 건의 Resend 실제 상태를 조회해 DB에 반영.
 * last_event: scheduled→유지 / canceled→canceled / bounced·failed→fail / 그 외(sent·delivered 등)→sent. 풀키 필요(GET). */
function cp_sync_status($cp_no, $id) {
    if ($id === '' || !defined('UFS_RESEND_API_KEY') || UFS_RESEND_API_KEY === '') return;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.resend.com/emails/'.$id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . UFS_RESEND_API_KEY));
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code < 200 || $code >= 300) return; // 조회 실패(restricted 키 등) → 그대로 둠
    $d = json_decode($res, true);
    $le = isset($d['last_event']) ? $d['last_event'] : '';
    if ($le === '' || $le === 'scheduled') return; // 아직 예약 상태 → 유지
    $new = ($le==='canceled') ? 'canceled' : (($le==='bounced'||$le==='failed') ? 'fail' : 'sent');
    sql_query("UPDATE cb_unreal_2026_coupon SET cp_status='".$new."' WHERE cp_no=".(int)$cp_no);
}

/* 서버측 난수 쿠폰코드 생성(UECPN-XXXX-XXXX, 혼동문자 제외, 중복검사). CSV 일괄발급용. */
function cp_gen_code() {
    $alpha = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    do {
        $s = '';
        for ($i = 0; $i < 8; $i++) $s .= $alpha[mt_rand(0, 31)];
        $c = 'UECPN-'.substr($s,0,4).'-'.substr($s,4,4);
        $ex = sql_fetch("SELECT cp_no FROM cb_unreal_2026_coupon WHERE cp_code='".sql_real_escape_string($c)."'");
    } while ($ex);
    return $c;
}

function cp_e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$msg = '';
// [TEST] PRG: 직전 처리 결과를 세션 플래시로 받아 표시(새로고침 재제출 방지)
if (!empty($_SESSION['cp_flash'])) { $msg = $_SESSION['cp_flash']; unset($_SESSION['cp_flash']); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cp_code'])) {
    $code = strtoupper(trim($_POST['cp_code']));
    $pct  = (int)$_POST['cp_percent']; if ($pct < 0) $pct = 0; if ($pct > 100) $pct = 100;
    $exp  = trim($_POST['cp_expire']);
    $max  = (int)$_POST['cp_max']; if ($max < 0) $max = 0;
    $memo = trim($_POST['cp_memo']);
    $rname  = isset($_POST['cp_recipient_name']) ? trim($_POST['cp_recipient_name']) : '';
    $remail = isset($_POST['cp_recipient_email']) ? trim($_POST['cp_recipient_email']) : '';
    $rlang  = (isset($_POST['cp_lang']) && $_POST['cp_lang']==='en') ? 'en' : 'ko';
    $rcat   = isset($_POST['cp_category']) ? trim($_POST['cp_category']) : '';        // [TEST] 분류
    $rcomp  = isset($_POST['cp_company']) ? trim($_POST['cp_company']) : '';          // [TEST] 업체/스피커
    $rcc    = implode(',', cp_split_emails(isset($_POST['cp_cc_email']) ? $_POST['cp_cc_email'] : '')); // [TEST] 참조이메일(CC·다중 허용)
    $catSql = sql_real_escape_string($rcat); $compSql = sql_real_escape_string($rcomp); $ccSql = sql_real_escape_string($rcc);
    $qty    = isset($_POST['cp_qty']) ? (int)$_POST['cp_qty'] : 1; if ($qty < 1) $qty = 1; if ($qty > 200) $qty = 200;
    $expSql = ($exp !== '') ? "'".sql_real_escape_string($exp)."'" : "NULL";
    if ($pct <= 0) {
        $msg = '1 이상의 할인율을 입력해 주세요.';
    } else if ($qty > 1) {
        // ── 다수 발급: 각각 난수 코드 자동생성 (수신자/즉시발송 미적용 — 특정 수신자 발송은 CSV 사용) ──
        $made = 0; $fail = 0;
        for ($i = 0; $i < $qty; $i++) {
            $c = cp_gen_code();
            $r = sql_query("INSERT INTO cb_unreal_2026_coupon (cp_code,cp_percent,cp_expire,cp_max,cp_memo,cp_recipient_name,cp_recipient_email,cp_category,cp_company,cp_lang,cp_reg)
                VALUES ('".$c."', $pct, $expSql, $max, '".sql_real_escape_string($memo)."', '', '', '".$catSql."', '".$compSql."', '".$rlang."', now())");
            if ($r) $made++; else $fail++;
        }
        $msg = "쿠폰 {$made}개를 일괄 발급했습니다 ({$pct}%".($max>0?" · 한도 {$max}":"").").".($fail?" (실패 {$fail})":"")." · 특정 수신자 지정 발송은 [CSV 일괄 발급]을 이용하세요.";
    } else {
        // ── 단일 발급: 코드 빈칸이면 자동생성 + 수신자/즉시발송 ──
        if ($code === '') $code = cp_gen_code();
        $r = sql_query("INSERT INTO cb_unreal_2026_coupon (cp_code,cp_percent,cp_expire,cp_max,cp_memo,cp_recipient_name,cp_recipient_email,cp_cc_email,cp_category,cp_company,cp_lang,cp_reg)
            VALUES ('".sql_real_escape_string($code)."', $pct, $expSql, $max, '".sql_real_escape_string($memo)."', '".sql_real_escape_string($rname)."', '".sql_real_escape_string($remail)."', '".$ccSql."', '".$catSql."', '".$compSql."', '".$rlang."', now())");
        if ($r) {
            $msg = "쿠폰 '$code' ($pct%) 발급되었습니다.";
            if (isset($_POST['send_now']) && $remail !== '') {
                $nid = sql_fetch("SELECT LAST_INSERT_ID() id"); $nid = $nid ? (int)$nid['id'] : 0;
                if ($nid) { $sm = cp_send_mail($nid); $msg .= ' · 메일 '.$sm['msg']; }
            } else if ($remail === '' && isset($_POST['send_now'])) {
                $msg .= ' · (수신자 이메일이 없어 발송 생략)';
            }
        } else {
            $msg = '발급 실패(중복 코드일 수 있음).';
        }
    }
}
// ── 쿠폰 메일 발송/재발송 ──
if (isset($_GET['send_mail'])) {
    $sm = cp_send_mail((int)$_GET['send_mail']);
    $msg = '쿠폰 메일: '.$sm['msg'];
}
// ── 예약 메일 취소 ──
if (isset($_GET['cancel_mail'])) {
    $cm = cp_cancel_mail((int)$_GET['cancel_mail']);
    $msg = '예약 취소: '.$cm['msg'];
}

/* [TEST] 만료일 정규화: 'YYYY.M.D' / 'YYYY/M/D' / 'YYYY-M-D' → 'YYYY-MM-DD'. 실패 시 '' */
function cp_norm_date($s) {
    $s = trim($s); if ($s === '') return '';
    if (preg_match('/^(\d{4})[.\-\/](\d{1,2})[.\-\/](\d{1,2})$/', $s, $m)) {
        return sprintf('%04d-%02d-%02d', (int)$m[1], (int)$m[2], (int)$m[3]);
    }
    return '';
}

// ── CSV 일괄 발급 (+ 옵션: 즉시 메일 발송) ── [TEST: 신규 11열 양식]
// 열: 분류, 코드(선택·빈칸이면 자동생성), 업체/스피커, 받는사람, 이메일1, 참조이메일2(CC), 할인율, 만료일, 한도(기본1), 메모, 언어(ko/en)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csv_mode'])) {
    if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== 0) {
        $msg = 'CSV 파일을 선택해 주세요.';
    } else {
        $fp = fopen($_FILES['csv']['tmp_name'], 'r');
        if (!$fp) { $msg = 'CSV 파일을 읽을 수 없습니다.'; }
        else {
            $send_now = isset($_POST['csv_send']);
            // 예약 발송 시각(datetime-local, KST 벽시계) → ISO8601 +09:00. 비우면 즉시.
            $sched_in = isset($_POST['csv_schedule']) ? trim($_POST['csv_schedule']) : '';
            $scheduled_at = '';
            if ($send_now && $sched_in !== '' && preg_match('/^(\d{4})-(\d{2})-(\d{2})[T ](\d{2}):(\d{2})/', $sched_in, $sm2)) {
                $scheduled_at = sprintf('%s-%s-%sT%s:%s:00+09:00', $sm2[1],$sm2[2],$sm2[3],$sm2[4],$sm2[5]);
                // 과거/현재 시각이면 예약 무의미(Resend 즉시발송) → 즉시 발송으로 처리해 '예약' 오표기 방지
                $__ts = strtotime($scheduled_at);
                if ($__ts !== false && $__ts <= time() + 60) $scheduled_at = '';
            }
            $ins=0; $skip=0; $err=0; $sent=0; $sfail=0; $rowi=0; $firstErr='';
            while (($row = fgetcsv($fp)) !== false) {
                $rowi++;
                $joined = implode(',', $row);
                if (trim($joined) === '') continue;
                // 헤더행 스킵(첫 줄에 '분류'/'이메일'/'할인'/'코드' 포함)
                if ($rowi === 1 && (mb_strpos($joined,'분류')!==false || mb_strpos($joined,'이메일')!==false || mb_strpos($joined,'할인')!==false || mb_strpos($joined,'코드')!==false)) continue;
                $cat     = trim(isset($row[0]) ? $row[0] : '');   // 분류(스폰서/스피커)
                $code    = strtoupper(trim(isset($row[1]) ? $row[1] : ''));
                $company = trim(isset($row[2]) ? $row[2] : '');   // 업체/스피커
                $rn      = trim(isset($row[3]) ? $row[3] : '');   // 받는사람
                $re      = trim(isset($row[4]) ? $row[4] : '');   // 이메일1(주수신)
                $cc      = implode(',', cp_split_emails(isset($row[5]) ? $row[5] : ''));   // 참조이메일2(CC·다중 허용)
                $pct     = (int)(isset($row[6]) ? $row[6] : 0);   // 할인율
                $exp     = cp_norm_date(isset($row[7]) ? $row[7] : ''); // 만료일(YYYY.M.D 등 허용)
                $max     = isset($row[8]) && trim($row[8])!=='' ? (int)$row[8] : 1; // 한도
                $memo    = trim(isset($row[9]) ? $row[9] : '');   // 메모
                $clang   = (isset($row[10]) && strtolower(trim($row[10]))==='en') ? 'en' : 'ko'; // 언어
                // 주수신 이메일·할인율 필수. CC는 유효할 때만 저장(무효 시 무시)
                if ($re === '' || !filter_var($re, FILTER_VALIDATE_EMAIL) || $pct < 1) { $err++; continue; }
                if ($pct > 100) $pct = 100; if ($max < 0) $max = 0;
                if ($code === '') $code = cp_gen_code();
                $expSql = ($exp !== '') ? "'".sql_real_escape_string($exp)."'" : "NULL";
                $r = sql_query("INSERT INTO cb_unreal_2026_coupon (cp_code,cp_percent,cp_expire,cp_max,cp_memo,cp_recipient_name,cp_recipient_email,cp_cc_email,cp_category,cp_company,cp_lang,cp_reg)
                    VALUES ('".sql_real_escape_string($code)."', $pct, $expSql, $max, '".sql_real_escape_string($memo)."', '".sql_real_escape_string($rn)."', '".sql_real_escape_string($re)."', '".sql_real_escape_string($cc)."', '".sql_real_escape_string($cat)."', '".sql_real_escape_string($company)."', '".$clang."', now())");
                if (!$r) { $skip++; continue; }   // 중복 코드
                $ins++;
                if ($send_now) {
                    $nid = sql_fetch("SELECT LAST_INSERT_ID() id"); $nid = $nid ? (int)$nid['id'] : 0;
                    if ($nid) { $sm = cp_send_mail($nid, $scheduled_at); if (!empty($sm['ok'])) $sent++; else { $sfail++; if ($firstErr==='') $firstErr = isset($sm['msg'])?$sm['msg']:'오류'; } }
                }
            }
            fclose($fp);
            $sendLabel = ($scheduled_at !== '') ? " · 예약 {$sent}건(".$scheduled_at.") · 실패 {$sfail}건" : " · 메일 성공 {$sent}건 · 실패 {$sfail}건";
            if ($sfail > 0 && $firstErr !== '') $sendLabel .= " · [실패사유] ".$firstErr;
            $msg = "CSV 발급 완료 — 신규 {$ins}건 · 중복skip {$skip}건 · 오류 {$err}건".($send_now ? $sendLabel : "");
        }
    }
}
if (isset($_GET['toggle'])) { $no=(int)$_GET['toggle']; sql_query("UPDATE cb_unreal_2026_coupon SET cp_active = IF(cp_active='Y','N','Y') WHERE cp_no=$no"); $msg='상태를 변경했습니다.'; }
if (isset($_GET['del']))    { $no=(int)$_GET['del']; sql_query("DELETE FROM cb_unreal_2026_coupon WHERE cp_no=$no"); $msg='삭제했습니다.'; }
// [TEST] 다중 선택 일괄 삭제
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['del_bulk'])) {
    $ids = (isset($_POST['del_ids']) && is_array($_POST['del_ids'])) ? array_map('intval', $_POST['del_ids']) : array();
    $ids = array_values(array_filter($ids, function($v){ return $v > 0; }));
    if ($ids) { sql_query("DELETE FROM cb_unreal_2026_coupon WHERE cp_no IN (".implode(',', $ids).")"); $msg = count($ids).'개를 삭제했습니다.'; }
    else { $msg = '선택된 항목이 없습니다.'; }
}

// ── 개인 등록 쿠폰 노출 ON/OFF (cb_unreal_2026_config[indiv_coupon]) ──
sql_query("CREATE TABLE IF NOT EXISTS cb_unreal_2026_config (cfg_key VARCHAR(50) NOT NULL, cfg_val VARCHAR(255) NOT NULL DEFAULT '', PRIMARY KEY (cfg_key)) DEFAULT CHARSET=utf8");
if (isset($_GET['feature'])) {
    $fv = ($_GET['feature'] === 'on') ? 'on' : 'off';
    $ex = sql_fetch("SELECT cfg_key FROM cb_unreal_2026_config WHERE cfg_key='indiv_coupon'");
    if ($ex) sql_query("UPDATE cb_unreal_2026_config SET cfg_val='$fv' WHERE cfg_key='indiv_coupon'");
    else     sql_query("INSERT INTO cb_unreal_2026_config (cfg_key,cfg_val) VALUES ('indiv_coupon','$fv')");
    $msg = '개인 등록 쿠폰 노출을 '.($fv==='on'?'켰습니다 (ON)':'껐습니다 (OFF)').'.';
}
$__ic = sql_fetch("SELECT cfg_val FROM cb_unreal_2026_config WHERE cfg_key='indiv_coupon'");
$indiv_on = ($__ic && trim((string)$__ic['cfg_val']) === 'on');

// ── 쿠폰 목록 CSV(엑셀) 다운로드 — HTML 출력 전 스트리밍 ──
if (isset($_GET['export'])) {
    $csv_safe = function($v){ $s=(string)$v; if ($s!=='' && strpos("=+-@\t\r", $s[0])!==false) return "'".$s; return $s; };
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="ufs2026_coupons_'.date('Ymd').'.csv"');
    $out = fopen('php://output', 'w');
    echo "\xEF\xBB\xBF"; // BOM(엑셀 한글)
    fputcsv($out, array('분류','업체/스피커','코드','할인율','만료일','한도','사용','상태','메모','수신자명','수신자이메일','참조이메일CC','언어','발송상태','발송시각','등록링크','발급일'));
    $rs = sql_query("SELECT * FROM cb_unreal_2026_coupon ORDER BY cp_no DESC");
    if ($rs) while ($c = $rs->fetch_assoc()) {
        $link = 'https://epiclounge.co.kr/unrealfest2026/ticket-all.php?coupon='.$c['cp_code'];
        $sst = isset($c['cp_status']) ? $c['cp_status'] : '';
        $sstl = ($sst==='sent'?'성공':($sst==='fail'?'실패':''));
        fputcsv($out, array_map($csv_safe, array(
            (isset($c['cp_category'])?$c['cp_category']:''),
            (isset($c['cp_company'])?$c['cp_company']:''),
            $c['cp_code'], (int)$c['cp_percent'].'%',
            ($c['cp_expire'] && $c['cp_expire']!=='0000-00-00' ? $c['cp_expire'] : ''),
            ((int)$c['cp_max']>0 ? (int)$c['cp_max'] : '무제한'), (int)$c['cp_used'],
            ($c['cp_active']==='Y'?'사용':'중지'), $c['cp_memo'],
            (isset($c['cp_recipient_name'])?$c['cp_recipient_name']:''),
            (isset($c['cp_recipient_email'])?$c['cp_recipient_email']:''),
            (isset($c['cp_cc_email'])?$c['cp_cc_email']:''),
            (isset($c['cp_lang'])?$c['cp_lang']:'ko'), $sstl,
            (isset($c['cp_sent_at'])?$c['cp_sent_at']:''), $link, $c['cp_reg']
        )));
    }
    fclose($out);
    exit;
}

// [TEST] PRG: 상태 변경 요청(POST 또는 GET 액션)은 처리 후 자기 자신으로 리다이렉트 → 새로고침 재실행 방지
$cp_mutated = ($_SERVER['REQUEST_METHOD'] === 'POST')
    || isset($_GET['toggle']) || isset($_GET['del']) || isset($_GET['send_mail'])
    || isset($_GET['cancel_mail']) || isset($_GET['feature']);
if ($cp_mutated && !headers_sent()) {
    $_SESSION['cp_flash'] = $msg;
    header('Location: 2026_coupon.php');
    exit;
}

// [TEST] 예약 건 상태 동기화 — 목록 표시 전, 예약(scheduled) 건들의 Resend 실제 상태를 반영(발송완료→발송/취소→취소)
$__sync = @sql_query("SELECT cp_no, cp_send_id FROM cb_unreal_2026_coupon WHERE cp_status='scheduled' AND cp_send_id<>''");
if ($__sync) { while ($__s = $__sync->fetch_assoc()) { cp_sync_status((int)$__s['cp_no'], $__s['cp_send_id']); } }

include_once('./admin.head.php');
?>
<style>
.cp-wrap{width:100%;max-width:100%;margin:16px 0}
.cp-card{border:1px solid #ddd;border-radius:6px;padding:18px;background:#fff;margin-bottom:16px}
.cp-card h2{font-size:15px;font-weight:700;margin:0 0 12px}
.cp-form{display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end}
.cp-form label{display:block;font-size:12px;color:#666;margin-bottom:4px}
.cp-form input{padding:8px;border:1px solid #ccc;border-radius:4px}
.cp-btn{background:#00C1D5;color:#fff;border:0;padding:9px 20px;font-weight:700;border-radius:4px;cursor:pointer}
.cp-msg{background:#e8fbfd;border:1px solid #00C1D5;color:#007a89;padding:10px 14px;border-radius:4px;margin-bottom:14px}
.cp-tbl{width:100%;border-collapse:collapse;font-size:13px}
.cp-tbl th,.cp-tbl td{border:1px solid #e5e5e5;padding:8px 10px;text-align:center}
.cp-tbl thead th{background:#fafafa}
.cp-off{opacity:.45}
.cp-cx td{text-decoration:line-through;color:#b0b0b0}
.cp-a{color:#0a7;text-decoration:underline;cursor:pointer}.cp-d{color:#c33;text-decoration:underline;cursor:pointer}
</style>
<div class="cp-wrap">
  <div style="margin-bottom:12px"><a href="2026_coupon_status.php" style="display:inline-block;background:#0f172a;color:#fff;padding:9px 18px;border-radius:5px;text-decoration:none;font-weight:700;font-size:13px">📋 쿠폰 등록 현황 (구글시트 붙여넣기) →</a></div>
  <?php if ($msg): ?><div class="cp-msg"><?= cp_e($msg) ?></div><?php endif; ?>

  <?php $usageCode = isset($_GET['usage']) ? strtoupper(trim($_GET['usage'])) : '';
  if ($usageCode !== ''): $uce = sql_real_escape_string($usageCode);
    $hasGrpU = ($t=@sql_query("SHOW TABLES LIKE 'cb_unreal_2026_group'")) && $t->num_rows;
    $hasIndCol = ($c=@sql_query("SHOW COLUMNS FROM cb_unreal_2026_event2_apply LIKE 'apply_coupon_code'")) && $c->num_rows;
    $inds = $hasIndCol ? sql_query("SELECT * FROM cb_unreal_2026_event2_apply WHERE apply_coupon_code='$uce' AND (apply_group_code IS NULL OR apply_group_code='') AND apply_pay_status <> 0 ORDER BY apply_no DESC") : null;
    $indN = $inds ? $inds->num_rows : 0;
    $grps = $hasGrpU ? sql_query("SELECT * FROM cb_unreal_2026_group WHERE coupon_code='$uce' AND pay_status <> 'cancel' ORDER BY grp_no DESC") : null;
    $grpN = $grps ? $grps->num_rows : 0; ?>
  <div class="cp-card">
    <h2>🔎 쿠폰 <?= cp_e($usageCode) ?> — 사용 등록 내역 <a href="2026_coupon.php" style="font-weight:400;font-size:12px;margin-left:8px;color:#888">✕ 닫기</a></h2>
    <h3 style="font-size:13px;color:#059669;margin:6px 0 6px">[개인] <?= $indN ?>건</h3>
    <?php if ($indN): ?>
    <table class="cp-tbl" style="font-size:12px">
      <thead><tr><th>#</th><th>이름</th><th>회사</th><th>이메일</th><th>연락처</th><th>상품</th><th>할인</th><th style="text-align:right">결제금액</th><th>상태</th><th>등록일</th></tr></thead>
      <tbody>
      <?php $i=0; while($a=$inds->fetch_assoc()): $i++; $ps=(int)$a['apply_pay_status'];
        $stt = ($ps===0)?'취소':(($ps===1)?'입금대기':(($a['pay_complete']==='Y')?(($a['free_yn']==='Y')?'무료완료':'완료'):'대기')); ?>
      <tr<?= $ps===0?' class="cp-cx"':'' ?>><td><?= $i ?></td><td><?= cp_e($a['apply_user_name']) ?></td><td><?= cp_e($a['apply_user_company']) ?></td><td><?= cp_e($a['apply_user_email']) ?></td><td><?= cp_e($a['apply_user_phone']) ?></td><td><?= cp_e($a['apply_product_name']) ?></td><td><?= (int)$a['apply_coupon_pct'] ?>%</td><td style="text-align:right">₩<?= number_format((int)$a['apply_product_price']) ?></td><td><?= cp_e($stt) ?></td><td><?= cp_e(substr($a['apply_reg_datetime'],0,16)) ?></td></tr>
      <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?><p style="color:#999;font-size:12px;margin:0">개인 사용 없음</p><?php endif; ?>

    <h3 style="font-size:13px;color:#2563eb;margin:16px 0 6px">[단체] <?= $grpN ?>건</h3>
    <?php if ($grpN): ?>
    <table class="cp-tbl" style="font-size:12px">
      <thead><tr><th>접수번호</th><th>회사</th><th>대표</th><th>인원</th><th style="text-align:right">결제금액</th><th>할인</th><th>상태</th><th>결제일</th><th></th></tr></thead>
      <tbody>
      <?php while($g=$grps->fetch_assoc()): $gst=$g['pay_status']; $gstt=($gst==='paid'?'결제완료':($gst==='cancel'?'취소':'대기')); ?>
      <tr<?= $gst==='cancel'?' class="cp-cx"':'' ?>><td><?= cp_e($g['grp_code']) ?></td><td><?= cp_e($g['rep_company']) ?></td><td><?= cp_e($g['rep_name']) ?></td><td><?= (int)$g['headcount'] ?>명</td><td style="text-align:right">₩<?= number_format((int)$g['total_amount']) ?></td><td><?= (int)$g['discount_pct'] ?>%</td><td><?= cp_e($gstt) ?></td><td><?= cp_e(substr($g['paid_at'],0,16)) ?></td><td><a href="2026_group_list.php?grp=<?= (int)$g['grp_no'] ?>" style="font-size:11px">상세→</a></td></tr>
      <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?><p style="color:#999;font-size:12px;margin:0">단체 사용 없음</p><?php endif; ?>
  </div>
  <?php endif; ?>

  <div class="cp-card">
    <h2 style="margin-bottom:8px">개인 등록 쿠폰 노출</h2>
    <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
      <span style="font-size:15px">현재 상태:
        <b style="color:<?= $indiv_on?'#1a9e54':'#c0392b' ?>"><?= $indiv_on?'ON — 노출 중':'OFF — 숨김' ?></b>
      </span>
      <?php if ($indiv_on): ?>
        <a href="?feature=off" onclick="return confirm('개인 등록 페이지(ticket-all/day)에서 쿠폰 입력창을 숨길까요?')"
           style="padding:7px 18px;background:#c0392b;color:#fff;text-decoration:none;border-radius:5px;font-weight:bold">끄기 (OFF)</a>
      <?php else: ?>
        <a href="?feature=on" onclick="return confirm('개인 등록 페이지(ticket-all/day)에 쿠폰 입력창을 노출하고 결제에 반영할까요?')"
           style="padding:7px 18px;background:#1a9e54;color:#fff;text-decoration:none;border-radius:5px;font-weight:bold">켜기 (ON)</a>
      <?php endif; ?>
    </div>
    <p style="color:#888;font-size:12px;margin-top:10px;line-height:1.6">
      ON 시 개인 등록 페이지(<b>양일권·1일권</b>)에 쿠폰 입력창이 노출되고, 입력한 쿠폰 할인이 결제 금액에 반영됩니다(100% 쿠폰=무료 완료).
      단체 등록 쿠폰 사용에는 영향이 없습니다. · 미리보기(공개 무영향): <code>?ufs_coupon_preview=ufscpn2026x9f3a</code>
    </p>
  </div>

  <form method="post" class="cp-card">
    <h2>쿠폰 발급</h2>
    <div class="cp-form">
      <div><label>쿠폰 코드</label>
        <input type="text" name="cp_code" id="cp_code" placeholder="빈칸이면 자동생성" style="width:160px;text-transform:uppercase">
        <button type="button" onclick="genCoupon()" title="추측 불가한 난수 코드 생성(권장)" style="padding:6px 10px;margin-left:4px;background:#334155;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:12px">🎲 자동생성</button>
      </div>
      <div><label>할인율(%)</label><input type="number" name="cp_percent" min="1" max="100" style="width:90px" required></div>
      <div><label>만료일(선택)</label><input type="date" name="cp_expire"></div>
      <div><label>사용 한도(0=무제한)</label><input type="number" name="cp_max" min="0" value="1" style="width:120px"></div>
      <div><label>발급 수량</label><input type="number" name="cp_qty" value="1" min="1" max="200" style="width:80px" title="2 이상이면 난수 코드로 여러 개 일괄 발급"></div>
      <div><label>메모(선택)</label><input type="text" name="cp_memo" placeholder="설명" style="width:160px"></div>
      <div><label>분류(메일양식)</label><select name="cp_category" style="padding:8px;border:1px solid #ccc;border-radius:4px"><option value="">선택안함</option><option value="스폰서">스폰서</option><option value="스피커">스피커</option><option value="기타">기타</option></select></div>
      <div><label>업체/스피커(선택)</label><input type="text" name="cp_company" placeholder="회사명 또는 스피커명" style="width:150px"></div>
      <div><label>수신자명(선택)</label><input type="text" name="cp_recipient_name" placeholder="홍길동" style="width:120px"></div>
      <div><label>수신자 이메일(선택)</label><input type="email" name="cp_recipient_email" placeholder="user@example.com" style="width:180px"></div>
      <div><label>참조 이메일 CC(선택·다중)</label><input type="text" name="cp_cc_email" placeholder="cc1@x.com, cc2@x.com" style="width:200px" title="여러 명은 콤마로 구분"></div>
      <div><label>메일 언어</label><select name="cp_lang" style="padding:8px;border:1px solid #ccc;border-radius:4px"><option value="ko">한국어</option><option value="en">English</option></select></div>
      <div style="align-self:flex-end"><label style="font-weight:400;font-size:12px;cursor:pointer"><input type="checkbox" name="send_now" value="1"> 발급 후 바로 메일 발송</label></div>
      <button type="submit" class="cp-btn">발급</button>
    </div>
    <p style="color:#888;font-size:12px;margin:6px 0 0">※ <b>발급 수량 2 이상</b>이면 코드가 각각 <b>난수로 자동생성</b>되어 여러 개 일괄 발급됩니다(수신자·즉시발송 미적용 — 특정 수신자 발송은 아래 CSV). 수량 1이면 코드 직접 입력·수신자 지정·즉시 발송 가능.<br>※ 수신자 이메일을 넣으면 [발급 후 바로 발송] 또는 목록의 [메일 발송] 버튼으로 <b>등록 링크+쿠폰</b> 메일을 보냅니다. 등록은 개인(카드·본인인증) 페이지에서 쿠폰 자동 적용. <b>개인 쿠폰 노출 ON</b> 상태여야 실제 사용 가능(정상가 전환 후).</p>
  </form>
  <script>
  /* 쿠폰 코드 난수 생성 — 암호학적 난수(crypto) 우선, 혼동문자(O,0,I,1) 제외한 32자 알파벳. 형식 UECPN-XXXX-XXXX (32^8≈1.1조). */
  function genCoupon(){
    var alpha='ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    function rnd(n){ var out='', i;
      if(window.crypto && crypto.getRandomValues){ var a=new Uint32Array(n); crypto.getRandomValues(a); for(i=0;i<n;i++) out+=alpha[a[i]%alpha.length]; }
      else { for(i=0;i<n;i++) out+=alpha[Math.floor(Math.random()*alpha.length)]; }
      return out; }
    document.getElementById('cp_code').value='UECPN-'+rnd(4)+'-'+rnd(4);
  }
  function dlCouponTemplate(){
    var csv='﻿분류,코드(선택),업체 / 스피커,받는사람,이메일 1,참조 이메일 2,할인율,만료일,한도,메모,언어(ko/en)\n'
          + '스폰서,,회사명A,홍길동,hong@example.com,"cc1@example.com, cc2@example.com",100,,5,,ko\n'
          + '스피커,,,John Doe,john@example.com,,100,,1,partner,en\n';
    var a=document.createElement('a'); a.href='data:text/csv;charset=utf-8,'+encodeURIComponent(csv);
    a.download='쿠폰_일괄발급_양식.csv'; document.body.appendChild(a); a.click(); document.body.removeChild(a);
  }
  function cpChks(){ return Array.prototype.slice.call(document.querySelectorAll('.cpchk')); }
  function cpUpdateCnt(){ var n=cpChks().filter(function(c){return c.checked;}).length; var el=document.getElementById('cpSelCnt'); if(el) el.textContent='선택 '+n+'개'; var all=document.getElementById('cpAll'); if(all){ var t=cpChks().length; all.checked=(t>0&&n===t); } }
  function cpToggleAll(src){ cpChks().forEach(function(c){ c.checked=src.checked; }); cpUpdateCnt(); }
  function cpBulkConfirm(){ var n=cpChks().filter(function(c){return c.checked;}).length; if(n===0){ alert('삭제할 쿠폰을 선택해 주세요.'); return false; } return confirm('선택한 '+n+'개 쿠폰을 삭제할까요? 되돌릴 수 없습니다.'); }
  function cpCopyFallback(text){ var ta=document.createElement('textarea'); ta.value=text; ta.style.position='fixed'; ta.style.opacity='0'; document.body.appendChild(ta); ta.select(); try{ document.execCommand('copy'); }catch(e){} document.body.removeChild(ta); }
  function cpCopy(btn, text){
    var o=btn.textContent;
    function done(){ btn.textContent='복사됨'; btn.style.background='#d4f4dd'; setTimeout(function(){ btn.textContent=o; btn.style.background='#f5f5f5'; }, 1200); }
    if(navigator.clipboard && navigator.clipboard.writeText){ navigator.clipboard.writeText(text).then(done, function(){ cpCopyFallback(text); done(); }); }
    else { cpCopyFallback(text); done(); }
  }
  </script>

  <div class="cp-card">
    <h2>CSV 일괄 발급 · 메일 발송</h2>
    <form method="post" enctype="multipart/form-data" class="cp-form" onsubmit="return confirm('CSV의 각 행으로 쿠폰을 일괄 발급합니다.' + (document.getElementById('csv_send').checked ? (document.getElementById('csv_schedule').value ? ('\n[예약 발송] '+document.getElementById('csv_schedule').value.replace('T',' ')+' 에 각 수신자에게 실제 메일이 발송됩니다.') : '\n체크된 [메일 발송]에 따라 각 수신자에게 즉시 실제 메일이 발송됩니다.') : '') + '\n계속할까요?');">
      <input type="hidden" name="csv_mode" value="1">
      <div><label>CSV 파일</label><input type="file" name="csv" accept=".csv" required></div>
      <div style="align-self:flex-end"><label style="font-weight:400;font-size:12px;cursor:pointer"><input type="checkbox" id="csv_send" name="csv_send" value="1"> 발급 후 메일 발송</label></div>
      <div><label>예약 발송 시각(비우면 즉시·최대 72h)</label><input type="datetime-local" id="csv_schedule" name="csv_schedule" style="padding:7px;border:1px solid #ccc;border-radius:4px" title="[메일 발송] 체크 시에만 적용. 비우면 즉시 발송. 최대 약 72시간 이내만 가능(Resend)"></div>
      <button type="submit" class="cp-btn" style="background:#1a7f37">CSV 일괄 발급</button>
      <button type="button" onclick="dlCouponTemplate()" style="background:#e5e7eb;color:#111;border:0;padding:9px 16px;font-weight:700;border-radius:4px;cursor:pointer">양식 다운로드</button>
    </form>
    <p style="color:#888;font-size:12px;margin:8px 0 0">열(11): <b>분류(스폰서/스피커)</b>, 코드(선택·빈칸이면 자동생성), 업체/스피커, 받는사람, <b>이메일 1(주수신)</b>, 참조 이메일 2(<b>CC 발송·다중 가능</b> — 여러 명은 콤마 구분, 셀 전체를 <b>큰따옴표로 감싸기</b> 예: "a@x.com, b@x.com"), 할인율(1~100), 만료일(<b>공란 권장=만료 없음</b>. 넣으려면 2026.8.15 또는 2026-08-15), 한도(선택·기본1), 메모(선택), <b>언어(ko/en·선택·기본ko)</b>. 첫 줄 헤더 자동 스킵. <b>이메일1 누락/형식오류·할인율 없음=오류 스킵</b>, 코드 중복=skip. CC는 형식 오류면 무시(주메일은 발송). 분류는 발송 메일 양식을 결정(스폰서/스피커 별도 — 양식 추후 적용).</p>
  </div>

  <div class="cp-card">
    <h2>발급 쿠폰 목록 <a href="?export=1" style="font-size:12px;font-weight:700;background:#1a7f37;color:#fff;padding:5px 12px;border-radius:4px;text-decoration:none;margin-left:8px">⬇ CSV 다운로드</a></h2>
    <form method="post" onsubmit="return cpBulkConfirm()">
    <input type="hidden" name="del_bulk" value="1">
    <div style="margin:0 0 8px;display:flex;align-items:center;gap:10px">
      <button type="submit" style="background:#c0392b;color:#fff;border:0;padding:7px 14px;font-weight:700;border-radius:4px;cursor:pointer">선택 삭제</button>
      <span id="cpSelCnt" style="font-size:12px;color:#888">선택 0개</span>
    </div>
    <table class="cp-tbl">
      <thead><tr><th style="width:34px"><input type="checkbox" id="cpAll" onclick="cpToggleAll(this)" title="전체 선택"></th><th>코드</th><th>할인율</th><th>만료일</th><th>사용/한도</th><th>사용 내역</th><th>상태</th><th>메모</th><th>수신/발송</th><th>관리</th></tr></thead>
      <tbody>
      <?php
      $grpChk = @sql_query("SHOW TABLES LIKE 'cb_unreal_2026_group'"); $hasGrp = ($grpChk && $grpChk->num_rows);
      $res = sql_query("SELECT * FROM cb_unreal_2026_coupon ORDER BY cp_no DESC");
      if ($res && $res->num_rows) { while ($r = $res->fetch_assoc()):
        $off = ($r['cp_active'] !== 'Y');
        $ce2 = sql_real_escape_string($r['cp_code']);
        $usedList = array();
        // 단체 쿠폰 사용 — 결제완료(paid)만 표시
        if ($hasGrp) {
          $gq = sql_query("SELECT rep_company, pay_status FROM cb_unreal_2026_group WHERE coupon_code='$ce2' ORDER BY grp_no DESC");
          if ($gq) { while ($gr = $gq->fetch_assoc()) {
            if ($gr['pay_status'] !== 'paid') continue; // 취소·대기 제외
            $usedList[] = '<span style="color:#2563eb">[단체]</span> '.cp_e($gr['rep_company']);
          } }
        }
        // 개인 쿠폰 사용 — 결제완료(성공)만 표시 (취소·입금대기 제외)
        $colChk = @sql_query("SHOW COLUMNS FROM cb_unreal_2026_event2_apply LIKE 'apply_coupon_code'");
        if ($colChk && $colChk->num_rows) {
          $iq = sql_query("SELECT apply_user_name, apply_user_company, apply_pay_status, pay_complete FROM cb_unreal_2026_event2_apply WHERE apply_coupon_code='$ce2' AND (apply_group_code IS NULL OR apply_group_code='') ORDER BY apply_no DESC");
          if ($iq) { while ($ir = $iq->fetch_assoc()) {
            $ps = (int)$ir['apply_pay_status'];
            $success = ($ir['pay_complete']==='Y') || ($ps >= 2); // 완료/무료완료
            if (!$success) continue; // 취소(0)·입금대기(1) 제외
            $nm = $ir['apply_user_name']; if (trim((string)$ir['apply_user_company']) !== '') $nm .= ' / '.$ir['apply_user_company'];
            $usedList[] = '<span style="color:#059669">[개인]</span> '.cp_e($nm);
          } }
        }
        $usedGroups = $usedList ? implode('<br>', $usedList) : '-'; ?>
        <tr class="<?= $off?'cp-off':'' ?>">
          <td><input type="checkbox" class="cpchk" name="del_ids[]" value="<?= (int)$r['cp_no'] ?>" onclick="cpUpdateCnt()"></td>
          <?php $cpLink = 'https://epiclounge.co.kr/unrealfest2026/'.((isset($r['cp_lang'])&&$r['cp_lang']==='en')?'ticket-coupon-en.php':'ticket-coupon.php').'?coupon='.rawurlencode($r['cp_code']); ?>
          <td style="white-space:nowrap"><b><a href="?usage=<?= rawurlencode($r['cp_code']) ?>" title="이 쿠폰 사용 등록 내역 보기"><?= cp_e($r['cp_code']) ?></a></b><br>
            <button type="button" onclick="cpCopy(this,'<?= cp_e($r['cp_code']) ?>')" title="쿠폰 코드 복사" style="margin-top:4px;padding:2px 7px;font-size:11px;border:1px solid #ccc;background:#f5f5f5;border-radius:3px;cursor:pointer">코드</button>
            <button type="button" onclick="cpCopy(this,'<?= cp_e($cpLink) ?>')" title="등록 링크 복사(<?= (isset($r['cp_lang'])&&$r['cp_lang']==='en')?'EN 무료전용':'KO 본인인증' ?>)" style="margin-top:4px;padding:2px 7px;font-size:11px;border:1px solid #2563eb;background:#eff4ff;color:#2563eb;border-radius:3px;cursor:pointer">링크복사</button></td>
          <td><?= (int)$r['cp_percent'] ?>%</td>
          <td><?= cp_e($r['cp_expire'] && $r['cp_expire']!=='0000-00-00' ? $r['cp_expire'] : '-') ?></td>
          <td><?= (int)$r['cp_used'] ?> / <?= ((int)$r['cp_max']>0 ? (int)$r['cp_max'] : '무제한') ?></td>
          <td style="text-align:left;font-size:12px"><?= $usedGroups ?></td>
          <td><?= $off ? '중지' : '사용' ?></td>
          <td><?= cp_e($r['cp_memo']) ?></td>
          <td style="font-size:12px;text-align:left">
            <?php $rem = trim($r['cp_recipient_email']); if ($rem !== ''): $cst = isset($r['cp_status'])?$r['cp_status']:''; $rcat=isset($r['cp_category'])?trim($r['cp_category']):''; $rcomp=isset($r['cp_company'])?trim($r['cp_company']):''; $rcc=isset($r['cp_cc_email'])?trim($r['cp_cc_email']):''; ?>
              <?php if ($rcat!==''||$rcomp!==''): ?><span style="font-size:10px;background:#f0f0f0;border:1px solid #ddd;border-radius:3px;padding:0 4px;color:#555"><?= cp_e(trim($rcat.' '.$rcomp)) ?></span><br><?php endif; ?>
              <?= cp_e($r['cp_recipient_name']!==''?$r['cp_recipient_name']:'(이름없음)') ?><?= (isset($r['cp_lang'])&&$r['cp_lang']==='en')?' <span style="background:#2563eb;color:#fff;font-size:10px;padding:0 4px;border-radius:3px">EN</span>':'' ?><br>
              <span style="color:#888"><?= cp_e($rem) ?></span><?php if ($rcc!==''): ?><br><span style="color:#aaa;font-size:11px">CC <?= cp_e($rcc) ?></span><?php endif; ?><br>
              <?php if ($cst==='sent'): ?><span style="color:#1a9e54;font-weight:700">✔ 발송</span> <span style="color:#aaa"><?= cp_e(substr($r['cp_sent_at'],5,11)) ?></span>
              <?php elseif ($cst==='scheduled'): ?><span style="color:#b8860b;font-weight:700">⏱ 예약</span> <span style="color:#aaa"><?= cp_e(isset($r['cp_scheduled_at'])?substr($r['cp_scheduled_at'],0,16):'') ?></span>
              <?php elseif ($cst==='canceled'): ?><span style="color:#999;font-weight:700">⊘ 예약취소</span>
              <?php elseif ($cst==='fail'): ?><span style="color:#c0392b;font-weight:700">✖ 실패</span>
              <?php else: ?><span style="color:#999">미발송</span><?php endif; ?><br>
              <?php if ($cst==='scheduled'): ?>
                <a href="?cancel_mail=<?= (int)$r['cp_no'] ?>" onclick="return confirm('예약된 메일 발송을 취소할까요?\n(<?= cp_e($rem) ?> · <?= cp_e(isset($r['cp_scheduled_at'])?substr($r['cp_scheduled_at'],0,16):'') ?>)')" style="display:inline-block;margin-top:3px;background:#c0392b;color:#fff;padding:2px 8px;border-radius:3px;text-decoration:none">예약 취소</a>
              <?php else: ?>
                <a href="?send_mail=<?= (int)$r['cp_no'] ?>" onclick="return confirm('<?= cp_e($rem) ?> 로 쿠폰 등록 안내 메일을 발송할까요?')" style="display:inline-block;margin-top:3px;background:#1a7f37;color:#fff;padding:2px 8px;border-radius:3px;text-decoration:none"><?= $cst==='sent'?'재발송':'메일 발송' ?></a>
              <?php endif; ?>
            <?php else: ?><span style="color:#ccc">수신자 없음</span><?php endif; ?>
          </td>
          <td>
            <a class="cp-a" href="?toggle=<?= (int)$r['cp_no'] ?>"><?= $off?'재개':'중지' ?></a> ·
            <a class="cp-d" href="?del=<?= (int)$r['cp_no'] ?>" onclick="return confirm('삭제하시겠습니까?')">삭제</a>
          </td>
        </tr>
      <?php endwhile; } else { ?>
        <tr><td colspan="10" style="color:#999;padding:18px">발급된 쿠폰이 없습니다.</td></tr>
      <?php } ?>
      </tbody>
    </table>
    </form>
    <p style="color:#888;font-size:12px;margin-top:8px;line-height:1.7">쿠폰은 <b>결제 완료 시</b> 사용 횟수가 1 증가하며, 한도를 두면 그 횟수까지만 사용됩니다.<br>
      · <b>단체 등록</b>: <a href="2026_group_config.php">단체 할인 설정</a>을 <b>쿠폰 모드(100)</b>로 두면, 각 회사가 자기 코드로 서로 다른 할인율을 받습니다. (회사 1곳 = 한도 1 권장)<br>
      · <b>개인 등록</b>: 참가자가 등록 시 쿠폰 코드를 입력하면 해당 할인율이 적용됩니다. <span style="color:#c0392b">(정상가 전환 시 적용 예정)</span><br>
      '사용 내역'은 <b>[단체]</b>·<b>[개인]</b> 사용을 함께 표시하며, (대기)는 무통장 입금 전, (취소)는 취소된 건입니다.</p>
  </div>
</div>
<?php include_once('./admin.tail.php'); ?>
