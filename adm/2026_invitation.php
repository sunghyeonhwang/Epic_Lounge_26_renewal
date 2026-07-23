<?php
/* Unreal Fest Seoul 2026 — 초청장 발송 (2026_invitation.php) [M5]
 * 초청 코드 발급(수동/CSV/로스터 sync)·현황·활성/삭제. 처리=2026_invitation_proc.php.
 * 공개 등록: /v3/unrealfest2026/ticket-invite.php?code=. 이메일 실발송(Resend)은 M6.
 */
$sub_menu = "700370";
include_once('./_common.php');
auth_check_menu($auth, $sub_menu, 'r');

define('INV_PUBLIC', 'https://epiclounge.co.kr/v3/unrealfest2026/ticket-invite.php');

// 스키마 보장
sql_query("CREATE TABLE IF NOT EXISTS cb_unreal_2026_speaker_code (
  sc_no INT UNSIGNED NOT NULL AUTO_INCREMENT, sc_code VARCHAR(40) NOT NULL DEFAULT '', sc_src VARCHAR(12) NOT NULL DEFAULT 'speaker',
  sc_ref_id INT NOT NULL DEFAULT 0, sc_name VARCHAR(100) NOT NULL DEFAULT '', sc_email VARCHAR(200) NOT NULL DEFAULT '',
  sc_phone VARCHAR(50) NOT NULL DEFAULT '', sc_company VARCHAR(200) NOT NULL DEFAULT '', sc_lang VARCHAR(5) NOT NULL DEFAULT 'ko',
  sc_quota INT NOT NULL DEFAULT 2, sc_used INT NOT NULL DEFAULT 0, sc_discount INT NOT NULL DEFAULT 100,
  sc_inviter VARCHAR(100) NOT NULL DEFAULT '에픽게임즈', sc_active CHAR(1) NOT NULL DEFAULT 'Y', sc_sent_at DATETIME DEFAULT NULL,
  sc_memo VARCHAR(255) NOT NULL DEFAULT '', sc_reg_datetime DATETIME DEFAULT NULL,
  PRIMARY KEY (sc_no), UNIQUE KEY uq_sc_code (sc_code)) DEFAULT CHARSET=utf8");
@sql_query("ALTER TABLE cb_unreal_2026_speaker_code ADD COLUMN sc_msg_id VARCHAR(80) DEFAULT ''");
@sql_query("ALTER TABLE cb_unreal_2026_speaker_code ADD COLUMN sc_status VARCHAR(20) DEFAULT ''");
@sql_query("ALTER TABLE cb_unreal_2026_speaker_code ADD COLUMN sc_status_at DATETIME DEFAULT NULL");
@sql_query("ALTER TABLE cb_unreal_2026_speaker_code ADD COLUMN sc_valid_from DATE DEFAULT NULL");
@sql_query("ALTER TABLE cb_unreal_2026_speaker_code ADD COLUMN sc_valid_until DATE DEFAULT NULL");

$token = get_admin_token();   // check_admin_token()이 ss_admin_token을 검사하므로 admin 토큰으로 발행(get_token=ss_token은 불일치)

// 통계
$st_total  = sql_fetch("SELECT COUNT(*) c, COALESCE(SUM(sc_quota),0) q, COALESCE(SUM(sc_used),0) u FROM cb_unreal_2026_speaker_code");
$st_active = sql_fetch("SELECT COUNT(*) c FROM cb_unreal_2026_speaker_code WHERE sc_active='Y'");

// 검색
$stx = isset($_GET['stx']) ? trim($_GET['stx']) : '';
$where = " WHERE 1 ";
if ($stx !== '') {
    $e = sql_real_escape_string($stx);
    $where .= " AND (sc_code LIKE '%$e%' OR sc_name LIKE '%$e%' OR sc_email LIKE '%$e%' OR sc_company LIKE '%$e%' OR sc_inviter LIKE '%$e%') ";
}
$total_count = 0;
$tc = sql_fetch("SELECT COUNT(*) c FROM cb_unreal_2026_speaker_code $where");
if ($tc) $total_count = (int)$tc['c'];

