# Epic Lounge v3 Renewal - Contents 리뉴얼 문서

> **작성일**: 2026-02-05
> **범위**: `contents/` 프론트엔드 페이지 전체 리뉴얼 (adm/ 제외)
> **도메인**: https://epiclounge.co.kr

---

## 1. 현행 Contents 시스템 분석

### 1.1 페이지 구성 (25개 파일 → 7개 기능 그룹)

| 그룹 | 파일 | 역할 |
|------|------|------|
| **이벤트** | `event_list.php` | 커뮤니티 이벤트 목록 (상태 필터: 진행중/종료/결과발표) |
| | `event_view.php` | 이벤트 상세 (SNS 공유, 관련 이벤트) |
| | `event_view_copy.php` | 이벤트 상세 변형 (참가 신청 버튼) |
| | `global_event_list.php` | 글로벌 이벤트 목록 |
| | `global_event_view.php` | 글로벌 이벤트 상세 |
| **다시보기** | `replay.php` | 영상 목록 (4중 필터: 산업분야/제품군/주제/난이도) |
| | `replay_all.php` | 다시보기 전체 목록 (replay.php와 거의 동일) |
| | `replay_view.php` | 영상 상세 (YouTube 임베드, PDF 다운로드) |
| | `replay_ajax.php` | 영상 필터 AJAX 엔드포인트 |
| **뉴스** | `news_list.php` | 뉴스 목록 (카테고리: 뉴스/업데이트/블로그) |
| | `news_view.php` | 뉴스 상세 (태그, 관련 뉴스) |
| | `news_ajax.php` | 뉴스 필터 AJAX 엔드포인트 |
| **무료콘텐츠** | `free.php` | 무료 리소스 목록 (3중 필터: 산업분야/엔진버전/카테고리) |
| | `free_view.php` | 무료콘텐츠 상세 (YouTube, 외부링크) |
| | `free_ajax.php` | 무료콘텐츠 필터 AJAX 엔드포인트 |
| | `free copy.php` | 백업 파일 (삭제 대상) |
| **백서** | `book.php` | 백서 목록 (1중 필터: 산업분야) |
| | `book_view.php` | 백서 상세 (YouTube, 외부링크) |
| | `book_ajax.php` | 백서 필터 AJAX 엔드포인트 |
| **통합검색** | `total_search.php` | 새소식+이벤트+리소스 통합 검색 |
| | `total_search_ajax_news.php` | 뉴스 검색 AJAX |
| | `total_search_ajax_event.php` | 이벤트 검색 AJAX |
| | `total_search_ajax_rsc.php` | 리소스 검색 AJAX (영상+무료+백서 합산) |
| **정적페이지** | `personal.php` | 개인정보보호정책 |
| | `ode.php` | 이용약관 |

---

### 1.2 현행 DB 스키마

#### 콘텐츠 테이블 (6개)

```
v3_rsc_event_bbs        ← 커뮤니티 이벤트
v3_rsc_global_event_bbs ← 글로벌 이벤트
v3_rsc_news_bbs         ← 뉴스
v3_rsc_review_bbs       ← 다시보기 영상
v3_rsc_free_bbs         ← 무료 콘텐츠
v3_rsc_book_bbs         ← 백서
```

#### 카테고리 테이블 (3개)

```
v3_rsc_review_category  ← 영상 카테고리 (산업분야, 제품군, 주제, 난이도)
v3_rsc_free_category    ← 무료콘텐츠 카테고리 (산업분야, 엔진버전, 카테고리)
v3_rsc_book_category    ← 백서 카테고리 (산업분야)
```

#### 부가 테이블

```
v3_shop_banner          ← 배너 (position별: 다시보기, 무료콘텐츠, 백서)
v3_seo_config           ← SEO/마케팅 설정
v3_main_banner_news     ← 메인 새소식
v3_main_banner_rsc      ← 메인 리소스
```

#### 공통 필드 구조

```sql
-- 모든 콘텐츠 테이블 공통
rsc_bbs_idx     INT AUTO_INCREMENT  -- PK
title           VARCHAR              -- 제목
contents        TEXT                 -- 본문 (HTML)
thumb_img       VARCHAR              -- 썸네일 파일명 (G5_DATA_PATH 기준)
thumb_img_url   VARCHAR              -- 외부 썸네일 URL
top_bbs_img     VARCHAR              -- 상단 배경 이미지
display_yn      CHAR(1)              -- 노출 여부 (Y/N)
ordr            INT                  -- 정렬 순서
reg_date        DATETIME             -- 등록일

-- 이벤트 전용
status          VARCHAR              -- 진행중/종료/결과발표
category        VARCHAR              -- 이벤트 카테고리
sdate, edate    DATE                 -- 시작/종료일
add_btn_yn      CHAR(1)              -- 버튼 노출 여부
add_btn_url     VARCHAR              -- 버튼 링크
doc_file        VARCHAR              -- 첨부파일

-- 리소스(영상/무료/백서) 전용
youtube_url     VARCHAR              -- YouTube URL
pdf_url         VARCHAR              -- PDF 다운로드 링크
site_url        VARCHAR              -- 외부 링크
speker          VARCHAR              -- 발표자
event_title     VARCHAR              -- 관련 이벤트 제목
event_year      VARCHAR              -- 이벤트 연도
tag             VARCHAR              -- 태그 (쉼표 구분)

-- 카테고리 필터 필드
cate_industry   VARCHAR              -- 산업분야
cate_product    VARCHAR              -- 제품군
cate_subject    VARCHAR              -- 주제
cate_difficult  VARCHAR              -- 난이도
cate_engine     VARCHAR              -- 엔진버전
```

---

### 1.3 현행 문제점

| 구분 | 문제 | 영향 |
|------|------|------|
| **코드 중복** | `replay.php`와 `replay_all.php`가 99% 동일 | 유지보수 2배 |
| **코드 중복** | `event_view.php`와 `event_view_copy.php` 거의 동일 | 버튼 텍스트만 다름 |
| **코드 중복** | `event_list`와 `global_event_list` 구조 동일 | 테이블명만 다름 |
| **코드 중복** | `replay`, `free`, `book` 목록 페이지 구조 동일 | 필터 종류만 다름 |
| **보안** | SQL 직접 조합 (`$_GET`, `$_REQUEST` 직접 사용) | SQL Injection 위험 |
| **보안** | AJAX 엔드포인트 요청 검증 없음 (Referer/XHR 미확인) | 외부 직접 호출 가능 |
| **보안** | 사용자 입력값 미이스케이프 출력 | XSS 위험 |
| **구조** | HTML + PHP + JS가 단일 파일에 혼재 | 분리 불가 |
| **구조** | 각 페이지마다 DB 쿼리 직접 작성 | 일관성 없음 |
| **불필요** | `free copy.php` 백업 파일 존재 | 정리 필요 |

---

### 1.4 현행 UI/UX 패턴

모든 리소스 목록 페이지가 공유하는 공통 패턴:

```
┌─────────────────────────────────────┐
│  [배너 슬라이드 (Slick)]            │
├──────────┬──────────────────────────┤
│ 사이드바  │  콘텐츠 영역             │
│          │                          │
│ □ 필터1  │  🔍 키워드 검색           │
│ □ 필터2  │  [리스트뷰] [갤러리뷰]    │
│ □ 필터3  │                          │
│          │  ┌──┐ ┌──┐ ┌──┐         │
│ (sticky) │  │  │ │  │ │  │  카드    │
│          │  └──┘ └──┘ └──┘         │
│          │                          │
│          │  [더보기] 버튼            │
└──────────┴──────────────────────────┘
```

---

## 2. 리뉴얼 설계

### 2.1 파일 정리 계획

#### 안전한 중복 제거 (같은 테이블, 미세한 차이만 있는 것)

| 현행 | 리뉴얼 | 사유 |
|------|--------|------|
| `replay.php` + `replay_all.php` | → `replay_list.php` 1개 | 99% 동일, 라벨만 다름. 분리 유지 시 한쪽만 수정하는 실수 위험 |
| `event_view.php` + `event_view_copy.php` | → `event_view.php` 1개 | 버튼 텍스트 1줄만 다름. DB 필드로 분기 |
| `free.php` + `free copy.php` | → `free_list.php` 1개 | `free copy.php`는 백업 파일, 삭제 |

#### 분리 유지 (DB 테이블이 다른 것)

| 파일 | 유지 이유 |
|------|-----------|
| `event_list.php` ≠ `global_event_list.php` | 서로 다른 테이블 조회. 통합 시 조건문 버그 위험 |
| `event_view.php` ≠ `global_event_view.php` | 동일. 데이터 소스가 다르면 분리가 안전 |

#### AJAX 파일 분리 유지 (장애 격리)

AJAX 파일은 **통합하지 않고 파일별 분리를 유지**한다.
하나의 파일에 버그가 생겨도 다른 기능에 영향을 주지 않는 **장애 격리** 원칙.

```
replay_ajax.php 에 버그 → 영상만 안됨, 뉴스/백서는 정상
news_ajax.php 에 버그   → 뉴스만 안됨, 나머지 정상
```

### 2.2 리뉴얼 파일 구조

