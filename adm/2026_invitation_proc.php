<?php
/* Unreal Fest Seoul 2026 — 초청장 발송 처리 (2026_invitation_proc.php) [M5]
 * 코드 발급(수동/CSV/로스터 sync)·활성토글·삭제·내보내기. cb_unreal_2026_speaker_code.
 * 공개 등록: /v3/unrealfest2026/ticket-invite.php?code=. PHP 7.0 / Gnuboard.
 */
include_once "./_common.php";

if (!$member['mb_id']) { alert('로그인이 필요합니다.', G5_BBS_URL . '/login.php'); exit; }

// ⚠️ 관리자 권한 게이트 — 로그인한 일반 회원 접근/ PII export 차단(페이지와 동일 메뉴 700370, 읽기 권한)
// 페이지(2026_invitation.php)와 동일한 'r' 레벨: 목록을 볼 수 있는 관리자면 액션도 허용(최고관리자는 무조건 통과).
// 'w'로 두면 700370에 쓰기 권한이 별도 부여되지 않은 서브관리자가 막힘 → 페이지는 보이는데 액션만 실패.
$sub_menu = '700370';
auth_check_menu($auth, $sub_menu, 'r');

define('INV_PUBLIC', 'https://epiclounge.co.kr/v3/unrealfest2026/ticket-invite.php');
define('INV_LIST',   '/v3/adm/2026_invitation.php');

/* 초청장 전용 CSRF 토큰 — 그누보드 일회용 ss_admin_token의 취약점 회피.
 * ss_admin_token은 (1) check_admin_token()이 1회 소비하고 (2) 모든 관리자 페이지의 get_admin_token()이 덮어써
 * 목록의 여러 액션이 토큰 하나를 공유하면 액션1회·메뉴이동·새탭·뒤로가기 시 전부 무효화된다.
 * 커스텀 세션키(set_session) 방식도 set_session()의 최초호출 session_regenerate_id 및 페이지 캐시 때문에
 * 페이지↔proc 세션이 어긋날 수 있다. → 세션 쓰기 없이, 로그인 시 고정되는 ss_mb_key에서 파생한 토큰 사용:
 * 페이지와 proc에서 동일하게 재계산되고(세션 저장 불필요), 값은 서버 세션 비밀의 해시라 공격자가 계산 불가(CSRF 방어 유지). */
if (!function_exists('inv_csrf_token')) {
function inv_csrf_token() {
    global $member;
    $base = get_session('ss_mb_key');   // 로그인 시 설정·매 요청 검증되는 값(부재 시 이미 로그아웃됨). IP/UA 기반이라 세션당 고정.
    if (!$base) $base = (isset($member['mb_id'])?$member['mb_id']:'').(isset($member['mb_datetime'])?$member['mb_datetime']:'');
    return md5('ufs_inv|'.$base);
}
function inv_csrf_check() {
    $req = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';
    if (!$req || $req !== inv_csrf_token())
        alert('토큰 정보가 올바르지 않습니다. 페이지를 새로고침한 뒤 다시 시도해 주세요.', '/v3/adm/2026_invitation.php');
    return true;
}
}

function inv_schema() {
    sql_query("CREATE TABLE IF NOT EXISTS cb_unreal_2026_speaker_code (
      sc_no        INT UNSIGNED NOT NULL AUTO_INCREMENT,
      sc_code      VARCHAR(40)  NOT NULL DEFAULT '',
      sc_src       VARCHAR(12)  NOT NULL DEFAULT 'speaker',
      sc_ref_id    INT          NOT NULL DEFAULT 0,
      sc_name      VARCHAR(100) NOT NULL DEFAULT '',
      sc_email     VARCHAR(200) NOT NULL DEFAULT '',
      sc_phone     VARCHAR(50)  NOT NULL DEFAULT '',
      sc_company   VARCHAR(200) NOT NULL DEFAULT '',
      sc_lang      VARCHAR(5)   NOT NULL DEFAULT 'ko',
      sc_quota     INT          NOT NULL DEFAULT 2,
      sc_used      INT          NOT NULL DEFAULT 0,
      sc_discount  INT          NOT NULL DEFAULT 100,
      sc_inviter   VARCHAR(100) NOT NULL DEFAULT '에픽게임즈',
      sc_active    CHAR(1)      NOT NULL DEFAULT 'Y',
      sc_sent_at   DATETIME     DEFAULT NULL,
      sc_memo      VARCHAR(255) NOT NULL DEFAULT '',
      sc_reg_datetime DATETIME  DEFAULT NULL,
      PRIMARY KEY (sc_no),
      UNIQUE KEY uq_sc_code (sc_code)
    ) DEFAULT CHARSET=utf8");
    // 발송/도달 이력(웹훅)
    @sql_query("ALTER TABLE cb_unreal_2026_speaker_code ADD COLUMN sc_msg_id VARCHAR(80) DEFAULT ''");
    @sql_query("ALTER TABLE cb_unreal_2026_speaker_code ADD COLUMN sc_status VARCHAR(20) DEFAULT ''");
    @sql_query("ALTER TABLE cb_unreal_2026_speaker_code ADD COLUMN sc_status_at DATETIME DEFAULT NULL");
    @sql_query("ALTER TABLE cb_unreal_2026_speaker_code ADD COLUMN sc_valid_from DATE DEFAULT NULL");
    @sql_query("ALTER TABLE cb_unreal_2026_speaker_code ADD COLUMN sc_valid_until DATE DEFAULT NULL");
}

