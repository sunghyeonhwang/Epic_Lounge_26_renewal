<?php
$sub_menu = "600500";
include_once('./_common.php');

$g5['title'] = '리소스 카테고리 관리';
include_once('./admin.head.php');
auth_check_menu($auth, $sub_menu, 'r');

?>



<form name="" id="fboardform" action="" onsubmit="return fboardform_submit(this)" method="post" enctype="multipart/form-data">


<section id="anc_bo_basic">
    <h2 class="h2_frm">리소스관리</h2>
    <?php echo $pg_anchor ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>리소스 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_3">
        </colgroup>
        <tbody>
        <tr>
            <th scope="row" class="text_center"><label for="bo_table">산업분야</label></th>
            <td>
					<table class="btn_table_re">
						<colgroup>
							<col style="width:6%;" />
							<col style="width:78%" />
							<col style="width:8%;" />
							<col style="width:8%;" />
						</colgroup>
						<thead  class="text_center">
							<tr>
								<th>No</th>
								<th>항목</th>
								<th>순서</th>
								<th>관리</th>
							</tr>
						</thead>
						<tbody class="text_center">
			
<?

	$sql = " select * from v3_rsc_book_category where rsc_type='산업분야' order by sort asc ";
	$result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
					<tr>
						<td><?=$i+1?></td>
						<td class="text_left"><?=$row["rsc_name"]?></td>
						<td><span class="arrow_btn"><a href="#n" onclick="move_to('up','<?=$row["rsc_idx"]?>',this)">▲</a><a href="#n"  onclick="move_to('down','<?=$row["rsc_idx"]?>',this)">▼</a></span></td>
						<td><a href="#b" onclick="delete_rsc('./rsc_book_mng_proc.php?rsc_idx=<?=$row["rsc_idx"]?>&mode=delete','<?=$row["rsc_name"]?>')" class="btn btn_05">삭제</a></td>
					</tr>
<?
	}
?>
				<tr class="last">
					<td colspan="4">
						<input type="text" name="bo_table" value="" id="bo_table" class="frm_input" maxlength="30"> <a href="#n" onclick="insert_rsc('산업분야',this)" class="btn btn_03">추가</a>
					</td>
				</tr>
				</tbody>
				</table>
            </td>
        </tr>
        <tr>
            <th scope="row" class="text_center"><label for="bo_table">제품군</label></th>
            <td>
					<table class="btn_table_re">
						<colgroup>
							<col style="width:6%;" />
							<col style="width:78%" />
							<col style="width:8%;" />
							<col style="width:8%;" />
						</colgroup>
						<thead  class="text_center">
							<tr>
								<th>No</th>
								<th>항목</th>
								<th>순서</th>
								<th>관리</th>
							</tr>
						</thead>
						<tbody class="text_center">
			
<?

	$sql = " select * from v3_rsc_book_category where rsc_type='제품군' order by sort asc ";
	$result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
					<tr>
						<td><?=$i+1?></td>
						<td class="text_left"><?=$row["rsc_name"]?></td>
						<td><span class="arrow_btn"><a href="#n" onclick="move_to('up','<?=$row["rsc_idx"]?>',this)">▲</a><a href="#n"  onclick="move_to('down','<?=$row["rsc_idx"]?>',this)">▼</a></span></td>
						<td><a href="#b" onclick="delete_rsc('./rsc_book_mng_proc.php?rsc_idx=<?=$row["rsc_idx"]?>&mode=delete','<?=$row["rsc_name"]?>')" class="btn btn_05">삭제</a></td>
					</tr>
<?
	}
?>
				<tr class="last">
					<td colspan="4">
						<input type="text" name="bo_table" value="" id="bo_table" class="frm_input" maxlength="30"> <a href="#n" onclick="insert_rsc('제품군',this)" class="btn btn_03">추가</a>
					</td>
				</tr>
				</tbody>
				</table>
            </td>
        </tr>
        <tr>
            <th scope="row" class="text_center"><label for="bo_table">주제</label></th>
            <td>
					<table class="btn_table_re">
						<colgroup>
							<col style="width:6%;" />
							<col style="width:78%" />
							<col style="width:8%;" />
							<col style="width:8%;" />
						</colgroup>
						<thead  class="text_center">
							<tr>
								<th>No</th>
								<th>항목</th>
								<th>순서</th>
								<th>관리</th>
							</tr>
						</thead>
						<tbody class="text_center">
			
<?

	$sql = " select * from v3_rsc_book_category where rsc_type='주제' order by sort asc ";
	$result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
					<tr>
						<td><?=$i+1?></td>
						<td class="text_left"><?=$row["rsc_name"]?></td>
						<td><span class="arrow_btn"><a href="#n" onclick="move_to('up','<?=$row["rsc_idx"]?>',this)">▲</a><a href="#n"  onclick="move_to('down','<?=$row["rsc_idx"]?>',this)">▼</a></span></td>
						<td><a href="#b" onclick="delete_rsc('./rsc_book_mng_proc.php?rsc_idx=<?=$row["rsc_idx"]?>&mode=delete','<?=$row["rsc_name"]?>')" class="btn btn_05">삭제</a></td>
					</tr>
<?
	}
?>
				<tr class="last">
					<td colspan="4">
						<input type="text" name="bo_table" value="" id="bo_table" class="frm_input" maxlength="30"> <a href="#n" onclick="insert_rsc('주제',this)" class="btn btn_03">추가</a>
					</td>
				</tr>
				</tbody>
				</table>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>




<script>
function delete_rsc(url,name){
	if(confirm(name+" 삭제를 진행하시겠습니까?")){
		location.href = url;
	}
}
function insert_rsc(cate,frm){
	var add_text =  $(frm).prev("input").val();

	if(confirm(cate+"카테고리에 "+add_text+"를 추가하시겠습니까?")){
		location.href = "./rsc_book_mng_proc.php?rsc_name="+add_text+"&rsc_type="+cate+"&mode=insert";
	}
	
}
</script>


<link rel="stylesheet" href="//rawgit.com/Soldier-B/jquery.toast/master/jquery.toast/jquery.toast.min.css" />
<script src="//rawgit.com/Soldier-B/jquery.toast/master/jquery.toast/jquery.toast.min.js"></script>
<script>
	function move_to(mode, idx,field){
		$.ajax({
			type : 'post',
			url : "./rsc_book_mng_proc.php",
			data : {'mode':'sort','idx': idx,'move':mode},
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