기존 레거시 파일을 덮어쓰지 않도록, 리뉴얼 파일은 **`contents/v4/`** 디렉토리에 별도 생성한다.
기존 사이트는 `contents/*.php`로 그대로 운영하면서, 리뉴얼은 `contents/v4/`에서 독립적으로 테스트한다.

```
contents/                        ← 기존 레거시 (라이브 운영, 손대지 않음)
├── event_list.php
├── event_view.php
├── replay.php
├── ...
│
contents/v4/                     ← 리뉴얼 신규 (테스트 → 검증 후 교체)
│
├── event_list.php               # 이벤트 목록 (커뮤니티+글로벌 탭 통합)
├── event_view.php               # 이벤트 상세 (커뮤니티+글로벌 type 파라미터 통합)
│
├── replay_list.php              # 영상 목록 (replay + replay_all 통합)
├── replay_view.php              # 영상 상세
│
├── news_list.php                # 뉴스 목록
├── news_view.php                # 뉴스 상세
│
├── free_list.php                # 무료콘텐츠 목록
├── free_view.php                # 무료콘텐츠 상세
│
├── book_list.php                # 백서 목록
├── book_view.php                # 백서 상세
│
├── total_search.php             # 통합 검색 (5개 테이블 동시)
│
├── personal.php                 # 개인정보보호정책
├── ode.php                      # 이용약관
│
└── ajax/                        # AJAX 엔드포인트 (파일별 분리 유지)
    ├── event.ajax.php           # 이벤트 필터 (커뮤니티+글로벌 분기)
    ├── replay.ajax.php          # 영상 필터
    ├── news.ajax.php            # 뉴스 필터
    ├── free.ajax.php            # 무료콘텐츠 필터
    ├── book.ajax.php            # 백서 필터
    └── search.ajax.php          # 통합 검색 (5개 테이블 동시)
```

#### 운영 전환 절차

```
1. contents/v4/ 에서 개발 및 테스트
2. 테스트 완료 후:
   contents/        → contents/legacy/  (기존 백업)
   contents/v4/     → contents/         (신규로 교체)
3. 문제 발생 시:
   contents/        → contents/v4/      (신규 롤백)
   contents/legacy/ → contents/         (기존 복원)
```

### 2.3 공통 컴포넌트 분리

현행 각 페이지에 중복되는 코드를 공통 모듈로 추출:

```
inc/
├── v4_helpers.php             # v4 전용 안전 래퍼 (v4_int, v4_str, v4_limit, v4_ajax_guard 등)
├── v4_cards.php               # 카드 렌더링 (render_event_card, render_resource_card)
├── components/
│   ├── card_list.php          # 카드 리스트 렌더링 (리스트뷰/갤러리뷰)
│   ├── sidebar_filter.php     # 사이드바 필터 (체크박스 필터 공통)
│   ├── banner_slide.php       # 배너 슬라이드 (position 파라미터)
│   ├── social_share.php       # SNS 공유 버튼 (Twitter, Facebook, Link)
│   ├── related_items.php      # 관련 콘텐츠 3개 표시
│   ├── pagination.php         # 더보기 버튼 / 무한 스크롤
│   └── search_bar.php         # 키워드 검색바
```

#### 컴포넌트 인터페이스 명세

**`sidebar_filter.php`**
```php
/**
 * 사이드바 필터 컴포넌트
 * @param array $filter_groups — 필터 그룹 배열
 *   [
 *     ['name' => 'cate_industry', 'label' => '산업분야', 'table' => 'v3_rsc_review_category', 'field' => 'cate_industry'],
 *     ['name' => 'cate_product',  'label' => '제품군',   'table' => 'v3_rsc_review_category', 'field' => 'cate_product'],
 *   ]
 * @param string $form_id — 폼 ID (AJAX serialize용)
 * @param string $ajax_url — AJAX 엔드포인트 URL
 */
function render_sidebar_filter($filter_groups, $form_id, $ajax_url) {
    // 각 그룹별로 DB에서 카테고리 옵션 조회 → 체크박스 렌더링
}
```

**`card_list.php`**
```php
/**
 * 카드 리스트 렌더링 컴포넌트
 * @param array $items — 콘텐츠 배열 (sql_fetch_array 결과)
 * @param string $type — 콘텐츠 타입 ('event'|'replay'|'news'|'free'|'book')
 * @param string $view_url — 상세 페이지 URL 패턴
 * @param bool $show_view_toggle — 리스트뷰/갤러리뷰 전환 버튼 표시 여부
 */
function render_card_list($items, $type, $view_url, $show_view_toggle = true) {
    // 카드 그리드 렌더링
    // 이벤트: 상태 배지 추가, 뉴스: 상대 시간 추가, 리소스: 카테고리 태그 추가
}
```

**`related_items.php`**
```php
/**
 * 관련 콘텐츠 3개 표시
 * @param string $table — DB 테이블명
 * @param int $current_idx — 현재 게시물 idx (제외용)
 * @param string $view_url — 상세 페이지 URL 패턴
 * @param int $count — 표시 개수 (기본 3)
 */
function render_related_items($table, $current_idx, $view_url, $count = 3) {
    $current_idx = v4_int($current_idx);
    $sql = "SELECT * FROM {$table} WHERE display_yn='Y'
            AND rsc_bbs_idx <> {$current_idx}
            ORDER BY ABS({$current_idx} - rsc_bbs_idx) LIMIT {$count}";
    // 관련 카드 렌더링
}
```

---

### 2.4 보안 개선

> **핵심 원칙**: GNU Board 5 내장 DB 함수(`sql_query()`, `sql_fetch()`, `sql_real_escape_string()`)를 그대로 사용하되, v4 전용 안전 래퍼(`inc/v4_helpers.php`)를 추가하여 일관성을 확보한다. PDO는 GNU Board와 호환 불가하므로 사용하지 않는다.

```php
// ❌ 현행 (위험)
$keyword = $_REQUEST["keyword"];
$sql = "SELECT * FROM v3_rsc_news_bbs WHERE title LIKE '%{$keyword}%'";

// ✅ 리뉴얼 (GNU Board 내장 함수 + 안전 래퍼)
$keyword = v4_str($_REQUEST["keyword"] ?? '');
$result = sql_fetch("SELECT * FROM v3_rsc_news_bbs
    WHERE title LIKE '%{$keyword}%' AND display_yn = 'Y'");
```

#### AJAX JSON 응답 — `json_encode()` 사용

현행 AJAX 파일들은 PHP echo로 JSON을 수동 생성(XSS 취약). `json_encode()` 사용으로 교체:

```php
// ❌ 현행 (replay_ajax.php — XSS 취약)
?>{"data":[<? for ($j=0; $row=sql_fetch_array($result); $j++) { ?>
    {"title":"<?=$title?>","link":"<?=$link?>"}
<? } ?>]}

// ✅ 리뉴얼
header('Content-Type: application/json; charset=utf-8');
$data = [];
while ($row = sql_fetch_array($result)) {
    $data[] = [
        'id'    => (int)$row['rsc_bbs_idx'],
        'title' => htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'),
        'link'  => $link,
        'image' => $bimg_str,
        'category' => array_filter([
            $row['cate_industry'], $row['cate_product'],
            $row['cate_subject'], $row['cate_difficult']
        ])
    ];
}
echo json_encode(['data' => $data], JSON_UNESCAPED_UNICODE);
exit;
```

| 항목 | 현행 | 리뉴얼 |
|------|------|--------|
| SQL | 직접 조합 | GNU Board `sql_*` 함수 + `v4_helpers.php` 래퍼 |
| 입력 검증 | 없음 | `v4_str()` / `v4_int()` + whitelist |
| XSS | 미이스케이프 | `get_text()` (기존) + `htmlspecialchars()` 적용 |
| CSRF | 없음 | 읽기전용 AJAX는 Referer+XHR 검증, 상태변경만 `get_token()`/`check_token()` |
| AJAX 응답 | PHP echo로 JSON 수동 생성 | `json_encode()` 사용 |

---

## 3. DB 스키마 고려사항

### 3.1 현행 유지 (adm과 공유)

리뉴얼 범위가 `contents/` 프론트엔드이므로, **기존 DB 테이블은 그대로 유지**한다.
`adm/`에서 데이터를 입력하고 `contents/`에서 읽기만 하는 구조이기 때문.

```
adm/ (유지) ──[INSERT/UPDATE]──→ DB ←──[SELECT]── contents/ (리뉴얼)
```

### 3.2 통합 가능한 테이블

리뉴얼 이후 단계에서 검토할 사항:

| 현행 | 통합 방향 |
|------|-----------|
| `v3_rsc_event_bbs` + `v3_rsc_global_event_bbs` | `event_type` 필드 추가로 단일 테이블 가능 |
| `v3_rsc_review_category` + `v3_rsc_free_category` + `v3_rsc_book_category` | 통합 카테고리 테이블 가능 |

단, **adm 수정 없이 진행하므로 현 단계에서는 테이블 변경 없음.**

---

## 4. 기준 구조: `index_test.php` (신규 메인 페이지)

