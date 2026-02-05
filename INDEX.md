# `v3/index_revnew.php` 구조 분석

이 문서는 `v3/index_revnew.php` 파일의 구조와 주요 로직을 분석한 내용입니다.

## 1. 개요

- **파일 역할**: 에픽 라운지 웹사이트의 메인 페이지 (GNU Board 5 기반)
- **주요 기능**: 접속 리디렉션, 메인 비주얼 슬라이드, 새소식/배너/리소스 섹션 표시, 공통 레이아웃(헤더/푸터) 포함

## 2. 코드 구조 상세

### A. PHP 초기화 및 리디렉션 로직 (상단)

- **리디렉션**: `www.epiclounge.co.kr`로 접속 시 `epiclounge.co.kr`로 301 리디렉션 (SEO 및 도메인 통일).
- **라이브러리 로드**: 그누보드 핵심 파일(`board/common.php`, `board/lib/latest.lib.php`)을 로드하여 DB 연결 및 공통 함수 사용.
- **상수 정의**: `_INDEX_` 상수를 `true`로 정의하여 이 파일이 인덱스 페이지임을 명시하고, `_GNUBOARD_` 상수가 정의되지 않은 경우 실행을 중단(보안).

### B. HTML 헤더 (`<head>`)

- **메타데이터**:
  - `viewport`: 모바일 대응 반응형 설정.
  - `OG (Open Graph)`: 페이스북, 카카오톡 등 SNS 공유 시 보여질 제목, 이미지, 설명 설정.
  - `favicon`: 다양한 디바이스(Apple, Android, PC) 호환 아이콘 설정.
- **외부 스크립트 (Tracking)**:
  - Google Analytics (UA 버전 - 구버전)
  - Google Ads
  - Facebook Pixel
- **CSS/JS**: `main2.css`, jQuery, Slick, ScrollTrigger 등 UI 라이브러리 로드.

### C. 본문 레이아웃 (`<body>`)

#### 1. 공통 요소

- **헤더**: `include 'inc/common_header.php'`로 상단 네비게이션 포함.
- **팝업**: `newwin.inc.php`를 통해 관리자 페이지에서 설정한 팝업 레이어 출력.

#### 2. 메인 비주얼 (`.visual_slide`)

- **기능**: Slick Slider를 사용한 영상 슬라이드 롤링.
- **컨텐츠**:
  1.  **UE5.7**: 영상(`ue57bh.mp4`) + 링크.
  2.  **Unreal Fest 2025**: 영상(`3_fusion_2_re2.mp4`) + 다시보기 버튼.
  3.  **Start Twin Unreal**: 영상(`lsu_2025_bg.mp4`) + 다시보기 버튼.

#### 3. 새소식 섹션 (`.news_sec`)

- **DB 연동**: `v3_main_banner_news` 테이블에서 최신 3개 데이터 조회.
- **출력**: 썸네일, 제목, 간단 설명.

#### 4. 중간 배너 슬라이드 (`.bg_slide_box`)

- **구조**: 2개의 **하드코딩된 슬라이드** (영상 배경).
- **컨텐츠**:
  1.  **에픽 메가그랜트**: 영상 배경 + 텍스트 + 바로가기 버튼.
  2.  **언리얼 엔진 5**: 영상 배경 + 텍스트 + 다운로드 버튼.
- **스타일**: 높이 800px, 배경 영상 전체 화면(`cover`), 텍스트 중앙 정렬.
- **기능**: Slick Slider 자동 재생 (6초 간격).

#### 5. 리소스 섹션 (`.resource_list`)

- **DB 연동**: `v3_main_banner_rsc` 테이블에서 최신 6개 데이터 조회.
- **출력**: 썸네일, 태그, 제목, 설명.

### D. 푸터 및 하단 스크립트

- **푸터**: `include 'inc/common_footer.php'`로 하단 정보 포함.
- **UI 스크립트**:
  - Slick Slider 초기화 (배너, 비주얼 슬라이드).
  - ScrollTrigger 초기화.
  - Smooth Scroll: 앵커 태그 클릭 시 부드러운 스크롤 이동 처리.

## 3. 수정 및 개선 제안 사항

1.  **동영상 경로**: 현재 일부 동영상 경로가 외부 서버(cafe24)나 하드코딩된 경로를 사용 중입니다. 로컬 리소스 사용 시 경로 확인이 필요합니다.
2.  **DB 테이블 의존성**: `v3_main_banner_news`, `v3_main_banner`, `v3_main_banner_rsc` 테이블이 존재해야 페이지가 정상 작동합니다.
3.  **GA 태그 업데이트**: 현재 사용 중인 UA(Universal Analytics) 태그는 서비스가 종료되었으므로 GA4(Google Analytics 4)로 업데이트를 권장합니다.

## 4. 변경 이력 (Change Log)

### [2026-01-08] 슬라이드 섹션 업데이트

- **파일**: `v3/index_revnew.php`, `v3/resource/css/main_layout26.css`
- **내용**:
  - `.bg_slide_box` 높이 **800px**로 변경.
  - 기존 DB 연동 슬라이드를 **하드코딩된 비디오 슬라이드 2개**로 대체.
  - 배경 영상(`.bg_video`), 텍스트 래퍼(`.txt_wrap`) 스타일 추가.
  - 슬라이드 자동 재생(6초) 설정 추가.
