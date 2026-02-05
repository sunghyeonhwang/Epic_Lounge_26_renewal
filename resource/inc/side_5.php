<div id="sub_visual" class="vi_5">
	<h2>회원광장</h2>
	<p>산림을 튼튼하게 국민을 안전하게</p>
</div>

  <div id="sub_container">
  	
    <div class="wrap clearfix">
      <div id="side">
        <div class="side_title">
          <div class="innerbox">
            <p class="title">회원광장</p>
          </div>
        </div>
        <nav class="side_menu" data-menu-type="4">
          <h2 class="skip">좌측메뉴</h2>
          <div class="depth1" data-menu-depth="1">
            <ul class="depth1_list" data-menu-list="1">

            <?php if ($is_member) {  ?>
           <li class="depth1_item" id="m_1"><a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=<?php echo G5_BBS_URL ?>/register_form.php"  class="depth1_text" data-menu-text="1">정보수정</a></li>
           <li class="depth1_item" id="m_2"><a href="<?php echo G5_BBS_URL ?>/logout.php"  class="depth1_text" data-menu-text="1">로그아웃</a></li>
            <?php if ($is_admin) {  ?>
            <li class="depth1_item" id="m_3"><a href="<?php echo correct_goto_url(G5_ADMIN_URL); ?>"  class="depth1_text" data-menu-text="1">관리자</a></li>
            <?php }  ?>
            <?php } else {  ?>
            <li class="depth1_item" id="m_4"><a href="/board/bbs/board.php?bo_table=join_send"  class="depth1_text" data-menu-text="1">회원가입</a></li>
            <li class="depth1_item" id="m_5"><a href="<?php echo G5_BBS_URL ?>/login.php"  class="depth1_text" data-menu-text="1">로그인</a></li>
            <?php }  ?>
            </ul>
          </div>
        </nav>
      </div>
      <!-- //.side -->
      <main id="colgroup" class="colgroup">
        <article>
           <div id="contents">