`index_test.php`는 이미 리뉴얼된 새 기준 구조를 가지고 있다.
**`contents/v4/` 페이지들은 이 구조를 따라야 한다.**

### 4.1 index_test.php (신규) vs contents/*.php (레거시) 비교

| 구분 | `index_test.php` (신규 기준) | `contents/*.php` (레거시) |
|------|------------------------------|--------------------------|
| **헤더** | `inc/common_header26.php` | `inc/common_header.php` |
| **로고** | `logo_new.svg` | `logo.svg` |
| **다크모드** | 있음 (테마 토글 버튼) | 없음 |
| **CSS** | `main26.css`, `common26.css` | `sub.css` |
| **JS** | `common26.js`, `main26.js` → v4: **`v4.app.js`** | `common.js`, `sub.js` |
| **SEO** | DB 동적 (`v3_seo_config` 테이블) | 하드코딩 (2023년 OG 태그 그대로) |
| **마케팅** | `inc/marketing_head.php` 중앙 관리 | 각 파일마다 GA/Pixel 인라인 복붙 |
| **슬라이더** | Swiper 11 (CDN) + Slick | Slick만 |
| **애니메이션** | GSAP 3.12.2 (CDN) + SplitText | 없음 (기본 ScrollTrigger만) |
| **GA** | GTM 기반 동적 (`seo_gtm_id`) | UA 직접 하드코딩 (서비스 종료됨) |
| **푸터** | `inc/common_footer.php` | `inc/common_footer.php` (동일) |
| **히어로 DB** | `v3_visual_main` (동적 슬라이드) | 없음 |

### 4.2 v4 페이지가 따라야 할 기준 (index_test.php 기반)

```php
<?php
// 1. 공통 초기화
include_once('../_common.php');

// 2. SEO 동적 로딩 (페이지별)
$v3_seo = sql_fetch("SELECT * FROM v3_seo_config WHERE seo_page = '{페이지명}'");
// fallback: 'default' 설정 사용
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

  <!-- 3. 마케팅 트래킹 (중앙 관리) -->
  <?php include '../inc/marketing_head.php'; ?>

  <!-- 4. 파비콘 -->
  <!-- (공통 include로 분리 예정) -->

  <!-- 5. CSS: 26 버전 사용 -->
  <link rel="stylesheet" href="/v3/resource/css/common26.css">
  <link rel="stylesheet" href="/v3/resource/css/{페이지}.css">

  <!-- 6. JS: v4 통합 스크립트 -->
  <script src="/v3/resource/js/jquery-3.4.1.min.js"></script>
  <script src="/v3/resource/js/slick.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script src="/v3/resource/js/ScrollTrigger.min.js"></script>
  <script src="/v3/resource/js/jquery.menu.min.js"></script>
  <script src="/v3/resource/js/jquery.responsive.min.js"></script>
  <script src="/v3/resource/js/v4.app.js"></script>  <!-- common26 + sub 통합 -->

  <title><?php echo get_text($seo_title); ?></title>
</head>
<body>
  <?php include '../inc/marketing_body.php'; ?>

  <!-- 7. 헤더: 26 버전 (다크모드 포함) -->
  <?php include '../inc/common_header26.php'; ?>

  <!-- 8. 콘텐츠 영역 -->
  <div class="container">
    <!-- 페이지 본문 -->
  </div>

  <!-- 9. 푸터 -->
  <?php include '../inc/common_footer.php'; ?>
</body>
</html>
```

### 4.3 레거시에서 반드시 제거해야 할 것

| 레거시 코드 | 제거 이유 |
|-------------|-----------|
| 인라인 GA UA 코드 (`UA-174668456-1`) | Google Universal Analytics 서비스 종료 |
| 인라인 Google Ads 코드 | `marketing_head.php`로 중앙 관리 |
| 인라인 Facebook Pixel 코드 | `marketing_head.php`로 중앙 관리 |
| 하드코딩된 OG 태그 (2023년) | `v3_seo_config` DB 동적 로딩으로 교체 |
| `common.js`, `sub.js` 참조 | `v4.app.js`로 교체 (common26 + sub 통합, 충돌 제거) |
| `sub.css` 참조 | `common26.css` + 페이지별 CSS로 교체 |
| `common_header.php` 참조 | `common_header26.php`로 교체 |

### 4.4 추가 DB 테이블 (index_test.php에서 확인)

```
v3_visual_main          ← 메인 히어로 슬라이드 (신규)
  vm_id                 -- PK
  vm_display            -- 노출 여부 (1/0)
  vm_order              -- 정렬 순서
  vm_bg_type            -- 배경 타입 (video/image)
  vm_bg_url             -- 배경 미디어 URL
  vm_title_img          -- 타이틀 이미지 URL
  vm_link_url           -- 버튼 링크
  vm_btn_text           -- 버튼 텍스트
  vm_duration           -- 자동재생 시간 (ms)

v3_seo_config           ← SEO/마케팅 설정 (페이지별)
  seo_page              -- 페이지 식별자 (default, event_list, ...)
  seo_title             -- 페이지 타이틀
  seo_description       -- 메타 설명
  seo_keywords          -- 메타 키워드
  seo_og_image          -- OG 이미지 URL
  seo_ga_id             -- Google Analytics ID
  seo_gtm_id            -- Google Tag Manager ID
  seo_pixel_id          -- Facebook Pixel ID
  seo_kakao_pixel_id    -- 카카오 Pixel ID
  seo_naver_verif       -- 네이버 사이트 인증
  seo_google_verif      -- 구글 사이트 인증
  seo_extra_head        -- 추가 head 코드
  seo_extra_body        -- 추가 body 코드
```

### 4.5 JS 통합: `common26.js` + `sub.js` → `v4.app.js`

`common26.js`와 `sub.js`를 동시 로드하면 **충돌이 발생**한다.
v4에서는 두 파일을 정리/병합하여 `v4.app.js` 단일 파일로 통합한다.

#### 충돌 현황

| 심각도 | 문제 | 위치 |
|--------|------|------|
| CRITICAL | `tab_layout` 초기화 2중 실행 | common26.js 내부에서 224-250줄, 256-285줄 중복 |
| CRITICAL | `lastScroll` 전역 변수 양쪽 동일 선언 | common26.js:419, sub.js:121 |
| WARNING | `$(window).scroll()` 2중 등록 | common26.js:420, sub.js:122 |
| WARNING | `$(document).ready()` 총 6개 산발 | 양쪽 곳곳 |

#### 통합 계획: `v4.app.js`

```
resource/js/v4.app.js (신규 생성)
│
├── [common26.js에서 가져올 것]
│   ├── web_menu() / mobile_menu()    ← 헤더 네비게이션
│   ├── 모바일 메뉴 열기/닫기          ← lnb 슬라이드
│   ├── tab_layout 초기화             ← 중복 제거, 1개만 유지
│   ├── PopupZone 플러그인            ← 팝업 롤링
│   ├── Theme Toggle (다크모드)       ← localStorage 연동
│   └── body_active 클래스 추가
│
├── [sub.js에서 가져올 것]
│   ├── SNS 공유 (.snsbox 토글)       ← 상세 페이지에서 사용
│   ├── naverSns() / facebookSns()    ← SNS 공유 함수
│   ├── 아코디언 (.acc_toggle)        ← FAQ 등에서 사용
│   └── body .scroll 클래스 추가      ← 스크롤 감지
│
├── [제거할 것]
│   ├── tab_layout 중복 (256-285줄)   ← common26.js 자체 버그
│   ├── lastScroll 중복 선언          ← 1개로 통합
│   ├── $(window).scroll() 중복       ← 1개로 합침
│   ├── GetIEVersion() / checkIE()    ← IE 지원 종료, 불필요
│   ├── 이미지 롤오버 (.overimg)       ← CSS :hover로 대체 가능
│   └── .step_type_1 높이 균일화      ← CSS flexbox로 대체 가능
│
└── [개선 사항]
    ├── .bind() → .on() 통일
    ├── 전역 변수 → IIFE 스코프로 격리
    └── $(document).ready() 산발 → 1개로 통합
```

#### v4.app.js 기본 구조

```js
/**
 * Epic Lounge v4 - App Script
 * common26.js + sub.js 통합 (충돌 제거)
 */
(function($) {
  'use strict';

  // ── 헤더 메뉴 ──
  function web_menu() { /* ... */ }
  function mobile_menu() { /* ... */ }

  // ── 모바일 네비 ──
  function initMobileNav() { /* ... */ }

  // ── 탭 레이아웃 (1개만) ──
  function initTabLayout() { /* ... */ }

  // ── SNS 공유 (sub.js에서 이동) ──
  function initSnsShare() { /* ... */ }

  // ── 아코디언 (sub.js에서 이동) ──
  function initAccordion() { /* ... */ }

  // ── 스크롤 감지 (통합, 1개) ──
  function initScrollDetect() {
    var lastScroll = 0; // 스코프 내 변수 (전역 아님)
    $(window).on('scroll', function() {
      var scroll = $(this).scrollTop();
      $('body').toggleClass('scroll', scroll > 100);
      $('#right_bar').toggleClass('fixed', scroll > 100);
      lastScroll = scroll;
    });
  }

  // ── 다크모드 ──
  function initThemeToggle() { /* ... */ }

  // ── 팝업존 ──
  $.fn.PopupZone = function(options) { /* ... */ };

  // ── 초기화 (1개의 ready) ──
  $(function() {
    if ($(window).width() > 1000) { web_menu(); } else { mobile_menu(); }
    $(window).on('resize', function() {
      if ($(window).width() > 1000) { web_menu(); } else { mobile_menu(); }
    });

    initMobileNav();
    initTabLayout();
    initSnsShare();
    initAccordion();
    initScrollDetect();
    initThemeToggle();

    $('html').addClass('body_active');
    $('body').addClass('active');
  });

})(jQuery);

// 전역 함수 (외부 호출용)
function naverSns(title, url) { /* ... */ }
function facebookSns(title, url) { /* ... */ }
```