// YYYY-MM-DD 정규화(아니면 '')
function inv_date($v) {
    $v = trim((string)$v);
    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) ? $v : '';
}

function inv_lang($raw) { $l = strtolower(trim((string)$raw));
    if ($l==='en'||$l==='영어'||$l==='english') return 'en';
    return 'ko'; }
function inv_clamp_quota($q) { $q=(int)$q; if ($q<1) $q=1; if ($q>10) $q=10; return $q; }
function inv_clamp_disc($d)  { $d=(int)$d; if ($d<50) $d=50; if ($d>100) $d=100; return $d; }

function inv_gen_code() {
    do {
        $c = 'UFS-'.strtoupper(substr(md5(uniqid('', true).mt_rand()),0,4)).'-'.strtoupper(substr(md5(uniqid('', true).mt_rand()),0,4));
        $e = sql_fetch("SELECT sc_no FROM cb_unreal_2026_speaker_code WHERE sc_code='".sql_real_escape_string($c)."'");
    } while ($e);
    return $c;
}

// 코드 1건 발급(중복 이메일이 이미 코드 보유 시 skip 옵션). 반환: 'ins'|'skip'|'err'
function inv_issue($d) {
    $email = trim($d['email']);
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) return 'err';
    if (!empty($d['skip_dup'])) {
        $ex = sql_fetch("SELECT sc_no FROM cb_unreal_2026_speaker_code WHERE sc_email='".sql_real_escape_string($email)."' AND sc_active='Y'");
        if ($ex) return 'skip';
    }
    if ((int)$d['ref_id'] > 0) {
        $exr = sql_fetch("SELECT sc_no FROM cb_unreal_2026_speaker_code WHERE sc_ref_id=".(int)$d['ref_id']." AND sc_src='speaker'");
        if ($exr) return 'skip';
    }
    $code = inv_gen_code();
    $f = function($v){ return sql_real_escape_string(strip_tags((string)$v)); };
    $vf = inv_date(isset($d['valid_from']) ? $d['valid_from'] : '');
    $vu = inv_date(isset($d['valid_until']) ? $d['valid_until'] : '');
    $vf_sql = ($vf !== '') ? "'".$vf."'" : "NULL";
    $vu_sql = ($vu !== '') ? "'".$vu."'" : "NULL";
    sql_query("INSERT INTO cb_unreal_2026_speaker_code
      (sc_code,sc_src,sc_ref_id,sc_name,sc_email,sc_phone,sc_company,sc_lang,sc_quota,sc_used,sc_discount,sc_inviter,sc_active,sc_memo,sc_valid_from,sc_valid_until,sc_reg_datetime)
      VALUES ('".$code."','".$f($d['src'])."',".(int)$d['ref_id'].",'".$f($d['name'])."','".$f($email)."','".$f($d['phone'])."','".$f($d['company'])."',
      '".inv_lang($d['lang'])."',".inv_clamp_quota($d['quota']).",0,".inv_clamp_disc($d['discount']).",'".$f($d['inviter'])."','Y','".$f($d['memo'])."',".$vf_sql.",".$vu_sql.",now())");
    return 'ins';
}

inv_schema();

