<?php
/* Unreal Fest Seoul 2026 — 관리자: 단건 카드결제 로그 조회 (adm/2026_misc_pay.php)
 * cb_unreal_2026_misc_pay (부스 추가 결제 등 booth-pay.php 기록) 목록·합계·CSV·대기건 삭제. 읽기 위주. PHP 7.0 호환.
 */
$sub_menu = '700367';
include_once('./_common.php');
if (!function_exists('is_admin') || !is_admin($member['mb_id'])) {
    alert('관리자 로그인이 필요합니다.', G5_ADMIN_URL);
}
$g5['title'] = '부스 결제 로그';
function mp_e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$has = @sql_query("SHOW TABLES LIKE 'cb_unreal_2026_misc_pay'");
$exists = ($has && $has->num_rows);

$STN = array('pending'=>'대기','paid'=>'완료','cancel'=>'취소');
$msg = '';
if (!empty($_SESSION['mp_flash'])) { $msg = $_SESSION['mp_flash']; unset($_SESSION['mp_flash']); }

// 대기/특정 건 삭제(완료건은 로그보존 위해 삭제 불가)
if ($exists && isset($_GET['del'])) {
    $no = (int)$_GET['del'];
    $r = sql_fetch("SELECT mp_status FROM cb_unreal_2026_misc_pay WHERE mp_no=".$no);
    if ($r && $r['mp_status'] !== 'paid') { sql_query("DELETE FROM cb_unreal_2026_misc_pay WHERE mp_no=".$no); $msg = '대기 건을 삭제했습니다.'; }
    else { $msg = '결제완료 건은 로그 보존을 위해 삭제할 수 없습니다.'; }
}
// 미완료(pending) 일괄 정리
if ($exists && isset($_GET['clean_pending'])) {
    sql_query("DELETE FROM cb_unreal_2026_misc_pay WHERE mp_status<>'paid'");
    $msg = '미완료(대기) 건을 모두 정리했습니다.';
}
if ($msg !== '' && !headers_sent()) { $_SESSION['mp_flash'] = $msg; header('Location: 2026_misc_pay.php'); exit; }

// CSV 다운로드
if ($exists && isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="ufs2026_booth_pay_'.date('Ymd').'.csv"');
    $out = fopen('php://output', 'w'); echo "\xEF\xBB\xBF";
    fputcsv($out, array('번호','항목','금액','상태','담당','연락처','이메일','승인번호','거래ID','결제시각','등록일','OID'));
    $rs = sql_query("SELECT * FROM cb_unreal_2026_misc_pay ORDER BY mp_no DESC");
    if ($rs) while ($r = $rs->fetch_assoc()) {
        fputcsv($out, array($r['mp_no'],$r['mp_item'],$r['mp_amount'],(isset($STN[$r['mp_status']])?$STN[$r['mp_status']]:$r['mp_status']),
            $r['mp_name'],$r['mp_tel'],$r['mp_email'],$r['mp_applnum'],$r['mp_tid'],$r['mp_paid_at'],$r['mp_reg'],$r['mp_oid']));
    }
    fclose($out); exit;
}

// 합계
$cnt_all=0; $cnt_paid=0; $sum_paid=0; $cnt_pending=0;
if ($exists) {
    $s = sql_query("SELECT mp_status, COUNT(*) c, SUM(mp_amount) s FROM cb_unreal_2026_misc_pay GROUP BY mp_status");
    if ($s) while ($r=$s->fetch_assoc()) { $cnt_all += (int)$r['c']; if ($r['mp_status']==='paid'){ $cnt_paid=(int)$r['c']; $sum_paid=(int)$r['s']; } if ($r['mp_status']==='pending') $cnt_pending=(int)$r['c']; }
}

