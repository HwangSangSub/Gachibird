<?
$menu = "4";
$smenu = "1";

include "../common/inc/inc_header.php";  //헤더 

$base_url = $PHP_SELF;

$sql_search = " WHERE 1=1";

if ($findword != "") {
	if ($findType == "kwd_nm") {
		$sql_search .= " AND name LIKE :findword ";
	}
}

$DB_con = db1();

//전체 카운트
$cntQuery = "";
$cntQuery = "SELECT COUNT(idx)  AS cntRow FROM keyword {$sql_search}  ";
$cntStmt = $DB_con->prepare($cntQuery);

$cntStmt->execute();
$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
$totalCnt = $row['cntRow'];


if ($rows == '') {
	$rows = '10';
}
$total_page  = ceil($totalCnt / $rows);  // 전체 페이지 계산
if ($page == "") {
	$page = 1;
} // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


if (!$sort1) {
	$sort1  = "A.reg_Date";
	$sort2 = "DESC";
}

$sql_order = "order by $sort1 $sort2";

//목록
$query = "SELECT A.idx as idx, A.name as name, A.reg_date as reg_date, A.search_date as search_date, COUNT(B.idx) as B_cnt FROM  keyword AS A LEFT JOIN member_keyword AS B ON B.key_idx = A.idx GROUP BY idx ORDER BY name ASC";

$stmt = $DB_con->prepare($query);

if ($fr_date != "" || $to_date != "") {
	$stmt->bindValue(":fr_date", $fr_date);
	$stmt->bindValue(":to_date", $to_date);
}

if ($mem_Lv != "") {
	$stmt->bindValue(":mem_lv", $mem_Lv);
}

if ($findword != "") {
	$stmt->bindValue(':findword', '%' . trim($findword) . '%');
}

$stmt->execute();
$numCnt = $stmt->rowCount();

$qstr = "fr_date=" . urlencode($fr_date) . "&amp;to_date=" . urlencode($to_date) . "&amp;findType=" . urlencode($findType) . "&amp;findOs=" . urlencode($findOs) . "&amp;findMir=" . urlencode($findMir) . "&amp;rows=" . urlencode($rows) . "&amp;findword=" . urlencode($findword);

include "../common/inc/inc_gnb.php";		//헤더 
include "../common/inc/inc_menu.php";		//메뉴 
?>
<script type="text/javascript" src="<?= UDEV_DIR ?>/keyword/js/member.js"></script>