#### 레거시 JS 파일 처리

| 파일 | v4 이후 |
|------|---------|
| `common.js` | 레거시 전용, v4 미사용 |
| `common26.js` | 레거시 전용, v4 미사용 |
| `sub.js` | 레거시 전용, v4 미사용 |
| `main26.js` | 메인 페이지 전용, 유지 |
| **`v4.app.js`** | **v4 전 페이지 공통 (신규)** |

### 4.6 CSS 구조 리뉴얼

> **참고**: `common26.css` (26.7K)는 헤더/네비/푸터 전용 (95% 이상). 서브페이지 카드, 필터, 사이드바, 갤러리 등의 스타일은 없으므로 `pages/*.css`에서 신규 작성이 필요하다.

```
resource/css/
├── common26.css         # 공통 레이아웃/컴포넌트 (기존, 26 버전 — 헤더/네비/푸터)
├── main26.css           # 메인 페이지 전용 (기존)
├── pages/
│   ├── list.css         # 목록 공통 (카드 그리드, 사이드바 필터, 뷰 전환, 더보기)
│   ├── detail.css       # 상세 공통 (히어로 이미지, 본문, SNS 공유, 관련 콘텐츠)
│   └── search.css       # 검색 (탭, 하이라이팅, 결과 카드)
└── sub.css              # 레거시 (v4 완료 후 제거 예정)
```

#### CSS 작성 시 준수 규칙

```css
/* 1. 기존 브레이크포인트 그대로 사용 (common26.css와 동일) */
@media (max-width: 1000px) { /* 태블릿 진입점 */ }
@media (max-width: 640px)  { /* 모바일 */ }
/* 기타: 1560px / 1200px / 1100px / 800px */

/* 2. 다크모드 — 기존 패턴 따르기 (body.dark-theme 셀렉터) */
body.dark-theme .v4-card {
    background: rgba(255,255,255,0.05);  /* common26.css 패턴 */
    color: #ffffff;
}

/* 3. 색상 팔레트 — common26.css에서 가져오기 */
:root {
    --v4-primary: #33aeec;
    --v4-dark-bg: #101014;
    --v4-accent: #ffd700;
    --v4-text-secondary: #a0a0a0;
}

/* 4. 컨테이너 — 기존 .wrap 클래스 재사용 (1240px) */
```

---

## 5. 페이지별 리뉴얼 상세

### 5.1 이벤트 목록 (`event_list.php`, `global_event_list.php`)

**각각 분리 유지** (DB 테이블이 다르므로)

- `event_list.php` → `v3_rsc_event_bbs` (커뮤니티)
- `global_event_list.php` → `v3_rsc_global_event_bbs` (글로벌)

**기능**:
- 상태 필터: 전체, 진행중, 종료, 결과발표
- 카드 레이아웃 (썸네일 + 제목 + 기간 + 상태 배지)

### 5.2 이벤트 상세 (`event_view.php`, `global_event_view.php`)

- `event_view.php`: `event_view_copy.php`를 흡수 (버튼 텍스트는 DB 필드 기반 분기)
- `global_event_view.php`: 분리 유지

**기능**:
- 상단 배경 이미지 (`top_bbs_img`)
- 본문 콘텐츠 + 첨부파일 다운로드
- 액션 버튼 (DB의 `add_btn_yn`, `add_btn_url`에 따라 동적 렌더링)
- SNS 공유 (Twitter, Facebook, 링크 복사)
- 관련 이벤트 3개

### 5.3 영상 목록 (`replay_list.php`)

**통합 대상**: `replay.php` + `replay_all.php` (99% 동일, 같은 테이블)

**기능**:
- 사이드바 필터 (산업분야, 제품군, 주제, 난이도) → `sidebar_filter.php` 컴포넌트
- 키워드 검색
- 리스트뷰/갤러리뷰 전환
- 더보기 무한 스크롤 → `ajax/replay.ajax.php`
- 상단 배너 슬라이드 → `banner_slide.php` 컴포넌트

### 5.4 영상 상세 (`replay_view.php`)

**기능**:
- YouTube 임베드 플레이어
- 발표자, 이벤트 정보
- PDF 자료 다운로드
- SNS 공유
- 관련 콘텐츠

### 5.5 뉴스 목록/상세 (`news_list.php`, `news_view.php`)

**기능**:
- 카테고리 필터 (뉴스, 업데이트/출시, 블로그)
- 상대 시간 표시 (방금전, X분전, X시간전...)
- 더보기 무한 스크롤 → `ajax/news.ajax.php`
- 태그 표시, 관련 뉴스

### 5.6 무료콘텐츠 (`free_list.php`, `free_view.php`)

**기능**:
- 사이드바 필터 (산업분야, 엔진버전, 카테고리)
- 리스트뷰/갤러리뷰 전환
- 더보기 → `ajax/free.ajax.php`
- YouTube 임베드, 외부 링크

### 5.7 백서 (`book_list.php`, `book_view.php`)

**기능**:
- 사이드바 필터 (산업분야만)
- 리스트뷰/갤러리뷰 전환
- 더보기 → `ajax/book.ajax.php`
- YouTube 임베드, 외부 링크

### 5.8 통합 검색 (`search.php`)

**기능**:
- 3개 섹션: 새소식, 이벤트, 리소스
- 키워드 하이라이팅
- 섹션별 더보기 → `ajax/search_*.ajax.php`
- 총 검색 건수 표시

### 5.9 정적 페이지

- `personal.php`: 개인정보보호정책 (내용 업데이트 필요 - 현재 2022.04.03 기준)
- `ode.php`: 이용약관

---

## 6. 삭제 대상

| 파일 | 사유 |
|------|------|
| `event_view_copy.php` | `event_view.php`로 흡수 (버튼 텍스트만 다름) |
| `replay_all.php` | `replay_list.php`로 통합 (99% 동일) |
| `replay.php` | `replay_list.php`로 대체 |
| `free copy.php` | 백업 파일 삭제 |
| `free.php` | `free_list.php`로 대체 (네이밍 정리) |
| `book.php` | `book_list.php`로 대체 (네이밍 정리) |
| `total_search.php` | `search.php`로 대체 (네이밍 정리) |

AJAX 파일은 삭제하지 않고 `ajax/` 디렉토리로 이동 + 네이밍 정리:

| 현행 | 이동 후 |
|------|---------|
| `replay_ajax.php` | `ajax/replay.ajax.php` |
| `news_ajax.php` | `ajax/news.ajax.php` |
| `free_ajax.php` | `ajax/free.ajax.php` |
| `book_ajax.php` | `ajax/book.ajax.php` |
| `total_search_ajax_news.php` | `ajax/search_news.ajax.php` |
| `total_search_ajax_event.php` | `ajax/search_event.ajax.php` |
| `total_search_ajax_rsc.php` | `ajax/search_rsc.ajax.php` |

---

## 7. Playwright MCP 활용 전략

개발 전 과정에서 **Playwright MCP**를 활용하여 테스트를 자동화하고, 레거시 ↔ v4 시각적 비교를 수행한다.

### 7.1 레거시 페이지 기준선 캡처 (Phase 1에서 수행)

개발 시작 전, 현행 레거시 페이지의 스크린샷을 캡처하여 리뉴얼 참조 자료로 보존한다.

```
캡처 대상 (운영 URL 기준):
├── 목록 페이지 (6개)
│   ├── /contents/event_list.php
│   ├── /contents/global_event_list.php
│   ├── /contents/replay.php
│   ├── /contents/news_list.php
│   ├── /contents/free.php
│   └── /contents/book.php
│
├── 상세 페이지 (6개, 샘플 idx 지정)
│   ├── /contents/event_view.php?idx={sample}
│   ├── /contents/global_event_view.php?idx={sample}
│   ├── /contents/replay_view.php?idx={sample}
│   ├── /contents/news_view.php?idx={sample}
│   ├── /contents/free_view.php?idx={sample}
│   └── /contents/book_view.php?idx={sample}
│
└── 기타 (2개)
    ├── /contents/total_search.php?keyword=테스트
    └── /contents/personal.php
```

**캡처 해상도** (각 페이지마다 3장):
- Desktop: 1440×900
- Tablet: 768×1024
- Mobile: 375×812

**저장 위치**: `_test/screenshots/legacy/`

### 7.2 개발 중 비교 테스트 (Phase 2~5에서 수행)

