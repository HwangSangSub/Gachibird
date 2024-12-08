<?
// ini_set("display_errors", 1);
$menu = "3";
$smenu = "6";

include "../common/inc/inc_header.php";  //헤더 

$DB_con = db1();

if ($mode == "mod") {
	$titNm = "회사 문의 메모 수정";

	$query = "SELECT idx, contact_Name, contact_Email, contact_Tel, contact_Content, contact_Comment, reg_Date FROM TB_COMPANY_CONTACT WHERE idx = :idx;";
	$stmt = $DB_con->prepare($query);
	$stmt->bindparam(":idx", $idx);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$idx = trim($row['idx']);
	$contact_Name =  trim($row['contact_Name']);
	$contact_Email =  trim($row['contact_Email']);
	$contact_Tel =  trim($row['contact_Tel']);
	$contact_Content   =  trim($row['contact_Content']);
	$contact_Comment   =  trim($row['contact_Comment']);
	$reg_Date = trim($row['reg_Date']);

	dbClose($DB_con);
	$stmt = null;
} else {
	$query = "SELECT idx, contact_Name, contact_Email, contact_Tel, contact_Content, contact_Comment, reg_Date FROM TB_COMPANY_CONTACT WHERE idx = :idx;";
	$stmt = $DB_con->prepare($query);
	$stmt->bindparam(":idx", $idx);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$idx = trim($row['idx']);
	$contact_Name =  trim($row['contact_Name']);
	$contact_Email =  trim($row['contact_Email']);
	$contact_Tel =  trim($row['contact_Tel']);
	$contact_Content   =  trim($row['contact_Content']);
	$contact_Comment   =  trim($row['contact_Comment']);
	$reg_Date = trim($row['reg_Date']);

	dbClose($DB_con);
	$stmt = null;
	$mode = "reg";
	$titNm = "회사 문의 메모 등록";
}

$qstr = "findType=" . urlencode($findType) . "&amp;findword=" . urlencode($findword);

include "../common/inc/inc_gnb.php";  //헤더 
include "../common/inc/inc_menu.php";  //메뉴 

?>

<div id="wrapper">

	<div id="container" class="">
		<h1 id="container_title"><?= $titNm ?></h1>
		<div class="container_wr">
			<form name="fmember" id="fmember" action="companyInquiryProc.php" onsubmit="return fubmit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
				<input type="hidden" name="mode" id="mode" value="<?= $mode ?>">
				<input type="hidden" name="idx" id="idx" value="<?= $idx ?>">
				<input type="hidden" name="qstr" id="qstr" value="<?= $qstr ?>">
				<input type="hidden" name="page" id="page" value="<?= $page ?>">

				<div class="tbl_frm01 tbl_wrap">
					<table>
						<caption>회사 문의 답변등록</caption>
						<colgroup>
							<col class="grid_4">
							<col>
						</colgroup>
						<tbody>
							<tr>
								<th scope="row"><label for="idx">회사 문의 번호</label></th>
								<td>
									<input type="text" name="idx" value="<?= $idx  ?>" id="idx" readonly disabled class="frm_input">
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="contact_Email">문의자 이메일</label></th>
								<td>
									<input type="text" name="contact_Email" value="<?= $contact_Email ?>" id="contact_Email" readonly disabled class="frm_input" size="50">
								</td>
								<th scope="row"><label for="contact_Tel">문의자 전화번호</label></th>
								<td>
									<input type="text" name="contact_Tel" value="<?= $contact_Tel ?>" id="contact_Tel" readonly disabled class="frm_input">
								</td>
							</tr>

							<tr>
								<th scope="row"><label for="reg_Date">등록 날짜</label></th>
								<td colspan='3'>
									<input type="text" name="reg_Date" value="<?= $reg_Date ?>" id="reg_Date" readonly disabled class="frm_input" size="50">
								</td>
							</tr>

							<tr>
								<th scope="row"><label for="contact_Content">회사 문의 내용</label></th>
								<td colspan='3'>
									<textarea name="contact_Content" id="contact_Content" cols="20" rows="8" readonly disabled><?= $contact_Content ?></textarea>
								</td>
							</tr>

							<tr>
								<th scope="row"><label for="contact_Comment">메모 내용</label></th>
								<td colspan='3'>
									<textarea name="contact_Comment" id="contact_Comment" cols="20" rows="8"><?= $contact_Comment ?></textarea>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="btn_fixed_top">
					<a href="companyInquiryList.php?<?= $qstr ?>&page=<?= $page ?>" class="btn btn_02">목록</a>
					<input type="submit" value="확인" class="btn_submit btn" accesskey='s'>
				</div>
			</form>


			<script>
				function fubmit(f) {

					if ($.trim($('#contact_Comment').val()) == '') {
						message = "메모를 입력해 주세요!";
						alert(message);
						chk = "#contact_Comment";
						$(chk).focus();
						return false;
					}

					return true;

				}
			</script>

		</div>

		<? include "../common/inc/inc_footer.php";  //푸터 
		?>