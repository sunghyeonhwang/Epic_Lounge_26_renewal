<?php
/* Unreal Fest Seoul 2026 — 관리자: 단체 등록 쿠폰 관리 (adm/2026_coupon.php)
 * cb_unreal_2026_coupon. 정률(%) 할인 쿠폰 발급/목록/사용중지/삭제. 단체 등록(ticket-group)에서 사용.
 * PHP 7.0 호환.
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

// 쿠폰 메일 발송 모듈(공개 repo 재사용) + Resend
@include_once(__DIR__ . '/../unrealfest2026/_coupon_mail.php');
@include_once(__DIR__ . '/../unrealfest2026/_resend.php');

/* 쿠폰 1건 메일 발송 + 발송상태 기록. 반환 array(ok,msg,to). */
function cp_send_mail($cp_no) {
    if (!function_exists('ufs_coupon_mail') || !function_exists('ufs_resend_send')) return array('ok'=>false,'msg'=>'메일 모듈 미로드');
    $r = sql_fetch("SELECT * FROM cb_unreal_2026_coupon WHERE cp_no=".(int)$cp_no);
    if (!$r) return array('ok'=>false,'msg'=>'쿠폰을 찾을 수 없습니다.');
    $to = trim($r['cp_recipient_email']);
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) return array('ok'=>false,'msg'=>'수신자 이메일이 없거나 형식이 올바르지 않습니다.');
    $m = ufs_coupon_mail($r, (isset($r['cp_lang']) && $r['cp_lang']==='en') ? 'en' : 'ko');
    $res = ufs_resend_send($to, $m['subject'], $m['html'], '', $m['text']);
    $ok = !empty($res['ok']);
    sql_query("UPDATE cb_unreal_2026_coupon SET cp_sent_at=now(), cp_status='".($ok?'sent':'fail')."' WHERE cp_no=".(int)$cp_no);
    return array('ok'=>$ok, 'to'=>$to, 'msg'=>($ok ? ('발송 성공 ('.$to.')') : ('발송 실패: '.(isset($res['error'])?$res['error']:'오류'))));
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cp_code'])) {
    $code = strtoupper(trim($_POST['cp_code']));
    $pct  = (int)$_POST['cp_percent']; if ($pct < 0) $pct = 0; if ($pct > 100) $pct = 100;
    $exp  = trim($_POST['cp_expire']);
    $max  = (int)$_POST['cp_max']; if ($max < 0) $max = 0;
    $memo = trim($_POST['cp_memo']);
    $rname  = isset($_POST['cp_recipient_name']) ? trim($_POST['cp_recipient_name']) : '';
    $remail = isset($_POST['cp_recipient_email']) ? trim($_POST['cp_recipient_email']) : '';
    $rlang  = (isset($_POST['cp_lang']) && $_POST['cp_lang']==='en') ? 'en' : 'ko';
    $qty    = isset($_POST['cp_qty']) ? (int)$_POST['cp_qty'] : 1; if ($qty < 1) $qty = 1; if ($qty > 200) $qty = 200;
    $expSql = ($exp !== '') ? "'".sql_real_escape_string($exp)."'" : "NULL";
    if ($pct <= 0) {
        $msg = '1 이상의 할인율을 입력해 주세요.';
    } else if ($qty > 1) {
        // ── 다수 발급: 각각 난수 코드 자동생성 (수신자/즉시발송 미적용 — 특정 수신자 발송은 CSV 사용) ──
        $made = 0; $fail = 0;
        for ($i = 0; $i < $qty; $i++) {
            $c = cp_gen_code();
            $r = sql_query("INSERT INTO cb_unreal_2026_coupon (cp_code,cp_percent,cp_expire,cp_max,cp_memo,cp_recipient_name,cp_recipient_email,cp_lang,cp_reg)
                VALUES ('".$c."', $pct, $expSql, $max, '".sql_real_escape_string($memo)."', '', '', '".$rlang."', now())");
            if ($r) $made++; else $fail++;
        }
        $msg = "쿠폰 {$made}개를 일괄 발급했습니다 ({$pct}%".($max>0?" · 한도 {$max}":"").").".($fail?" (실패 {$fail})":"")." · 특정 수신자 지정 발송은 [CSV 일괄 발급]을 이용하세요.";
    } else {
        // ── 단일 발급: 코드 빈칸이면 자동생성 + 수신자/즉시발송 ──
        if ($code === '') $code = cp_gen_code();
        $r = sql_query("INSERT INTO cb_unreal_2026_coupon (cp_code,cp_percent,cp_expire,cp_max,cp_memo,cp_recipient_name,cp_recipient_email,cp_lang,cp_reg)
            VALUES ('".sql_real_escape_string($code)."', $pct, $expSql, $max, '".sql_real_escape_string($memo)."', '".sql_real_escape_string($rname)."', '".sql_real_escape_string($remail)."', '".$rlang."', now())");
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

// ── CSV 일괄 발급 (+ 옵션: 즉시 메일 발송) ──
// 헤더/열: 코드(선택·빈칸이면 자동생성), 수신자명, 수신자이메일, 할인율, 만료일(YYYY-MM-DD·선택), 한도(선택·기본1), 메모(선택)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csv_mode'])) {
    if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== 0) {
        $msg = 'CSV 파일을 선택해 주세요.';
    } else {
        $fp = fopen($_FILES['csv']['tmp_name'], 'r');
        if (!$fp) { $msg = 'CSV 파일을 읽을 수 없습니다.'; }
        else {
            $send_now = isset($_POST['csv_send']);
            $ins=0; $skip=0; $err=0; $sent=0; $sfail=0; $rowi=0;
            while (($row = fgetcsv($fp)) !== false) {
                $rowi++;
                $joined = implode(',', $row);
                if (trim($joined) === '') continue;
                // 헤더행 스킵(첫 줄에 '이메일' 또는 '할인' 포함)
                if ($rowi === 1 && (mb_strpos($joined,'이메일')!==false || mb_strpos($joined,'할인')!==false || mb_strpos($joined,'코드')!==false)) continue;
                $code = strtoupper(trim(isset($row[0]) ? $row[0] : ''));
                $rn   = trim(isset($row[1]) ? $row[1] : '');
                $re   = trim(isset($row[2]) ? $row[2] : '');
                $pct  = (int)(isset($row[3]) ? $row[3] : 0);
                $exp  = trim(isset($row[4]) ? $row[4] : '');
                $max  = isset($row[5]) && trim($row[5])!=='' ? (int)$row[5] : 1;
                $memo = trim(isset($row[6]) ? $row[6] : '');
                $clang = (isset($row[7]) && strtolower(trim($row[7]))==='en') ? 'en' : 'ko';
                if ($re === '' || !filter_var($re, FILTER_VALIDATE_EMAIL) || $pct < 1) { $err++; continue; }
                if ($pct > 100) $pct = 100; if ($max < 0) $max = 0;
                if ($code === '') $code = cp_gen_code();
                $expSql = ($exp !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $exp)) ? "'".sql_real_escape_string($exp)."'" : "NULL";
                $r = sql_query("INSERT INTO cb_unreal_2026_coupon (cp_code,cp_percent,cp_expire,cp_max,cp_memo,cp_recipient_name,cp_recipient_email,cp_lang,cp_reg)
                    VALUES ('".sql_real_escape_string($code)."', $pct, $expSql, $max, '".sql_real_escape_string($memo)."', '".sql_real_escape_string($rn)."', '".sql_real_escape_string($re)."', '".$clang."', now())");
                if (!$r) { $skip++; continue; }   // 중복 코드
                $ins++;
                if ($send_now) {
                    $nid = sql_fetch("SELECT LAST_INSERT_ID() id"); $nid = $nid ? (int)$nid['id'] : 0;
                    if ($nid) { $sm = cp_send_mail($nid); if (!empty($sm['ok'])) $sent++; else $sfail++; }
                }
            }
            fclose($fp);
            $msg = "CSV 발급 완료 — 신규 {$ins}건 · 중복skip {$skip}건 · 오류 {$err}건".($send_now ? " · 메일 성공 {$sent}건 · 실패 {$sfail}건" : "");
        }
    }
}
if (isset($_GET['toggle'])) { $no=(int)$_GET['toggle']; sql_query("UPDATE cb_unreal_2026_coupon SET cp_active = IF(cp_active='Y','N','Y') WHERE cp_no=$no"); $msg='상태를 변경했습니다.'; }
if (isset($_GET['del']))    { $no=(int)$_GET['del']; sql_query("DELETE FROM cb_unreal_2026_coupon WHERE cp_no=$no"); $msg='삭제했습니다.'; }

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
    fputcsv($out, array('코드','할인율','만료일','한도','사용','상태','메모','수신자명','수신자이메일','언어','발송상태','발송시각','등록링크','발급일'));
    $rs = sql_query("SELECT * FROM cb_unreal_2026_coupon ORDER BY cp_no DESC");
    if ($rs) while ($c = $rs->fetch_assoc()) {
        $link = 'https://epiclounge.co.kr/unrealfest2026/ticket-all.php?coupon='.$c['cp_code'];
        $sst = isset($c['cp_status']) ? $c['cp_status'] : '';
        $sstl = ($sst==='sent'?'성공':($sst==='fail'?'실패':''));
        fputcsv($out, array_map($csv_safe, array(
            $c['cp_code'], (int)$c['cp_percent'].'%',
            ($c['cp_expire'] && $c['cp_expire']!=='0000-00-00' ? $c['cp_expire'] : ''),
            ((int)$c['cp_max']>0 ? (int)$c['cp_max'] : '무제한'), (int)$c['cp_used'],
            ($c['cp_active']==='Y'?'사용':'중지'), $c['cp_memo'],
            (isset($c['cp_recipient_name'])?$c['cp_recipient_name']:''),
            (isset($c['cp_recipient_email'])?$c['cp_recipient_email']:''),
            (isset($c['cp_lang'])?$c['cp_lang']:'ko'), $sstl,
            (isset($c['cp_sent_at'])?$c['cp_sent_at']:''), $link, $c['cp_reg']
        )));
    }
    fclose($out);
    exit;
}

