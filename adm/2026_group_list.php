<?php
/* Unreal Fest Seoul 2026 — 관리자: 단체 등록 현황 (adm/2026_group_list.php)
 * cb_unreal_2026_group / _group_member 목록·상세. PHP 7.0 호환.
 */
$sub_menu = '700367';
include_once('./_common.php');
if (!function_exists('is_admin') || !is_admin($member['mb_id'])) {
    alert('관리자 로그인이 필요합니다.', G5_ADMIN_URL);
}
$g5['title'] = '단체 등록 현황';
function gl_e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$PRODNAME = array('NORMAL_ALL'=>'양일권','NORMAL_20'=>'1일권(Day1)','NORMAL_21'=>'1일권(Day2)');
$PAYNAME  = array('card'=>'신용카드','bank'=>'무통장');
$STNAME   = array('pending'=>'결제대기','paid'=>'결제완료','cancel'=>'취소');

// 참석일 변경 허용 여부 — "등록 단계 == 현재 판매 단계". 얼리버드 신청 단체는 얼리버드 기간에만 / 일반판매 단체는 얼리버드 종료 후에만.
// (그룹은 얼리버드 '가격'이 없어 등록시각 reg 를 얼리버드 마감시각과 비교해 단계 판별.)
function adm_group_date_change_ok($g) {
    @include_once(__DIR__ . '/../unrealfest2026/_pricing.php');
    if (!function_exists('ufs_is_earlybird') || !function_exists('ufs_earlybird_end_ts')) return true; // 판별 불가 시 관리자 재량 허용
    $now_eb    = ufs_is_earlybird();
    $reg_ts    = (isset($g['reg']) && $g['reg'] !== '') ? strtotime((string)$g['reg']) : false;
    $reg_is_eb = ($reg_ts !== false && $reg_ts <= ufs_earlybird_end_ts());
    return ($now_eb && $reg_is_eb) || (!$now_eb && !$reg_is_eb);
}

$has = @sql_query("SHOW TABLES LIKE 'cb_unreal_2026_group'");
$exists = ($has && $has->num_rows);

$detail = isset($_GET['grp']) ? (int)$_GET['grp'] : 0;
@include_once(__DIR__ . '/../unrealfest2026/_sms.php'); // ufs_send_text_sms (등록자 문자)
$gl_msg = '';

// 무통장 입금확인 → 결제완료 + 쿠폰 사용증가 + 등록자(②) 문자
if ($exists && isset($_GET['confirm_pay'])) {
    $no = (int)$_GET['confirm_pay'];
    $g = sql_fetch("SELECT * FROM cb_unreal_2026_group WHERE grp_no=".$no);
    if ($g && $g['pay_status'] !== 'paid') {
        sql_query("UPDATE cb_unreal_2026_group SET pay_status='paid', paid_at=now() WHERE grp_no=".$no);
        if ($g['coupon_code'] !== '' && (int)$g['discount_pct'] > 0) @sql_query("UPDATE cb_unreal_2026_coupon SET cp_used=cp_used+1 WHERE cp_code='".sql_real_escape_string($g['coupon_code'])."'"); // 실제 할인건만 증가
        @include_once(__DIR__ . '/../unrealfest2026/_group_apply.php'); // 등록현황(apply) 반영 + QR
        if (function_exists('ufs_group_reflect')) @ufs_group_reflect($no);
        $sent = 0;
        if (function_exists('ufs_group_send_member')) {
            $ms = sql_query("SELECT * FROM cb_unreal_2026_group_member WHERE grp_no=".$no);
            while ($m = $ms->fetch_assoc()) {
                if (trim($m['phone']) === '') continue;
                if ((int)$m['apply_no'] > 0) {
                    @ufs_group_send_member($m, $g['rep_company']); // 개인 QR 첨부 MMS (0보정+상태기록)
                } else if (function_exists('ufs_send_text_sms')) {
                    $msg = "[언리얼 페스트 서울 2026] 단체 등록이 완료되었습니다.\n".$m['name']."님, 8월 20일(목)~21일(금) 행사 참가 등록이 확정되었습니다.\n단체 대표: ".$g['rep_company']."\n문의: 02-326-3701";
                    @ufs_send_text_sms($m['name'], $m['phone'], '언리얼 페스트 서울 2026', $msg, 'group-paid'); // QR 미생성 예외 → 텍스트 대체
                }
                $sent++;
            }
        }
        $gl_msg = '입금 확인 처리 완료 — 등록자 '.$sent.'명에게 QR 안내 발송.';
    } else { $gl_msg = '이미 처리되었거나 없는 건입니다.'; }
}

// ── 발송상태 컬럼 보장(MariaDB) ──
if ($exists) {
    @sql_query("ALTER TABLE cb_unreal_2026_group_member ADD COLUMN IF NOT EXISTS gm_sms_status VARCHAR(10) NOT NULL DEFAULT ''");
    @sql_query("ALTER TABLE cb_unreal_2026_group_member ADD COLUMN IF NOT EXISTS gm_sms_at DATETIME DEFAULT NULL");
    @sql_query("ALTER TABLE cb_unreal_2026_group_member ADD COLUMN IF NOT EXISTS gm_sms_phone VARCHAR(20) NOT NULL DEFAULT ''");
    @sql_query("ALTER TABLE cb_unreal_2026_group_member ADD COLUMN IF NOT EXISTS gm_sms_msg VARCHAR(200) NOT NULL DEFAULT ''");
}

