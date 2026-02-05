<div id="sub_visual" class="vi_3">
	<h2>직원페이지</h2>
	<p>산림을 튼튼하게 국민을 안전하게</p>
</div>

  <div id="sub_container">
  	
    <div class="wrap clearfix">
      <div id="side">
        <div class="side_title">
          <div class="innerbox">
            <p class="title">직원페이지</p>
          </div>
        </div>
        <nav class="side_menu" data-menu-type="4">
          <h2 class="skip">좌측메뉴</h2>
          <div class="depth1" data-menu-depth="1">
            <ul class="depth1_list" data-menu-list="1">

            <?php if ($is_member) {  ?>

           <li class="depth1_item" id="m_5"><a href="/board/bbs/board.php?bo_table=memeber_board_1"  class="depth1_text" data-menu-text="1">사방사업 관련자료</a></li>
           <li class="depth1_item" id="m_6"><a href="/board/bbs/board.php?bo_table=memeber_board_2_new"  class="depth1_text" data-menu-text="1">월간보고 업무 자료 </a></li>
           <li class="depth1_item" id="m_7"><a href="/board/bbs/board.php?bo_table=memeber_board_3"  class="depth1_text" data-menu-text="1">경조사 알림</a></li>
           <li class="depth1_item" id="m_8"><a href="/board/bbs/board.php?bo_table=memeber_board_4"  class="depth1_text" data-menu-text="1">일반게시판</a></li>
           <li class="depth1_item" id="m_9"><a href="/board/bbs/board.php?bo_table=memeber_board_5"  class="depth1_text" data-menu-text="1">노사협의회 참여마당</a></li>

           <li class="depth1_item" id="m_1"><a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=<?php echo G5_BBS_URL ?>/register_form.php"  class="depth1_text" data-menu-text="1">정보수정</a></li>
           <li class="depth1_item" id="m_2"><a href="<?php echo G5_BBS_URL ?>/logout.php"  class="depth1_text" data-menu-text="1">로그아웃</a></li>
            <?php } else {  ?>
            <li class="depth1_item" id="m_3"><a href="<?php echo G5_BBS_URL ?>/register.php"  class="depth1_text" data-menu-text="1">회원가입</a></li>
            <li class="depth1_item" id="m_4"><a href="<?php echo G5_BBS_URL ?>/login.php"  class="depth1_text" data-menu-text="1">로그인</a></li>
            <?php }  ?>
            </ul>
          </div>
        </nav>
      </div>
      <!-- //.side -->
      <main id="colgroup" class="colgroup">
        <article>
           <div id="contents">
