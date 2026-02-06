<?php
/**
 * V4 수정사항 미리보기 페이지
 * 작성일: 2026-02-06
 */
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>V4 수정사항 미리보기</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0a0a0f;
            color: #fff;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            font-size: 32px;
            margin-bottom: 8px;
            color: #33aeec;
        }
        .subtitle {
            color: #888;
            margin-bottom: 40px;
        }
        .section {
            margin-bottom: 40px;
        }
        .section-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #33aeec;
        }
        .link-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 16px;
        }
        .link-card {
            display: block;
            padding: 20px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            text-decoration: none;
            color: #fff;
            transition: all 0.3s ease;
        }
        .link-card:hover {
            background: rgba(51, 174, 236, 0.1);
            border-color: #33aeec;
            transform: translateY(-2px);
        }
        .link-card__title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .link-card__desc {
            font-size: 14px;
            color: #888;
            line-height: 1.5;
        }
        .link-card__badge {
            display: inline-block;
            padding: 4px 8px;
            background: #33aeec;
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            border-radius: 4px;
            margin-bottom: 8px;
        }
        .link-card__badge--legacy {
            background: #666;
        }
        .link-card__badge--new {
            background: #22c55e;
        }
        .compare-row {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
        }
        .compare-row .link-card {
            flex: 1;
        }
        @media (max-width: 640px) {
            .compare-row {
                flex-direction: column;
            }
        }
        .footer {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>V4 수정사항 미리보기</h1>
        <p class="subtitle">2026-02-06 작업 기준 | modify0206.md 기반 수정</p>

        <!-- 메인 테스트 페이지 -->
        <div class="section">
            <h2 class="section-title">메인 테스트 페이지</h2>
            <div class="link-grid">
                <a href="/v3/index_test.php" class="link-card" target="_blank">
                    <span class="link-card__badge link-card__badge--new">TEST</span>
                    <div class="link-card__title">index_test.php</div>
                    <div class="link-card__desc">V4 메인 테스트 페이지</div>
                </a>
            </div>
        </div>

        <!-- 목록 페이지 -->
        <div class="section">
            <h2 class="section-title">목록 페이지 (V4 vs Legacy 비교)</h2>

            <div class="compare-row">
                <a href="/v3/contents/v4/event_list.php" class="link-card" target="_blank">
                    <span class="link-card__badge">V4</span>
                    <div class="link-card__title">이벤트 목록</div>
                    <div class="link-card__desc">sub_visual + 2열 그리드 + 상태 탭</div>
                </a>
                <a href="/contents/event.php" class="link-card" target="_blank">
                    <span class="link-card__badge link-card__badge--legacy">Legacy</span>
                    <div class="link-card__title">이벤트 목록 (레거시)</div>
                    <div class="link-card__desc">기존 이벤트 페이지</div>
                </a>
            </div>

            <div class="compare-row">
                <a href="/v3/contents/v4/event_list.php?category=글로벌%20이벤트" class="link-card" target="_blank">
                    <span class="link-card__badge">V4</span>
                    <div class="link-card__title">글로벌 이벤트</div>
                    <div class="link-card__desc">글로벌 이벤트 바로가기 URL</div>
                </a>
                <a href="/contents/event.php?category=글로벌%20이벤트" class="link-card" target="_blank">
                    <span class="link-card__badge link-card__badge--legacy">Legacy</span>
                    <div class="link-card__title">글로벌 이벤트 (레거시)</div>
                    <div class="link-card__desc">기존 글로벌 이벤트</div>
                </a>
            </div>

            <div class="compare-row">
                <a href="/v3/contents/v4/news_list.php" class="link-card" target="_blank">
                    <span class="link-card__badge">V4</span>
                    <div class="link-card__title">새소식 목록</div>
                    <div class="link-card__desc">sub_visual + 카테고리 탭 (뉴스/출시&업데이트/블로그)</div>
                </a>
                <a href="/contents/news.php" class="link-card" target="_blank">
                    <span class="link-card__badge link-card__badge--legacy">Legacy</span>
                    <div class="link-card__title">새소식 목록 (레거시)</div>
                    <div class="link-card__desc">기존 새소식 페이지</div>
                </a>
            </div>

            <div class="compare-row">
                <a href="/v3/contents/v4/replay_list.php" class="link-card" target="_blank">
                    <span class="link-card__badge">V4</span>
                    <div class="link-card__title">다시보기 (영상)</div>
                    <div class="link-card__desc">sub_visual + 리소스 탭 + 상단 검색</div>
                </a>
                <a href="/contents/replay.php" class="link-card" target="_blank">
                    <span class="link-card__badge link-card__badge--legacy">Legacy</span>
                    <div class="link-card__title">다시보기 (레거시)</div>
                    <div class="link-card__desc">기존 다시보기 페이지</div>
                </a>
            </div>

            <div class="compare-row">
                <a href="/v3/contents/v4/free_list.php" class="link-card" target="_blank">
                    <span class="link-card__badge">V4</span>
                    <div class="link-card__title">무료 콘텐츠</div>
                    <div class="link-card__desc">sub_visual + 리소스 탭 + 상단 검색</div>
                </a>
                <a href="/contents/free.php" class="link-card" target="_blank">
                    <span class="link-card__badge link-card__badge--legacy">Legacy</span>
                    <div class="link-card__title">무료 콘텐츠 (레거시)</div>
                    <div class="link-card__desc">기존 무료 콘텐츠 페이지</div>
                </a>
            </div>

            <div class="compare-row">
                <a href="/v3/contents/v4/book_list.php" class="link-card" target="_blank">
                    <span class="link-card__badge">V4</span>
                    <div class="link-card__title">백서/eBook</div>
                    <div class="link-card__desc">sub_visual + 리소스 탭 + 상단 검색</div>
                </a>
                <a href="/contents/book.php" class="link-card" target="_blank">
                    <span class="link-card__badge link-card__badge--legacy">Legacy</span>
                    <div class="link-card__title">백서 (레거시)</div>
                    <div class="link-card__desc">기존 백서 페이지</div>
                </a>
            </div>
        </div>

        <!-- 검색 페이지 -->
        <div class="section">
            <h2 class="section-title">검색 페이지</h2>
            <div class="compare-row">
                <a href="/v3/contents/v4/total_search.php" class="link-card" target="_blank">
                    <span class="link-card__badge">V4</span>
                    <div class="link-card__title">통합 검색</div>
                    <div class="link-card__desc">sub_visual + form width 조정</div>
                </a>
                <a href="/contents/total_search.php" class="link-card" target="_blank">
                    <span class="link-card__badge link-card__badge--legacy">Legacy</span>
                    <div class="link-card__title">통합 검색 (레거시)</div>
                    <div class="link-card__desc">기존 검색 페이지</div>
                </a>
            </div>
            <div class="link-grid" style="margin-top: 16px;">
                <a href="/v3/contents/v4/total_search.php?keyword=언리얼" class="link-card" target="_blank">
                    <span class="link-card__badge link-card__badge--new">TEST</span>
                    <div class="link-card__title">검색: "언리얼"</div>
                    <div class="link-card__desc">검색 결과 테스트</div>
                </a>
            </div>
        </div>

        <!-- 약관/정책 -->
        <div class="section">
            <h2 class="section-title">약관/정책</h2>
            <div class="link-grid">
                <a href="/v3/contents/v4/personal.php" class="link-card" target="_blank">
                    <span class="link-card__badge">V4</span>
                    <div class="link-card__title">개인정보보호정책</div>
                    <div class="link-card__desc">sub_visual 추가 (내용 업데이트 별도)</div>
                </a>
                <a href="/v3/contents/v4/ode.php" class="link-card" target="_blank">
                    <span class="link-card__badge">V4</span>
                    <div class="link-card__title">이용약관</div>
                    <div class="link-card__desc">sub_visual 추가 (내용 업데이트 별도)</div>
                </a>
            </div>
        </div>

        <!-- 수정 요약 -->
        <div class="section">
            <h2 class="section-title">수정 요약 (2026-02-06)</h2>
            <div style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 12px; line-height: 1.8;">
                <p><strong>✅ 완료:</strong></p>
                <ul style="margin-left: 20px; margin-bottom: 16px;">
                    <li>8개 페이지에 #sub_visual 추가</li>
                    <li>새소식: 카테고리 탭 (전체/뉴스/출시&업데이트/블로그) + 새창 열기</li>
                    <li>이벤트: 2열 그리드 레이아웃</li>
                    <li>리소스 3종: 상단 검색바 + 리소스 카테고리 탭 (건수 표시)</li>
                    <li>검색: form max-width 600px</li>
                    <li>더보기 버튼 하단 여백 200px</li>
                </ul>
                <p><strong>⏳ 미완료 (별도 진행):</strong></p>
                <ul style="margin-left: 20px;">
                    <li>이벤트/리소스 상세 페이지 레이아웃 개선</li>
                    <li>약관/정책 내용 업데이트 (2026년)</li>
                    <li>배너 슬라이더 (.visual_item)</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            Epic Lounge V4 리뉴얼 프로젝트 |
            <a href="https://github.com/sunghyeonhwang/Epic_Lounge_26_renewal" style="color: #33aeec;">GitHub</a>
        </div>
    </div>
</body>
</html>