// ── 단체 QR 문자 전체 발송/재발송 (수신번호 0 보정 + 성공/실패 기록) ──
if ($exists && isset($_GET['send_all_sms']) && function_exists('ufs_group_send_member')) {
    $no = (int)$_GET['send_all_sms'];
    $g = sql_fetch("SELECT * FROM cb_unreal_2026_group WHERE grp_no=".$no);
    if ($g) {
        $ok=0; $fail=0; $skip=0;
        $ms = sql_query("SELECT * FROM cb_unreal_2026_group_member WHERE grp_no=".$no." ORDER BY gm_no");
        while ($m = $ms->fetch_assoc()) {
            if (isset($m['gm_status']) && $m['gm_status']==='C') { $skip++; continue; }  // 취소 멤버 제외
            $r = ufs_group_send_member($m, $g['rep_company']);
            if ($r['ok']===true) $ok++; else if ($r['ok']===null) $skip++; else $fail++;
        }
        $gl_msg = "QR 문자 발송 완료 — 성공 {$ok}건 · 실패 {$fail}건".($skip?" · 제외/미발송 {$skip}건":"");
    } else { $gl_msg = '발송 대상 단체를 찾을 수 없습니다.'; }
}
// ── 단체 QR 문자 개별 발송/재발송 ──
if ($exists && isset($_GET['send_member']) && function_exists('ufs_group_send_member')) {
    $ano = (int)$_GET['send_member'];
    $gno = (int)(isset($_GET['grp']) ? $_GET['grp'] : 0);
    $g = $gno ? sql_fetch("SELECT * FROM cb_unreal_2026_group WHERE grp_no=".$gno) : null;
    $m = $g ? sql_fetch("SELECT * FROM cb_unreal_2026_group_member WHERE grp_no=".$gno." AND apply_no=".$ano." LIMIT 1") : null;
    if ($m) {
        $r = ufs_group_send_member($m, $g['rep_company']);
        $gl_msg = $m['name'].'님 QR 문자: '.($r['ok']===true?'발송 성공':($r['ok']===null?'테스트모드(미발송)':$r['msg'])).' ('.$r['phone'].')';
    } else { $gl_msg = '대상 멤버를 찾을 수 없습니다.'; }
}

// 단체 수동취소 → 좌석 홀드 해제(정원 반환) + 상태 취소 (미입금 노쇼 처리용)
if ($exists && isset($_GET['cancel_grp'])) {
    $no = (int)$_GET['cancel_grp'];
    $g = sql_fetch("SELECT * FROM cb_unreal_2026_group WHERE grp_no=".$no);
    if ($g && $g['pay_status'] !== 'paid' && $g['pay_status'] !== 'cancel') {
        @include_once(__DIR__ . '/../unrealfest2026/_group_apply.php');
        $rel = function_exists('ufs_group_release') ? (int)ufs_group_release($no) : 0;
        sql_query("UPDATE cb_unreal_2026_group SET pay_status='cancel' WHERE grp_no=".$no);
        $gl_msg = '단체 등록을 취소했습니다 — 홀드 좌석 '.$rel.'석을 정원으로 반환했습니다.';
    } else { $gl_msg = '결제완료/이미 취소된 건은 이 버튼으로 취소할 수 없습니다.'; }
}

// 단체 카드 결제완료 → 전액 환불(INICIS 실환불) + 등록 취소(좌석 반환)
if ($exists && isset($_GET['refund_grp'])) {
    $no = (int)$_GET['refund_grp'];
    $g = sql_fetch("SELECT * FROM cb_unreal_2026_group WHERE grp_no=".$no);
    if ($g && $g['pay_status']==='paid' && $g['paymethod']==='card') {
        require_once(__DIR__ . '/../unrealfest2026/_refund.php');
        $rf = function_exists('ufs_inicis_refund') ? ufs_inicis_refund($g['pay_tid'], 'Card', '단체 등록 취소(관리자)', $no) : array('ok'=>false,'msg'=>'환불 모듈 없음');
        $ok = (!empty($rf['skipped']) || !empty($rf['ok']) || !empty($rf['already']));
        if ($ok) {
            @include_once(__DIR__ . '/../unrealfest2026/_group_apply.php');
            $rel = function_exists('ufs_group_release') ? (int)ufs_group_release($no) : 0;
            sql_query("UPDATE cb_unreal_2026_group SET pay_status='cancel' WHERE grp_no=".$no);
            $gl_msg = '전액 환불·등록 취소 완료'.(!empty($rf['skipped'])?' (테스트모드: 실환불 미수행)':'').(!empty($rf['already'])?' (이미 환불된 거래)':'').' — 좌석 '.$rel.'석 반환.';
        } else {
            $gl_msg = '환불 실패 — '.(isset($rf['msg'])?gl_e($rf['msg']):'').' / INICIS 상점관리자에서 확인하세요. (등록은 취소하지 않았습니다)';
        }
    } else { $gl_msg = '카드 결제완료 단체건만 환불할 수 있습니다.'; }
}

