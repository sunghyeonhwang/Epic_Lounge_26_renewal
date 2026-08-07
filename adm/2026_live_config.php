<?php
/* Unreal Fest Seoul 2026 — 관리자: 온라인 라이브 설정 (adm/2026_live_config.php)
 * 공개 시청페이지 live.php가 읽는 설정. cb_unreal_2026_config(키-값) 재사용.
 * 키: live_active(0/1) · live_notice · live_yt_d1t1..d1t4 · live_yt_d2t1..d2t4 (YouTube ID/URL). PHP 7.0 호환.
 */
$sub_menu = '700367';
include_once('./_common.php');
if (!function_exists('is_admin') || !is_admin($member['mb_id'])) {
    alert('관리자 로그인이 필요합니다.', G5_ADMIN_URL);
}
$g5['title'] = '온라인 라이브 설정';
function lc_e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// datetime-local(YYYY-MM-DDTHH:MM) → 'YYYY-MM-DD HH:MM' 정규화(빈값/형식오류=빈문자)
function lc_dt($s){
    $s = trim((string)$s);
    if ($s === '') return '';
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})[T ](\d{2}):(\d{2})/', $s, $m)) return $m[1].'-'.$m[2].'-'.$m[3].' '.$m[4].':'.$m[5];
    return '';
}
// YouTube ID 추출(전체 URL/임베드/라이브/공유링크/원본 ID 모두 허용) → 11자 ID
function lc_ytid($s){
    $s = trim((string)$s);
    if ($s === '') return '';
    if (preg_match('~(?:youtu\.be/|/embed/|/live/|[?&]v=)([A-Za-z0-9_-]{11})~', $s, $m)) return $m[1];
    if (preg_match('~^[A-Za-z0-9_-]{11}$~', $s)) return $s;
    return $s; // 알 수 없는 형식은 그대로(관리자 확인용)
}

sql_query("CREATE TABLE IF NOT EXISTS cb_unreal_2026_config (cfg_key VARCHAR(50) NOT NULL, cfg_val VARCHAR(255) NOT NULL DEFAULT '', PRIMARY KEY (cfg_key)) DEFAULT CHARSET=utf8");
function lc_set($k,$v){ $k=sql_real_escape_string($k); $v=sql_real_escape_string($v);
    $ex=sql_fetch("SELECT cfg_key FROM cb_unreal_2026_config WHERE cfg_key='$k'");
    if ($ex) sql_query("UPDATE cb_unreal_2026_config SET cfg_val='$v' WHERE cfg_key='$k'");
    else     sql_query("INSERT INTO cb_unreal_2026_config (cfg_key,cfg_val) VALUES ('$k','$v')"); }
function lc_get($k){ $r=sql_fetch("SELECT cfg_val FROM cb_unreal_2026_config WHERE cfg_key='".sql_real_escape_string($k)."'"); return $r?$r['cfg_val']:''; }

$CH = array(
  'd1t1'=>'20일(Day1) · 게임: 프로그래밍', 'd1t2'=>'20일(Day1) · 게임: 아트',
  'd1t3'=>'20일(Day1) · 미디어 & 엔터',   'd1t4'=>'20일(Day1) · 공통',
  'd2t1'=>'21일(Day2) · 게임: 프로그래밍', 'd2t2'=>'21일(Day2) · 게임: 아트',
  'd2t3'=>'21일(Day2) · 미디어 & 엔터',   'd2t4'=>'21일(Day2) · 제조 및 시뮬',
);

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    lc_set('live_active', (isset($_POST['live_active']) && $_POST['live_active']==='1') ? '1' : '0');
    lc_set('live_notice', isset($_POST['live_notice']) ? trim($_POST['live_notice']) : '');
    foreach ($CH as $k=>$label) {
        $raw = isset($_POST['yt_'.$k]) ? trim($_POST['yt_'.$k]) : '';
        lc_set('live_yt_'.$k, lc_ytid($raw));
    }
    lc_set('live_banner_start', lc_dt(isset($_POST['banner_start'])?$_POST['banner_start']:''));
    lc_set('live_banner_end',   lc_dt(isset($_POST['banner_end'])?$_POST['banner_end']:''));
    lc_set('live_start', lc_dt(isset($_POST['live_start'])?$_POST['live_start']:''));
    lc_set('live_end',   lc_dt(isset($_POST['live_end'])?$_POST['live_end']:''));
    $msg = '저장했습니다.';
}

