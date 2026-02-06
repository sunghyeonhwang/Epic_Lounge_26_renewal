# V4 페이지 디자인 수정 결과 보고서

> 작업일: 2026-02-06
> 기반 문서: modify0206.md

---

## 완료된 작업 요약

| Phase | 구분 | 내용 | 상태 |
|-------|------|------|------|
| 1 | 공통 | #sub_visual 추가 (8개 페이지) | ✅ 완료 |
| 1 | 공통 | sub.css 연결 | ✅ 완료 |
| 1 | 공통 | 더보기 버튼 하단 여백 200px | ✅ 완료 |
| 2 | 새소식 | 카테고리 탭 (전체/뉴스/출시&업데이트/블로그) | ✅ 완료 |
| 2 | 새소식 | 새창 열기 처리 (site_url 있을 경우) | ✅ 완료 |
| 3 | 이벤트 | 2열 그리드 레이아웃 | ✅ 완료 |
| 3 | 이벤트 | 글로벌 이벤트 URL 유지 | ✅ 완료 |
| 5 | 리소스 3종 | 상단 검색바 추가 | ✅ 완료 |
| 5 | 리소스 3종 | 리소스 카테고리 탭 + 건수 표시 | ✅ 완료 |
| 7 | 검색 | form max-width 600px 조정 | ✅ 완료 |

---

## 수정된 파일 목록

### PHP 파일 (12개)

| 파일 | 수정 내용 |
|------|----------|
| `contents/v4/event_list.php` | sub_visual + 2열 그리드 클래스 |
| `contents/v4/news_list.php` | sub_visual + 카테고리 탭 + 필터 파라미터 |
| `contents/v4/replay_list.php` | sub_visual + 리소스 탭 + 상단 검색바 |
| `contents/v4/free_list.php` | sub_visual + 리소스 탭 + 상단 검색바 |
| `contents/v4/book_list.php` | sub_visual + 리소스 탭 + 상단 검색바 |
| `contents/v4/total_search.php` | sub_visual (search_vi) |
| `contents/v4/personal.php` | sub_visual (resource_vi) |
| `contents/v4/ode.php` | sub_visual (resource_vi) |
| `contents/v4/ajax/news.ajax.php` | 카테고리 필터 지원 추가 |
| `inc/v4_cards.php` | 새창 열기 처리 (news + site_url) |

### CSS 파일 (2개)

| 파일 | 수정 내용 |
|------|----------|
| `resource/css/pages/list.css` | 여백 200px, 2열 그리드, 리소스 탭, 뉴스 탭 스타일 |
| `resource/css/pages/search.css` | form max-width 800px → 600px |

---

## 주요 변경 상세

### 1. #sub_visual 추가

모든 v4 목록 페이지에 레거시와 동일한 서브 비주얼 영역 추가:

```html
<div id="sub_visual" class="[클래스]">
    <h2>[제목]</h2>
    <p>[설명]</p>
</div>
```

| 페이지 | 클래스 | 제목 |
|--------|--------|------|
| event_list.php | event_vi | 이벤트 |
| news_list.php | news_vi | 새소식 |
| replay_list.php | resource_vi | 리소스 |
| free_list.php | resource_vi | 리소스 |
| book_list.php | resource_vi | 리소스 |
| total_search.php | search_vi | 검색 |
| personal.php | resource_vi | 개인정보보호정책 |
| ode.php | resource_vi | 이용약관 |

### 2. 새소식 카테고리 탭

레거시 드롭다운 → V4 탭 버튼으로 변환:

```html
<div class="v4-news-tabs" id="category-tabs">
    <button data-category="">전체</button>
    <button data-category="뉴스">뉴스</button>
    <button data-category="업데이트/출시">출시&업데이트</button>
    <button data-category="블로그">블로그</button>
</div>
```

### 3. 이벤트 2열 그리드

```css
.v4-card-grid--event {
    grid-template-columns: repeat(2, 1fr);
}
```

### 4. 리소스 카테고리 탭

3개 리소스 페이지에 공통 탭 추가:

```html
<div class="v4-resource-tabs">
    <a href="replay_list.php" class="v4-resource-tabs__item">
        <span class="v4-resource-tabs__text">영상</span>
        <span class="v4-resource-tabs__count">123</span>
    </a>
    <a href="free_list.php" class="v4-resource-tabs__item">
        <span class="v4-resource-tabs__text">무료 콘텐츠</span>
        <span class="v4-resource-tabs__count">45</span>
    </a>
    <a href="book_list.php" class="v4-resource-tabs__item">
        <span class="v4-resource-tabs__text">백서</span>
        <span class="v4-resource-tabs__count">67</span>
    </a>
</div>
```

---

## 미완료 작업 (별도 진행 필요)

### Phase 4: 이벤트 상세 페이지
- [ ] 상단 배경 이미지 추가
- [ ] 상태 + 기간 표시 개선
- [ ] 자세히 보기 버튼 (site_url)
- [ ] 첨부파일 다운로드 (pdf_url, doc_file)

### Phase 6: 리소스 상세 페이지
- [ ] replay_view.php: 발표자 정보 + 발표자료 다운로드
- [ ] YouTube 임베드 반응형 개선
- [ ] 외부 링크 새창 버튼

### Phase 8: 약관/정책
- [ ] personal.php: 개인정보보호정책 내용 업데이트 (2026년)
- [ ] ode.php: 이용약관 내용 업데이트 (2026년)

### 기타 확인 필요
- [ ] 리소스 배너 슬라이더 (.visual_item) - v3_shop_banner 테이블 데이터 확인
- [ ] 썸네일 이미지 문제 - v4_thumb_url() 함수 검증
- [ ] 검색 결과 HTML 엔티티 (&nbsp;) 노출 문제

---

## Git 커밋 정보

```
커밋: 02b32e4
브랜치: main
메시지: feat: v4 페이지 디자인 수정 (modify0206.md 기반)
파일: 13개 변경 (+853, -64)
```

---

## 테스트 URL

| 페이지 | URL |
|--------|-----|
| 이벤트 목록 | https://epiclounge.co.kr/v3/contents/v4/event_list.php |
| 글로벌 이벤트 | https://epiclounge.co.kr/v3/contents/v4/event_list.php?category=글로벌%20이벤트 |
| 새소식 목록 | https://epiclounge.co.kr/v3/contents/v4/news_list.php |
| 다시보기 목록 | https://epiclounge.co.kr/v3/contents/v4/replay_list.php |
| 무료 콘텐츠 목록 | https://epiclounge.co.kr/v3/contents/v4/free_list.php |
| 백서 목록 | https://epiclounge.co.kr/v3/contents/v4/book_list.php |
| 통합 검색 | https://epiclounge.co.kr/v3/contents/v4/total_search.php |
| 개인정보보호정책 | https://epiclounge.co.kr/v3/contents/v4/personal.php |
| 이용약관 | https://epiclounge.co.kr/v3/contents/v4/ode.php |