$rows = 100;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; if ($page < 1) $page = 1;
$from = ($page - 1) * $rows;
$list = sql_query("SELECT * FROM cb_unreal_2026_speaker_code $where ORDER BY sc_no DESC LIMIT $from, $rows");

// 코드별 등록자 수(활성 apply)
$cntMap = array();
$cr = sql_query("SELECT apply_speaker_code c, COUNT(*) n FROM cb_unreal_2026_event2_apply WHERE apply_speaker_code<>'' AND apply_pay_status<>0 GROUP BY apply_speaker_code");
if ($cr) while ($x = sql_fetch_array($cr)) $cntMap[$x['c']] = (int)$x['n'];

$g5['title'] = '2026 초청장 발송';
include_once('./admin.head.php');
?>
<link rel="stylesheet" type="text/css" href="https://epiclounge.co.kr/cib/assets/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" />
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<style>
  h1{font-weight:bold;font-size:1.5em}
  body table td{font-size:12px;vertical-align:middle}
  .vm-summary{display:flex;gap:1rem;margin:1rem 0}
  .vm-summary-item{flex:1;text-align:center;padding:1rem;background:#fff;border:1px solid #e5e7eb;border-radius:6px}
  .vm-summary-item h3{margin:0 0 .5rem 0;font-size:.9rem;color:#6b7280}
  .vm-summary-number{margin:0;font-size:1.6rem;font-weight:bold;color:#1f2937}
  .inv-panel{margin:1rem 0;padding:1rem;background:#fff;border:1px solid #e5e7eb;border-radius:8px}
  .inv-panel h3{margin-top:0;font-size:1rem;font-weight:bold}
  .inv-row{display:flex;flex-wrap:wrap;gap:.5rem;align-items:flex-end}
  .inv-row .fld{display:flex;flex-direction:column;font-size:12px}
  .inv-row input,.inv-row select{padding:6px 8px;border:1px solid #ccc;border-radius:4px}
  .badge-off{color:#9ca3af}
  .btn-xs2{padding:2px 6px;font-size:11px}
  code.copy{cursor:pointer}
</style>

<h1>2026 초청장 발송</h1>
<p style="color:#6b7280">초청 코드를 발급하면 <b>공개 등록 링크</b>(<?php echo INV_PUBLIC; ?>?code=코드)로 스피커·스폰서·VIP가 무인증 등록합니다. 무료(100%)는 즉시완료, 부분할인(50~99%)은 카드결제. 초청장 이메일은 <b>Resend</b>로 발송(from noreply@update.epiclounge.co.kr).</p>

<div class="vm-summary">
  <div class="vm-summary-item"><h3>전체 코드</h3><p class="vm-summary-number"><?php echo number_format((int)$st_total['c']); ?></p></div>
  <div class="vm-summary-item"><h3>활성 코드</h3><p class="vm-summary-number"><?php echo number_format((int)$st_active['c']); ?></p></div>
  <div class="vm-summary-item"><h3>발급 매수(사용/전체)</h3><p class="vm-summary-number"><?php echo number_format((int)$st_total['u']).' / '.number_format((int)$st_total['q']); ?></p></div>
</div>

<!-- 수동 발급 -->
<div class="inv-panel">
  <h3>개별 발급</h3>
  <form name="fissue" method="post" action="2026_invitation_proc.php" onsubmit="return chkIssue(this)">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
    <input type="hidden" name="mode" value="issue">
    <div class="inv-row">
      <div class="fld"><label>대상명</label><input type="text" name="sc_name" style="width:110px"></div>
      <div class="fld"><label>이메일 *</label><input type="text" name="sc_email" style="width:180px"></div>
      <div class="fld"><label>연락처</label><input type="text" name="sc_phone" style="width:130px"></div>
      <div class="fld"><label>소속</label><input type="text" name="sc_company" style="width:140px"></div>
      <div class="fld"><label>언어</label><select name="sc_lang"><option value="ko">한국어</option><option value="en">English</option></select></div>
      <div class="fld"><label>매수</label><input type="number" name="sc_quota" value="2" min="1" max="10" style="width:60px"></div>
      <div class="fld"><label>할인율(%)</label><select name="sc_discount"><option>100</option><option>90</option><option>80</option><option>70</option><option>60</option><option>50</option></select></div>
      <div class="fld"><label>초청인</label><input type="text" name="sc_inviter" value="에픽게임즈" style="width:120px"></div>
      <div class="fld"><label>메모</label><input type="text" name="sc_memo" style="width:120px"></div>
      <div class="fld"><label>사용 시작일</label><input type="date" name="sc_valid_from" style="width:140px"></div>
      <div class="fld"><label>사용 종료일</label><input type="date" name="sc_valid_until" style="width:140px"></div>
      <button type="submit" class="btn btn-primary btn-sm">발급</button>
    </div>
    <p style="margin:.5rem 0 0;color:#9ca3af;font-size:11px">할인율 100=무료 즉시완료 · 50~99=카드결제. 매수=코드당 등록 가능 인원(대표+동반).</p>
  </form>
</div>

<!-- 일괄/자동 -->
<div class="inv-panel">
  <h3>일괄 발급 · 로스터 sync · 내보내기</h3>
  <div class="inv-row" style="gap:1.5rem">
    <form method="post" action="2026_invitation_proc.php" enctype="multipart/form-data" style="display:flex;gap:.5rem;align-items:flex-end">
      <input type="hidden" name="token" value="<?php echo $token; ?>">
      <input type="hidden" name="mode" value="csv">
      <div class="fld"><label>CSV 업로드 일괄발급</label><input type="file" name="csv" accept=".csv"></div>
      <button type="submit" class="btn btn-success btn-sm">CSV 발급</button>
      <button type="button" class="btn btn-default btn-sm" onclick="dlTemplate()">양식 다운로드</button>
    </form>
    <form method="post" action="2026_invitation_proc.php" onsubmit="return confirm('스피커 로스터에서 코드를 자동 발급합니다. 계속할까요?')" style="display:flex;align-items:flex-end">
      <input type="hidden" name="token" value="<?php echo $token; ?>">
      <input type="hidden" name="mode" value="sync">
      <button type="submit" class="btn btn-warning btn-sm">스피커 로스터 sync</button>
    </form>
    <div style="align-self:flex-end">
      <a href="2026_invitation_proc.php?mode2=export&token=<?php echo $token; ?>" class="btn btn-info btn-sm"><i class="fa fa-download"></i> 코드 내보내기(CSV)</a>
    </div>
    <form method="post" action="2026_invitation_proc.php" onsubmit="return confirm('미발송 상태인 활성 코드 전체에 초청장을 발송할까요?')" style="align-self:flex-end">
      <input type="hidden" name="token" value="<?php echo $token; ?>">
      <input type="hidden" name="mode" value="sendall">
      <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-paper-plane"></i> 미발송 일괄 발송</button>
    </form>
    <form method="post" action="2026_invitation_proc.php" onsubmit="return confirm('전체 코드의 사용기간을 이 값으로 설정할까요? (빈칸=제한없음)')" style="align-self:flex-end;display:flex;gap:6px;align-items:flex-end">
      <input type="hidden" name="token" value="<?php echo $token; ?>">
      <input type="hidden" name="mode" value="setperiod">
      <div class="fld"><label>전체 사용기간(시작~종료)</label><input type="date" name="sc_valid_from" style="width:140px"></div>
      <span style="align-self:center;padding-bottom:6px">~</span>
      <input type="date" name="sc_valid_until" style="width:140px;padding:6px 8px;border:1px solid #ccc;border-radius:4px">
      <button type="submit" class="btn btn-default btn-sm">일괄 적용</button>
    </form>
  </div>
  <p style="margin:.5rem 0 0;color:#9ca3af;font-size:11px">CSV 헤더: 초청인,대상명,이메일,연락처,소속,언어,매수,할인율,메모,<b>시작일,종료일</b>(YYYY-MM-DD, 선택) · 로스터 sync는 <b>내부(internal) 스피커만</b> 대상입니다(외부/키노트 제외). 사용기간 빈칸=제한없음.</p>
</div>

<!-- 검색 -->
<form name="fsearch" method="get" class="inv-panel" style="display:flex;gap:.5rem;align-items:center">
  <input type="text" name="stx" value="<?php echo htmlspecialchars($stx); ?>" placeholder="코드·이름·이메일·소속·초청인" style="padding:6px 8px;border:1px solid #ccc;border-radius:4px;width:280px">
  <input type="submit" value="검색" class="btn btn-default btn-sm">
  <a href="2026_invitation.php" class="btn btn-default btn-sm">전체</a>
  <span style="color:#6b7280">검색결과 <?php echo number_format($total_count); ?>건</span>
</form>

<div class="tbl_head01 tbl_wrap">
<table class="table table-bordered table-hover" style="background:#fff">
  <caption>초청 코드 목록</caption>
  <thead>
    <tr style="background:#f8f9fa">
      <th>코드</th><th>대상</th><th>언어</th><th>할인</th><th>매수<br>(사용/전체)</th><th>등록자</th><th>초청인</th><th>상태</th><th>발송</th><th>관리</th>
    </tr>
  </thead>
  <tbody>
  <?php if ($list && sql_num_rows($list)): while ($r = sql_fetch_array($list)):
      $link = INV_PUBLIC.'?code='.$r['sc_code'].'&lang='.$r['sc_lang'];
      $reg  = isset($cntMap[$r['sc_code']]) ? $cntMap[$r['sc_code']] : 0;
      $active = ($r['sc_active'] === 'Y');
  ?>
    <tr<?php if(!$active) echo ' style="opacity:.5"'; ?>>
      <td><code class="copy" title="링크 복사" onclick="cpy('<?php echo htmlspecialchars($link, ENT_QUOTES); ?>')"><?php echo htmlspecialchars($r['sc_code']); ?></code>
          <?php if ($r['sc_src']==='speaker'): ?><br><span class="label label-default">스피커</span><?php endif; ?>
          <?php
          $vf = isset($r['sc_valid_from']) ? $r['sc_valid_from'] : ''; $vu = isset($r['sc_valid_until']) ? $r['sc_valid_until'] : '';
          if (($vf && $vf!=='0000-00-00') || ($vu && $vu!=='0000-00-00')) {
              $expired = ($vu && $vu!=='0000-00-00' && date('Y-m-d') > substr($vu,0,10));
              echo '<br><span style="font-size:10px;color:'.($expired?'#dc2626':'#6b7280').'">기간 '.htmlspecialchars($vf && $vf!=='0000-00-00'?substr($vf,0,10):'…').' ~ '.htmlspecialchars($vu && $vu!=='0000-00-00'?substr($vu,0,10):'…').($expired?' (만료)':'').'</span>';
          }
          ?></td>
      <td><b><?php echo htmlspecialchars($r['sc_name']); ?></b><br>
          <span style="color:#6b7280"><?php echo htmlspecialchars($r['sc_email']); ?></span><br>
          <span style="color:#9ca3af"><?php echo htmlspecialchars($r['sc_company']); ?></span></td>
      <td><?php echo $r['sc_lang']==='en'?'EN':'KO'; ?></td>
      <td><?php echo (int)$r['sc_discount']==100 ? '무료' : ((int)$r['sc_discount'].'%'); ?></td>
      <td><?php echo (int)$r['sc_used'].' / '.(int)$r['sc_quota']; ?></td>
      <td><?php echo $reg; ?>명</td>
      <td><?php echo htmlspecialchars($r['sc_inviter']); ?></td>
      <td><?php echo $active ? '<span class="text-success">활성</span>' : '<span class="badge-off">중지</span>'; ?><br>
          <a href="2026_invitation_proc.php?mode2=toggle&no=<?php echo (int)$r['sc_no']; ?>&token=<?php echo $token; ?>" class="btn btn-default btn-xs2"><?php echo $active?'중지':'활성'; ?></a></td>
      <td>
        <?php if (trim($r['sc_email'])!==''): ?>
        <a href="2026_invitation_proc.php?mode2=send&no=<?php echo (int)$r['sc_no']; ?>&token=<?php echo $token; ?>" class="btn btn-<?php echo $r['sc_sent_at']?'default':'primary'; ?> btn-xs2" onclick="return confirm('<?php echo htmlspecialchars($r['sc_email'],ENT_QUOTES); ?> 로 초청장을 발송할까요?')"><?php echo $r['sc_sent_at']?'재발송':'발송'; ?></a>
        <?php else: ?><span class="badge-off">이메일없음</span><?php endif; ?>
        <a href="2026_invitation_proc.php?mode2=preview&no=<?php echo (int)$r['sc_no']; ?>" target="_blank" class="btn btn-default btn-xs2" title="발송되는 이메일 미리보기">미리보기</a><br>
        <?php
        if ($r['sc_sent_at']) {
            $stmap = array('sent'=>array('발송','#6b7280'),'delivered'=>array('도달','#16a34a'),'opened'=>array('열람','#2563eb'),'clicked'=>array('클릭','#7c3aed'),'delayed'=>array('지연','#d97706'),'bounced'=>array('반송','#dc2626'),'complained'=>array('스팸신고','#dc2626'));
            $st = isset($r['sc_status']) ? $r['sc_status'] : '';
            $lab = isset($stmap[$st]) ? $stmap[$st] : array(($st!==''?$st:'발송'),'#6b7280');
            $at = ($r['sc_status_at'] ? $r['sc_status_at'] : $r['sc_sent_at']);
            echo '<span style="color:'.$lab[1].';font-size:11px;font-weight:bold">● '.$lab[0].'</span>';
            echo '<br><span style="color:#9ca3af;font-size:10px">'.htmlspecialchars(substr((string)$at,0,16)).'</span>';
        } else {
            echo '<span class="badge-off">미발송</span>';
        }
        ?></td>
      <td>
        <button type="button" class="btn btn-info btn-xs2" onclick="cpy('<?php echo htmlspecialchars($link, ENT_QUOTES); ?>')">링크복사</button>
        <?php if ((int)$r['sc_used']===0): ?>
        <a href="2026_invitation_proc.php?mode2=del&no=<?php echo (int)$r['sc_no']; ?>&token=<?php echo $token; ?>" class="btn btn-danger btn-xs2" onclick="return confirm('삭제할까요?')">삭제</a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endwhile; else: ?>
    <tr><td colspan="10" style="text-align:center;padding:2rem;color:#9ca3af">발급된 코드가 없습니다.</td></tr>
  <?php endif; ?>
  </tbody>
</table>
</div>

<?php
echo get_paging($config['cf_write_pages'], $page, ceil($total_count / $rows), "{$_SERVER['SCRIPT_NAME']}?stx=".urlencode($stx)."&amp;page=");
?>

<script>
function chkIssue(f){ if(!f.sc_email.value || f.sc_email.value.indexOf('@')<0){ alert('이메일을 확인해 주세요.'); return false; } return true; }
function cpy(t){
  if(navigator.clipboard){ navigator.clipboard.writeText(t).then(function(){ alert('링크 복사됨:\n'+t); }); }
  else { var ta=document.createElement('textarea'); ta.value=t; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta); alert('링크 복사됨:\n'+t); }
}
function dlTemplate(){
  var csv='﻿초청인,대상명,이메일,연락처,소속,언어,매수,할인율,메모,시작일,종료일\n에픽게임즈,홍길동,hong@example.com,01012345678,에픽게임즈,ko,2,100,예시,2026-07-24,2026-08-19\n';
  var a=document.createElement('a'); a.href='data:text/csv;charset=utf-8,'+encodeURIComponent(csv);
  a.download='초청리스트_template.csv'; document.body.appendChild(a); a.click(); document.body.removeChild(a);
}
</script>

<?php include_once('./admin.tail.php'); ?>
