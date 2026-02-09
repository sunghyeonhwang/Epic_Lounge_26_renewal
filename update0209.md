# 0209 수정 완료 보고서

## A. 공통 — 트위터 → X 공유 버튼 교체

### 수정 파일
- `contents/v4/event_view.php`
- `contents/v4/replay_view.php`
- `contents/v4/news_view.php`
- `contents/v4/free_view.php`
- `contents/v4/book_view.php`
- `resource/css/pages/detail.css`

### 변경 내용
- 클래스명: `v4-share__button--twitter` → `v4-share__button--x`
- 공유 URL: `twitter.com/intent/tweet` → `x.com/intent/tweet`
- aria-label: "트위터 공유" → "X 공유"
- SVG 아이콘: 트위터 새 아이콘 → X 공식 로고 (viewBox 1200x1227)
- CSS 호버: `#1da1f2` → `#000` (다크모드: `#fff`)

---

## B. 새소식

### B-1. 검색 작동 수정
**수정 파일**: `contents/v4/ajax/news.ajax.php`
- **원인**: DB 컬럼명 오타 (`content` → `contents`)
- **수정**: `OR content LIKE` → `OR contents LIKE`
- 레거시/v4 news_view.php 모두 `$RData['contents']` 사용 확인

### B-2. 검색 버튼 너비 확대
**수정 파일**: `resource/css/pages/list.css`
- `.v4-search-bar__button`: `width: 40px` → `min-width: 40px; padding: 0 16px; gap: 6px; font-size: 14px; font-weight: 500`
- `.v4-search-bar__input`: `padding-right: 50px` → `padding-right: 100px` (버튼 영역 확보)

---

## C. 이벤트 — 상태 탭 가운데 정렬

### 수정 파일
- `contents/v4/event_list.php`

### 변경 내용
- `#status-tabs` div에 `justify-content: center` 인라인 스타일 추가
- "전체 / 진행중 / 종료 / 결과발표" 탭이 가운데 정렬됨

---

## D. 리소스

### D-1. 필터란에서 검색 삭제
**수정 파일**: `contents/v4/replay_list.php`
- 사이드바 내 `.v4-search-bar` HTML 블록 제거 (검색 input + 버튼)
- JS에서 `#search-btn`, `#keyword-input` 참조 제거 → `#top-search-btn`, `#top-keyword-input`만 유지
- 상단 검색바는 유지

### D-2. 필터 인터페이스 레거시 맞춤

**free_list.php** (무료 콘텐츠):
- 필터 변경: 산업분야/제품/주제/난이도 → **산업분야/엔진버전/카테고리** (레거시와 동일)
- 카테고리 소스: DISTINCT 쿼리 → `v3_rsc_free_category` 테이블 (sort 정렬)
- 필터 name 속성: `cate_product[]`/`cate_subject[]`/`cate_difficult[]` → `cate_engine[]`/`category[]`

**수정 파일**:
- `contents/v4/free_list.php` — PHP 카테고리 쿼리 + HTML 필터 그룹
- `contents/v4/ajax/free.ajax.php` — 파라미터/WHERE절 변경

**book_list.php** (백서):
- 필터 변경: 산업분야/제품/주제/난이도 → **산업분야만** (레거시와 동일)
- 카테고리 소스: `v3_rsc_book_category` 테이블

**수정 파일**:
- `contents/v4/book_list.php` — PHP 카테고리 쿼리 + HTML 필터 그룹 (제품/주제/난이도 제거)
- `contents/v4/ajax/book.ajax.php` — 파라미터/WHERE절 (산업분야만)

### D-3. 썸네일 이미지 경로 수정
**원인**: 레거시는 모든 리소스(replay/free/book) 썸네일을 `data/rsc/` 디렉토리에 저장하는데, v4는 `data/review/`, `data/free/`, `data/book/`으로 잘못 설정

**수정 파일 (data_subdir 변경)**:
- `contents/v4/replay_list.php` — `'review'` → `'rsc'`
- `contents/v4/free_list.php` — `'free'` → `'rsc'`
- `contents/v4/book_list.php` — `'book'` → `'rsc'`
- `contents/v4/ajax/replay.ajax.php` — `'review'` → `'rsc'`
- `contents/v4/ajax/free.ajax.php` — `'free'` → `'rsc'`
- `contents/v4/ajax/book.ajax.php` — `'book'` → `'rsc'`

**추가 수정**:
- `inc/v4_helpers.php`: 기본 이미지 `event_list_img.jpg` → `no_img.jpg` (레거시와 동일)
- `inc/v4_cards.php`: 카테고리 태그에 `cate_engine`, `category` 컬럼 추가
- `inc/v4_cards.php`: 설명 텍스트 컬럼 `content` → `contents` (fallback 포함)