include_once('./admin.head.php');
?>
<style>
.cp-wrap{max-width:900px;margin:16px 0}
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
.cp-a{color:#0a7;text-decoration:underline;cursor:pointer}.cp-d{color:#c33;text-decoration:underline;cursor:pointer}
</style>
<div class="cp-wrap">
  <?php if ($msg): ?><div class="cp-msg"><?= cp_e($msg) ?></div><?php endif; ?>

  <?php $usageCode = isset($_GET['usage']) ? strtoupper(trim($_GET['usage'])) : '';
  if ($usageCode !== ''): $uce = sql_real_escape_string($usageCode);
    $hasGrpU = ($t=@sql_query("SHOW TABLES LIKE 'cb_unreal_2026_group'")) && $t->num_rows;
    $hasIndCol = ($c=@sql_query("SHOW COLUMNS FROM cb_unreal_2026_event2_apply LIKE 'apply_coupon_code'")) && $c->num_rows;
    $inds = $hasIndCol ? sql_query("SELECT * FROM cb_unreal_2026_event2_apply WHERE apply_coupon_code='$uce' AND (apply_group_code IS NULL OR apply_group_code='') ORDER BY apply_no DESC") : null;
    $indN = $inds ? $inds->num_rows : 0;
    $grps = $hasGrpU ? sql_query("SELECT * FROM cb_unreal_2026_group WHERE coupon_code='$uce' ORDER BY grp_no DESC") : null;
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
      <tr><td><?= $i ?></td><td><?= cp_e($a['apply_user_name']) ?></td><td><?= cp_e($a['apply_user_company']) ?></td><td><?= cp_e($a['apply_user_email']) ?></td><td><?= cp_e($a['apply_user_phone']) ?></td><td><?= cp_e($a['apply_product_name']) ?></td><td><?= (int)$a['apply_coupon_pct'] ?>%</td><td style="text-align:right">₩<?= number_format((int)$a['apply_product_price']) ?></td><td><?= cp_e($stt) ?></td><td><?= cp_e(substr($a['apply_reg_datetime'],0,16)) ?></td></tr>
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
      <tr><td><?= cp_e($g['grp_code']) ?></td><td><?= cp_e($g['rep_company']) ?></td><td><?= cp_e($g['rep_name']) ?></td><td><?= (int)$g['headcount'] ?>명</td><td style="text-align:right">₩<?= number_format((int)$g['total_amount']) ?></td><td><?= (int)$g['discount_pct'] ?>%</td><td><?= cp_e($gstt) ?></td><td><?= cp_e(substr($g['paid_at'],0,16)) ?></td><td><a href="2026_group_list.php?grp=<?= (int)$g['grp_no'] ?>" style="font-size:11px">상세→</a></td></tr>
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
      <div><label>수신자명(선택)</label><input type="text" name="cp_recipient_name" placeholder="홍길동" style="width:120px"></div>
      <div><label>수신자 이메일(선택)</label><input type="email" name="cp_recipient_email" placeholder="user@example.com" style="width:180px"></div>
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
    var csv='﻿코드(선택),수신자명,수신자이메일,할인율,만료일,한도,메모,언어(ko/en)\n'
          + ',홍길동,hong@example.com,30,2026-08-15,1,VIP 초청,ko\n'
          + ',John,john@example.com,50,,1,partner,en\n';
    var a=document.createElement('a'); a.href='data:text/csv;charset=utf-8,'+encodeURIComponent(csv);
    a.download='쿠폰_일괄발급_양식.csv'; document.body.appendChild(a); a.click(); document.body.removeChild(a);
  }
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
    <form method="post" enctype="multipart/form-data" class="cp-form" onsubmit="return confirm('CSV의 각 행으로 쿠폰을 일괄 발급합니다.' + (document.getElementById('csv_send').checked ? '\n체크된 [발급 후 즉시 메일 발송]에 따라 각 수신자에게 실제 메일이 발송됩니다.' : '') + '\n계속할까요?');">
      <input type="hidden" name="csv_mode" value="1">
      <div><label>CSV 파일</label><input type="file" name="csv" accept=".csv" required></div>
      <div style="align-self:flex-end"><label style="font-weight:400;font-size:12px;cursor:pointer"><input type="checkbox" id="csv_send" name="csv_send" value="1"> 발급 후 즉시 메일 발송</label></div>
      <button type="submit" class="cp-btn" style="background:#1a7f37">CSV 일괄 발급</button>
      <button type="button" onclick="dlCouponTemplate()" style="background:#e5e7eb;color:#111;border:0;padding:9px 16px;font-weight:700;border-radius:4px;cursor:pointer">양식 다운로드</button>
    </form>
    <p style="color:#888;font-size:12px;margin:8px 0 0">열: <b>코드(선택·빈칸이면 자동생성)</b>, 수신자명, 수신자이메일, 할인율(1~100), 만료일(YYYY-MM-DD·선택), 한도(선택·기본1), 메모(선택), <b>언어(ko/en·선택·기본ko)</b>. 첫 줄이 헤더면 자동 스킵. 이메일 누락/할인율 없음=오류 스킵, 코드 중복=skip.</p>
  </div>

  <div class="cp-card">
    <h2>발급 쿠폰 목록 <a href="?export=1" style="font-size:12px;font-weight:700;background:#1a7f37;color:#fff;padding:5px 12px;border-radius:4px;text-decoration:none;margin-left:8px">⬇ CSV 다운로드</a></h2>
    <table class="cp-tbl">
      <thead><tr><th>코드</th><th>할인율</th><th>만료일</th><th>사용/한도</th><th>사용 내역</th><th>상태</th><th>메모</th><th>수신/발송</th><th>관리</th></tr></thead>
      <tbody>
      <?php
      $grpChk = @sql_query("SHOW TABLES LIKE 'cb_unreal_2026_group'"); $hasGrp = ($grpChk && $grpChk->num_rows);
      $res = sql_query("SELECT * FROM cb_unreal_2026_coupon ORDER BY cp_no DESC");
      if ($res && $res->num_rows) { while ($r = $res->fetch_assoc()):
        $off = ($r['cp_active'] !== 'Y');
        $ce2 = sql_real_escape_string($r['cp_code']);
        $usedList = array();
        // 단체 쿠폰 사용 (cb_unreal_2026_group.coupon_code)
        if ($hasGrp) {
          $gq = sql_query("SELECT rep_company, pay_status FROM cb_unreal_2026_group WHERE coupon_code='$ce2' ORDER BY grp_no DESC");
          if ($gq) { while ($gr = $gq->fetch_assoc()) {
            $st = $gr['pay_status']; $mark = ($st==='paid') ? '' : ' ('.($st==='cancel'?'취소':'대기').')';
            $usedList[] = '<span style="color:#2563eb">[단체]</span> '.cp_e($gr['rep_company']).$mark;
          } }
        }
        // 개인 쿠폰 사용 (cb_unreal_2026_event2_apply.apply_coupon_code)
        $colChk = @sql_query("SHOW COLUMNS FROM cb_unreal_2026_event2_apply LIKE 'apply_coupon_code'");
        if ($colChk && $colChk->num_rows) {
          $iq = sql_query("SELECT apply_user_name, apply_user_company, apply_pay_status, pay_complete FROM cb_unreal_2026_event2_apply WHERE apply_coupon_code='$ce2' AND (apply_group_code IS NULL OR apply_group_code='') ORDER BY apply_no DESC");
          if ($iq) { while ($ir = $iq->fetch_assoc()) {
            $ps = (int)$ir['apply_pay_status'];
            $mark = ($ps===0) ? ' (취소)' : (($ps===1) ? ' (대기)' : '');
            $nm = $ir['apply_user_name']; if (trim((string)$ir['apply_user_company']) !== '') $nm .= ' / '.$ir['apply_user_company'];
            $usedList[] = '<span style="color:#059669">[개인]</span> '.cp_e($nm).$mark;
          } }
        }
        $usedGroups = $usedList ? implode('<br>', $usedList) : '-'; ?>
        <tr class="<?= $off?'cp-off':'' ?>">
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
            <?php $rem = trim($r['cp_recipient_email']); if ($rem !== ''): $cst = isset($r['cp_status'])?$r['cp_status']:''; ?>
              <?= cp_e($r['cp_recipient_name']!==''?$r['cp_recipient_name']:'(이름없음)') ?><?= (isset($r['cp_lang'])&&$r['cp_lang']==='en')?' <span style="background:#2563eb;color:#fff;font-size:10px;padding:0 4px;border-radius:3px">EN</span>':'' ?><br>
              <span style="color:#888"><?= cp_e($rem) ?></span><br>
              <?php if ($cst==='sent'): ?><span style="color:#1a9e54;font-weight:700">✔ 발송</span> <span style="color:#aaa"><?= cp_e(substr($r['cp_sent_at'],5,11)) ?></span>
              <?php elseif ($cst==='fail'): ?><span style="color:#c0392b;font-weight:700">✖ 실패</span>
              <?php else: ?><span style="color:#999">미발송</span><?php endif; ?><br>
              <a href="?send_mail=<?= (int)$r['cp_no'] ?>" onclick="return confirm('<?= cp_e($rem) ?> 로 쿠폰 등록 안내 메일을 발송할까요?')" style="display:inline-block;margin-top:3px;background:#1a7f37;color:#fff;padding:2px 8px;border-radius:3px;text-decoration:none"><?= $cst==='sent'?'재발송':'메일 발송' ?></a>
            <?php else: ?><span style="color:#ccc">수신자 없음</span><?php endif; ?>
          </td>
          <td>
            <a class="cp-a" href="?toggle=<?= (int)$r['cp_no'] ?>"><?= $off?'재개':'중지' ?></a> ·
            <a class="cp-d" href="?del=<?= (int)$r['cp_no'] ?>" onclick="return confirm('삭제하시겠습니까?')">삭제</a>
          </td>
        </tr>
      <?php endwhile; } else { ?>
        <tr><td colspan="9" style="color:#999;padding:18px">발급된 쿠폰이 없습니다.</td></tr>
      <?php } ?>
      </tbody>
    </table>
    <p style="color:#888;font-size:12px;margin-top:8px;line-height:1.7">쿠폰은 <b>결제 완료 시</b> 사용 횟수가 1 증가하며, 한도를 두면 그 횟수까지만 사용됩니다.<br>
      · <b>단체 등록</b>: <a href="2026_group_config.php">단체 할인 설정</a>을 <b>쿠폰 모드(100)</b>로 두면, 각 회사가 자기 코드로 서로 다른 할인율을 받습니다. (회사 1곳 = 한도 1 권장)<br>
      · <b>개인 등록</b>: 참가자가 등록 시 쿠폰 코드를 입력하면 해당 할인율이 적용됩니다. <span style="color:#c0392b">(정상가 전환 시 적용 예정)</span><br>
      '사용 내역'은 <b>[단체]</b>·<b>[개인]</b> 사용을 함께 표시하며, (대기)는 무통장 입금 전, (취소)는 취소된 건입니다.</p>
  </div>
</div>
<?php include_once('./admin.tail.php'); ?>
