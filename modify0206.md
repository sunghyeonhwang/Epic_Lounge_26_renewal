# 에픽라운지(Epic Lounge) 페이지 및 테스트 포인트

## a. 공통 
### 각 페이지에는 상단에 .body_acti #sub_visual 이 존재 했었음 현재 페이지들에는 구현이 안되어 있음 

## b. 새소식 목록
### 1. 새소식:  새소식의 모든 게시물을 클릭하면 내용 페이지가 나오는게 아니라 새창으로 열려야함 

### 2. 새소식 서브 메뉴: 뉴스 / 출시&업데이트 / 블로그의 메뉴 분류가 안보임 레거시 페이지는 드랍다운으로 구현되어 있었음 

### 3. 새소식 상단의 검색의 width값이 잘못된듯 하고 버튼도 크기가 잘못 되어 있음 

### 4. 새소식 하단 더보기 버튼 과 Footer 사이에 여백이 200px 정도 필요 

## C.이벤트 목록
### 1. https://epiclounge.co.kr/v3/contents/v4/event_list.php 2개씩 나오던게 3개씩 나와서 복잡해짐 -> 레거시와 동일한 숫자로 노출 
### 2. 전체 /진행중/ 종료/ 결과발표 탭(버튼)이 사라짐 
### 3.  이벤트 리스트 하단 더보기 버튼 과 Footer 사이에 여백이 200px 정도 필요 

### 4. 글로벌 이벤트가 사라짐 (링크로 따로 제공 필요)

## d. 이벤트 보기 페이지
### 1. 이벤트 보기 페이지 수정 : 보기 페이지에서 레거시에서 보이던 레이아웃과 달라짐, 내용 게시물이 https://epiclounge.co.kr/contents/event_view.php?rsc_bbs_idx=326&status=&category=%EC%BB%A4%EB%AE%A4%EB%8B%88%ED%8B%B0%20%EC%9D%B4%EB%B2%A4%ED%8A%B8 이렇게 보이거나 https://epiclounge.co.kr/contents/event_view.php?rsc_bbs_idx=324&status=&category=%EC%BB%A4%EB%AE%A4%EB%8B%88%ED%8B%B0%20%EC%9D%B4%EB%B2%A4%ED%8A%B8 이렇게 보이는 규칙이 있었음 (확인후 수정 필요 )

## d. 다시보기, 무료 콘텐츠, 백서 페이지 공통 
### 1 상단 검색 사라짐 

### 2 영상 / 무료 콘텐츠 / 백서 카테고리와 카운트가 사라짐 : 레거시 페이지 레이아웃 참고 
### 3 .sub_visual .visual_item 영역이 사라짐: 레거시 레이아웃 참고 
### 4 검색 영역이 사라짐 
### 5 리스트: 레거시 레이아웃이 더 좋은듯, 왼쪽의 리스트나 버튼식으로 구현된 점을 보고 다시 작업해줘 
### 6 썸네일 이미지: 현재 썸네일이 모두 잘못되어 있음 
### 7 보기: 이것도 레거시 페이지를 참조해줘 레거시의 보기페이지의 레이아웃과 동일하게 변경해줘 

## e. 검색 
###  1 검색입력: 검색 입력 form Witdh 값조절 : 현재는 검색 버튼의 위치가 어색함 
### 2 검색결과: 특정키워드(예: 언리얼) 검색시 전체179 새소식143 이벤트36 다시보기0 무료 콘텐츠0 백서/eBook 0 - 다시보기, 무료콘텐츠, 백서의 결과값은 노출이 안되는것 같음 
### 3. 검색 결과에 &nbsp; 같은 html 코드들도 같이 노츨 되고 있음 

## f. 기타
### 1 개인정보 보호정책 : 정책을 검토해주고 2026년도에 맞게 업데이트 필요 
### 2 이용약관: 약관역시 2026년에 맞게 업데이트 필요 


