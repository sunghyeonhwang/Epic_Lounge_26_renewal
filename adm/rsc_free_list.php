<?php
$sub_menu = "600400";
include_once('./_common.php');

$g5['title'] = '리소스 관리';
include_once('./admin.head.php');
auth_check_menu($auth, $sub_menu, 'r');

$where = "";
if(!empty($_GET[sc_text])){
	$where .= " and (";
	$where .= " title like '%{$_GET[sc_text]}%' ";
	$where .= " or cate_industry like '%{$_GET[sc_text]}%' ";
	$where .= " or cate_engine like '%{$_GET[sc_text]}%' ";
	$where .= " or category like '%{$_GET[sc_text]}%' ";
	
	$where .= " ) ";

}

	$page = $_GET["page"];
	if(empty($page)) $page = 1;

	$pageunit = $_GET["pageunit"];
	if(empty($pageunit)) $pageunit  = 10;

	$page_limit = $pageunit * $page;
	$start = ($page-1) * $pageunit;
	
	$sql_list_cnt = 
		" select count(*) cnt from v3_rsc_free_bbs 
		  where 1=1 " 
		  . $where . " ";
	$cnt= sql_fetch($sql_list_cnt);

	$sql_list = 
		" select * from v3_rsc_free_bbs 
		  where 1=1 " 
		  . $where . "
		  order by ordr desc, rsc_bbs_idx desc ";
	$sql_list .= " limit {$start}, ".$pageunit;
	$result2 = sql_query($sql_list);
	$total_page  = ceil($cnt["cnt"] / $pageunit);  // 전체 페이지 계산
	$qstr = "pageunit=".$pageunit."&sc_text=".$_GET["sc_text"];

?>



<div class="local_ov01 local_ov">
  <span class="btn_ov01"><span class="ov_txt">생성된 리소스수</span><span class="ov_num"> <?=$cnt["cnt"]?>개</span></span>
</div>

<form name="" id="" class="local_sch01 local_sch" method="get">


<label for="" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="sc_text" value="<?=$_GET["sc_text"]?>" id="sc_text" required="" class="required frm_input">
<input type="submit" value="검색" class="btn_submit">

</form>



<form name="frm_delete" id="frm_delete" action="./rsc_free_list_proc.php" method="post">
<input type="hidden" name="mode" value="group_delete" />
<div class="tbl_head01 tbl_wrap">
	<?php echo get_paging($pageunit, $page, $total_page, '?'.$qstr.'&amp;page='); ?>
    <table>
    <caption>게시판관리 목록</caption>
    <thead>
    <tr>
        <th scope="col" width="60px">
            <label for="chkall" class="sound_only">게시판 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col" width="200px">카테고리</th>
        <th scope="col" >제목</th>
        <th scope="col"  width="150px">등록일</th>
        <th scope="col"  width="100px">출력여부</th>
        <th scope="col"  width="100px">순서변경</th>
        <th scope="col"  width="100px">미리보기</th>
        <th scope="col"  width="60px">구분</th>
    </tr>
    </thead>
    <tbody>
    
<?

    for ($j=0; $row_list=sql_fetch_array($result2); $j++) {
?>

    <tr class="bg0" id="tr_<?=$row_list["rsc_bbs_idx"]?>">
        <td class="td_chk">
            <label for="chk_<?=$row_list["rsc_bbs_idx"]?>" class="sound_only"><?=$row_list["title"]?></label>
            <input type="checkbox" name="chk[]" value="<?=$row_list["rsc_bbs_idx"]?>" id="chk_<?=$row_list["rsc_bbs_idx"]?>">
        </td>
        <td>
            <select name="category"  onchange="change_category('<?=$row_list["rsc_bbs_idx"]?>',this)" style="width:180px;">
		<?

			$sql = " select * from v3_rsc_free_category where rsc_type='카테고리' order by sort asc ";
			$result = sql_query($sql);
			for ($i=0; $row=sql_fetch_array($result); $i++) {
		?>
							<option value="<?=$row["rsc_name"]?>" <?=$row_list["category"] == $row["rsc_name"] ? "selected='selected'" : ""?>><?=$row["rsc_name"]?></option>
		<?
			}
		?>
			</select>
		</td>
        <td class="text_left">
			<h3 class="subject_h3"><?=$row_list["title"]?></h3>
			<div  class="button_div">
			<p class="button_title">산업분야</p>
			<ul class="button_ul">
<?

	$sql = " select * from v3_rsc_free_category where rsc_type='산업분야' order by sort asc ";
	$result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
				<li><input type="checkbox" name="industry_<?=$row_list["rsc_bbs_idx"]?>" id="industry_<?=$row_list["rsc_bbs_idx"]?>_<?=$i?>" <?=strpos($row_list["cate_industry"], $row["rsc_name"]) === false ? "":"checked='checked'"?> onchange="change_iti(<?=$row_list["rsc_bbs_idx"]?>,this)" value="<?=$row["rsc_name"]?>"><label for="industry_<?=$row_list["rsc_bbs_idx"]?>_<?=$i?>"><?=$row["rsc_name"]?></label></li>
<?
	}