각 v4 페이지 완성 시, 동일 URL의 레거시 스크린샷과 나란히 비교한다.

```
Playwright 흐름:
1. browser_navigate → v4 페이지 접속
2. browser_resize → 해상도 설정 (1440, 768, 375)
3. browser_take_screenshot → v4 스크린샷 저장
4. 레거시 스크린샷과 시각적 비교 (누락된 요소, 레이아웃 깨짐 확인)
```

**저장 위치**: `_test/screenshots/v4/`

### 7.3 기능 테스트 자동화 (Phase 6에서 수행)

Playwright로 사용자 인터랙션을 자동 시뮬레이션:

| 테스트 시나리오 | Playwright 동작 |
|----------------|----------------|
| **필터 선택** | `browser_snapshot` → 필터 체크박스 `ref` 확인 → `browser_click` → 결과 변경 확인 |
| **더보기 클릭** | 더보기 버튼 `browser_click` → `browser_wait_for` (새 카드 로딩) → 카드 수 증가 확인 |
| **키워드 검색** | 검색바 `browser_type` → submit → 결과 페이지 `browser_snapshot` 확인 |
| **뷰 전환** | 갤러리뷰 버튼 `browser_click` → DOM 클래스 변경 확인 |
| **SNS 공유** | 공유 버튼 `browser_click` → 공유 팝업/모달 노출 확인 |
| **다크모드** | 테마 토글 `browser_click` → `browser_take_screenshot` → 스타일 변경 확인 |
| **AJAX 응답** | `browser_network_requests` → AJAX 호출 상태코드 200 확인 |
| **콘솔 에러** | `browser_console_messages(level: "error")` → JS 에러 없는지 확인 |

### 7.4 반응형 + 크로스 브라우저 테스트 (Phase 6에서 수행)

```
반응형 자동 캡처 루프:
for each page in v4_pages:
  for each size in [1920x1080, 1440x900, 1280x720, 1024x768, 768x1024, 375x812]:
    browser_navigate(page)
    browser_resize(size)
    browser_take_screenshot(filename: "{page}_{size}.png")
```

Playwright는 Chromium/Firefox/WebKit 엔진을 지원하므로 크로스 브라우저 테스트도 가능.

### 7.5 운영 전환 후 검증 (Phase 7에서 수행)

전환 직후, 전 페이지를 자동으로 순회하며 확인:

```
운영 검증 자동화:
1. 전 페이지 접속 → HTTP 상태 확인 (browser_network_requests)
2. 콘솔 에러 확인 (browser_console_messages level: "error")
3. 주요 요소 존재 확인 (browser_snapshot → 헤더/푸터/콘텐츠 영역)
4. 스크린샷 캡처 → v4 개발 시 캡처본과 비교 (전환 과정에서 깨진 것 없는지)
```

---

## 8. 모델 활용 전략 (Opus / Sonnet / Haiku)

> **핵심 원칙**: Opus가 패턴을 만들고, Sonnet이 패턴을 반복하고, Haiku가 단순 작업을 처리한다.

### 8.1 실제 워크플로우 (Claude Code 세션 기준)

```
[메인 세션: Opus]
├── 설계 판단 + 핵심 로직 직접 작성
├── 디버깅 + 문제 해결
└── 서브에이전트 지시 + 결과 검증

[Task 서브에이전트: Sonnet] — 병렬 실행
├── 패턴 확립 후 나머지 페이지 생성
├── CSS 작성
├── 공통 컴포넌트 구현
└── Playwright 테스트 실행

[Task 서브에이전트: Haiku] — 병렬 실행
├── 파일 탐색/검색
├── 스크린샷 캡처 (Playwright)
└── 단순 복제 (테이블명만 변경)
```

### 8.2 Phase별 실행 예시

```
Phase 2 실행 흐름:
1. Opus: event_list.php 직접 작성 (패턴 확립)
2. Opus → Task(Sonnet) 3개 병렬 실행:
   - Agent A: replay_list.php + news_list.php 작성
   - Agent B: free_list.php + book_list.php 작성
   - Agent C: list.css 작성
3. Opus → Task(Haiku): global_event_list.php 복제 (테이블명만 변경)
4. Opus: 결과 검증 + 수정

Phase 4 실행 흐름:
1. Opus: replay.ajax.php 직접 작성 (AJAX 패턴 확립)
2. Opus → Task(Sonnet) 2개 병렬 실행:
   - Agent A: news.ajax.php + free.ajax.php + book.ajax.php 작성
   - Agent B: search_news.ajax.php + search_event.ajax.php + search_rsc.ajax.php 작성
3. Opus: 결과 검증 + 보안 점검
```

### 8.3 모델별 역할 상세

| 모델 | 비중 | 역할 | 작업 예시 |
|------|------|------|-----------|
| **Opus** (메인) | ~20% | 설계, 핵심 로직, 디버깅 | `v4.app.js` 통합, `v4_helpers.php` 설계, 패턴 확립 페이지, `search.php` |
| **Sonnet** (Task) | ~60% | 패턴 기반 개발, 테스트 | 패턴 따라가기 페이지, CSS, 컴포넌트 구현, Playwright 테스트 |
| **Haiku** (Task) | ~20% | 검색, 캡처, 단순 복제 | 파일 탐색, 스크린샷, 테이블명만 변경하는 복제 |

### 8.4 Phase별 모델 배분

```
Phase 1 (기반 구축)
├── Opus   → v4.app.js 설계 + 작성 (핵심)
├── Opus   → v4_helpers.php + 컴포넌트 인터페이스 설계
├── Sonnet → Task: 설계 확정 후 컴포넌트 PHP 작성 + CSS
└── Haiku  → Task: 레거시 스크린샷 캡처

Phase 2 (목록 페이지)
├── Opus   → event_list.php 직접 작성 ← 패턴 확립
├── Sonnet → Task 병렬: replay_list + news_list / free_list + book_list / list.css
├── Haiku  → Task: global_event_list.php 복제 (테이블명만 변경)
└── Opus   → 결과 검증 + 수정

Phase 3 (상세 페이지)
├── Opus   → event_view.php 직접 작성 ← 패턴 확립
├── Sonnet → Task 병렬: replay_view + news_view / free_view + book_view / detail.css
├── Haiku  → Task: global_event_view.php 복제
└── Opus   → 결과 검증 + 수정

Phase 4 (AJAX 엔드포인트)
├── Opus   → replay.ajax.php 직접 작성 ← AJAX 패턴 확립
├── Sonnet → Task 병렬: 나머지 AJAX 6개 작성
└── Opus   → 결과 검증 + 보안 점검

Phase 5 (검색 + 정적)
├── Opus   → search.php (3테이블 합산 복합 로직)
├── Sonnet → Task: personal.php + ode.php + search.css
└── Opus   → 결과 검증

Phase 6 (테스트) — 🔁 Ralph Loop
└── Ralph Loop → 테스트 → 버그 수정 → 재테스트 반복 (max 15회, 전부 통과 시 자동 종료)

Phase 7 (전환)
├── Sonnet → Task: Playwright 운영 검증 자동 순회
└── Opus   → 문제 발생 시 원인 분석 + 수정
```

---

## 9. 작업 순서 (상세)

> **진행 표시**: ⬜ 대기 / 🔄 진행중 / ✅ 완료

---

### Phase 1: 기반 구축

#### 1-1. 디렉토리 구조 생성 `[Haiku]`
- ✅ `contents/v4/` 디렉토리 생성
- ✅ `contents/v4/ajax/` 디렉토리 생성
- ✅ `inc/components/` 디렉토리 생성
- ✅ `resource/css/pages/` 디렉토리 생성
- ✅ `_test/screenshots/legacy/` 디렉토리 생성 (Playwright 캡처용)
- ✅ `_test/screenshots/v4/` 디렉토리 생성 (Playwright 캡처용)

#### 1-0. 🎭 Playwright - 레거시 페이지 기준선 캡처 `[Haiku]`
> Phase 1 시작 시 가장 먼저 수행. 리뉴얼 전 현행 페이지의 스크린샷을 보존한다.
- ✅ 목록 페이지 6개 스크린샷 캡처 (Desktop 1440px / Tablet 768px / Mobile 375px)
  - `event_list`, `global_event_list`, `replay`, `news_list`, `free`, `book` — 각 3해상도 × 6 = 18장
- ✅ 상세 페이지 스크린샷 캡처 (샘플 idx 지정)
  - `event_view`(idx=326), `global_event_view`(idx=222), `replay_view`(idx=1406) — 각 3해상도 × 3 = 9장
  - ⚠️ `news_view`, `free_view`, `book_view` — 레거시에 상세 페이지 없음 (외부 링크/PDF로 연결)
- ✅ 통합 검색 + 정적 페이지 스크린샷 캡처
  - `total_search` (키워드 "언리얼"), `personal` — 각 3해상도 × 2 = 6장
- ✅ `_test/screenshots/legacy/` 에 총 33장 저장 완료

