<?php
/* Unreal Fest Seoul 2026 — 초청장 발송 처리 (2026_invitation_proc.php) [M5]
 * 코드 발급(수동/CSV/로스터 sync)·활성토글·삭제·내보내기. cb_unreal_2026_speaker_code.
 * 공개 등록: /v3/unrealfest2026/ticket-invite.php?code=. PHP 7.0 / Gnuboard.
 */
include_once "./_common.php";

if (!$member['mb_id']) { alert('로그인이 필요합니다.', G5_BBS_URL . '/login.php'); exit; }

define('INV_PUBLIC', 'https://epiclounge.co.kr/v3/unrealfest2026/ticket-invite.php');
define('INV_LIST',   '/v3/adm/2026_invitation.php');

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
    if ($email === '' || strpos($email,'@') === false) return 'err';
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
    sql_query("INSERT INTO cb_unreal_2026_speaker_code
      (sc_code,sc_src,sc_ref_id,sc_name,sc_email,sc_phone,sc_company,sc_lang,sc_quota,sc_used,sc_discount,sc_inviter,sc_active,sc_memo,sc_reg_datetime)
      VALUES ('".$code."','".$f($d['src'])."',".(int)$d['ref_id'].",'".$f($d['name'])."','".$f($email)."','".$f($d['phone'])."','".$f($d['company'])."',
      '".inv_lang($d['lang'])."',".inv_clamp_quota($d['quota']).",0,".inv_clamp_disc($d['discount']).",'".$f($d['inviter'])."','Y','".$f($d['memo'])."',now())");
    return 'ins';
}

inv_schema();

$mode  = isset($_POST['mode']) ? preg_replace('/[^a-z]/','',$_POST['mode']) : '';
$mode2 = isset($_GET['mode2']) ? preg_replace('/[^a-z]/','',$_GET['mode2']) : '';

// ── 수동 발급 ──
if ($mode === 'issue') {
    check_admin_token();
    $r = inv_issue(array(
        'src'=>'manual','ref_id'=>0,
        'name'=>$_POST['sc_name'],'email'=>$_POST['sc_email'],'phone'=>$_POST['sc_phone'],'company'=>$_POST['sc_company'],
        'lang'=>$_POST['sc_lang'],'quota'=>$_POST['sc_quota'],'discount'=>$_POST['sc_discount'],
        'inviter'=>($_POST['sc_inviter']!=='' ? $_POST['sc_inviter'] : '에픽게임즈'),'memo'=>$_POST['sc_memo'],'skip_dup'=>1,
    ));
    if ($r==='err') alert('이메일을 확인해 주세요.', INV_LIST);
    if ($r==='skip') alert('이미 활성 코드가 있는 이메일입니다.', INV_LIST);
    alert('초청 코드를 발급했습니다.', INV_LIST);
    exit;
}

// ── CSV 일괄 발급 ── (헤더: 초청인,대상명,이메일,연락처,소속,언어,매수,할인율,메모)
if ($mode === 'csv') {
    check_admin_token();
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
            'discount'=>isset($row[7])?$row[7]:100, 'memo'=>isset($row[8])?$row[8]:'', 'skip_dup'=>1,
        ));
        if ($r==='ins') $ins++; elseif ($r==='skip') $skip++; else $err++;
    }
    fclose($fp);
    alert('CSV 발급 완료 — 신규 '.$ins.'건 / 중복skip '.$skip.'건 / 오류 '.$err.'건', INV_LIST);
    exit;
}

// ── 스피커 로스터 자동 sync ── (cb_unreal_2026_speaker_apply, email 보유행. 키노트 마커 없음→전건, 키노트는 개별 비활성)
if ($mode === 'sync') {
    check_admin_token();
    $ins=0; $skip=0;
    $rs = sql_query("SELECT id, speaker_name, speaker_email, speaker_ph, speaker_cp FROM cb_unreal_2026_speaker_apply WHERE speaker_email <> '' ORDER BY id");
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

// ── 활성 토글 ──
if ($mode2 === 'toggle') {
    $no = (int)$_GET['no'];
    $r = sql_fetch("SELECT sc_active FROM cb_unreal_2026_speaker_code WHERE sc_no=".$no);
    if ($r) { $nv = ($r['sc_active']==='Y') ? 'N' : 'Y'; sql_query("UPDATE cb_unreal_2026_speaker_code SET sc_active='".$nv."' WHERE sc_no=".$no); }
    goto_url(INV_LIST);
}

// ── 삭제(미사용 코드만) ──
if ($mode2 === 'del') {
    $no = (int)$_GET['no'];
    $r = sql_fetch("SELECT sc_used FROM cb_unreal_2026_speaker_code WHERE sc_no=".$no);
    if (!$r) alert('코드를 찾을 수 없습니다.', INV_LIST);
    if ((int)$r['sc_used'] > 0) alert('이미 등록에 사용된 코드는 삭제할 수 없습니다. 비활성으로 전환하세요.', INV_LIST);
    sql_query("DELETE FROM cb_unreal_2026_speaker_code WHERE sc_no=".$no);
    alert('삭제되었습니다.', INV_LIST);
    exit;
}

// ── 내보내기(CSV) ──
if ($mode2 === 'export') {
    $rs = sql_query("SELECT * FROM cb_unreal_2026_speaker_code ORDER BY sc_no DESC");
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=ufs2026_invitation_codes.csv');
    echo "\xEF\xBB\xBF"; // BOM(엑셀 한글)
    $out = fopen('php://output', 'w');
    fputcsv($out, array('코드','링크','초청인','대상명','이메일','연락처','소속','언어','할인율','매수','사용','활성','발송시각','메모','발급일'));
    if ($rs) while ($r = sql_fetch_array($rs)) {
        $link = INV_PUBLIC.'?code='.$r['sc_code'].'&lang='.$r['sc_lang'];
        fputcsv($out, array($r['sc_code'],$link,$r['sc_inviter'],$r['sc_name'],$r['sc_email'],$r['sc_phone'],$r['sc_company'],
            $r['sc_lang'],$r['sc_discount'],$r['sc_quota'],$r['sc_used'],$r['sc_active'],$r['sc_sent_at'],$r['sc_memo'],$r['sc_reg_datetime']));
    }
    fclose($out);
    exit;
}

goto_url(INV_LIST);