// 초청장 메일 1건 발송 + sc_sent_at 기록(공개 repo 헬퍼 재사용). 반환: array('ok'|'error')
function inv_send_row($r) {
    if (!function_exists('ufs_invite_mail') || !function_exists('ufs_resend_send')) return array('ok'=>false,'error'=>'메일 모듈 미로드');
    if (trim($r['sc_email']) === '') return array('ok'=>false,'error'=>'이메일 없음');
    $m = ufs_invite_mail($r, ($r['sc_lang']==='en' ? 'en' : 'ko'));
    $res = ufs_resend_send($r['sc_email'], $m['subject'], $m['html'], '', $m['text']);
    if (!empty($res['ok'])) {
        $mid = isset($res['id']) ? sql_real_escape_string($res['id']) : '';
        sql_query("UPDATE cb_unreal_2026_speaker_code SET sc_sent_at=now(), sc_msg_id='".$mid."', sc_status='sent', sc_status_at=now() WHERE sc_no=".(int)$r['sc_no']);
    }
    return $res;
}

$mode  = isset($_POST['mode']) ? preg_replace('/[^a-z]/','',$_POST['mode']) : '';
$mode2 = isset($_GET['mode2']) ? preg_replace('/[^a-z]/','',$_GET['mode2']) : '';

// ── 수동 발급 ──
if ($mode === 'issue') {
    inv_csrf_check();
    $r = inv_issue(array(
        'src'=>'manual','ref_id'=>0,
        'name'=>$_POST['sc_name'],'email'=>$_POST['sc_email'],'phone'=>$_POST['sc_phone'],'company'=>$_POST['sc_company'],
        'lang'=>$_POST['sc_lang'],'quota'=>$_POST['sc_quota'],'discount'=>$_POST['sc_discount'],
        'inviter'=>($_POST['sc_inviter']!=='' ? $_POST['sc_inviter'] : '에픽게임즈'),'memo'=>$_POST['sc_memo'],
        'valid_from'=>(isset($_POST['sc_valid_from'])?$_POST['sc_valid_from']:''),'valid_until'=>(isset($_POST['sc_valid_until'])?$_POST['sc_valid_until']:''),'skip_dup'=>1,
    ));
    if ($r==='err') alert('이메일을 확인해 주세요.', INV_LIST);
    if ($r==='skip') alert('이미 활성 코드가 있는 이메일입니다.', INV_LIST);
    alert('초청 코드를 발급했습니다.', INV_LIST);
    exit;
}

// ── CSV 일괄 발급 ── (헤더: 초청인,대상명,이메일,연락처,소속,언어,매수,할인율,메모)
if ($mode === 'csv') {
    inv_csrf_check();
    if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== 0) alert('CSV 파일을 선택해 주세요.', INV_LIST);
    $fp = fopen($_FILES['csv']['tmp_name'], 'r');
    if (!$fp) alert('CSV 파일을 읽을 수 없습니다.', INV_LIST);
    $ins=0; $skip=0; $err=0; $ln=0;
    while (($row = fgetcsv($fp)) !== false) {
        $ln++;
        if ($ln === 1) continue; // 헤더
        if (count($row) < 3) continue;
        // BOM 제거(첫 컬럼)
        $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);
        $r = inv_issue(array(
            'src'=>'manual','ref_id'=>0,
            'inviter'=>(isset($row[0])&&trim($row[0])!=='')?$row[0]:'에픽게임즈',
            'name'=>isset($row[1])?$row[1]:'', 'email'=>isset($row[2])?$row[2]:'',
            'phone'=>isset($row[3])?$row[3]:'', 'company'=>isset($row[4])?$row[4]:'',
            'lang'=>isset($row[5])?$row[5]:'ko', 'quota'=>isset($row[6])?$row[6]:2,
            'discount'=>isset($row[7])?$row[7]:100, 'memo'=>isset($row[8])?$row[8]:'',
            'valid_from'=>isset($row[9])?$row[9]:'', 'valid_until'=>isset($row[10])?$row[10]:'', 'skip_dup'=>1,
        ));
        if ($r==='ins') $ins++; elseif ($r==='skip') $skip++; else $err++;
    }
    fclose($fp);
    alert('CSV 발급 완료 — 신규 '.$ins.'건 / 중복skip '.$skip.'건 / 오류 '.$err.'건', INV_LIST);
    exit;
}

