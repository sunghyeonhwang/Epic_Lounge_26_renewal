
    
<div class="accessibility">
	<a href="#conatiner">본문 바로가기</a>
</div>
<header class="header">
<div class="wrap">
    <div class="logo"><a href="../../index.php" class="logo__link"><span class="blind">한국치산기술협회</span></a></div>
   <div class="language">
        <ul>
			  <?php if ($is_member) {  ?>
            <li><a href="/../../board/bbs/member_confirm.php?url=/../../board/bbs/register_form.php">정보수정</a></li>
            <li><a href="/../../board/bbs/logout.php">로그아웃</a></li>
            <?php } else {  ?>
            <li><a href="/board/bbs/board.php?bo_table=join_send">회원가입</a></li>
            <li><a href="/../../board/bbs/login.php">로그인</a></li>
            <?php }  ?>

			  <?php  if ($member['mb_level'] >= 4){ ?>
			
			<br />
            <li><a href="/board/bbs/board.php?bo_table=memeber_board_1">직원전용게시판</a></li>
            <?php }  ?>
            <?php if ($is_admin) {  ?>
            <li class="tnb_admin"><a href="<?php echo correct_goto_url(G5_ADMIN_URL); ?>">관리자</a></li>
            <?php }  ?>

        </ul>
    </div>



    </div>
    <div class="lnb_shadow"></div>
    <div id="lnb">
        <div data-menu-open="lnb" class="nav_open">
            <button type="button" data-menu-button class="nav_button"><span class="blind">메뉴 열기</span><span class="bar"></span><span class="bar"></span><span class="bar"></span></button>
        </div>
        <nav data-menu-type="1" data-menu-top-background="full" data-menu-bottom-background="full" class="nav">
            <h2 class="skip">전체메뉴</h2>
            <div class="nav_top">
                <div class="nav_logo">한국치산기술협회</div>
            </div>
            <div data-menu-depth="1" class="depth1">
                <!-- 메뉴 depth 1 // -->
                <ul class="depth1_list clearfix" data-menu-list="1">


                    <li class="depth1_item"><a href="/../../sub_1_1.php" target="_self" data-menu-text="1" class="depth1_text"><span>기관 소개</span></a>
                        <div data-menu-depth="2" class="depth2">
                            <ul data-menu-list="2" class="depth2_list">
                                <li class="depth2_item"><a href="/../../sub_1_1.php" target="_self" data-menu-text="2" class="depth2_text">인사말</a></li>
                                <li class="depth2_item"><a href="/../../sub_1_2.php" target="_self" data-menu-text="2" class="depth2_text">미션과 비젼</a></li>
                                <li class="depth2_item"><a href="/../../sub_1_3.php" target="_self" data-menu-text="2" class="depth2_text">정관</a></li>
                                <li class="depth2_item"><a href="/../../sub_1_4.php" target="_self" data-menu-text="2" class="depth2_text">연혁</a></li>
                                <li class="depth2_item"><a href="/../../sub_1_5.php" target="_self" data-menu-text="2" class="depth2_text">CI소개</a></li>
                                <li class="depth2_item"><a href="/../../sub_1_6.php" target="_self" data-menu-text="2" class="depth2_text">조직안내</a></li>
                                <li class="depth2_item"><a href="/../../sub_5_1.php" target="_self" data-menu-text="2" class="depth2_text">회원자격, 가입안내/신청</a></li>
                                <li class="depth2_item"><a href="/../../sub_1_7_1.php" target="_self" data-menu-text="2" class="depth2_text">오시는길</a>
								<div data-menu-depth="3" class="depth3">
									<ul data-menu-list="3" class="depth3_list">
										<li class="depth3_item"><a href="/../../sub_1_7_1.php" target="_self" data-menu-text="3" class="depth3_text"><span>본회</span></a></li>
									   <li class="depth3_item"><a href="/../../sub_1_7_2_1.php" target="_self" data-menu-text="3" class="depth3_text"><span>지부</span></a></li>
									</ul>
								</div>
								</li>

                            </ul>
                        </div>
                    </li>
                    <li class="depth1_item"><a href="/../../sub_2_1.php" target="_self" data-menu-text="1" class="depth1_text"><span>사업안내</span></a>
                        <div data-menu-depth="2" class="depth2">
                            <ul data-menu-list="2" class="depth2_list">
                                <li class="depth2_item"><a href="/../../sub_2_1.php" target="_self" data-menu-text="2" class="depth2_text">주요사업</a>
								<div data-menu-depth="3" class="depth3">
									<ul data-menu-list="3" class="depth3_list">
										<li class="depth3_item"><a href="/../../sub_2_1.php" data-menu-text="3" class="depth3_text"><span>평가</span></a></li>
										<li class="depth3_item"><a href="/../../sub_2_1_2.php" data-menu-text="3" class="depth3_text"><span>점검</span></a></li>
										<li class="depth3_item"><a href="/../../sub_2_1_3.php" data-menu-text="3" class="depth3_text"><span>산사태관련</span></a></li>
									</ul>
								</div>
								</li>
                                <li class="depth2_item"><a href="/../../sub_2_2_1.php" target="_self" data-menu-text="2" class="depth2_text">연구사업</a>
								<div data-menu-depth="3" class="depth3">
									<ul data-menu-list="3" class="depth3_list">
									   <li class="depth3_item"><a href="/../../sub_2_2_1.php" data-menu-text="3" class="depth3_text"><span>사방ㆍ산사태 관련 연구</span></a></li>
									   <li class="depth3_item"><a href="/../../sub_2_2_2.php" data-menu-text="3" class="depth3_text"><span>임도 관련 연구</span></a></li>
									   <li class="depth3_item"><a href="/../../sub_2_2_3.php" data-menu-text="3" class="depth3_text"><span>공간분석 관련 연구</span></a></li>
										<li class="depth3_item"><a href="/board/bbs/board.php?bo_table=sub_2_2_4" data-menu-text="3" class="depth3_text"><span>주요 연구 추진실적</span></a></li>
									</ul>
								</div>
								</li>
                                <li class="depth2_item"><a href="/../../sub_2_3.php" target="_self" data-menu-text="2" class="depth2_text">산지전용 타당성조사</a>
								<div data-menu-depth="3" class="depth3">
									<ul data-menu-list="3" class="depth3_list">
                    <li class="depth3_item"><a href="/../../sub_2_3.php" data-menu-text="3" class="depth3_text"><span>사업소개</span></a></li>
                    <li class="depth3_item"><a href="/board/bbs/board.php?bo_table=sub_2_3_1_new" data-menu-text="3" class="depth3_text"><span>사업추진 및 진행현황</span></a></li>
                    <li class="depth3_item"><a href="/board/bbs/board.php?bo_table=sub_2_3_2_new" data-menu-text="3" class="depth3_text"><span>결과 정보공개</span></a></li>
									</ul>
								</div>
								
								
								</li>
                                <li class="depth2_item"><a href="/../../sub_2_5.php" target="_self" data-menu-text="2" class="depth2_text">신재생에너지</a></li>
                                <li class="depth2_item"><a href="/../../sub_2_4.php" target="_self" data-menu-text="2" class="depth2_text">교육사업</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="depth1_item"><a href="/board/bbs/board.php?bo_table=sub_3_1_1" target="_self" data-menu-text="1" class="depth1_text"><span>정보마당</span></a>
                        <div data-menu-depth="2" class="depth2">
                            <ul data-menu-list="2" class="depth2_list">
                                <li class="depth2_item"><a href="/board/bbs/board.php?bo_table=sub_3_1_1" target="_self" data-menu-text="2" class="depth2_text">관계법령</a>
								<div data-menu-depth="3" class="depth3">
									<ul data-menu-list="3" class="depth3_list">
										  <li class="depth3_item"><a href="/board/bbs/board.php?bo_table=sub_3_1_1" data-menu-text="3" class="depth3_text"><span>사방사업관련</span></a></li>
										  <li class="depth3_item"><a href="/board/bbs/board.php?bo_table=sub_3_1_2" data-menu-text="3" class="depth3_text"><span>생태복원관련</span></a></li>
										  <li class="depth3_item"><a href="/board/bbs/board.php?bo_table=sub_3_1_3" data-menu-text="3" class="depth3_text"><span>임도관련</span></a></li>
										  <li class="depth3_item"><a href="/board/bbs/board.php?bo_table=sub_3_1_4" data-menu-text="3" class="depth3_text"><span>기타</span></a></li>
									</ul>
								</div>
								</li>
                                <li class="depth2_item"><a href="/board/bbs/board.php?bo_table=sub_3_2_1" target="_self" data-menu-text="2" class="depth2_text">자료실</a>
								<div data-menu-depth="3" class="depth3">
									<ul data-menu-list="3" class="depth3_list">
                    <li class="depth3_item"><a href="/board/bbs/board.php?bo_table=sub_3_2_1" data-menu-text="3" class="depth3_text"><span>사업관련</span></a></li>
                    <li class="depth3_item"><a href="/board/bbs/board.php?bo_table=sub_3_2_2" data-menu-text="3" class="depth3_text"><span>일반자료</span></a></li>
									</ul>
								</div>
								</li>
                                <li class="depth2_item"><a href="/../../sub_3_3_1.php" target="_self" data-menu-text="2" class="depth2_text">관련사이트</a>
								<div data-menu-depth="3" class="depth3">
									<ul data-menu-list="3" class="depth3_list">
                    <li class="depth3_item"><a href="/../../sub_3_3_1.php" data-menu-text="3" class="depth3_text"><span>국내</span></a></li>
                    <li class="depth3_item"><a href="/../../sub_3_3_2.php" data-menu-text="3" class="depth3_text"><span>국외</span></a></li>
									</ul>
								</div>
								</li>
                            </ul>
                        </div>
                    </li>
                    <li class="depth1_item"><a href="/board/bbs/board.php?bo_table=sub_4_1" target="_self" data-menu-text="1" class="depth1_text"><span>알림 홍보</span></a>
                        <div data-menu-depth="2" class="depth2">
                            <ul data-menu-list="2" class="depth2_list">
                                <li class="depth2_item"><a href="/board/bbs/board.php?bo_table=sub_4_1" target="_self" data-menu-text="2" class="depth2_text">공지사항</a></li>
                                <li class="depth2_item"><a href="/board/bbs/board.php?bo_table=sub_4_2" target="_self" data-menu-text="2" class="depth2_text">채용정보</a></li>
                                <li class="depth2_item"><a href="/board/bbs/board.php?bo_table=sub_4_3" target="_self" data-menu-text="2" class="depth2_text">Q&A</a></li>
                                <li class="depth2_item"><a href="/board/bbs/board.php?bo_table=sub_4_4" target="_self" data-menu-text="2" class="depth2_text">홍보자료</a></li>
                                <li class="depth2_item"><a href="/board/bbs/board.php?bo_table=sub_4_5" target="_self" data-menu-text="2" class="depth2_text">포토갤러리</a></li>
                                <li class="depth2_item"><a href="/board/bbs/board.php?bo_table=sub_4_6" target="_self" data-menu-text="2" class="depth2_text">사방사업 품질경진대회</a></li>
                            </ul>
                        </div>
					</li>
                 


					

                    <!-- language -->

                </ul>

            </div>
				<div class="m_language">
					<ul>
					  <?php if ($is_member) {  ?>
            <li><a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=<?php echo G5_BBS_URL ?>/register_form.php">정보수정</a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/logout.php">로그아웃</a></li>
            <?php if ($is_admin) {  ?>
            <li class="tnb_admin"><a href="<?php echo correct_goto_url(G5_ADMIN_URL); ?>">관리자</a></li>
            <?php }  ?>
            <?php } else {  ?>
            <li><a href="<?php echo G5_BBS_URL ?>/register.php">회원가입</a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/login.php">로그인</a></li>
            <?php }  ?>

						
					</ul>
				</div>
        </nav>
        <div data-menu-close="lnb" class="nav_close">
            <button type="button" data-menu-button class="nav_button">메뉴 닫기</button>
        </div>

    </div>
    <!-- //lnb -->


 

</header>