<?php
$g5_path = '../..';
chdir($g5_path);
include_once('./_common.php');

// ----- SEO -----
$v3_seo = sql_fetch("SELECT * FROM v3_seo_config WHERE seo_page = 'ode'");
if (empty($v3_seo['seo_title'])) {
    $v3_seo = sql_fetch("SELECT * FROM v3_seo_config WHERE seo_page = 'default'");
}
$seo_title = '이용약관 | 에픽 라운지';

$seo_ga_id          = trim($v3_seo['seo_ga_id'] ?? '');
$seo_gtm_id         = trim($v3_seo['seo_gtm_id'] ?? '');
$seo_pixel_id       = trim($v3_seo['seo_pixel_id'] ?? '');
$seo_kakao_pixel_id = trim($v3_seo['seo_kakao_pixel_id'] ?? '');
$seo_naver_verif    = trim($v3_seo['seo_naver_verif'] ?? '');
$seo_google_verif   = trim($v3_seo['seo_google_verif'] ?? '');
$seo_extra_head     = $v3_seo['seo_extra_head'] ?? '';
$seo_extra_body     = $v3_seo['seo_extra_body'] ?? '';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta property="og:type" content="website">
    <meta property="og:title" content="이용약관 | 에픽 라운지">
    <meta property="og:url" content="https://epiclounge.co.kr/contents/v4/ode.php">

    <?php include G5_PATH.'/inc/marketing_head.php'; ?>

    <link rel="icon" type="image/png" sizes="32x32" href="/v3/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/v3/favicon/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/v3/favicon/apple-icon-180x180.png">

    <title><?php echo get_text($seo_title); ?></title>

    <link rel="stylesheet" href="/v3/resource/css/main26.css">
    <link rel="stylesheet" href="/v3/resource/css/sub.css">
    <link rel="stylesheet" href="/v3/resource/css/pages/detail.css?v=20260318b">

    <script src="/v3/resource/js/jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
</head>
<body>
<?php include G5_PATH.'/inc/marketing_body.php'; ?>
<?php include G5_PATH.'/inc/common_header26.php'; ?>

<!-- sub_visual -->
<div id="sub_visual" class="resource_vi">
    <h2>이용약관</h2>
    <p></p>
</div>