// ── 스피커 로스터 자동 sync ── (cb_unreal_2026_speaker_apply, speaker_type='internal' + email 보유행만. 외부/키노트 제외)
if ($mode === 'sync') {
    inv_csrf_check();
    $ins=0; $skip=0;
    $rs = sql_query("SELECT id, speaker_name, speaker_email, speaker_ph, speaker_cp FROM cb_unreal_2026_speaker_apply WHERE speaker_email <> '' AND speaker_type='internal' ORDER BY id");
    if ($rs) while ($sp = sql_fetch_array($rs)) {
        $r = inv_issue(array(
            'src'=>'speaker','ref_id'=>(int)$sp['id'],
            'name'=>$sp['speaker_name'],'email'=>$sp['speaker_email'],'phone'=>$sp['speaker_ph'],'company'=>$sp['speaker_cp'],
            'lang'=>'ko','quota'=>2,'discount'=>100,'inviter'=>'에픽게임즈','memo'=>'스피커 로스터 자동발급','skip_dup'=>1,
        ));
        if ($r==='ins') $ins++; else $skip++;
    }
    alert('로스터 sync 완료 — 신규 '.$ins.'건 / 기존skip '.$skip.'건', INV_LIST);
    exit;
}

// ── 미발송 일괄 발송 (Resend) ──
if ($mode === 'sendall') {
    inv_csrf_check();
    require_once __DIR__ . '/../unrealfest2026/_resend.php';
    require_once __DIR__ . '/../unrealfest2026/_invite_mail.php';
    @set_time_limit(0); @ignore_user_abort(true);   // 대량 발송 타임아웃 방지
    $ok=0; $fail=0; $fails=array();
    $rs = sql_query("SELECT * FROM cb_unreal_2026_speaker_code WHERE sc_active='Y' AND sc_email<>'' AND (sc_sent_at IS NULL OR sc_sent_at='0000-00-00 00:00:00') ORDER BY sc_no");
    if ($rs) while ($r = sql_fetch_array($rs)) {
        $res = inv_send_row($r);
        if (!empty($res['ok'])) { $ok++; }
        else { $fail++; if (count($fails) < 5) $fails[] = $r['sc_email'].'('.(isset($res['error'])?$res['error']:'오류').')'; }
        usleep(300000);   // Resend rate limit 여유(약 3건/초)
    }
    $msg = '일괄 발송 완료 — 성공 '.$ok.'건 / 실패 '.$fail.'건';
    if ($fails) $msg .= ' · 실패예: '.implode(', ', $fails);
    alert($msg, INV_LIST);
    exit;
}

// ── 전체 사용기간 일괄 설정 ──
if ($mode === 'setperiod') {
    inv_csrf_check();
    $vf = inv_date(isset($_POST['sc_valid_from']) ? $_POST['sc_valid_from'] : '');
    $vu = inv_date(isset($_POST['sc_valid_until']) ? $_POST['sc_valid_until'] : '');
    $vf_sql = ($vf !== '') ? "'".$vf."'" : "NULL";
    $vu_sql = ($vu !== '') ? "'".$vu."'" : "NULL";
    sql_query("UPDATE cb_unreal_2026_speaker_code SET sc_valid_from=".$vf_sql.", sc_valid_until=".$vu_sql);
    alert('전체 코드 사용기간을 설정했습니다. ('.($vf!==''?$vf:'제한없음').' ~ '.($vu!==''?$vu:'제한없음').')', INV_LIST);
    exit;
}

// ── 활성 토글 ──
if ($mode2 === 'toggle') {
    inv_csrf_check();
    $no = (int)$_GET['no'];
    $r = sql_fetch("SELECT sc_active FROM cb_unreal_2026_speaker_code WHERE sc_no=".$no);
    if ($r) { $nv = ($r['sc_active']==='Y') ? 'N' : 'Y'; sql_query("UPDATE cb_unreal_2026_speaker_code SET sc_active='".$nv."' WHERE sc_no=".$no); }
    goto_url(INV_LIST);
}

// ── 초청장 개별 발송/재발송 (Resend) ──
if ($mode2 === 'send') {
    inv_csrf_check();
    require_once __DIR__ . '/../unrealfest2026/_resend.php';
    require_once __DIR__ . '/../unrealfest2026/_invite_mail.php';
    $no = (int)$_GET['no'];
    $r = sql_fetch("SELECT * FROM cb_unreal_2026_speaker_code WHERE sc_no=".$no);
    if (!$r) alert('코드를 찾을 수 없습니다.', INV_LIST);
    if ($r['sc_active'] !== 'Y') alert('비활성 코드는 발송할 수 없습니다. 먼저 활성화하세요.', INV_LIST);
    $res = inv_send_row($r);
    if (!empty($res['ok'])) alert('초청장을 발송했습니다. ('.$r['sc_email'].')', INV_LIST);
    alert('발송 실패: '.(isset($res['error']) ? $res['error'] : '오류'), INV_LIST);
}