### D-4. 무료 콘텐츠 새창 연결
**수정 파일**: `inc/v4_cards.php`
- 새창 조건: `$type === 'news'` → `in_array($type, ['news', 'free', 'book'])`
- `site_url`이 있는 무료 콘텐츠/백서는 외부 링크로 새창 열기 (`target="_blank"`)

### D-5. 네비바 콘텐츠 가림 수정
**수정 파일**: `resource/css/pages/detail.css`
- `.v4-detail-hero`: `margin-top: 90px` 추가 (헤더 높이만큼 오프셋)
- `.v4-detail-container`: `padding-top: 0` → `padding-top: 90px` (히어로 없는 페이지용)
- `.v4-detail-hero ~ .v4-detail-container`: `padding-top: 0` (히어로 있으면 중복 여백 제거)

### D-6. HTML 테이블 렌더링 수정
**수정 파일**:
- `resource/css/pages/detail.css` — 테이블 스타일 개선
- `resource/js/v4.app.js` — 테이블 래핑 JS 추가

**CSS 변경**:
- `.v4-detail-content table`: `width: 100%` → `max-width: 100%` (인라인 스타일 보존)
- 기본 테이블 스타일을 `:not([style])` 선택자로 변경 → 인라인 스타일 우선
- `.table-wrapper` 추가: `overflow-x: auto` (모바일 가로 스크롤)
- 테이블 내 이미지: `margin: 0; border-radius: 0` (레이아웃 깨짐 방지)

**JS 변경**:
- v4.app.js ready에서 `.v4-detail-content table`을 `.table-wrapper`로 자동 래핑

---

## E. 배너 슬라이더 추가 (목록 페이지)

### 수정 파일
- `contents/v4/replay_list.php`
- `contents/v4/free_list.php`
- `contents/v4/book_list.php`
- `resource/css/pages/list.css`

### 변경 내용
- 레거시 `.visual_item` 배너 슬라이더를 v4에 Swiper.js로 구현
- `v3_shop_banner` 테이블에서 `bn_position`별 배너 조회 (다시보기/무료콘텐츠/백서)
- CSS: `.v4-banner-slider` (height 182px, 모바일 120px)
- Swiper: `slidesPerView: 1, loop: true, autoplay: 4000ms, pagination + navigation`
- 리소스 탭 하단, 사이드바 상단에 배치

---

## F. 상세 페이지 레이아웃 개선

### F-1. 리소스 상세 이미지 경로 수정
**수정 파일**: `contents/v4/free_view.php`, `contents/v4/book_view.php`
- 히어로 이미지: `G5_DATA_URL . '/free/'` / `'/book/'` → `'/rsc/'`
- 썸네일: `v4_thumb_url($RData, 'free')` / `'book'` → `'rsc'`
- 관련 콘텐츠 썸네일: `v4_thumb_url($rel, 'free')` / `'book'` → `'rsc'`

### F-2. YouTube 비디오 반응형 수정
**수정 파일**: `contents/v4/free_view.php`, `contents/v4/book_view.php`
- **변경 전**: `width="100%" height="500"` (고정 높이)
- **변경 후**: `padding-bottom: 56.25%; height: 0` (16:9 반응형 비율)
- replay_view.php와 동일한 반응형 패턴 적용

### F-3. 모바일 히어로 없는 페이지 상단 여백 수정
**수정 파일**: `resource/css/pages/detail.css`
- **버그**: 640px 이하에서 `.v4-detail-container { padding: 0 16px 48px }` → 히어로 없는 페이지에서 고정 헤더 아래 콘텐츠 가림
- **수정**: `padding: 90px 16px 48px` + `.v4-detail-hero ~ .v4-detail-container { padding-top: 0 }` 추가

---

## G. #sub_visual 높이 축소

### 수정 파일
- `resource/css/pages/list.css`

### 변경 내용
- 데스크탑: `height: 470px; padding-top: 140px` → `height: 300px; padding-top: 100px`
- h2 폰트: `65px` → `45px`
- p 폰트: `22px` → `18px`
- 1000px 이하: `height: 380px` → `height: 280px; padding-top: 90px`
- h2: `55px` → `38px`, p: `20px` → `16px`
- 640px 이하: 기존 300px 유지 (변경 없음)
- `sub_layout.css`는 레거시 영향 때문에 수정하지 않고 `list.css`에서 오버라이드

---

## H. 다크모드 텍스트 수정

### 수정 파일
- `resource/css/pages/list.css`
- `resource/css/pages/detail.css`