#### 1-2. `v4.app.js` 생성 (common26.js + sub.js 통합) `[Opus]`
- ✅ `common26.js` 분석 → 유지할 함수/제거할 함수 목록 확정
- ✅ `sub.js` 분석 → 유지할 함수/제거할 함수 목록 확정
- ✅ IIFE 래퍼 + `'use strict'` 기본 골격 작성
- ✅ 헤더 메뉴 이식 (`web_menu`, `mobile_menu`, 모바일 네비)
- ✅ `tab_layout` 중복 제거 후 1개로 통합 이식
- ✅ 스크롤 감지 통합 (`lastScroll` 전역변수 → 스코프 내 변수)
- ✅ SNS 공유 이식 (`.snsbox` 토글, `naverSns`, `facebookSns`)
- ✅ 아코디언 이식 (`.acc_toggle`)
- ✅ 다크모드 테마 토글 이식 (`localStorage` 연동)
- ✅ `PopupZone` 플러그인 이식
- ✅ IE 관련 코드 제거 (`GetIEVersion`, `checkIE`)
- ✅ `.bind()` → `.on()` 전환, `$(document).ready()` 1개로 통합
- ⬜ 동작 테스트 (헤더 메뉴, 다크모드, SNS 공유, 스크롤)

#### 1-3. 공통 컴포넌트 생성 (`inc/components/`) `[Opus → Sonnet]`
> Opus가 `sidebar_filter.php` 인터페이스 설계 → Sonnet이 나머지 구현
- ✅ `sidebar_filter.php` - 사이드바 체크박스 필터 (카테고리 데이터 파라미터화) `[Opus]`
- ✅ `card_list.php` - 카드 리스트 렌더링 (리스트뷰/갤러리뷰 전환) `[Sonnet]`
- ✅ `banner_slide.php` - 배너 슬라이드 (position 파라미터로 Swiper 초기화) `[Sonnet]`
- ✅ `social_share.php` - SNS 공유 버튼 (Twitter, Facebook, 링크 복사) `[Sonnet]`
- ✅ `related_items.php` - 관련 콘텐츠 3개 표시 (테이블명/현재ID 파라미터) `[Sonnet]`
- ✅ `pagination.php` - 더보기 버튼 (AJAX 엔드포인트 URL 파라미터) `[Sonnet]`
- ✅ `search_bar.php` - 키워드 검색바 (폼 action URL 파라미터) `[Sonnet]`

#### 1-4. CSS 구축 `[Sonnet]`
- ✅ `resource/css/pages/list.css` - 목록 페이지 공통 (사이드바, 카드 그리드, 필터, 뷰 전환, 더보기)
- ✅ `resource/css/pages/detail.css` - 상세 페이지 공통 (히어로 이미지, 본문, SNS 공유, 관련 콘텐츠)
- ✅ `resource/css/pages/search.css` - 검색 페이지 (탭, 하이라이팅, 결과 카드)
- ✅ 다크모드: `body.dark-theme` 셀렉터 사용 (common26.css line 1243-1430 패턴)
- ✅ 색상 팔레트: `--v4-primary: #33aeec`, `--v4-dark-bg: #101014`, `--v4-accent: #ffd700`
- ✅ 반응형 브레이크포인트: 1000px (태블릿 핵심), 640px (모바일) — common26.css와 동일
- ✅ 컨테이너: 기존 `.wrap` 클래스 재사용 (1240px)

#### 1-5. v4 헬퍼 함수 준비 `[Opus]`

> **원칙**: GNU Board `common.lib.php`에 이미 존재하는 함수는 재사용하고, v4에서만 필요한 최소한의 헬퍼만 새로 만든다.

기존 함수 그대로 사용 (신규 생성 불필요):
| 기능 | 기존 함수 | 위치 |
|------|----------|------|
| 출력 이스케이프 (XSS 방지) | `get_text()` | common.lib.php:1445 |
| HTML 태그 정제 | `clean_xss_tags()` | common.lib.php:3012 |
| 입력 정제 | `escape_trim()` | common.lib.php:2130 |
| SQL 이스케이프 | `sql_real_escape_string()` | common.lib.php:2116 |
| CSRF 토큰 (상태변경용) | `get_token()` / `check_token()` | common.lib.php:2022-2031 |
| 본문 HTML 처리 | `conv_content()` | common.lib.php:513 |
| 제목 자르기 | `conv_subject()` | common.lib.php:507 |
| 페이지네이션 HTML | `get_paging()` | common.lib.php:21 |

- ✅ `inc/v4_helpers.php` 생성 (v4 전용 최소 헬퍼)
- ✅ `inc/v4_cards.php` 생성 (카드 렌더링 함수)
  - `render_event_card($item, $view_url)` — 이벤트 카드 (상태 배지 포함)
  - `render_resource_card($item, $type, $view_url, $data_subdir)` — 리소스 카드 (카테고리 태그 포함)
  - `v4_int($val)` — 정수 파라미터 강제 캐스팅
  - `v4_str($val)` — 문자열 안전 처리 (strip_tags + trim + stripslashes + sql_real_escape_string)
  - `v4_filter_array($arr, $allowed)` — 필터 배열 + whitelist 검증
  - `v4_where_like($field, $values)` — WHERE 조건 빌더 (LIKE 검색)
  - `v4_where_in($field, $values)` — WHERE 조건 빌더 (정확히 일치)
  - `v4_limit($page, $per_page)` — 페이지네이션 LIMIT 계산
  - `v4_relative_time($datetime)` — 상대 시간 표시 (방금전, X분전, X시간전...)
  - `v4_highlight($text, $keyword)` — 검색 키워드 하이라이팅
  - `v4_thumb_url($row, $subdir)` — 썸네일 URL 헬퍼
  - `v4_ajax_guard()` — AJAX 보안 검증 (POST + XHR 헤더)
  - `v4_youtube_embed_id($url)` — YouTube URL → 임베드 ID 추출

---

### Phase 2: 목록 페이지 (`contents/v4/`)

#### 2-1. `event_list.php` (커뮤니티+글로벌 이벤트 목록) `[Opus]` ← 패턴 확립
> **변경**: 커뮤니티/글로벌을 카테고리탭으로 통합 (별도 global_event_list.php 불필요)
- ✅ 기준 구조 (섹션 4.2) 기반 PHP 골격 작성
- ✅ SEO 동적 로딩 (`v3_seo_config WHERE seo_page = 'event_list'`)
- ✅ 카테고리 탭 구현 (커뮤니티/글로벌 — 테이블 분기)
- ✅ 상태 필터 탭 구현 (전체/진행중/종료/결과발표/예고)
- ✅ `v3_rsc_event_bbs` + `v3_rsc_global_event_bbs` 목록 쿼리 (탭에 따라 분기)
- ✅ 카드 레이아웃 렌더링 (`render_event_card()` — 썸네일 + 제목 + 기간 + 상태 배지)
- ✅ 리스트뷰/갤러리뷰 전환
- ✅ 페이지네이션 / 더보기 AJAX 연동 (`ajax/event.ajax.php`)
- ✅ XSS 방지 출력 이스케이프 적용

#### 2-2. ~~`global_event_list.php`~~ → `event_list.php`에 통합 ✅
> **변경**: 별도 파일 대신 `event_list.php`의 카테고리탭(커뮤니티/글로벌)으로 통합 구현
- ✅ `event_list.php` 카테고리탭에서 `v3_rsc_global_event_bbs` 테이블 조회
- ✅ AJAX(`event.ajax.php`)에서도 `category=global` 파라미터로 글로벌 테이블 분기

#### 2-3. `replay_list.php` (영상 목록 - replay + replay_all 통합) `[Sonnet]`
- ✅ 기준 구조 기반 PHP 골격 작성
- ✅ SEO 동적 로딩
- ✅ 사이드바 필터 (산업분야/제품군/주제/난이도) — `v3_rsc_review_category` 동적 조회
- ✅ 키워드 검색바
- ✅ 리스트뷰/갤러리뷰 전환 버튼
- ✅ `v3_rsc_review_bbs` 목록 쿼리 (필터 조건 + `v4_where_like()` + `sql_query()`)
- ✅ `render_resource_card()` 카드 렌더링
- ✅ 더보기 AJAX 연동 (`ajax/replay.ajax.php`)
- ✅ CSS 클래스 list.css와 일치 검증 완료

#### 2-4. `news_list.php` (뉴스 목록) `[Sonnet]`
- ✅ 기준 구조 기반 PHP 골격 작성
- ✅ SEO 동적 로딩
- ✅ 키워드 검색 (사이드바 없음)
- ✅ `v3_rsc_news_bbs` 목록 쿼리 (`v4_str()` + `sql_query()`)
- ✅ `render_resource_card()` 카드 렌더링
- ✅ 리스트뷰/갤러리뷰 전환
- ✅ 더보기 AJAX 연동 (`ajax/news.ajax.php`)

#### 2-5. `free_list.php` (무료콘텐츠 목록) `[Sonnet]`
- ✅ 기준 구조 기반 PHP 골격 작성
- ✅ SEO 동적 로딩
- ✅ 사이드바 필터 (산업분야/제품군/주제/엔진버전) — `v3_rsc_free_category` 동적 조회
- ✅ 키워드 검색바
- ✅ 리스트뷰/갤러리뷰 전환
- ✅ `v3_rsc_free_bbs` 목록 쿼리 (`v4_str()` + `v4_where_like()` + `sql_query()`)
- ✅ `render_resource_card()` 카드 렌더링
- ✅ 더보기 AJAX 연동 (`ajax/free.ajax.php`)
- ✅ CSS 클래스 list.css와 일치 검증 완료