// ── 삭제(미사용 코드만) ──
if ($mode2 === 'del') {
    inv_csrf_check();
    $no = (int)$_GET['no'];
    $r = sql_fetch("SELECT sc_used FROM cb_unreal_2026_speaker_code WHERE sc_no=".$no);
    if (!$r) alert('코드를 찾을 수 없습니다.', INV_LIST);
    if ((int)$r['sc_used'] > 0) alert('이미 등록에 사용된 코드는 삭제할 수 없습니다. 비활성으로 전환하세요.', INV_LIST);
    sql_query("DELETE FROM cb_unreal_2026_speaker_code WHERE sc_no=".$no);
    alert('삭제되었습니다.', INV_LIST);
    exit;
}

// ── 초청장 이메일 미리보기(발송되는 실제 HTML을 브라우저 렌더) ──
if ($mode2 === 'preview') {
    require_once __DIR__ . '/../unrealfest2026/_invite_mail.php';
    $no = (int)$_GET['no'];
    $r = sql_fetch("SELECT * FROM cb_unreal_2026_speaker_code WHERE sc_no=".$no);
    if (!$r) { header('Content-Type: text/html; charset=UTF-8'); echo '코드를 찾을 수 없습니다.'; exit; }
    $lang = isset($_GET['lang']) ? $_GET['lang'] : $r['sc_lang'];   // ?lang=en/ko 로 언어 강제 미리보기
    $m = ufs_invite_mail($r, ($lang === 'en' ? 'en' : 'ko'));
    header('Content-Type: text/html; charset=UTF-8');
    echo '<div style="background:#e5e7eb;padding:8px 14px;font:12px/1.5 sans-serif;color:#374151;border-bottom:1px solid #d1d5db;">'
        .'초청장 이메일 미리보기 — 코드 <b>'.htmlspecialchars($r['sc_code']).'</b> · 수신 '.htmlspecialchars($r['sc_email'])
        .' · 제목: <b>'.htmlspecialchars($m['subject']).'</b> · '
        .'<a href="?mode2=preview&no='.$no.'&lang=ko">KO</a> | <a href="?mode2=preview&no='.$no.'&lang=en">EN</a></div>';
    echo $m['html'];
    exit;
}

// ── 내보내기(CSV) ──
if ($mode2 === 'export') {
    inv_csrf_check();   // 재사용 토큰이라 1회소진 문제 없음 → PII 다운로드에도 CSRF 검사 적용
    // CSV 수식 인젝션 방어: =,+,-,@,탭,개행으로 시작하는 셀 앞에 ' 부착
    $csv_safe = function($v){ $s=(string)$v; if ($s!=='' && strpos("=+-@\t\r", $s[0])!==false) return "'".$s; return $s; };
    $rs = sql_query("SELECT * FROM cb_unreal_2026_speaker_code ORDER BY sc_no DESC");
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=ufs2026_invitation_codes.csv');
    echo "\xEF\xBB\xBF"; // BOM(엑셀 한글)
    $out = fopen('php://output', 'w');
    fputcsv($out, array('코드','링크','초청인','대상명','이메일','연락처','소속','언어','할인율','매수','사용','활성','발송시각','상태','사용시작','사용종료','메모','발급일'));
    if ($rs) while ($r = sql_fetch_array($rs)) {
        $link = INV_PUBLIC.'?code='.$r['sc_code'].'&lang='.$r['sc_lang'];
        fputcsv($out, array_map($csv_safe, array($r['sc_code'],$link,$r['sc_inviter'],$r['sc_name'],$r['sc_email'],$r['sc_phone'],$r['sc_company'],
            $r['sc_lang'],$r['sc_discount'],$r['sc_quota'],$r['sc_used'],$r['sc_active'],$r['sc_sent_at'],$r['sc_status'],$r['sc_valid_from'],$r['sc_valid_until'],$r['sc_memo'],$r['sc_reg_datetime'])));
    }
    fclose($out);
    exit;
}

goto_url(INV_LIST);