include_once('./admin.head.php');
?>
<style>
.mp-wrap{width:100%;max-width:100%;margin:16px 0}
.mp-card{border:1px solid #ddd;border-radius:6px;padding:18px;background:#fff;margin-bottom:16px}
.mp-msg{background:#e8fbfd;border:1px solid #00C1D5;color:#007a89;padding:10px 14px;border-radius:4px;margin-bottom:14px}
.mp-tbl{width:100%;border-collapse:collapse;font-size:13px}
.mp-tbl th,.mp-tbl td{border:1px solid #e5e5e5;padding:8px 10px;text-align:center}
.mp-tbl thead th{background:#fafafa}
.mp-tbl td.l{text-align:left}
.badge{display:inline-block;padding:1px 9px;border-radius:10px;font-size:11px;font-weight:700}
.b-paid{background:#d4edda;color:#155724}.b-pending{background:#fff3cd;color:#856404}.b-cancel{background:#f8d7da;color:#721c24}
.mp-btn{display:inline-block;padding:7px 14px;border-radius:4px;text-decoration:none;font-weight:700;font-size:13px;color:#fff}
</style>
<div class="mp-wrap">
  <?php if ($msg): ?><div class="mp-msg"><?= mp_e($msg) ?></div><?php endif; ?>
  <div class="mp-card">
    <h2 style="font-size:16px;margin:0 0 6px">💳 단건 카드결제 로그 (부스 추가 결제 등)
      <span style="font-size:12px;font-weight:400;color:#888;margin-left:8px">총 <?= (int)$cnt_all ?>건 · 완료 <?= (int)$cnt_paid ?>건 · 합계 ₩<?= number_format((int)$sum_paid) ?><?= $cnt_pending?' · 대기 '.$cnt_pending.'건':'' ?></span>
    </h2>
    <p style="color:#888;font-size:12px;margin:0 0 12px">booth-pay.php로 발생한 결제 기록입니다. 결제완료(paid) 건은 보존됩니다. 대기(pending)=결제창을 열었으나 승인 전/중단된 건.</p>
    <div style="margin:0 0 12px;display:flex;gap:8px;flex-wrap:wrap">
      <a href="?export=1" class="mp-btn" style="background:#1a7f37">⬇ CSV 다운로드</a>
      <?php if ($cnt_pending): ?><a href="?clean_pending=1" onclick="return confirm('미완료(대기) 건을 모두 삭제할까요? 완료 건은 유지됩니다.')" class="mp-btn" style="background:#6b7280">대기건 정리 (<?= (int)$cnt_pending ?>)</a><?php endif; ?>
    </div>
    <div style="overflow-x:auto">
    <table class="mp-tbl">
      <thead><tr><th>#</th><th>항목</th><th>금액</th><th>상태</th><th>담당</th><th>연락처</th><th>이메일</th><th>승인번호</th><th>거래ID</th><th>결제시각</th><th>등록일</th><th>관리</th></tr></thead>
      <tbody>
      <?php
      $rows = $exists ? sql_query("SELECT * FROM cb_unreal_2026_misc_pay ORDER BY mp_no DESC") : null;
      if ($rows && $rows->num_rows) { while ($r = $rows->fetch_assoc()):
        $st = $r['mp_status']; $bcls = $st==='paid'?'b-paid':($st==='cancel'?'b-cancel':'b-pending'); ?>
        <tr>
          <td><?= (int)$r['mp_no'] ?></td>
          <td class="l"><?= mp_e($r['mp_item']) ?></td>
          <td style="text-align:right">₩<?= number_format((int)$r['mp_amount']) ?></td>
          <td><span class="badge <?= $bcls ?>"><?= mp_e(isset($STN[$st])?$STN[$st]:$st) ?></span></td>
          <td><?= mp_e($r['mp_name']) ?></td>
          <td><?= mp_e($r['mp_tel']) ?></td>
          <td class="l"><?= mp_e($r['mp_email']) ?></td>
          <td><?= mp_e($r['mp_applnum']) ?></td>
          <td style="font-size:11px"><?= mp_e($r['mp_tid']) ?></td>
          <td style="font-size:12px"><?= mp_e($r['mp_paid_at']) ?></td>
          <td style="font-size:12px"><?= mp_e($r['mp_reg']) ?></td>
          <td><?php if ($st!=='paid'): ?><a href="?del=<?= (int)$r['mp_no'] ?>" onclick="return confirm('이 대기 건을 삭제할까요?')" style="color:#c0392b;text-decoration:underline">삭제</a><?php else: ?><span style="color:#bbb">-</span><?php endif; ?></td>
        </tr>
      <?php endwhile; } else { ?>
        <tr><td colspan="12" style="color:#999;padding:18px"><?= $exists ? '결제 기록이 없습니다.' : '아직 결제가 발생하지 않았습니다. (첫 결제 시 테이블이 생성됩니다.)' ?></td></tr>
      <?php } ?>
      </tbody>
    </table>
    </div>
    <p style="color:#888;font-size:12px;margin-top:10px;line-height:1.7">· 결제완료 건 환불은 현재 이 화면에서 지원하지 않습니다 — INICIS 상점관리자에서 거래ID로 취소하거나, 필요 시 환불 버튼을 추가할 수 있습니다.<br>· 결제 창(비공개 링크)은 <code>booth-pay.php?o=슬러그&k=토큰</code> 형식입니다.</p>
  </div>
</div>
<?php include_once('./admin.tail.php'); ?>