?>
			</ul>
			</div>

			<div  class="button_div">
			<p class="button_title">엔진버전</p>
			<ul class="button_ul">
<?

	$sql = " select * from v3_rsc_free_category where rsc_type='엔진버전' order by sort asc ";
	$result = sql_query($sql);
	$arr_engine = explode("|",$row_list["cate_engine"]);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
		$checked = false;
		foreach($arr_engine as $val){
			if(trim($val) == trim($row["rsc_name"])) $checked = true;
		}
?>
				<li><input type="checkbox" name="product_<?=$row_list["rsc_bbs_idx"]?>" id="product_<?=$row_list["rsc_bbs_idx"]?>_<?=$i?>" <?=!$checked ? "":"checked='checked'"?> onchange="change_product(<?=$row_list["rsc_bbs_idx"]?>,this)" value="<?=$row["rsc_name"]?>"><label for="product_<?=$row_list["rsc_bbs_idx"]?>_<?=$i?>"><?=$row["rsc_name"]?></label></li>
<?
	}
?>
			</ul>
			</div>
			
        </td>
        <td><?=$row_list["reg_date"]?></td>
        <td>
            <select id="display_yn" name="display_yn"  onchange="change_display('<?=$row_list["rsc_bbs_idx"]?>',this)" style="width:50px;"> 
				<option value="Y" <?=$row_list["display_yn"] == "Y" ? "selected='selected'":""?>>Y</option>
				<option value="N" <?=$row_list["display_yn"] == "N" ? "selected='selected'":""?>>N</option>
			</select>
        </td>
<?

	$link = "";
	if($row_list["site_url"]){
		$link = $row_list["site_url"];
	}else{
		$link = "/v3/contents/free_view.php?idx=".$row_list["rsc_bbs_idx"];
	}
?>
        <td><span class="arrow_btn"><a href="#n" onclick="move_to('up','<?=$row_list["rsc_bbs_idx"]?>',this)">▲</a><a href="#n"  onclick="move_to('down','<?=$row_list["rsc_bbs_idx"]?>',this)">▼</a></span></td>

        <td><a href="<?=$link?>" target="_blank" class="btn btn_01">미리보기</a></td>
        <td class="">
            <a href="./rsc_free_modify.php?rsc_bbs_idx=<?=$row_list["rsc_bbs_idx"]?>" class="btn btn_03 margin_b_5">수정</a><br />
			<a href="#n" onclick="delete_bbs('<?=$row_list["rsc_bbs_idx"]?>',this)" class="btn btn_02">삭제</a>
		</td>
    </tr>

<?
	}
?>
    
        </tbody>
    </table>
	<?php echo get_paging($pageunit, $page, $total_page, '?'.$qstr.'&amp;page='); ?>
</div>

<div class="btn_fixed_top">
        <input type="button" onclick="group_delete()" name="" value="선택삭제" onclick="" class="btn_02 btn">
            <a href="./rsc_free_write.php" id="bo_add" class="btn_01 btn">리소스 추가</a>
    </div>

</form>


<script>
function group_delete(){
	if(confirm("정말로 선택한 리소스를 삭제 하시겠습니까?")){
		$("#frm_delete").submit();
	}
}
$(function(){
    $(".board_copy").click(function(){
        window.open(this.href, "win_board_copy", "left=100,top=100,width=550,height=450");
        return false;
    });
});
</script>