$active = (lc_get('live_active') === '1');
$notice = lc_get('live_notice');
$vals = array(); foreach ($CH as $k=>$l) $vals[$k] = lc_get('live_yt_'.$k);
$bstart = lc_get('live_banner_start'); $bend = lc_get('live_banner_end');
// 'Y-m-d H:i' → datetime-local value 'Y-m-dTH:i'
$bstart_in = ($bstart!=='') ? str_replace(' ','T',$bstart) : '';
$bend_in   = ($bend!=='')   ? str_replace(' ','T',$bend)   : '';
$now_str = date('Y-m-d H:i');
$banner_live = ($bstart!=='' && $bend!=='' && $now_str >= $bstart && $now_str <= $bend);
// 라이브 활성 = 수동 토글 ON 또는 예약 기간 내
$lstart = lc_get('live_start'); $lend = lc_get('live_end');
$lstart_in = ($lstart!=='') ? str_replace(' ','T',$lstart) : '';
$lend_in   = ($lend!=='')   ? str_replace(' ','T',$lend)   : '';
$sched_on = ($lstart!=='' && $lend!=='' && $now_str >= $lstart && $now_str <= $lend);
$eff_active = ($active || $sched_on);

include_once('./admin.head.php');
?>
<style>
.lc-wrap{width:100%;max-width:900px;margin:16px 0}
.lc-card{border:1px solid #ddd;border-radius:6px;padding:18px;background:#fff;margin-bottom:16px}
.lc-msg{background:#e8fbfd;border:1px solid #00C1D5;color:#007a89;padding:10px 14px;border-radius:4px;margin-bottom:14px}
.lc-row{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f0f0f0;flex-wrap:wrap}
.lc-row label{width:230px;font-size:13px;color:#444;font-weight:600}
.lc-row input[type=text]{flex:1;min-width:240px;padding:8px;border:1px solid #ccc;border-radius:4px;font-size:13px}
.lc-btn{background:#00C1D5;color:#fff;border:0;padding:11px 26px;font-weight:700;border-radius:5px;cursor:pointer;font-size:15px}
.lc-prev{font-size:11px;color:#888}
.on-badge{display:inline-block;padding:2px 10px;border-radius:10px;font-weight:700;font-size:12px}
</style>
<div class="lc-wrap">
  <?php if ($msg): ?><div class="lc-msg"><?= lc_e($msg) ?></div><?php endif; ?>
  <form method="post">
  <div class="lc-card">
    <h2 style="font-size:16px;margin:0 0 6px">📺 온라인 라이브 설정
      <span class="on-badge" style="background:<?= $eff_active?'#d4edda':'#f8d7da' ?>;color:<?= $eff_active?'#155724':'#721c24' ?>;margin-left:8px"><?= $eff_active?'ON — 시청 가능':'OFF — 준비중' ?></span>
      <?php if ($sched_on && !$active): ?><span class="on-badge" style="background:#e0f2fe;color:#075985;margin-left:4px">예약 기간 자동 ON</span><?php endif; ?>
    </h2>
    <p style="color:#888;font-size:12px;margin:0 0 12px">공개 시청 페이지: <a href="../unrealfest2026/live.php" target="_blank">/unrealfest2026/live.php</a> · 등록자(온라인 결제완료) 이메일 확인 후 시청.</p>
    <div class="lc-row">
      <label>라이브 활성화(수동)</label>
      <label style="width:auto;font-weight:400;cursor:pointer"><input type="checkbox" name="live_active" value="1" <?= $active?'checked':'' ?>> 즉시 ON (체크 시 기간과 무관하게 바로 노출)</label>
    </div>
    <div class="lc-row">
      <label>자동 활성 시작 일시</label>
      <input type="datetime-local" name="live_start" value="<?= lc_e($lstart_in) ?>" style="padding:8px;border:1px solid #ccc;border-radius:4px">
    </div>
    <div class="lc-row" style="border-bottom:2px solid #eee">
      <label>자동 활성 종료 일시</label>
      <input type="datetime-local" name="live_end" value="<?= lc_e($lend_in) ?>" style="padding:8px;border:1px solid #ccc;border-radius:4px">
      <span style="font-size:11px;color:#aaa">이 기간엔 자동 ON(수동 체크 OFF여도). 비우면 수동 토글만.</span>
    </div>
    <div class="lc-row">
      <label>공지 문구(선택)</label>
      <input type="text" name="live_notice" value="<?= lc_e($notice) ?>" placeholder="예: 세션 사이 쉬는 시간에는 대기 화면이 표시됩니다.">
    </div>
  </div>
  <div class="lc-card">
    <h2 style="font-size:15px;margin:0 0 4px">index 배너 노출 기간
      <span class="on-badge" style="background:<?= $banner_live?'#d4edda':'#f8f9fa' ?>;color:<?= $banner_live?'#155724':'#888' ?>;margin-left:8px;border:1px solid #eee"><?= $banner_live?'현재 노출 중':'현재 미노출' ?></span>
    </h2>
    <p style="color:#888;font-size:12px;margin:0 0 10px">홈(index) 상단 <b>“지금 온라인 라이브 진행 중 — 시청하기”</b> 배너를 <b>이 기간에만</b> 노출합니다. (라이브 활성화 토글과 무관 · 시작/종료 둘 다 있어야 동작 · 서버 시각 기준)</p>
    <div class="lc-row">
      <label>노출 시작 일시</label>
      <input type="datetime-local" name="banner_start" value="<?= lc_e($bstart_in) ?>" style="padding:8px;border:1px solid #ccc;border-radius:4px">
    </div>
    <div class="lc-row">
      <label>노출 종료 일시</label>
      <input type="datetime-local" name="banner_end" value="<?= lc_e($bend_in) ?>" style="padding:8px;border:1px solid #ccc;border-radius:4px">
    </div>
    <p style="color:#aaa;font-size:11px;margin:8px 0 0">비우면 배너가 노출되지 않습니다. 현재 서버 시각: <?= lc_e($now_str) ?></p>
  </div>
  <div class="lc-card">
    <h2 style="font-size:15px;margin:0 0 4px">채널별 YouTube (ID 또는 전체 URL 붙여넣기)</h2>
    <p style="color:#888;font-size:12px;margin:0 0 10px">비워두면 해당 채널은 "준비중"으로 표시됩니다. `youtu.be/...`·`watch?v=...`·`/live/...`·`/embed/...`·11자 ID 모두 허용(저장 시 ID로 정규화).</p>
    <?php foreach ($CH as $k=>$label): ?>
    <div class="lc-row">
      <label><?= lc_e($label) ?></label>
      <input type="text" name="yt_<?= $k ?>" value="<?= lc_e($vals[$k]) ?>" placeholder="YouTube ID 또는 URL">
      <?php if ($vals[$k] !== ''): ?><a class="lc-prev" href="https://www.youtube.com/watch?v=<?= rawurlencode($vals[$k]) ?>" target="_blank">미리보기 ↗</a><?php endif; ?>
    </div>
    <?php endforeach; ?>
    <div style="margin-top:16px"><button type="submit" class="lc-btn">저장</button></div>
  </div>
  </form>
</div>
<?php include_once('./admin.tail.php'); ?>