<!-- 본문 컨테이너 -->
<div class="v4-detail-container">
    <div class="v4-detail-wrapper">

        <div class="v4-detail-header">
            <h1 class="v4-detail-header__title">이용약관</h1>
            <div class="v4-detail-header__meta">
                <span class="v4-detail-header__meta-item">최종 업데이트: 2026년 2월 12일</span>
            </div>
        </div>

        <div class="v4-detail-content">

            <h3>제1조(목적)</h3>
            <p>이 약관은 에픽 라운지(이하 "회사")가 운영하는 웹사이트 및 관련 서비스(이하 "서비스")의 이용과 관련하여 회사와 이용자의 권리·의무 및 책임사항을 규정함을 목적으로 합니다.</p>

            <h3>제2조(정의)</h3>
            <ol>
                <li>"이용자"란 본 약관에 따라 회사가 제공하는 서비스를 이용하는 회원 및 비회원을 말합니다.</li>
                <li>"회원"이란 회사에 회원등록을 한 자로서, 계속적으로 서비스를 이용할 수 있는 자를 말합니다.</li>
                <li>"게시물"이란 이용자가 서비스 내에 게시한 문자, 문서, 그림, 영상, 링크, 파일 등 모든 정보를 말합니다.</li>
            </ol>

            <h3>제3조(약관의 효력 및 변경)</h3>
            <ol>
                <li>회사는 본 약관의 내용을 서비스 화면에 게시하거나 기타 방법으로 이용자에게 공지합니다.</li>
                <li>회사는 관련 법령을 위반하지 않는 범위에서 약관을 개정할 수 있으며, 개정 시 적용일자와 개정사유를 명시하여 사전 공지합니다.</li>
                <li>이용자가 개정약관 시행일 이후에도 서비스를 계속 이용하는 경우 개정약관에 동의한 것으로 봅니다.</li>
            </ol>

            <h3>제4조(서비스의 제공 및 변경)</h3>
            <ol>
                <li>회사는 다음 각 호의 서비스를 제공합니다.
                    <ul>
                        <li>이벤트/콘텐츠 정보 제공</li>
                        <li>이벤트 신청 및 참가 관리</li>
                        <li>게시판 등 커뮤니티 기능</li>
                        <li>기타 회사가 정하는 서비스</li>
                    </ul>
                </li>
                <li>회사는 운영상, 기술상의 필요가 있는 경우 서비스의 전부 또는 일부를 변경할 수 있습니다.</li>
                <li>회사는 시스템 점검, 장애, 통신두절 등 상당한 사유가 있는 경우 서비스 제공을 일시 중단할 수 있습니다.</li>
            </ol>

            <h3>제5조(회원가입 및 계정관리)</h3>
            <ol>
                <li>이용자는 회사가 정한 절차에 따라 회원가입을 신청하고, 회사가 이를 승낙함으로써 회원가입이 완료됩니다.</li>
                <li>이용자는 가입 시 정확하고 최신의 정보를 제공해야 하며, 변경 시 지체없이 수정해야 합니다.</li>
                <li>회원은 계정(ID, 비밀번호)을 스스로 관리할 책임이 있으며, 이를 제3자에게 이용하게 해서는 안 됩니다.</li>
                <li>회원 계정의 도용이나 무단 사용을 인지한 경우 즉시 회사에 통지해야 합니다.</li>
            </ol>

            <h3>제6조(회원에 대한 통지)</h3>
            <p>회사는 회원이 등록한 이메일, 문자, 서비스 내 알림, 공지사항 게시 등 적절한 방법으로 통지할 수 있습니다. 전체 회원 대상 통지는 7일 이상 서비스 내 게시로 갈음할 수 있습니다.</p>

            <h3>제7조(회사의 의무)</h3>
            <ol>
                <li>회사는 관련 법령과 본 약관이 금지하는 행위를 하지 않으며 안정적으로 서비스를 제공하기 위해 노력합니다.</li>
                <li>회사는 이용자의 개인정보를 보호하기 위해 노력하며, 개인정보 처리에 관한 사항은 <a href="/v3/contents/v4/personal.php">개인정보처리방침</a>에 따릅니다.</li>
                <li>회사는 이용자가 제기한 의견이나 불만이 정당하다고 인정되는 경우 지체없이 처리합니다.</li>
            </ol>

            <h3>제8조(이용자의 의무)</h3>
            <p>이용자는 다음 행위를 하여서는 안 됩니다.</p>
            <ul>
                <li>타인의 개인정보, 계정정보 도용</li>
                <li>허위정보 입력 또는 부정한 방법의 서비스 이용</li>
                <li>회사 또는 제3자의 지식재산권, 명예, 프라이버시 등 권리 침해</li>
                <li>악성코드 유포, 서비스 운영 방해, 비정상적 접근 시도</li>
                <li>음란물, 혐오표현, 불법정보 게시 등 관련 법령 위반 행위</li>
                <li>기타 공서양속에 반하는 행위</li>
            </ul>

            <h3>제9조(게시물의 관리 및 권리)</h3>
            <ol>
                <li>게시물에 대한 권리와 책임은 이를 게시한 이용자에게 있습니다.</li>
                <li>회사는 법령 또는 본 약관에 위반되거나 권리침해 우려가 있는 게시물에 대해 사전 통지 없이 삭제, 비공개, 이동, 등록 거부 등의 조치를 할 수 있습니다.</li>
                <li>이용자는 본인이 작성한 게시물을 회사가 서비스 운영, 노출, 홍보를 위해 필요한 범위 내에서 사용할 수 있도록 비독점적 이용권을 허락합니다.</li>
            </ol>

            <h3>제10조(서비스 이용 제한 및 계약 해지)</h3>
            <ol>
                <li>회사는 이용자가 본 약관 또는 관련 법령을 위반한 경우 경고, 이용제한, 회원자격 정지 또는 해지 조치를 할 수 있습니다.</li>
                <li>회원은 언제든지 탈퇴를 요청할 수 있으며, 회사는 관련 법령 및 개인정보처리방침에 따라 처리합니다.</li>
                <li>법령 위반, 타인 권리침해, 서비스 운영 방해 등 중대한 위반의 경우 즉시 이용계약을 해지할 수 있습니다.</li>
            </ol>

            <h3>제11조(지식재산권)</h3>
            <ol>
                <li>서비스 및 서비스에 포함된 모든 콘텐츠에 관한 저작권 및 지식재산권은 회사 또는 정당한 권리자에게 귀속됩니다.</li>
                <li>이용자는 회사의 사전 동의 없이 서비스를 상업적으로 이용하거나, 콘텐츠를 복제·배포·전송·2차적 저작물 작성 등의 방법으로 이용할 수 없습니다.</li>
            </ol>

            <h3>제12조(손해배상 및 면책)</h3>
            <ol>
                <li>회사 또는 이용자는 본 약관 위반으로 상대방에게 손해를 발생시킨 경우 관련 법령에 따라 손해를 배상할 책임이 있습니다.</li>
                <li>회사는 천재지변, 불가항력, 이용자의 귀책사유, 제3자의 불법행위 등 회사의 합리적 통제범위를 벗어난 사유로 인한 손해에 대하여 책임을 지지 않습니다.</li>
                <li>회사는 이용자 상호 간 또는 이용자와 제3자 간 분쟁에 원칙적으로 관여하지 않으며, 이로 인해 발생한 손해에 대하여 책임을 지지 않습니다.</li>
            </ol>

            <h3>제13조(준거법 및 관할)</h3>
            <p>본 약관은 대한민국 법령에 따라 해석·적용됩니다. 서비스 이용과 관련하여 발생한 분쟁은 민사소송법상 관할법원에 제기합니다.</p>

            <h3>제14조(청소년 보호)</h3>
            <p>회사는 청소년 유해정보로부터 청소년을 보호하기 위하여 관련 법령 및 정책에 따른 조치를 시행합니다. 만 14세 미만 아동의 회원가입 또는 개인정보 처리가 필요한 경우 관련 법령에 따른 법정대리인 동의 절차를 따릅니다.</p>

            <h3>제15조(고객센터)</h3>
            <p>서비스 이용 관련 문의는 아래로 연락해 주시기 바랍니다.<br>
            고객센터: 02-326-3701 / 이메일: info@griff.co.kr</p>

            <h3>부칙</h3>
            <p>본 약관은 2026년 2월 12일부터 시행합니다.</p>

        </div><!-- /.v4-detail-content -->

        <!-- 목록으로 (메인으로) -->
        <div class="v4-detail-nav">
            <a href="/v3/index.php" class="v4-detail-nav__button--list">
                <span class="v4-detail-nav__label">메인으로</span>
            </a>
        </div>

    </div><!-- /.v4-detail-wrapper -->
</div><!-- /.v4-detail-container -->

<?php include G5_PATH.'/inc/common_footer.php'; ?>

<script src="/v3/resource/js/v4.app.js"></script>
</body>
</html>