<link rel="stylesheet" href="//rawgit.com/Soldier-B/jquery.toast/master/jquery.toast/jquery.toast.min.css" />
<script src="//rawgit.com/Soldier-B/jquery.toast/master/jquery.toast/jquery.toast.min.js"></script>
<script>
	/* switch (t) {
      case 'danger':
        $.toast('<h4>경고!</h4> 위험을 알리는 토스트. 닫아야 없어집니다.', {
          sticky: true,
          type: 'danger'
        });
        return;
      case 'info':
        $.toast('<h4>알림!</h4> 2초간 알려드립니다.', {
          duration: 2000,
          type: 'info'
        });
        return;
      case 'success':
        $.toast('<h4>성공!</h4> 토스트 생성에 성공했습니다. 닫아야 없어집니다.', {
          sticky: true,
          type: 'success'
        });
        return;
    }*/
	function ajax_module(idx,field,value,mode){
		$.ajax({
			type : 'post',
			url : "./rsc_free_list_proc.php",
			data : {'mode':mode,'idx': idx,'field':field,'value':value},
			dataType : 'json',
			success : function(json) {
				if(mode == "delete"){
					$.toast('<h4>성공!</h4> 삭제가 완료되었습니다. ', {
					  sticky: true,
					  duration: 2000,
					  type: 'success'
					});
					$(field).parent().remove();
				}else{
						$.toast('<h4>성공!</h4> 수정이 완료되었습니다. ', {
						  sticky: true,
						  duration: 2000,
						  type: 'success'
						});
				}


				
			},error : function() {
				$.toast('<h4>실패!</h4> 수정에 실패하였습니다. 화면을 새로고침 후 다시 시도하여주세요. ', {
				  sticky: true,
		          duration: 2000,
				  type: 'error'
				});
			}
		});
	}
	function change_category(idx,field){
		var value = $(field).val();
		ajax_module(idx,'category',value,'update')
	}
	function change_iti(idx,field){
		var value = $(field).val();
		var selected = [];
		var checekd_value = "";
		$('input:checkbox[name="'+$(field).attr("name")+'"]').each(function() {
			if($(this).is(':checked')){
				checekd_value  += $(this).val()+"|";
			}
		});
		ajax_module(idx,'iti',checekd_value,'update')

	}
	function change_product(idx,field){
		var selected = [];
		var checekd_value = "";
		$('input:checkbox[name="'+$(field).attr("name")+'"]').each(function() {
			if($(this).is(':checked')){
				checekd_value  += $(this).val()+"|";
			}
		});
		ajax_module(idx,'product',checekd_value,'update')
	}
	function change_subject(idx,field){
		var selected = [];
		var checekd_value = "";
		$('input:checkbox[name="'+$(field).attr("name")+'"]').each(function() {
			if($(this).is(':checked')){
				checekd_value  += $(this).val()+"|";
			}
		});
		ajax_module(idx,'subject',checekd_value,'update')
	}
	function change_difficult(idx,field){
		var value = $(field).val();
		ajax_module(idx,'diff',value,'update')
	}
	function change_display(idx,field){
		var value = $(field).val();
		ajax_module(idx,'display',value,'update')
	}
	function delete_bbs(idx, field){
		if(confirm("정말로 삭제 하시겠습니까?")){
			$.ajax({
				type : 'post',
				url : "./rsc_free_list_proc.php",
				data : {'mode':'delete','idx': idx,'field':'','value':''},
				dataType : 'json',
				success : function(json) {
					$.toast('<h4>성공!</h4> 삭제가 완료되었습니다. ', {
					  sticky: true,
					  duration: 2000,
					  type: 'success'
					});
					$(field).parent().parent().remove();
					
				},error : function() {
					$.toast('<h4>실패!</h4> 삭제를 실패하였습니다. 화면을 새로고침 후 다시 시도하여주세요. ', {
					  sticky: true,
					  duration: 2000,
					  type: 'error'
					});
				}
			});
		}
	}



    function move_to(mode, idx,field){
        $.ajax({
            type : 'post',
            url : "./rsc_sort_proc.php",
            data : {'mode':'free','idx': idx,'move':mode},
            dataType : 'json',
            success : function(json) {
                $.toast('<h4>성공!</h4> 순서가 변경되었습니다. ', {
                    sticky: true,
                    duration: 2000,
                    type: 'success'
                });


                if(mode =="up"){
                    var ele = $(field).parent().parent().parent();
                    ele.insertBefore(ele.prev());
                }else if(mode =="down"){
                    var ele = $(field).parent().parent().parent();
                    console.log(ele.next());
                    if(!ele.next().hasClass("last")){
                        ele.insertAfter(ele.next());

                    }
                }

            },error : function() {
                $.toast('<h4>실패!</h4> 변경에 실패하였습니다. 화면을 새로고침 후 다시 시도하여주세요. ', {
                    sticky: true,
                    duration: 2000,
                    type: 'error'
                });
            }
        });
    }

	$(document).ready(function() {
		$.toast.config.align = 'right';
		$.toast.config.width = 400;
	});

</script>

<?php
include_once ('./admin.tail.php');