<div id="wrapper">
	<div id="container" class="">
		<div class="container_wr">
			<h1 id="container_title">키워드관리</h1>

			<div class="local_ov01 local_ov">
				<span class="btn_ov01"><span class="ov_txt">총키워드수 </span><span class="ov_num"><?= number_format($totalCnt); ?>명 </span>&nbsp;
			</div>


			<form class="local_sch03 local_sch" autocomplete="off">

				<div>
					<strong>리스트출력</strong>
					<select id="rows" name="rows" onchange="$('.local_sch').submit();">
						<option value="10" <? if ($rows == "10") { ?>selected="selected" <? } ?>>10개 씩 보기</option>
						<option value="15" <? if ($rows == "15") { ?>selected="selected" <? } ?>>15개 씩 보기</option>
						<option value="20" <? if ($rows == "20") { ?>selected="selected" <? } ?>>20개 씩 보기</option>
					</select>
				</div>

				<div>
					<strong>분류</strong>
					<select name="findType" id="findType">
						<option value="kwd_nm" <? if ($findType == "kwd_nm") { ?>selected<? } ?>>키워드명</option>
					</select>
					<label for="findword" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
					<input type="text" name="findword" id="findword" value="<?= $findword ?>" class=" frm_input">
				</div>

				<div class="sch_last">
					<strong>최초 생성일</strong>
					<input type="text" name="fr_date" id="fr_date" value="<?= $fr_date ?>" class="frm_input" size="11" maxlength="10">
					<label for="fr_date" class="sound_only">시작일</label>
					~
					<input type="text" name="to_date" id="to_date" value="<?= $to_date ?>" class="frm_input" size="11" maxlength="10">
					<label for="to_date" class="sound_only">종료일</label>
					<input type="submit" value="검색" class="btn_submit">

					<a href="<?= $base_url ?>" class="btn btn_06">새로고침</a>
				</div>
			</form>

			<div class="local_desc01 local_desc">
				<p>
					회원자료 삭제 시 다른 회원이 기존 회원아이디를 사용하지 못하도록 회원아이디, 이름은 삭제하지 않고 영구 보관합니다.
				</p>
			</div>

			<nav class="pg_wrap">
				<?= get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
			</nav>

			<form name="fmemberlist" id="fmemberlist" method="post" autocomplete="off">

				<div class="tbl_head01 tbl_wrap">
					<table>
						<caption>회원관리 목록</caption>
						<thead>

							<!-- 아이디, 이름, 등급, 휴대폰번호, 가입일 -->
							<tr>
								<th scope="col" id="mb_list_chk">
									<label for="chkall" class="sound_only">회원 전체</label>
									<input type="checkbox" name="chkall" class="chkc" id="chkAll" onclick="check_all(this.form)">
								</th>
								<th scope="col" id="mb_list_idx">순번</th>
								<th scope="col" id="mb_list_id">키워드명</th>
								<th scope="col" id="mb_list_mailc">등록중인 유저 수</th>
								<th scope="col" id="mb_list_mailr">최근 뉴스 검색일 </th>
								<th scope="col" id="mb_list_mailr">최초 생성일 </th>
								<th scope="col" id="mb_list_mng" class="last_cell">관리</th>
							</tr>
						</thead>
						<tbody>

							<?

							if ($numCnt > 0) {

								$stmt->setFetchMode(PDO::FETCH_ASSOC);

								while ($row = $stmt->fetch()) {
									$from_record++;
							?>

									<tr class="<?= $bg ?>">
										<td headers="mb_list_chk" class="td_chk">
											<input type="hidden" name="mb_id[<?= $row['idx'] ?>]" id="mb_id_<?= $row['idx'] ?>" value="<?= $row['name'] ?>">
											<!-- <input type="hidden" name="mir_chk[<?= $row['idx'] ?>]" id="mir_chk_<?= $row['idx'] ?>" value="<?= $mir_chk ?>"> -->
											<input type="checkbox" id="chk_<?= $row['idx'] ?>" class="chk" name="chk[]" value="<?= $row['idx'] ?>">
										</td>
										<td headers="mb_list_idx" class="td_idx"><?= $from_record ?></td>
										<td headers="mb_list_lastcall" class="td_date"><?= $row['name'] ?></td>
										<td headers="mb_list_id"><?= $row['B_cnt'] ?></td>
										<td headers="mb_list_open" class="td_name td_mng_s"><?= substr($row['search_date'], 2, 8) . "<br>(" . substr($row['search_date'], 11, 5) . ")" ?></td>
										<td headers="mb_list_open" class="td_mbstat td_mng_s"><?= substr($row['reg_date'], 2, 8) ?></td>
										<td headers="mb_list_mng" class="td_mng td_mng_s">
											<a href="keywordReg.php?mode=mod&idx=<?= $row['idx'] ?>&<?= $qstr ?>&page=<?= $page ?>" class="btn btn_03">수정</a>
										</td>
									</tr>
								<?

								}
								?>
							<? } else { ?>
								<tr>
									<td colspan="13" class="empty_table">자료가 없습니다.</td>
								</tr>
							<? } ?>
						</tbody>
					</table>
				</div>

				<div class="btn_fixed_top">
					<a href="keywordReg.php?mode=reg" id="keyword_add" class="btn btn_01">키워드 추가</a>
				</div>

			</form>
			<nav class="pg_wrap">
				<?= get_apaging($rows, $page, $total_page, "$_SERVER[PHP_SELF]?$qstr"); ?>
			</nav>

			<script>
				$(function() {
					$("#fr_date, #to_date").datepicker({
						changeMonth: true,
						changeYear: true,
						dateFormat: "yy-mm-dd",
						showButtonPanel: true,
						yearRange: "c-99:c+99",
						maxDate: "+0d"
					});
				});
			</script>

		</div>

		<?
		dbClose($DB_con);
		$cntStmt = null;
		$stmt = null;
		$mcntStmt = null;
		$mstmt = null;

		include "../common/inc/inc_footer.php";  //푸터 

		?>