// 단체 구성원 1명 개별 취소 — 카드=INICIS 부분환불, 무통장=좌석반환(계좌환불 수동) + 그룹 집계 동기화. apply_no 기준·그룹 소속 검증.
if ($exists && isset($_GET['cancel_member'])) {
    $ano = (int)$_GET['cancel_member'];
    $gno = (int)(isset($_GET['grp']) ? $_GET['grp'] : 0);
    $g   = $gno ? sql_fetch("SELECT * FROM cb_unreal_2026_group WHERE grp_no=".$gno) : null;
    $chk = $g ? sql_fetch("SELECT gm_no FROM cb_unreal_2026_group_member WHERE grp_no=".$gno." AND apply_no=".$ano." LIMIT 1") : null;
    if (!$g || !$chk) { $gl_msg = '해당 단체의 구성원이 아닙니다.'; }
    else if ($g['pay_status'] !== 'paid') { $gl_msg = '결제완료 단체의 구성원만 개별 취소할 수 있습니다. (미결제 단체는 "단체 취소"를 사용하세요)'; }
    else {
        @include_once(__DIR__ . '/../unrealfest2026/_group_apply.php');
        $mc = function_exists('ufs_group_member_cancel') ? ufs_group_member_cancel($ano, true) : array('ok'=>false, 'msg'=>'환불 모듈 없음');
        if (!empty($mc['ok'])) {
            $amt = number_format((int)(isset($mc['refund']) ? $mc['refund'] : 0));
            $gl_msg = !empty($mc['manual'])
                ? ('구성원 1명 좌석 반환 완료 — 무통장이므로 ₩'.$amt.'은 계좌로 수동 환불하세요.')
                : ('구성원 1명 취소 완료 — ₩'.$amt.' 부분환불(실환불)이 처리되었습니다.');
        } else {
            $gl_msg = '구성원 취소 실패 — '.(isset($mc['msg']) ? gl_e($mc['msg']) : '').' (등록은 취소되지 않았습니다)';
        }
    }
}

// 구성원 참석일 변경 — 1일권만(NORMAL_20 8.20 ↔ NORMAL_21 8.21). 같은 가격이라 결제·집계 무영향, 티켓+트랙만 변경. POST.
if ($exists && isset($_POST['save_member_day'])) {
    $ano = (int)(isset($_POST['edit_member']) ? $_POST['edit_member'] : 0);
    $gno = (int)(isset($_POST['grp']) ? $_POST['grp'] : 0);
    $newday   = ((isset($_POST['newday']) ? $_POST['newday'] : '') === '2') ? 2 : 1;
    $newtrack = isset($_POST['newtrack']) ? trim($_POST['newtrack']) : '';
    $g = $gno ? sql_fetch("SELECT * FROM cb_unreal_2026_group WHERE grp_no=".$gno) : null;
    $m = $g ? sql_fetch("SELECT * FROM cb_unreal_2026_group_member WHERE grp_no=".$gno." AND apply_no=".$ano." LIMIT 1") : null;
    $a = $ano ? sql_fetch("SELECT apply_pay_status FROM cb_unreal_2026_event2_apply WHERE apply_no=".$ano." LIMIT 1") : null;
    $validTr = ($newday===1) ? array('DAY1_TR1','DAY1_TR2','DAY1_TR3','DAY1_TR4') : array('DAY2_TR1','DAY2_TR2','DAY2_TR3','DAY2_TR4');
    if (!$g || !$m || !$a) { $gl_msg = '구성원을 찾을 수 없습니다.'; }
    else if ($m['ticket']!=='NORMAL_20' && $m['ticket']!=='NORMAL_21') { $gl_msg = '1일권 구성원만 참석일을 변경할 수 있습니다. (양일권은 변경 불가)'; }
    else if ((int)$a['apply_pay_status']===0) { $gl_msg = '취소된 구성원은 변경할 수 없습니다.'; }
    else if (!adm_group_date_change_ok($g)) { $gl_msg = (function_exists('ufs_is_earlybird') && ufs_is_earlybird()) ? '얼리버드 기간에는 얼리버드 신청 단체만 참석일을 변경할 수 있습니다.' : '얼리버드 종료 후에는 일반판매분 단체만 참석일을 변경할 수 있습니다.'; }
    else if (!in_array($newtrack, $validTr, true)) { $gl_msg = '선택하신 참석일의 트랙을 골라주세요.'; }
    else {
        $newticket = ($newday===1) ? 'NORMAL_20' : 'NORMAL_21';
        $newname   = ($newday===1) ? '언리얼 페스트 서울 2026 1일권(8월 20일)' : '언리얼 페스트 서울 2026 1일권(8월 21일)';
        $d1 = ($newday===1) ? $newtrack : '';
        $d2 = ($newday===2) ? $newtrack : '';
        sql_query("UPDATE cb_unreal_2026_group_member SET ticket='".$newticket."', day1='".sql_real_escape_string($d1)."', day2='".sql_real_escape_string($d2)."' WHERE gm_no=".(int)$m['gm_no']);
        sql_query("UPDATE cb_unreal_2026_event2_apply SET apply_product_code='".$newticket."', apply_product_name='".sql_real_escape_string($newname)."', apply_track='".sql_real_escape_string($newtrack)."' WHERE apply_no=".$ano." AND apply_pay_status<>0");
        $gl_msg = gl_e($m['name']).'님 참석일을 '.($newday===1?'8월 20일(Day1)':'8월 21일(Day2)').' · '.gl_e($newtrack).'(으)로 변경했습니다. (1일권 · 가격 동일)';
    }
}

// 취소된 단체 삭제 (완전 삭제 — 'cancel' 상태만 허용). 취소상태(status=0) apply 레코드도 정리(활성건 미영향).
if ($exists && isset($_GET['delete_grp'])) {
    $no = (int)$_GET['delete_grp'];
    $g = sql_fetch("SELECT * FROM cb_unreal_2026_group WHERE grp_no=".$no);
    if ($g && $g['pay_status']==='cancel') {
        $gc = sql_real_escape_string($g['grp_code']);
        @sql_query("DELETE FROM cb_unreal_2026_event2_apply WHERE apply_group_code='".$gc."' AND apply_pay_status=0");
        sql_query("DELETE FROM cb_unreal_2026_group_member WHERE grp_no=".$no);
        sql_query("DELETE FROM cb_unreal_2026_group WHERE grp_no=".$no);
        $detail = 0;                                  // 삭제됨 → 목록 화면으로
        $gl_msg = '취소된 단체 등록을 삭제했습니다.';
    } else { $gl_msg = "취소('cancel') 상태인 단체건만 삭제할 수 있습니다."; }
}