### 변경 내용 (list.css)
- **리소스 탭** (`a:link { color: inherit }` 오버라이드):
  - `.v4-resource-tabs__item`: `color: #a0a0a0` (다크모드)
  - `.v4-resource-tabs__item:hover/.active`: `color: var(--v4-primary)`
  - `.v4-resource-tabs__count`: `background: rgba(255,255,255,0.1); color: #a0a0a0`
- **배지**: `.v4-badge--result` 다크모드 `color: #ffd700`, `.v4-badge--primary` 다크모드 추가
- **필터 체크박스**: `color: #e0e0e0`
- **뷰 토글/필터 초기화/빈 상태/배너**: 다크모드 색상 추가

### 변경 내용 (detail.css)
- `.v4-related__thumbnail`: 다크모드 배경
- `.v4-detail-tags .v4-tag`: 다크모드 색상
- `.v4-badge--primary`: 다크모드 색상
- `.v4-detail-header`/`.v4-detail-nav`: 다크모드 border 색상

---

## I. CSS 캐시 버스팅

### 수정 파일 (12개)
- `contents/v4/event_list.php`, `replay_list.php`, `free_list.php`, `book_list.php`, `news_list.php`
- `contents/v4/event_view.php`, `replay_view.php`, `news_view.php`, `free_view.php`, `book_view.php`
- `contents/v4/personal.php`, `ode.php`

### 변경 내용
- `list.css` → `list.css?v=20250209b`
- `detail.css` → `detail.css?v=20250209b`

---

## J. 필터 사이드바 UX 리디자인 (아코디언 + 칩 UI)

### 변경 요약
필터 사이드바를 체크박스 목록 UI에서 **아코디언 + 칩(Chip) UI**로 전면 리디자인.
사이드바 높이 ~800px → ~200px(접힌 상태)로 대폭 축소되어 UX 개선.

### 적용 페이지
| 페이지 | 필터 그룹 |
|--------|----------|
| `replay_list.php` | 산업분야, 제품군, 주제, 난이도 (4개) |
| `free_list.php` | 산업분야, 엔진버전, 카테고리 (3개) |
| `book_list.php` | 산업분야 (1개) |

### 수정 파일
- `resource/css/pages/list.css` — 아코디언/칩/활성태그/배지 CSS + 다크모드
- `contents/v4/replay_list.php` — HTML + JS 전면 교체
- `contents/v4/free_list.php` — HTML + JS 전면 교체
- `contents/v4/book_list.php` — HTML + JS 전면 교체

### 신규 UI 기능
1. **아코디언 필터 그룹**: 클릭 시 expand/collapse (slideToggle 200ms), 첫 번째 그룹 기본 열림
2. **칩(Chip) 선택 UI**: pill 형태 버튼, 선택 시 primary 색상 배경 + 흰색 텍스트
3. **선택 배지**: 각 그룹 헤더에 선택 수 배지 표시
4. **활성 필터 태그**: 사이드바 상단에 removable 태그 + "전체 초기화" 버튼
5. **칩 텍스트**: 12px, 사이드바 스크롤바 제거

### AJAX 호환성
- POST 파라미터명 동일 유지, AJAX 엔드포인트 변경 없음

### 검증 결과
- replay_list: 315건→114건 필터링 정상
- free_list: 76건→21건 필터링 정상
- book_list: 16건→3건 필터링 정상
- 활성 태그 X 제거, 전체 초기화, 배지 카운트, 다크모드, 모바일 반응형 모두 정상

---

## 수정 파일 전체 목록

| 파일 | 수정 항목 |
|------|----------|
| `contents/v4/event_view.php` | A, I |
| `contents/v4/replay_view.php` | A, I |
| `contents/v4/news_view.php` | A, I |
| `contents/v4/free_view.php` | A, F-1, F-2, I |
| `contents/v4/book_view.php` | A, F-1, F-2, I |
| `contents/v4/event_list.php` | C, I |
| `contents/v4/news_list.php` | I |
| `contents/v4/replay_list.php` | D-1, D-3, E, I |
| `contents/v4/free_list.php` | D-2, D-3, E, I |
| `contents/v4/book_list.php` | D-2, D-3, E, I |
| `contents/v4/personal.php` | I |
| `contents/v4/ode.php` | I |
| `contents/v4/ajax/news.ajax.php` | B-1 |
| `contents/v4/ajax/replay.ajax.php` | D-3 |
| `contents/v4/ajax/free.ajax.php` | D-2, D-3 |
| `contents/v4/ajax/book.ajax.php` | D-2, D-3 |
| `inc/v4_helpers.php` | D-3 |
| `inc/v4_cards.php` | D-3, D-4 |
| `resource/css/pages/detail.css` | A, D-5, D-6, F-3, H |
| `resource/css/pages/list.css` | B-2, E, G, H |
| `resource/js/v4.app.js` | D-6 |
