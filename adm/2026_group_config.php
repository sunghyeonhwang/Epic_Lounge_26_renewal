<?php
/* Unreal Fest Seoul 2026 — 관리자: 단체 할인 설정 (adm/2026_group_config.php)
 * cb_unreal_2026_config (key/val) 에 group_discount(%) 저장. 단체 등록 페이지가 정상가에서 차감.
 * PHP 7.0 호환.
 */
$sub_menu = '700365';
include_once('./_common.php');
if (!function_exists('is_admin') || !is_admin($member['mb_id'])) {
    alert('관리자 로그인이 필요합니다.', G5_ADMIN_URL);
}
$g5['title'] = '단체 할인 설정';
require_once(__DIR__ . '/../unrealfest2026/_pricing.php');

sql_query("CREATE TABLE IF NOT EXISTS cb_unreal_2026_config (cfg_key VARCHAR(40) NOT NULL, cfg_val VARCHAR(200) NOT NULL DEFAULT '', PRIMARY KEY(cfg_key)) DEFAULT CHARSET=utf8");

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['group_discount'])) {
    $d = (int)$_POST['group_discount'];
    if ($d < 0) $d = 0;
    if ($d >= 100) $d = 100;            // 쿠폰 모드 sentinel
    else if ($d > 50) $d = 50;          // 51~99 → 50 스냅
    sql_query("REPLACE INTO cb_unreal_2026_config (cfg_key, cfg_val) VALUES ('group_discount', '".sql_real_escape_string((string)$d)."')");
    $msg = ($d >= 100) ? '저장되었습니다. (쿠폰 모드 — 회사별 쿠폰 할인 적용)' : '저장되었습니다. (일괄 할인 '.$d.'%)';
}
$cur = sql_fetch("SELECT cfg_val FROM cb_unreal_2026_config WHERE cfg_key='group_discount'");
$val = $cur ? (int)$cur['cfg_val'] : 0;
$coupon_mode = ($val >= 100);

function gc_e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

include_once('./admin.head.php');
?>
<style>
.gc-wrap{max-width:720px;margin:16px 0}
.gc-card{border:1px solid #ddd;border-radius:6px;padding:20px;background:#fff;margin-bottom:16px}
.gc-card h2{font-size:15px;font-weight:700;margin:0 0 12px}
.gc-row{display:flex;align-items:center;gap:10px;margin:10px 0}
.gc-row input[type=number]{width:120px;padding:8px;border:1px solid #ccc;border-radius:4px;font-size:16px}
.gc-btn{background:#00C1D5;color:#fff;border:0;padding:10px 24px;font-weight:700;border-radius:4px;cursor:pointer}
.gc-msg{background:#e8fbfd;border:1px solid #00C1D5;color:#007a89;padding:10px 14px;border-radius:4px;margin-bottom:14px}
.gc-tbl{width:100%;border-collapse:collapse;font-size:14px}
.gc-tbl th,.gc-tbl td{border:1px solid #e5e5e5;padding:8px 10px;text-align:right}
.gc-tbl th:first-child,.gc-tbl td:first-child{text-align:left}
.gc-tbl thead th{background:#fafafa}
.gc-help{color:#888;font-size:13px;margin-top:6px}
</style>
<div class="gc-wrap">
  <?php if ($msg): ?><div class="gc-msg"><?= gc_e($msg) ?></div><?php endif; ?>
  <form method="post" class="gc-card">
    <h2>단체 할인 설정 (모드 스위치)</h2>
    <div class="gc-row">
      <label for="gd"><b>설정값</b></label>
      <input type="number" id="gd" name="group_discount" min="0" max="100" step="1" value="<?= gc_e($val) ?>">
      <button type="submit" class="gc-btn">저장</button>
      <span style="margin-left:12px;font-weight:700;<?= $coupon_mode?'color:#c0392b':'color:#007a89' ?>">현재: <?= $coupon_mode ? '쿠폰 모드 (회사별 쿠폰)' : '일괄 모드 ('.(int)$val.'%)' ?></span>
    </div>
    <div class="gc-help">
      <b>0~50</b> = <b>일괄 모드</b> : 모든 단체가 이 할인율(%)로 동일 적용. 쿠폰은 무시됩니다.<br>
      <b>100</b> = <b>쿠폰 모드</b> : 일괄 할인 없음. 각 단체가 <a href="2026_coupon.php">단체 쿠폰</a> 코드로 회사별 다른 할인율을 적용받습니다. (쿠폰 없으면 정상가)<br>
      정상가(정가) 기준 차감 · 100원 단위 반올림 · 단체 등록 페이지에 즉시 반영. (51~99는 사용하지 마세요 — 50으로 저장됩니다.)
    </div>
  </form>

  <div class="gc-card">
    <h2>현재 적용 단가 미리보기 <?= $coupon_mode ? '(쿠폰 모드)' : '(일괄 할인 '.(int)$val.'%)' ?></h2>
    <?php if ($coupon_mode): ?><p class="gc-help" style="margin:0 0 10px">쿠폰 모드에서는 일괄 할인이 없어 아래는 <b>정상가</b>입니다. 실제 할인은 각 단체가 입력한 <a href="2026_coupon.php">쿠폰</a>에 따라 달라집니다.</p><?php endif; ?>
    <table class="gc-tbl">
      <thead><tr><th>티켓</th><th>정상가</th><th><?= $coupon_mode ? '적용가(정상가)' : '단체가' ?></th></tr></thead>
      <tbody>
      <?php foreach (array('NORMAL_ALL'=>'양일권','NORMAL_20'=>'1일권 · Day 1','NORMAL_21'=>'1일권 · Day 2') as $code=>$label): ?>
        <tr>
          <td><?= gc_e($label) ?></td>
          <td>₩<?= number_format(ufs_ticket_orig($code)) ?></td>
          <td><b>₩<?= number_format(ufs_group_price($code)) ?></b></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include_once('./admin.tail.php'); ?>