// ── CSV(엑셀) 내보내기 — HTML 출력(admin.head) 전에 실행 ──
if ($exists && isset($_GET['export'])) {
    @include_once(__DIR__ . '/../unrealfest2026/_group_apply.php');
    if (function_exists('ufs_group_apply_cols')) @ufs_group_apply_cols();   // gm_status 등 컬럼 보강
    $ex  = $_GET['export'];
    $gno = isset($_GET['grp']) ? (int)$_GET['grp'] : 0;
    $csv_safe = function($v){ $s=(string)$v; if ($s!=='' && strpos("=+-@\t\r", $s[0])!==false) return "'".$s; return $s; };
    header('Content-Type: text/csv; charset=utf-8');
    $suf = date('Ymd');
    $out = fopen('php://output', 'w');
    echo "\xEF\xBB\xBF"; // BOM (엑셀 한글 깨짐 방지)
    if ($ex === 'groups') {
        header('Content-Disposition: attachment; filename="ufs2026_groups_'.$suf.'.csv"');
        fputcsv($out, array('접수번호','대표자','대표참석','회사','사업자번호','이메일','연락처','인원','결제수단','할인율','총액','상태','접수일시','결제일'));
        $rs = sql_query("SELECT * FROM cb_unreal_2026_group ORDER BY grp_no DESC");
        if ($rs) while ($g = $rs->fetch_assoc()) {
            fputcsv($out, array_map($csv_safe, array($g['grp_code'],$g['rep_name'],($g['rep_attend']==='Y'?'참석':'결제만'),$g['rep_company'],isset($g['rep_biznum'])?$g['rep_biznum']:'',$g['rep_email'],$g['rep_phone'],$g['headcount'],(isset($PAYNAME[$g['paymethod']])?$PAYNAME[$g['paymethod']]:$g['paymethod']),(int)$g['discount_pct'].'%',(int)$g['total_amount'],(isset($STNAME[$g['pay_status']])?$STNAME[$g['pay_status']]:$g['pay_status']),$g['reg'],isset($g['paid_at'])?$g['paid_at']:'')));
        }
    } else {   // members (기본)
        header('Content-Disposition: attachment; filename="'.($gno?('ufs2026_group_'.$gno.'_members_'):'ufs2026_group_members_').$suf.'.csv"');
        fputcsv($out, array('접수번호','회사','구분','이름','이메일','연락처','부서','직무','관심분야','티켓','Day1','Day2','티셔츠','금액','결제수단','단체상태','문자상태','문자시각','접수일시'));
        $gw = $gno ? (" WHERE m.grp_no=".$gno) : "";
        $rs = sql_query("SELECT m.*, g.grp_code gcode, g.rep_company, g.paymethod, g.pay_status gstatus, g.reg greg FROM cb_unreal_2026_group_member m LEFT JOIN cb_unreal_2026_group g ON g.grp_no=m.grp_no".$gw." ORDER BY m.grp_no DESC, m.gm_no ASC");
        if ($rs) while ($m = $rs->fetch_assoc()) {
            $sst = isset($m['gm_sms_status']) ? $m['gm_sms_status'] : '';
            $sstl = ($sst==='sent'?'성공':($sst==='fail'?'실패':($sst==='test'?'테스트':'미확인')));
            $cancelled = (isset($m['gm_status']) && $m['gm_status']==='C');
            fputcsv($out, array_map($csv_safe, array(
                $m['gcode'], $m['rep_company'], (($m['role']==='rep'?'대표자':'멤버').($cancelled?'(취소)':'')),
                $m['name'],$m['email'],$m['phone'],$m['depart'],$m['grade'],$m['ex1'],
                (isset($PRODNAME[$m['ticket']])?$PRODNAME[$m['ticket']]:$m['ticket']),$m['day1'],$m['day2'],$m['tshirt'],(int)$m['price'],
                (isset($PAYNAME[$m['paymethod']])?$PAYNAME[$m['paymethod']]:$m['paymethod']),
                (isset($STNAME[$m['gstatus']])?$STNAME[$m['gstatus']]:$m['gstatus']),
                $sstl, isset($m['gm_sms_at'])?$m['gm_sms_at']:'', $m['greg']
            )));
        }
    }
    fclose($out);
    exit;
}