#### 2-6. `book_list.php` (백서 목록) `[Sonnet]`
- ✅ 기준 구조 기반 PHP 골격 작성
- ✅ SEO 동적 로딩
- ✅ 사이드바 필터 (산업분야/제품군/주제) — `v3_rsc_book_category` 동적 조회
- ✅ 키워드 검색바
- ✅ 리스트뷰/갤러리뷰 전환
- ✅ `v3_rsc_book_bbs` 목록 쿼리 (`v4_str()` + `v4_where_like()` + `sql_query()`)
- ✅ `render_resource_card()` 카드 렌더링
- ✅ 더보기 AJAX 연동 (`ajax/book.ajax.php`)
- ✅ CSS 클래스 list.css와 일치 검증 완료

---

### Phase 3: 상세 페이지 (`contents/v4/`)

#### 3-1. `event_view.php` (커뮤니티+글로벌 이벤트 상세) `[Opus]` ← 패턴 확립
> **변경**: 커뮤니티/글로벌을 `type=global` GET 파라미터로 통합 (별도 global_event_view.php 불필요)
- ✅ 기준 구조 기반 PHP 골격 작성
- ✅ SEO 동적 로딩 (이벤트 제목 기반 OG 태그 동적 생성)
- ✅ `rsc_bbs_idx` 파라미터 검증 (정수 캐스팅, 존재 여부 확인)
- ✅ `type=global` 파라미터로 커뮤니티/글로벌 테이블 분기
- ✅ 상단 배경 이미지 렌더링 (`top_bbs_img`) — 히어로 + 오버레이 + 상태 배지
- ✅ 본문 콘텐츠 렌더링 (`contents` HTML 출력)
- ✅ 첨부파일 다운로드 버튼 (`doc_file` 필드)
- ✅ 액션 버튼 분기 (`add_btn_yn` + `add_btn_url` → 참가/신청 버튼 동적 렌더링)
- ✅ 소셜 공유 (Facebook, Twitter, URL 복사)
- ✅ 관련 이벤트 3개 (`ORDER BY ABS(idx - rsc_bbs_idx) ASC`)

#### 3-2. ~~`global_event_view.php`~~ → `event_view.php`에 통합 ✅
> **변경**: 별도 파일 대신 `event_view.php?type=global&rsc_bbs_idx=N` 으로 통합 구현
- ✅ `event_view.php`에서 `type=global` 파라미터로 `v3_rsc_global_event_bbs` 테이블 조회
- ✅ 관련 콘텐츠도 글로벌 테이블에서 조회

#### 3-3. `replay_view.php` (영상 상세) `[Sonnet]`
- ✅ 기준 구조 기반 PHP 골격 작성
- ✅ SEO 동적 로딩 (영상 제목 기반 OG 태그)
- ✅ `rsc_bbs_idx` 파라미터 검증 + 권한 체크 (비공개=관리자만)
- ✅ YouTube 임베드 플레이어 렌더링 (`v4_youtube_embed_id()` 헬퍼 사용)
- ✅ 발표자(`speker`), 이벤트 제목(`event_title`) 표시
- ✅ PDF 자료 다운로드 버튼 (`pdf_url`)
- ✅ 카테고리 태그 표시 (산업분야/제품군/주제)
- ✅ 소셜 공유 (Facebook, Twitter, URL 복사)
- ✅ 관련 리플레이 3개

#### 3-4. `news_view.php` (뉴스 상세) `[Sonnet]`
- ✅ 기준 구조 기반 PHP 골격 작성
- ✅ SEO 동적 로딩 (뉴스 제목 기반 OG 태그)
- ✅ `rsc_bbs_idx` 파라미터 검증 + 권한 체크
- ✅ 본문 콘텐츠 렌더링
- ✅ 카테고리 배지 + 태그 표시 (쉼표 구분)
- ✅ 소셜 공유 (Facebook, Twitter, URL 복사)
- ✅ 관련 뉴스 3개

#### 3-5. `free_view.php` (무료콘텐츠 상세) `[Sonnet]`
- ✅ 기준 구조 기반 PHP 골격 작성
- ✅ SEO 동적 로딩
- ✅ `rsc_bbs_idx` 파라미터 검증 + 권한 체크
- ✅ YouTube 임베드 (`v4_youtube_embed_id()`) + 외부 링크 분기 렌더링
- ✅ 카테고리 태그 (산업분야/제품군/주제/엔진버전 — 4종)
- ✅ 소셜 공유 + 관련 무료자료 3개

#### 3-6. `book_view.php` (백서 상세) `[Sonnet]`
- ✅ 기준 구조 기반 PHP 골격 작성
- ✅ SEO 동적 로딩
- ✅ `rsc_bbs_idx` 파라미터 검증 + 권한 체크
- ✅ YouTube 임베드 (`v4_youtube_embed_id()`) + 외부 링크 분기 렌더링
- ✅ 카테고리 태그 (산업분야/제품군/주제 — 3종)
- ✅ 소셜 공유 + 관련 도서 3개

---

### Phase 4: AJAX 엔드포인트 (`contents/v4/ajax/`)

#### 4-0. AJAX 공통 보안 패턴 `[Opus]` ← 패턴 확립

> **원칙**: 모든 AJAX 엔드포인트는 읽기전용(SELECT)이므로 CSRF 토큰은 불필요. 대신 Referer + XHR 헤더 검증을 적용한다.
> 상태 변경(이벤트 참가 신청 등 POST)에만 기존 GNU Board `get_token()` / `check_token()`을 사용한다.

```php
// AJAX 엔드포인트 공통 보안 패턴
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    http_response_code(403);
    exit;
}
header('Content-Type: application/json; charset=utf-8');
```

- ✅ AJAX 공통 보안 패턴 확립 → `v4_ajax_guard()` 함수로 구현 (`v4_helpers.php`)
- ✅ `json_encode()` 기반 JSON 응답 패턴 확립

#### 4-0b. `event.ajax.php` (이벤트 필터) `[Opus]` ← 추가 생성
> **추가**: 원안에 없었으나, event_list.php의 카테고리탭/상태탭/더보기를 위해 필요
- ✅ `v4_ajax_guard()` 보안 검증 적용
- ✅ 카테고리(community/global) + 상태 필터 + 페이지네이션
- ✅ `render_event_card()` HTML + `json_encode()` JSON 응답

#### 4-1. `replay.ajax.php` (영상 필터) `[Opus]` ← 패턴 확립
- ✅ `v4_ajax_guard()` 보안 검증 적용 (POST + XHR 헤더)
- ✅ 필터 파라미터 수신 (산업분야/제품군/주제/난이도/키워드/페이지)
- ✅ `v4_str()` / `v4_filter_array()` 로 입력값 sanitize
- ✅ `v4_where_like()` + `v4_limit()` 로 쿼리 빌드 → `sql_query()` 실행
- ✅ `render_resource_card()` HTML 렌더링 + `json_encode()` JSON 응답
- ✅ 응답 형식: `{success, html, total_count, has_more}`

#### 4-2. `news.ajax.php` (뉴스 필터) `[Sonnet]`
- ✅ `v4_ajax_guard()` 보안 검증 적용
- ✅ 필터 파라미터 수신 (키워드/페이지)
- ✅ `v4_str()` sanitize + `sql_query()` 쿼리
- ✅ `render_resource_card()` HTML + `json_encode()` JSON 응답

#### 4-3. `free.ajax.php` (무료콘텐츠 필터) `[Sonnet]`
- ✅ `v4_ajax_guard()` 보안 검증 적용
- ✅ 필터 파라미터 수신 (산업분야/제품군/주제/엔진버전/키워드/페이지)
- ✅ `v4_str()` sanitize + `v4_where_like()` + `sql_query()` 쿼리
- ✅ `render_resource_card()` HTML + `json_encode()` JSON 응답

#### 4-4. `book.ajax.php` (백서 필터) `[Sonnet]`
- ✅ `v4_ajax_guard()` 보안 검증 적용
- ✅ 필터 파라미터 수신 (산업분야/제품군/주제/키워드/페이지)
- ✅ `v4_str()` sanitize + `v4_where_like()` + `sql_query()` 쿼리
- ✅ `render_resource_card()` HTML + `json_encode()` JSON 응답

#### 4-5. ~~검색 AJAX (3개)~~ → `search.ajax.php` 1개로 통합 ✅
> **변경**: 3개 파일 대신 `search.ajax.php` 1개에서 5개 테이블(뉴스/이벤트/리플레이/무료/백서) 동시 검색
- ✅ `search.ajax.php` — 통합 검색 AJAX (`section` 파라미터로 개별/전체 분기)
- ✅ `v4_ajax_guard()` 보안 검증 적용
- ✅ `v4_str()` sanitize + 5개 테이블 동시 쿼리
- ✅ `render_search_result_card()` — 검색 전용 카드 렌더링 (키워드 하이라이팅)
- ✅ 응답 형식: `{success, keyword, total_all, sections: {news: {label, total, html, has_more}, ...}}`