│ V4 페이지 디자인 수정 계획                                                                         │
│                                                                                                    │
│ modify0206.md 기반 수정사항 구현 계획                                                              │
│                                                                                                    │
│ ---                                                                                                │
│ 수정 범위 요약                                                                                     │
│ ┌─────────────┬────────────────────────────────────────┬──────────┐                                │
│ │    구분     │                  항목                  │ 우선순위 │                                │
│ ├─────────────┼────────────────────────────────────────┼──────────┤                                │
│ │ 공통        │ #sub_visual 추가 (8개 페이지)          │ HIGH     │                                │
│ ├─────────────┼────────────────────────────────────────┼──────────┤                                │
│ │ 새소식      │ 서브메뉴 탭, 새창 열기, 검색/여백 수정 │ HIGH     │                                │
│ ├─────────────┼────────────────────────────────────────┼──────────┤                                │
│ │ 이벤트      │ 2열 레이아웃, 글로벌 링크, 여백 수정   │ HIGH     │                                │
│ ├─────────────┼────────────────────────────────────────┼──────────┤                                │
│ │ 이벤트 상세 │ V4 스타일 유지 + 누락 요소 추가        │ MEDIUM   │                                │
│ ├─────────────┼────────────────────────────────────────┼──────────┤                                │
│ │ 리소스 3종  │ 검색/카테고리탭/썸네일/레이아웃 수정   │ HIGH     │                                │
│ ├─────────────┼────────────────────────────────────────┼──────────┤                                │
│ │ 리소스 상세 │ V4 스타일 유지 + 누락 요소 추가        │ MEDIUM   │                                │
│ ├─────────────┼────────────────────────────────────────┼──────────┤                                │
│ │ 검색        │ form width, 검색결과 버그 수정         │ HIGH     │                                │
│ ├─────────────┼────────────────────────────────────────┼──────────┤                                │
│ │ 약관/정책   │ sub_visual만 (내용은 별도 진행)        │ LOW      │                                │
│ └─────────────┴────────────────────────────────────────┴──────────┘                                │
│ ---                                                                                                │
│ Phase 1: 공통 수정 (sub_visual + 여백)                                                             │
│                                                                                                    │
│ 1.1 CSS 파일 수정                                                                                  │
│                                                                                                    │
│ 파일: resource/css/pages/list.css                                                                  │
│                                                                                                    │
│ 추가할 내용:                                                                                       │
│ /* sub.css import 또는 sub_layout.css 스타일 복사 */                                               │
│ /* 더보기 버튼 하단 여백 200px */                                                                  │
│ .v4-load-more {                                                                                    │
│     margin-bottom: 200px;                                                                          │
│ }                                                                                                  │
│                                                                                                    │
│ 파일: resource/css/sub.css (기존 import 활용)                                                      │
│ - v4 페이지에 <link rel="stylesheet" href="/v3/resource/css/sub.css"> 추가                         │
│                                                                                                    │
│ 1.2 8개 목록 페이지에 #sub_visual 추가                                                             │
│ 페이지: event_list.php                                                                             │
│ CSS 클래스: event_vi                                                                               │
│ 제목: 이벤트                                                                                       │
│ 설명: 웨비나, 테크토크, 챌린지 등 온/오프라인 이벤트를 모두 만나보세요.                            │
│ ────────────────────────────────────────                                                           │
│ 페이지: replay_list.php                                                                            │
│ CSS 클래스: resource_vi                                                                            │
│ 제목: 리소스                                                                                       │
│ 설명: 언리얼 페스트, 에픽 라이브, 웨비나...                                                        │
│ ────────────────────────────────────────                                                           │
│ 페이지: news_list.php                                                                              │
│ CSS 클래스: news_vi                                                                                │
│ 제목: 새소식                                                                                       │
│ 설명: 언리얼 엔진 뉴스, 이벤트 그리고 영감을 주는 사례...                                          │
│ ────────────────────────────────────────                                                           │
│ 페이지: free_list.php                                                                              │
│ CSS 클래스: resource_vi                                                                            │
│ 제목: 리소스                                                                                       │
│ 설명: (동일)                                                                                       │
│ ────────────────────────────────────────                                                           │
│ 페이지: book_list.php                                                                              │
│ CSS 클래스: resource_vi                                                                            │
│ 제목: 리소스                                                                                       │
│ 설명: (동일)                                                                                       │
│ ────────────────────────────────────────                                                           │
│ 페이지: total_search.php                                                                           │
│ CSS 클래스: search_vi                                                                              │
│ 제목: 검색                                                                                         │
│ 설명: 키워드를 검색하세요                                                                          │
│ ────────────────────────────────────────                                                           │
│ 페이지: personal.php                                                                               │
│ CSS 클래스: resource_vi                                                                            │
│ 제목: 개인정보보호정책                                                                             │
│ 설명: -                                                                                            │
│ ────────────────────────────────────────                                                           │
│ 페이지: ode.php                                                                                    │
│ CSS 클래스: resource_vi                                                                            │
│ 제목: 이용약관                                                                                     │
│ 설명: -                                                                                            │
│ 추가 위치: 헤더 include 직후, container 이전                                                       │
│                                                                                                    │
│ <?php include G5_PATH.'/inc/common_header26.php'; ?>                                               │
│                                                                                                    │
│ <div id="sub_visual" class="event_vi">                                                             │
│     <h2>이벤트</h2>                                                                                │
│     <p>웨비나, 테크토크, 챌린지 등 온/오프라인 이벤트를 모두 만나보세요.</p>                       │
│ </div>                                                                                             │
│                                                                                                    │
│ <div class="container">                                                                            │
│                                                                                                    │
│ 기존 인라인 타이틀 제거:                                                                           │
│ <!-- 삭제 대상 -->                                                                                 │
│ <div class="wrap" style="padding-top: 48px; padding-bottom: 16px;">                                │
│     <h1 style="font-size: 32px; ...">이벤트</h1>                                                   │
│ </div>                                                                                             │
│                                                                                                    │
│ ---                                                                                                │
│ Phase 2: 새소식 목록 수정                                                                          │
│                                                                                                    │
│ 파일: contents/v4/news_list.php, contents/v4/ajax/news.ajax.php                                    │
│                                                                                                    │
│ 2.1 서브메뉴 탭 추가 (드롭다운 → 탭 변환)                                                          │
│                                                                                                    │
│ 레거시 드롭다운:                                                                                   │
│ <select id="cate_sel">                                                                             │
│     <option value="">전체보기</option>                                                             │
│     <option value="뉴스">뉴스</option>                                                             │
│     <option value="업데이트/출시">업데이트 / 출시</option>                                         │
│     <option value="블로그">블로그</option>                                                         │
│ </select>                                                                                          │
│                                                                                                    │
│ V4 탭으로 변환:                                                                                    │
│ <div class="v4-search-tabs" id="category-tabs">                                                    │
│     <button type="button" class="v4-search-tabs__item active" data-category="">전체</button>       │
│     <button type="button" class="v4-search-tabs__item" data-category="뉴스">뉴스</button>          │
│     <button type="button" class="v4-search-tabs__item"                                             │
│ data-category="업데이트/출시">출시&업데이트</button>                                               │
│     <button type="button" class="v4-search-tabs__item" data-category="블로그">블로그</button>      │
│ </div>                                                                                             │
│                                                                                                    │
│ 2.2 새창 열기 처리                                                                                 │
│                                                                                                    │
│ DB 확인 필요: v3_rsc_news_bbs 테이블의 site_url 또는 blank 컬럼                                    │
│                                                                                                    │
│ 카드 렌더링 수정 (v4_cards.php 또는 직접):                                                         │
│ $target = '';                                                                                      │
│ if (!empty($item['site_url'])) {                                                                   │
│     $link = get_text($item['site_url']);                                                           │
│     $target = ' target="_blank" rel="noopener noreferrer"';                                        │
│ }                                                                                                  │
│                                                                                                    │
│ 2.3 검색 폼 width 수정                                                                             │
│                                                                                                    │
│ 파일: resource/css/pages/list.css                                                                  │
│                                                                                                    │
│ .v4-search-bar {                                                                                   │
│     max-width: 400px;  /* 조정 */                                                                  │
│ }                                                                                                  │
│ .v4-search-bar__button {                                                                           │
│     min-width: 80px;   /* 버튼 크기 조정 */                                                        │
│ }                                                                                                  │
│                                                                                                    │
│ ---                                                                                                │
│ Phase 3: 이벤트 목록 수정                                                                          │
│                                                                                                    │
│ 파일: contents/v4/event_list.php                                                                   │
│                                                                                                    │
│ 3.1 카드 그리드 2열로 변경                                                                         │
│                                                                                                    │
│ 현재: 3열 (grid-template-columns: repeat(3, 1fr))                                                  │
│ 변경: 2열                                                                                          │
│                                                                                                    │
│ /* list.css 또는 인라인 */                                                                         │
│ .v4-card-grid--event {                                                                             │
│     grid-template-columns: repeat(2, 1fr);                                                         │
│ }                                                                                                  │
│                                                                                                    │
│ 또는 페이지 내 스타일:                                                                             │
│ <style>                                                                                            │
│ #card-list.v4-card-grid--gallery {                                                                 │
│     grid-template-columns: repeat(2, 1fr);                                                         │
│ }                                                                                                  │
│ </style>                                                                                           │
│                                                                                                    │
│ 3.2 상태 탭 확인                                                                                   │
│                                                                                                    │
│ 현황: 이미 구현되어 있음 (전체/진행중/종료/결과발표)                                               │
│ <div class="v4-search-tabs" id="status-tabs">                                                      │
│                                                                                                    │
│ 확인 필요: 실제 작동 여부 테스트                                                                   │
│                                                                                                    │
│ 3.3 글로벌 이벤트 링크 추가                                                                        │
│                                                                                                    │
│ 카테고리 탭 유지 + 네비게이션 바로가기용 별도 링크 제공                                            │
│                                                                                                    │
│ 탭: 커뮤니티 | 글로벌  (기존 유지)                                                                 │
│ 별도 URL: /v3/contents/v4/event_list.php?category=글로벌%20이벤트                                  │
│                                                                                                    │
│ 이 URL을 네비게이션 메뉴에서 바로가기로 사용 가능                                                  │
│                                                                                                    │
│ ---                                                                                                │
│ Phase 4: 이벤트 상세 페이지 수정                                                                   │
│                                                                                                    │
│ 파일: contents/v4/event_view.php                                                                   │
│                                                                                                    │
│ 4.1 V4 스타일 유지하며 개선                                                                        │
│                                                                                                    │
│ 현재 V4 구조 유지, 레거시에서 누락된 요소만 추가:                                                  │
│                                                                                                    │
│ 1. 상단 배경 이미지 — 레거시처럼 전체 너비 배경 추가                                               │
│ 2. 상태 + 기간 표시 — 진행중│2023.01.01 ~ 2023.01.31                                               │
│ 3. 자세히 보기 버튼 — site_url이 있을 경우 표시                                                    │
│ 4. 첨부파일 다운로드 — pdf_url 또는 doc_file                                                       │
│ 5. 다른 이벤트 3개 — 관련 콘텐츠 섹션 개선                                                         │
│                                                                                                    │
│ CSS: resource/css/pages/detail.css 수정 (V4 클래스명 유지)                                         │
│                                                                                                    │
│ ---                                                                                                │
│ Phase 5: 리소스 페이지 공통 수정 (다시보기/무료콘텐츠/백서)                                        │
│                                                                                                    │
│ 파일:                                                                                              │
│ - contents/v4/replay_list.php                                                                      │
│ - contents/v4/free_list.php                                                                        │
│ - contents/v4/book_list.php                                                                        │
│                                                                                                    │
│ 5.1 상단 검색 + 카테고리 탭 추가                                                                   │
│                                                                                                    │
│ 레거시 구조:                                                                                       │
│ <div id="resource_search">                                                                         │
│     <div class="serach_in_box">                                                                    │
│         <input type="text" id="sc_keyword" />                                                      │
│         <input type="image" src=".../search_btn.png" />                                            │
│     </div>                                                                                         │
│                                                                                                    │
│     <div class="search_word_box">                                                                  │
│         <ul>                                                                                       │
│             <li class="on"><a href="./replay.php">                                                 │
│                 <span class="text_box">영상</span>                                                 │
│                 <span class="num_box"><?=$replay_cnt?></span>                                      │
│             </a></li>                                                                              │
│             <li><a href="./free.php">                                                              │
│                 <span class="text_box">무료 콘텐츠</span>                                          │
│                 <span class="num_box"><?=$free_cnt?></span>                                        │
│             </a></li>                                                                              │
│             <li><a href="./book.php">                                                              │
│                 <span class="text_box">백서</span>                                                 │
│                 <span class="num_box"><?=$book_cnt?></span>                                        │
│             </a></li>                                                                              │
│         </ul>                                                                                      │
│     </div>                                                                                         │
│ </div>                                                                                             │
│                                                                                                    │
│ PHP 카운트 쿼리:                                                                                   │
│ $replay_cnt = sql_fetch("SELECT COUNT(*) as cnt FROM v3_rsc_review_bbs WHERE                       │
│ display_yn='Y'")['cnt'];                                                                           │
│ $free_cnt = sql_fetch("SELECT COUNT(*) as cnt FROM v3_rsc_free_bbs WHERE display_yn='Y'")['cnt'];  │
│ $book_cnt = sql_fetch("SELECT COUNT(*) as cnt FROM v3_rsc_book_bbs WHERE display_yn='Y'")['cnt'];  │
│                                                                                                    │
│ 5.2 배너 슬라이더 영역 (.visual_item)                                                              │
│                                                                                                    │
│ 레거시:                                                                                            │
│ <div class="sub_visual">                                                                           │
│     <div class="visual_slide">                                                                     │
│         <?php                                                                                      │
│         $banner_result = sql_query("SELECT * FROM v3_shop_banner WHERE bn_position = '다시보기'"); │
│         while($banner = sql_fetch_array($banner_result)) {                                         │
│             $bn_img = G5_DATA_URL.'/banner/'.$banner['bn_id'];                                     │
│         ?>                                                                                         │
│         <a href="<?=$banner['bn_url']?>" target="_blank">                                          │
│             <div class="visual_item" style="background-image:url(<?=$bn_img?>)"></div>             │
│         </a>                                                                                       │
│         <?php } ?>                                                                                 │
│     </div>                                                                                         │
│     <div class="visual_ctrl">                                                                      │
│         <button class="prev">이전</button>                                                         │
│         <button class="next">다음</button>                                                         │
│     </div>                                                                                         │
│ </div>                                                                                             │
│                                                                                                    │
│ 5.3 썸네일 이미지 수정                                                                             │
│                                                                                                    │
│ 현재 v4_helpers.php의 v4_thumb_url() 확인 필요                                                     │
│                                                                                                    │
│ 레거시 로직:                                                                                       │
│ function v4_thumb_url($item, $subdir) {                                                            │
│     // 1. 외부 URL 우선                                                                            │
│     if (!empty($item['thumb_img_url'])) {                                                          │
│         return str_replace(" ", "%20", $item['thumb_img_url']);                                    │
│     }                                                                                              │
│     // 2. 로컬 파일                                                                                │
│     if (!empty($item['thumb_img'])) {                                                              │
│         $path = G5_DATA_PATH."/rsc/".$item['thumb_img'];                                           │
│         if (file_exists($path)) {                                                                  │
│             return G5_DATA_URL.'/rsc/'.$item['thumb_img'];                                         │
│         }                                                                                          │
│     }                                                                                              │
│     // 3. 기본 이미지                                                                              │
│     return "/v3/resource/images/sub/no_img.jpg";                                                   │
│ }                                                                                                  │
│                                                                                                    │
│ 5.4 좌측 필터 버튼 스타일                                                                          │
│                                                                                                    │
│ 레거시 .check_wrap 버튼 스타일:                                                                    │
│ .check_wrap {                                                                                      │
│     display: inline-block;                                                                         │
│     margin: 0 5px 10px 0;                                                                          │
│ }                                                                                                  │
│ .check_wrap input:checked + label {                                                                │
│     background-color: #6E3CFF;                                                                     │
│     color: #fff;                                                                                   │
│ }                                                                                                  │
│                                                                                                    │
│ 현재 v4: .v4-filter-checkbox 사용 중 → 레거시 버튼 스타일로 변경 필요                              │
│                                                                                                    │
│ ---                                                                                                │
│ Phase 6: 리소스 상세 페이지 수정                                                                   │
│                                                                                                    │
│ 파일:                                                                                              │
│ - contents/v4/replay_view.php                                                                      │
│ - contents/v4/free_view.php                                                                        │
│ - contents/v4/book_view.php                                                                        │
│                                                                                                    │
│ V4 스타일 유지하며 개선                                                                            │
│                                                                                                    │
│ 현재 V4 구조 유지, 레거시에서 누락된 요소만 추가:                                                  │
│                                                                                                    │
│ 1. 상단 배경 이미지 — 전체 너비 히어로 이미지                                                      │
│ 2. 발표자 정보 — replay_view: 발표자명 + 발표자료 다운로드                                         │
│ 3. YouTube 임베드 개선 — 반응형 비율 유지                                                          │
│ 4. 외부 링크 표시 — site_url이 있을 경우 새창 버튼                                                 │
│ 5. 목록으로 버튼 — 현재 구현 확인 및 개선                                                          │
│                                                                                                    │
│ ---                                                                                                │
│ Phase 7: 검색 페이지 수정                                                                          │
│                                                                                                    │
│ 파일: contents/v4/total_search.php, contents/v4/ajax/search.ajax.php                               │
│                                                                                                    │
│ 7.1 검색 폼 width 조정                                                                             │
│                                                                                                    │
│ .v4-search-hero__form {                                                                            │
│     max-width: 600px;  /* 조정 */                                                                  │
│ }                                                                                                  │
│                                                                                                    │
│ 7.2 검색 결과 버그 수정                                                                            │
│                                                                                                    │
│ 문제: 다시보기/무료콘텐츠/백서 결과가 0건                                                          │
│                                                                                                    │
│ 원인 조사:                                                                                         │
│ - search.ajax.php의 SQL 쿼리 확인                                                                  │
│ - 테이블명/컬럼명 확인 (content vs contents)                                                       │
│                                                                                                    │
│ // search.ajax.php 확인 필요                                                                       │
│ $search_configs = [                                                                                │
│     'replay' => [                                                                                  │
│         'table' => 'v3_rsc_review_bbs',                                                            │
│         'fields' => "title, content",  // contents가 아닌 content인지 확인                         │
│     ],                                                                                             │
│     ...                                                                                            │
│ ];                                                                                                 │
│                                                                                                    │
│ ---                                                                                                │
│ Phase 8: 약관/정책 업데이트 — 별도 진행 (이번 작업 제외)                                           │
│                                                                                                    │
│ 파일:                                                                                              │
│ - contents/v4/personal.php                                                                         │
│ - contents/v4/ode.php                                                                              │
│                                                                                                    │
│ 이번 작업 범위                                                                                     │
│                                                                                                    │
│ - sub_visual 추가만 진행                                                                           │
│ - 내용 업데이트는 나중에 별도 진행                                                                 │
│                                                                                                    │
│ ---                                                                                                │
│ 수정 대상 파일 목록                                                                                │
│                                                                                                    │
│ PHP 파일 (13개)                                                                                    │
│                                                                                                    │
│ contents/v4/event_list.php      — sub_visual + 2열 그리드                                          │
│ contents/v4/event_view.php      — 레거시 레이아웃                                                  │
│ contents/v4/news_list.php       — sub_visual + 카테고리탭 + 새창열기                               │
│ contents/v4/replay_list.php     — sub_visual + 검색탭 + 배너                                       │
│ contents/v4/free_list.php       — sub_visual + 검색탭 + 배너                                       │
│ contents/v4/book_list.php       — sub_visual + 검색탭 + 배너                                       │
│ contents/v4/replay_view.php     — 레거시 레이아웃                                                  │
│ contents/v4/free_view.php       — 레거시 레이아웃                                                  │
│ contents/v4/book_view.php       — 레거시 레이아웃                                                  │
│ contents/v4/total_search.php    — sub_visual + form width                                          │
│ contents/v4/personal.php        — sub_visual + 내용 검토                                           │
│ contents/v4/ode.php             — sub_visual + 내용 검토                                           │
│ contents/v4/ajax/news.ajax.php  — 카테고리 필터 추가                                               │
│ contents/v4/ajax/search.ajax.php — 검색 버그 수정                                                  │
│ inc/v4_helpers.php              — v4_thumb_url() 수정                                              │
│ inc/v4_cards.php                — 새창 열기 처리                                                   │
│                                                                                                    │
│ CSS 파일 (3개)                                                                                     │
│                                                                                                    │
│ resource/css/pages/list.css     — 여백, 2열 그리드, 필터 스타일                                    │
│ resource/css/pages/detail.css   — 레거시 상세 스타일                                               │
│ resource/css/pages/search.css   — form width                                                       │
│                                                                                                    │
│ ---                                                                                                │
│ 검증 방법                                                                                          │
│                                                                                                    │
│ Playwright 테스트                                                                                  │
│                                                                                                    │
│ 1. 각 페이지 접속 → sub_visual 렌더링 확인                                                         │
│ 2. 새소식 카테고리 탭 클릭 → 필터 동작 확인                                                        │
│ 3. 이벤트 2열 그리드 확인                                                                          │
│ 4. 리소스 검색탭 카운트 확인                                                                       │
│ 5. 검색 페이지에서 "언리얼" 검색 → 모든 섹션 결과 확인                                             │
│ 6. 더보기 버튼 클릭 후 하단 여백 200px 확인                                                        │
│                                                                                                    │
│ 스크린샷 비교                                                                                      │
│                                                                                                    │
│ - 레거시 vs v4 스크린샷 비교                                                                       │
│ - 반응형 (1920px, 1000px, 640px) 각각 확인                                                         │
│                                                                                                    │
│ ---                                                                                                │
│ 작업 순서                                                                                          │
│                                                                                                    │
│ 1. Phase 1 (공통): sub.css 연결 + sub_visual 추가 + 여백 수정                                      │
│ 2. Phase 2 (새소식): 카테고리 탭 + 새창 열기                                                       │
│ 3. Phase 3 (이벤트): 2열 그리드 + 글로벌 링크 URL 제공                                             │
│ 4. Phase 5 (리소스 목록): 검색탭 + 배너 + 썸네일 수정                                              │
│ 5. Phase 7 (검색): form width + 버그 수정                                                          │
│ 6. Phase 4, 6 (상세 페이지): V4 스타일 유지 + 누락 요소 추가                                       │
│ 7. 테스트: Playwright 자동화 테스트                                                                │
│                                                                                                    │
│ Phase 8 (약관/정책 내용 업데이트)은 별도 진행                                                      │
│                                                                                                    │
│ ---                                                                                                │
│ 사용자 결정 사항 (확인됨)                                                                          │
│                                                                                                    │
│ 1. 글로벌 이벤트: 탭 유지 + 네비 바로가기용 별도 URL 제공                                          │
│ 2. 상세 페이지: V4 스타일 유지하며 개선 (레거시 그대로 X)                                          │
│ 3. 약관/정책: 나중에 별도 진행 (이번 작업에서 내용 업데이트 제외)                                  │
│                                                                                                    │
│ 구현 중 확인 필요                                                                                  │
│                                                                                                    │
│ 1. 리소스 배너: v3_shop_banner 테이블에 데이터가 있는지 확인                                       │
│ 2. 검색 버그: search.ajax.php의 content vs contents 컬럼명 확인                                    │
│ 3. 새소식 새창 열기: v3_rsc_news_bbs 테이블의 site_url/blank 컬럼 확인   