include_once('./admin.head.php');
if ($gl_msg !== '') echo '<div style="background:#e8fbfd;border:1px solid #00C1D5;color:#007a89;padding:10px 14px;border-radius:4px;margin:10px 0;max-width:1100px">'.gl_e($gl_msg).'</div>';
?>
<style>
.gl{max-width:1100px;margin:14px 0}
.gl table{width:100%;border-collapse:collapse;font-size:13px}
.gl th,.gl td{border:1px solid #e5e5e5;padding:7px 9px;text-align:center}
.gl thead th{background:#fafafa}
.gl a{color:#0a7;text-decoration:underline}
.gl .r{text-align:right}.gl .l{text-align:left}
.badge{display:inline-block;padding:1px 8px;border-radius:10px;font-size:11px;font-weight:700}
.b-pending{background:#fff3cd;color:#856404}.b-paid{background:#d4edda;color:#155724}.b-cancel{background:#f8d7da;color:#721c24}
</style>
<div class="gl">
<div style="margin:4px 0 12px"><a href="2026_coupon_status.php" style="display:inline-block;background:#0f172a;color:#fff;padding:9px 18px;border-radius:5px;text-decoration:none;font-weight:700;font-size:13px">📋 쿠폰 등록 현황 (구글시트 붙여넣기) →</a></div>
<?php if (!$exists): ?>
  <p style="color:#999;padding:20px">아직 단체 등록 데이터가 없습니다. (첫 등록 시 테이블이 생성됩니다.)</p>
<?php elseif ($detail): ?>
  <?php @include_once(__DIR__ . '/../unrealfest2026/_group_apply.php'); if (function_exists('ufs_group_apply_cols')) @ufs_group_apply_cols(); /* gm_status/refunded_amount 컬럼 보강 */ ?>
  <?php $g = sql_fetch("SELECT * FROM cb_unreal_2026_group WHERE grp_no=".$detail); ?>
  <?php if (!$g): ?><p>없는 접수입니다.</p><?php else: ?>
  <?php $change_ok = adm_group_date_change_ok($g); ?>
  <p><a href="2026_group_list.php">← 목록</a></p>
  <h2 style="font-size:16px;margin:8px 0"><?= gl_e($g['grp_code']) ?> · 대표자 <?= gl_e($g['rep_name']) ?> (<?= gl_e($g['rep_company']) ?>)</h2>
  <p style="font-size:13px;color:#555;margin-bottom:10px">
    연락처 <?= gl_e($g['rep_phone']) ?> · 이메일 <?= gl_e($g['rep_email']) ?> · 사업자번호 <?= gl_e(isset($g['rep_biznum'])?$g['rep_biznum']:'') ?> · 대표자참석 <?= $g['rep_attend']==='Y'?'예':'아니오(결제만)' ?><br>
    결제 <?= gl_e(isset($PAYNAME[$g['paymethod']])?$PAYNAME[$g['paymethod']]:$g['paymethod']) ?> · 할인 <?= (int)$g['discount_pct'] ?>%<?= $g['coupon_code']!==''?' (쿠폰 '.gl_e($g['coupon_code']).')':'' ?> · 총액 <b>₩<?= number_format((int)$g['total_amount']) ?></b> · 상태 <?= gl_e(isset($STNAME[$g['pay_status']])?$STNAME[$g['pay_status']]:$g['pay_status']) ?> · 접수 <?= gl_e($g['reg']) ?>
  </p>
  <?php if ($g['paymethod']==='bank' && $g['pay_status']!=='paid' && $g['pay_status']!=='cancel'): ?>
    <a href="?grp=<?= (int)$detail ?>&confirm_pay=<?= (int)$detail ?>" onclick="return confirm('입금 확인 처리하고 등록자에게 안내 문자를 발송할까요?')" style="display:inline-block;background:#00C1D5;color:#fff;padding:7px 16px;border-radius:4px;text-decoration:none;font-weight:700;margin-bottom:12px">입금 확인 + 등록자 문자 발송</a>
  <?php endif; ?>
  <?php if ($g['pay_status']!=='paid' && $g['pay_status']!=='cancel'): ?>
    <a href="?grp=<?= (int)$detail ?>&cancel_grp=<?= (int)$detail ?>" onclick="return confirm('이 단체 등록을 취소하고 홀드된 좌석을 정원으로 반환할까요? (미입금 노쇼 처리)')" style="display:inline-block;background:#e0492f;color:#fff;padding:7px 16px;border-radius:4px;text-decoration:none;font-weight:700;margin-bottom:12px;margin-left:6px">단체 취소 (좌석 해제)</a>
  <?php endif; ?>
  <?php if ($g['paymethod']==='card' && $g['pay_status']==='paid'): ?>
    <a href="?grp=<?= (int)$detail ?>&refund_grp=<?= (int)$detail ?>" onclick="return confirm('이 단체의 신용카드 결제를 전액 환불하고 등록을 취소할까요?\n⚠ 실제 환불(실거래)이 진행되며 되돌릴 수 없습니다.')" style="display:inline-block;background:#c0392b;color:#fff;padding:7px 16px;border-radius:4px;text-decoration:none;font-weight:700;margin-bottom:12px;margin-left:6px">전액 환불 + 등록 취소</a>
  <?php endif; ?>
  <?php if ($g['pay_status']==='cancel'): ?>
    <a href="?delete_grp=<?= (int)$detail ?>" onclick="return confirm('이 취소된 단체 등록을 완전히 삭제할까요?\n단체·멤버·취소된 개인등록 레코드가 삭제되며 되돌릴 수 없습니다.')" style="display:inline-block;background:#6b7280;color:#fff;padding:7px 16px;border-radius:4px;text-decoration:none;font-weight:700;margin-bottom:12px;margin-left:6px">취소건 삭제</a>
  <?php endif; ?>
  <?php if ($g['pay_status']==='paid'): ?>
    <a href="?grp=<?= (int)$detail ?>&send_all_sms=<?= (int)$detail ?>" onclick="return confirm('구성원 전원에게 QR 포함 등록확인 문자를 발송/재발송할까요?\n(수신번호 앞자리 0 자동 보정 · 실제 발송)')" style="display:inline-block;background:#2d7ff9;color:#fff;padding:7px 16px;border-radius:4px;text-decoration:none;font-weight:700;margin-bottom:12px;margin-left:6px">📨 전체 QR 문자 발송/재발송</a>
    <a href="?export=members&grp=<?= (int)$detail ?>" style="display:inline-block;background:#1a7f37;color:#fff;padding:7px 16px;border-radius:4px;text-decoration:none;font-weight:700;margin-bottom:12px;margin-left:6px">⬇ 이 단체 구성원 CSV</a>
  <?php endif; ?>
  <?php if (isset($g['tax_request']) && $g['tax_request']==='Y'): ?>
    <div style="font-size:13px;color:#444;margin-bottom:12px;padding:10px;background:#fafafa;border:1px solid #eee;border-radius:4px">
      <b>세금계산서 발행</b> · 상호 <?= gl_e($g['rep_company']) ?> · 사업자번호 <?= gl_e($g['rep_biznum']) ?> · 대표자 <?= gl_e($g['tax_ceo']) ?> · 주소 <?= gl_e($g['tax_addr']) ?> · 업태 <?= gl_e($g['tax_biztype']) ?> · 종목 <?= gl_e($g['tax_bizitem']) ?> · 수신메일 <?= gl_e($g['rep_email']) ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($_GET['edit_member'])):
    $eano = (int)$_GET['edit_member'];
    $em = sql_fetch("SELECT * FROM cb_unreal_2026_group_member WHERE grp_no=".$detail." AND apply_no=".$eano." LIMIT 1");
    $em_is1 = ($em && ($em['ticket']==='NORMAL_20' || $em['ticket']==='NORMAL_21'));
    if ($em && $em_is1 && $change_ok):
      $curday = ($em['ticket']==='NORMAL_20') ? 1 : 2;
      $curtrack = ($curday===1) ? $em['day1'] : $em['day2']; ?>
    <div style="max-width:700px;margin:0 0 14px;padding:14px 16px;background:#f0fbfc;border:1px solid #00C1D5;border-radius:6px">
      <b><?= gl_e($em['name']) ?></b> 참석일 변경 <span style="color:#888;font-size:12px">· 1일권끼리(8.20↔8.21)만 · 가격 동일(정산 없음)</span>
      <form method="post" action="2026_group_list.php?grp=<?= (int)$detail ?>" style="margin-top:10px;display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <input type="hidden" name="save_member_day" value="1">
        <input type="hidden" name="edit_member" value="<?= $eano ?>">
        <input type="hidden" name="grp" value="<?= (int)$detail ?>">
        <label>참석일 <select name="newday" id="emDay" onchange="emFillTracks()" style="padding:5px;margin-left:4px">
          <option value="1"<?= $curday===1?' selected':'' ?>>8월 20일 (Day1)</option>
          <option value="2"<?= $curday===2?' selected':'' ?>>8월 21일 (Day2)</option>
        </select></label>
        <label>트랙 <select name="newtrack" id="emTrack" style="padding:5px;margin-left:4px"></select></label>
        <button type="submit" style="background:#00C1D5;color:#fff;border:0;padding:6px 14px;border-radius:4px;font-weight:700;cursor:pointer">변경 저장</button>
        <a href="?grp=<?= (int)$detail ?>" style="color:#888">취소</a>
      </form>
      <script>
      var EM_TR={"1":{"DAY1_TR1":"게임: 프로그래밍","DAY1_TR2":"게임: 아트","DAY1_TR3":"미디어 & 엔터테인먼트","DAY1_TR4":"공통"},
                 "2":{"DAY2_TR1":"게임: 프로그래밍","DAY2_TR2":"게임: 아트","DAY2_TR3":"미디어 & 엔터테인먼트","DAY2_TR4":"제조 및 시뮬레이션"}};
      var EM_CUR={day:"<?= $curday ?>",track:<?= json_encode($curtrack, JSON_UNESCAPED_UNICODE) ?>};
      function emFillTracks(){
        var d=document.getElementById('emDay').value, sel=document.getElementById('emTrack'), tr=EM_TR[d]||{};
        while(sel.firstChild){ sel.removeChild(sel.firstChild); }
        for(var k in tr){ var o=document.createElement('option'); o.value=k; o.text=tr[k]+' ('+k+')'; if(d===EM_CUR.day && k===EM_CUR.track){ o.selected=true; } sel.appendChild(o); }
      }
      emFillTracks();
      </script>
    </div>
    <?php elseif ($em && !$em_is1): ?>
    <div style="max-width:700px;margin:0 0 14px;padding:10px 14px;background:#fff8e1;border:1px solid #f0c000;border-radius:6px;font-size:13px">양일권 구성원은 참석일을 변경할 수 없습니다.</div>
    <?php elseif ($em && !$change_ok): ?>
    <div style="max-width:700px;margin:0 0 14px;padding:10px 14px;background:#fff8e1;border:1px solid #f0c000;border-radius:6px;font-size:13px">현재 판매 단계와 맞지 않아 변경할 수 없습니다. (얼리버드 신청 단체는 얼리버드 기간에만, 일반판매분은 종료 후에만 변경 가능)</div>
    <?php endif; ?>
  <?php endif; ?>
  <?php
    // 참석일 구성 집계 — 양일권/1일권(Day1·Day2). 취소 구성원 제외.
    $cnt_all=0; $cnt_d1=0; $cnt_d2=0; $cnt_active=0; $cnt_cancel=0;
    $cs = sql_query("SELECT ticket, gm_status FROM cb_unreal_2026_group_member WHERE grp_no=".$detail);
    if ($cs) while ($c=$cs->fetch_assoc()) {
      if (isset($c['gm_status']) && $c['gm_status']==='C') { $cnt_cancel++; continue; }
      $cnt_active++;
      if ($c['ticket']==='NORMAL_ALL') $cnt_all++;
      else if ($c['ticket']==='NORMAL_20') $cnt_d1++;
      else if ($c['ticket']==='NORMAL_21') $cnt_d2++;
    }
    // 일자별 실참석 인원(양일권은 양일 모두 참석)
    $day1_att = $cnt_all + $cnt_d1;
    $day2_att = $cnt_all + $cnt_d2;
  ?>
  <div style="max-width:820px;margin:0 0 12px;padding:10px 14px;background:#f7fbfc;border:1px solid #cbe9ee;border-radius:6px;font-size:13px;color:#333">
    <b>참석 구성</b> ·
    양일권 <b><?= $cnt_all ?></b>명 ·
    1일권(8월 20일) <b><?= $cnt_d1 ?></b>명 ·
    1일권(8월 21일) <b><?= $cnt_d2 ?></b>명
    <span style="color:#888"> · 유효 <?= $cnt_active ?>명<?= $cnt_cancel?' (취소 '.$cnt_cancel.'명 제외)':'' ?></span>
    <br><span style="color:#666;font-size:12px">일자별 실참석(양일권 포함) — 8월 20일 <b><?= $day1_att ?></b>명 · 8월 21일 <b><?= $day2_att ?></b>명</span>
  </div>
  <table>
    <thead><tr><th>#</th><th>구분</th><th>이름</th><th>이메일</th><th>연락처</th><th>부서</th><th>직무</th><th>관심분야</th><th>티켓</th><th>Day1</th><th>Day2</th><th>티셔츠</th><th class="r">금액</th><th>문자(QR)</th><th>관리</th></tr></thead>
    <tbody>
    <?php $i=0; $ms=sql_query("SELECT * FROM cb_unreal_2026_group_member WHERE grp_no=".$detail." ORDER BY gm_no");
    while ($m=$ms->fetch_assoc()): $i++; $m_cancelled = (isset($m['gm_status']) && $m['gm_status']==='C'); ?>
      <tr<?= $m_cancelled?' style="opacity:.5"':'' ?>>
        <td><?= $i ?></td><td><?= $m['role']==='rep'?'대표자':'멤버' ?></td>
        <td class="l"><?= gl_e($m['name']) ?></td><td class="l"><?= gl_e($m['email']) ?></td><td><?= gl_e($m['phone']) ?></td>
        <td><?= gl_e($m['depart']) ?></td><td><?= gl_e($m['grade']) ?></td><td><?= gl_e($m['ex1']) ?></td>
        <td><?= gl_e(isset($PRODNAME[$m['ticket']])?$PRODNAME[$m['ticket']]:$m['ticket']) ?></td>
        <td><?= gl_e($m['day1']) ?></td><td><?= gl_e($m['day2']) ?></td><td><?= gl_e($m['tshirt']) ?></td>
        <td class="r">₩<?= number_format((int)$m['price']) ?></td>
        <td style="white-space:nowrap;font-size:11px;text-align:center">
        <?php
          $sst  = isset($m['gm_sms_status']) ? $m['gm_sms_status'] : '';
          $rawp = preg_replace('/[^0-9]/','',(string)$m['phone']);
          $normp = function_exists('ufs_normalize_phone') ? ufs_normalize_phone($m['phone']) : $rawp;
          $corrected = ($normp !== $rawp && $rawp !== '');
          if ($sst==='sent')      echo '<span style="color:#1a9e54;font-weight:700">✔ 성공</span>';
          else if ($sst==='fail') echo '<span style="color:#c0392b;font-weight:700">✖ 실패</span>';
          else if ($sst==='test') echo '<span style="color:#e67e22">테스트</span>';
          else                    echo '<span style="color:#999" title="이 기능 도입(2026-07-25) 이후 발송분부터 기록. 과거 발송 여부는 결제로그 확인.">미확인</span>';
          if (!empty($m['gm_sms_at']))  echo '<br><span style="color:#aaa">'.gl_e(substr($m['gm_sms_at'],5,11)).'</span>';
          if ($sst==='fail' && !empty($m['gm_sms_msg'])) { $__fm = function_exists('mb_strimwidth') ? mb_strimwidth($m['gm_sms_msg'],0,22,'…','UTF-8') : $m['gm_sms_msg']; echo '<br><span style="color:#c0392b" title="'.gl_e($m['gm_sms_msg']).'">'.gl_e($__fm).'</span>'; }
          if ($corrected) echo '<br><span style="color:#e67e22" title="발송 시 앞자리 0 보정">0보정→'.gl_e($normp).'</span>';
        ?>
        </td>
        <td style="white-space:nowrap">
        <?php if ($m_cancelled): ?>
          <span style="color:#c0392b;font-weight:700">취소됨</span>
        <?php elseif ((int)$m['apply_no']>0 && $g['pay_status']!=='cancel'):
          $mauto = ($g['paymethod']==='card');
          $mcf = $mauto
            ? ('이 구성원 1명을 취소하고 ₩'.number_format((int)$m['price']).'을 부분환불할까요?\n⚠ 실제 환불(실거래)이 진행됩니다.')
            : ('이 구성원 1명의 좌석을 반환할까요?\n무통장이므로 ₩'.number_format((int)$m['price']).' 계좌 환불은 수동으로 진행해야 합니다.');
          $is1day = ($m['ticket']==='NORMAL_20' || $m['ticket']==='NORMAL_21'); ?>
          <?php if ($g['pay_status']==='paid'): ?>
          <a href="?grp=<?= (int)$detail ?>&send_member=<?= (int)$m['apply_no'] ?>" onclick="return confirm('<?= gl_e($m['name']) ?>님(<?= gl_e($normp) ?>)에게 QR 문자를 발송할까요?\n(실제 발송)')" style="color:#fff;background:#2d7ff9;padding:3px 9px;border-radius:3px;text-decoration:none;font-size:12px"><?= $sst==='sent'?'재발송':'문자발송' ?></a>
          <a href="?grp=<?= (int)$detail ?>&cancel_member=<?= (int)$m['apply_no'] ?>" onclick="return confirm('<?= $mcf ?>')" style="color:#fff;background:#c0392b;padding:3px 9px;border-radius:3px;text-decoration:none;font-size:12px">개별취소</a>
          <?php endif; ?>
          <?php if ($is1day && $change_ok): ?>
          <a href="?grp=<?= (int)$detail ?>&edit_member=<?= (int)$m['apply_no'] ?>" style="color:#fff;background:#0a7;padding:3px 9px;border-radius:3px;text-decoration:none;font-size:12px">날짜변경</a>
          <?php elseif (!$is1day): ?>
          <span style="color:#bbb;font-size:11px" title="양일권은 참석일 변경 불가">양일고정</span>
          <?php endif; ?>
        <?php else: ?>
          <span style="color:#bbb">-</span>
        <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  <?php endif; ?>
<?php else: ?>
  <?php
    // 전체 단체 참석 구성 합계 — 취소 단체(pay_status='cancel')·취소 구성원(gm_status='C') 제외.
    @include_once(__DIR__ . '/../unrealfest2026/_group_apply.php'); if (function_exists('ufs_group_apply_cols')) @ufs_group_apply_cols();
    $t_all=0; $t_d1=0; $t_d2=0; $t_active=0; $t_grp=0; $t_paid=0;
    $ts = sql_query("SELECT m.ticket, m.gm_status, g.pay_status FROM cb_unreal_2026_group_member m LEFT JOIN cb_unreal_2026_group g ON g.grp_no=m.grp_no WHERE g.pay_status<>'cancel'");
    if ($ts) while ($c=$ts->fetch_assoc()) {
      if (isset($c['gm_status']) && $c['gm_status']==='C') continue;
      $t_active++;
      if ($c['ticket']==='NORMAL_ALL') $t_all++;
      else if ($c['ticket']==='NORMAL_20') $t_d1++;
      else if ($c['ticket']==='NORMAL_21') $t_d2++;
    }
    $gs = sql_query("SELECT pay_status, COUNT(*) c FROM cb_unreal_2026_group WHERE pay_status<>'cancel' GROUP BY pay_status");
    if ($gs) while ($gr=$gs->fetch_assoc()) { $t_grp += (int)$gr['c']; if ($gr['pay_status']==='paid') $t_paid=(int)$gr['c']; }
    $t_day1 = $t_all + $t_d1; $t_day2 = $t_all + $t_d2;
  ?>
  <div style="max-width:820px;margin:0 0 12px;padding:10px 14px;background:#f7fbfc;border:1px solid #cbe9ee;border-radius:6px;font-size:13px;color:#333">
    <b>전체 참석 구성</b> (취소 제외) ·
    양일권 <b><?= $t_all ?></b>명 ·
    1일권(8월 20일) <b><?= $t_d1 ?></b>명 ·
    1일권(8월 21일) <b><?= $t_d2 ?></b>명
    <span style="color:#888"> · 유효 <?= $t_active ?>명 / 단체 <?= $t_grp ?>건(결제완료 <?= $t_paid ?>)</span>
    <br><span style="color:#666;font-size:12px">일자별 실참석(양일권 포함) — 8월 20일 <b><?= $t_day1 ?></b>명 · 8월 21일 <b><?= $t_day2 ?></b>명</span>
  </div>
  <div style="margin:0 0 12px;display:flex;gap:8px;flex-wrap:wrap">
    <a href="?export=members" style="display:inline-block;background:#1a7f37;color:#fff;padding:7px 16px;border-radius:4px;text-decoration:none;font-weight:700">⬇ 구성원 전체 CSV</a>
    <a href="?export=groups" style="display:inline-block;background:#2d7ff9;color:#fff;padding:7px 16px;border-radius:4px;text-decoration:none;font-weight:700">⬇ 단체 목록 CSV</a>
    <span style="color:#999;font-size:12px;align-self:center">※ CSV(엑셀) · UTF-8 BOM(한글 정상)</span>
  </div>
  <table>
    <thead><tr><th>접수번호</th><th>대표자</th><th>회사</th><th>인원</th><th>결제</th><th>할인</th><th class="r">총액</th><th>상태</th><th>접수일시</th><th>상세</th></tr></thead>
    <tbody>
    <?php $res=sql_query("SELECT * FROM cb_unreal_2026_group ORDER BY grp_no DESC");
    if ($res && $res->num_rows){ while($g=$res->fetch_assoc()):
      $st=$g['pay_status']; $bcls=$st==='paid'?'b-paid':($st==='cancel'?'b-cancel':'b-pending'); ?>
      <tr>
        <td><?= gl_e($g['grp_code']) ?></td>
        <td><?= gl_e($g['rep_name']) ?><?= $g['rep_attend']!=='Y'?' <span style="color:#999">(결제만)</span>':'' ?></td>
        <td><?= gl_e($g['rep_company']) ?></td>
        <td><?= (int)$g['headcount'] ?>명</td>
        <td><?= gl_e(isset($PAYNAME[$g['paymethod']])?$PAYNAME[$g['paymethod']]:$g['paymethod']) ?></td>
        <td><?= (int)$g['discount_pct'] ?>%</td>
        <td class="r">₩<?= number_format((int)$g['total_amount']) ?></td>
        <td><span class="badge <?= $bcls ?>"><?= gl_e(isset($STNAME[$st])?$STNAME[$st]:$st) ?></span></td>
        <td><?= gl_e($g['reg']) ?></td>
        <td><a href="2026_group_list.php?grp=<?= (int)$g['grp_no'] ?>">보기</a><?php if ($st==='cancel'): ?> · <a href="?delete_grp=<?= (int)$g['grp_no'] ?>" onclick="return confirm('이 취소된 단체 등록을 삭제할까요? (되돌릴 수 없습니다)')" style="color:#c0392b">삭제</a><?php endif; ?></td>
      </tr>
    <?php endwhile; } else { ?>
      <tr><td colspan="10" style="color:#999;padding:18px">단체 등록이 없습니다.</td></tr>
    <?php } ?>
    </tbody>
  </table>
<?php endif; ?>
</div>
<?php include_once('./admin.tail.php'); ?>