---

### Phase 5: 검색 + 정적 (`contents/v4/`)

#### 5-1. `total_search.php` (통합 검색) `[Opus]` ← 5테이블 합산 복합 로직
> **변경**: `search.php` → `total_search.php`로 명명 (레거시와 일관성 유지)
- ✅ 기준 구조 기반 PHP 골격 작성
- ✅ SEO 동적 로딩
- ✅ 검색 키워드 입력 폼 (`v4_str()` sanitize 적용)
- ✅ 5개 섹션 탭 구현 (새소식 / 이벤트 / 다시보기 / 무료콘텐츠 / 백서)
- ✅ 초기 검색 결과 서버사이드 렌더링
- ✅ 키워드 하이라이팅 (`v4_highlight()` 함수)
- ✅ 총 검색 건수 표시 (섹션별 카운트)
- ✅ 섹션별 더보기 → `ajax/search.ajax.php` (통합) 연동

#### 5-2. `personal.php` (개인정보보호정책) `[Sonnet]`
- ✅ 기준 구조 기반 PHP 골격 작성 (v4 패턴: common_header26 + detail.css)
- ✅ SEO 동적 로딩 (v3_seo_config → personal)
- ✅ 기존 내용 이관 (2022.04.03 기준 원본 그대로 유지)
- ✅ 스타일 적용 (v4-detail-content 타이포그래피 자동 적용)

#### 5-3. `ode.php` (이용약관) `[Sonnet]`
- ✅ 기준 구조 기반 PHP 골격 작성 (v4 패턴: common_header26 + detail.css)
- ✅ SEO 동적 로딩 (v3_seo_config → ode)
- ✅ 기존 내용 이관 (개인정보처리방침 링크를 v4 경로로 업데이트)
- ✅ 스타일 적용

---

### Phase 6: 통합 테스트 (🎭 Playwright MCP + 🔁 Ralph Loop)

> **Ralph Loop 활용**: Phase 6 전체를 Ralph Loop로 실행한다. "테스트 → 버그 발견 → 수정 → 재테스트" 반복 루프에 최적.
> ```
> /ralph-loop "contents/v4/ 전체 페이지를 Playwright로 테스트하고
> 발견된 버그를 수정해라. 모든 페이지에서 JS 에러 0건,
> AJAX 200 응답, 반응형 레이아웃 정상이면
> <promise>ALL TESTS PASS</promise> 출력."
> --max-iterations 15 --completion-promise "ALL TESTS PASS"
> ```

#### 6-1. 🎭 기능 테스트 (Playwright 자동화) `[Ralph Loop]`
- ⬜ **페이지 로딩 검증** - 전 페이지 `browser_navigate` → `browser_snapshot`으로 주요 요소 존재 확인
- ⬜ **JS 에러 검증** - 전 페이지 `browser_console_messages(level: "error")` → 에러 0건 확인
- ⬜ **필터 테스트** - `browser_snapshot`으로 필터 체크박스 ref 확인 → `browser_click` → 카드 목록 변경 확인
- ⬜ **더보기 테스트** - 더보기 버튼 `browser_click` → `browser_wait_for` → 카드 수 증가 확인
- ⬜ **AJAX 검증** - `browser_network_requests` → AJAX 호출 상태코드 200 + 응답 데이터 확인
- ⬜ **키워드 검색 테스트** - 검색바 `browser_type` → submit → 결과 `browser_snapshot` 확인
- ⬜ **뷰 전환 테스트** - 갤러리뷰/리스트뷰 버튼 `browser_click` → DOM 클래스 변경 확인
- ⬜ **SNS 공유 테스트** - 공유 버튼 `browser_click` → 공유 팝업/모달 노출 확인
- ⬜ **다크모드 테스트** - 테마 토글 `browser_click` → `browser_take_screenshot` → 스타일 변경 확인

#### 6-2. 보안 테스트 `[Ralph Loop]`
- ⬜ SQL Injection 테스트 (필터, 검색, idx 파라미터에 악성 입력)
- ⬜ XSS 테스트 (검색 키워드, URL 파라미터에 스크립트 삽입)
  - ⬜ 🎭 `browser_type`으로 `<script>alert(1)</script>` 입력 → 실행 안 되는지 확인
- ⬜ AJAX 보안 검증 테스트 (XHR 헤더 없이 직접 호출 → 403 거부 확인)
  - ⬜ 🎭 `browser_evaluate`로 X-Requested-With 없는 fetch 호출 → 403 응답 확인
- ⬜ 존재하지 않는 idx 접근 시 적절한 에러 처리 확인
  - ⬜ 🎭 `browser_navigate(?idx=99999)` → 에러 페이지 또는 리다이렉트 확인

#### 6-3. 🎭 반응형 UI 테스트 (Playwright 자동 캡처) `[Ralph Loop]`
- ⬜ **데스크톱** - `browser_resize(1920, 1080)` → 전 페이지 스크린샷
- ⬜ **데스크톱 소** - `browser_resize(1440, 900)` → 전 페이지 스크린샷
- ⬜ **노트북** - `browser_resize(1280, 720)` → 전 페이지 스크린샷
- ⬜ **태블릿 가로** - `browser_resize(1024, 768)` → 전 페이지 스크린샷
- ⬜ **태블릿 세로** - `browser_resize(768, 1024)` → 전 페이지 스크린샷
- ⬜ **모바일** - `browser_resize(375, 812)` → 전 페이지 스크린샷
- ⬜ 배너 슬라이드 동작 확인 (각 해상도에서)
- ⬜ 사이드바 sticky 동작 확인 / 모바일에서 접힘 확인
- ⬜ 레거시 스크린샷(`_test/screenshots/legacy/`)과 v4 스크린샷 시각적 비교
- ⬜ `_test/screenshots/v4/` 에 최종 캡처본 저장

#### 6-4. 크로스 브라우저 테스트 `[Ralph Loop]`
> Playwright는 Chromium / Firefox / WebKit 3개 엔진 지원
- ⬜ Chromium (Chrome/Edge 대응) - 전 페이지 스크린샷 + 기능 테스트
- ⬜ WebKit (Safari 대응) - 전 페이지 스크린샷 + 기능 테스트
- ⬜ Firefox - 전 페이지 스크린샷 + 기능 테스트
- ⬜ 모바일 에뮬레이션 (iOS Safari viewport) - 주요 페이지 확인
- ⬜ 모바일 에뮬레이션 (Android Chrome viewport) - 주요 페이지 확인

#### 6-5. 성능 확인 `[Ralph Loop]`
- ⬜ 🎭 `browser_network_requests` → 페이지별 요청 수/용량 확인
- ⬜ 🎭 `browser_evaluate` → `performance.timing` 으로 로딩 시간 측정
- ⬜ AJAX 응답 속도 확인 (네트워크 탭에서 응답 시간)
- ⬜ 이미지 로딩 최적화 확인 (lazy loading 동작 여부)
- ⬜ 레거시 대비 개선 여부 비교

---

### Phase 7: 운영 전환

#### 7-1. 전환 준비 `[Sonnet]`
- ⬜ `contents/v4/` 전체 파일 최종 점검
- ⬜ 불필요한 디버그 코드 / console.log 제거
  - ⬜ 🎭 `browser_console_messages(level: "debug")` → 디버그 로그 잔존 확인
- ⬜ 운영 서버 백업 계획 수립

#### 7-2. 전환 실행 `[Sonnet]`
- ⬜ 기존 `contents/` → `contents/legacy/` 백업 (mv)
- ⬜ `contents/v4/` → `contents/` 교체 (mv)
- ⬜ 파일 경로 참조 점검 (`../` 경로 등이 깨지지 않는지)
- ⬜ 레거시 JS/CSS 파일 참조가 남아있지 않은지 확인

#### 7-3. 🎭 운영 검증 (Playwright 자동 순회) `[Sonnet → Opus(문제 시)]`
- ⬜ **전 페이지 접속 검증** - 모든 URL `browser_navigate` → `browser_network_requests`로 HTTP 200 확인
- ⬜ **JS 에러 제로 확인** - `browser_console_messages(level: "error")` → 에러 0건
- ⬜ **주요 요소 존재 확인** - `browser_snapshot` → 헤더/콘텐츠/푸터 정상 렌더링
- ⬜ **AJAX 엔드포인트 확인** - 필터/더보기 클릭 → `browser_network_requests`로 응답 확인
- ⬜ **SEO 메타태그 확인** - `browser_evaluate` → `document.querySelector('meta[property="og:title"]')` 등 확인
- ⬜ **마케팅 코드 확인** - `browser_evaluate` → `window.dataLayer` (GTM), `fbq` (Pixel) 존재 확인
- ⬜ **최종 스크린샷 캡처** - 전 페이지 Desktop/Mobile 스크린샷 → 전환 전 v4 캡처본과 비교
- ⬜ 문제 발생 시 즉시 롤백 (`contents/` → `contents/v4/`, `contents/legacy/` → `contents/`)
