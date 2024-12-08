<?
/*======================================================================================================================

* 프로그램			: 게시판 보기
* 페이지 설명		: 게시판 보기
* 파일명            : boardView.php

========================================================================================================================*/

include "../udev/lib/common.php";

$type = trim($type);  //게시판 ID
$idx = trim($idx);  //게시판 Idx
$mem_Id  = trim($memId);
$memLv = $memLv;

if ($type == "1") {  //공지사항
	$titNm = "공지사항";
} else 	if ($type == "2") {  //자주 묻는 질문
	$titNm = "자주 묻는 질문";
} else 	if ($type == "3") {  //이용가이드
	$titNm = "이용가이드";
}

$DB_con = db1();

include "boardSetting.php";  //게시판 환경설정

if ($b_list_lv < $memLv) {  //게시판 글보기권한 
	$message = $altMessage;
	proc_amsg($message);
}


//조회수 업데이트
$bquery = "";
$bquery = "SELECT mem_idx, read_cnt FROM board WHERE type = :type AND idx = :idx LIMIT 1";
$bStmt = $DB_con->prepare($bquery);
$bStmt->bindparam(":type", $type);
$bStmt->bindparam(":idx", $idx);
$bStmt->execute();
$bNum = $bStmt->rowCount();

if ($bNum < 1) { //아닐경우
	$message = "잘못된 접근 방식입니다.";
	proc_msg3($message);
} else {
	while ($bsRow = $bStmt->fetch(PDO::FETCH_ASSOC)) {
		$mem_idx = trim($bsRow['mem_idx']);
		$read_cnt = trim($bsRow['read_cnt']);
	}
}

$chkMemID = $mem_idx;  //세션아이디

//조회수 업데이트
if ($chkMemID != $wMemID || $wMemID == "") {

	//echo "DD";
	// exit;
	if ($b_Ip != $wIp) {
		$upQuery = "UPDATE board SET read_cnt = $read_cnt + 1 WHERE type = :type AND idx = :idx LIMIT 1";
		$upStmt = $DB_con->prepare($upQuery);
		$upStmt->bindparam(":type", $type);
		$upStmt->bindparam(":idx", $idx);
		$upStmt->execute();
	}
}
//조회수 업데이트 끝


$query = " SELECT mem_idx, mem_name, title, content, r_content, reg_date, read_cnt, is_notice, disply ";
$query .= "  FROM  board  WHERE type = :type AND idx = :idx LIMIT 1 ";
$qStmt = $DB_con->prepare($query);
$qStmt->bindparam(":type", $type);
$qStmt->bindparam(":idx", $idx);
$qStmt->execute();
$bNum = $qStmt->rowCount();

if ($bNum < 1) { //아닐경우
	$message = "잘못된 접근 방식입니다.";
	proc_amsg($message);
} else {
	$mode = "mod";
	$chkType = "M";

	while ($v = $qStmt->fetch(PDO::FETCH_ASSOC)) {
		$mem_idx = trim($v['mem_idx']);
		$title = trim($v['title']);
		$mem_name = trim($v['mem_name']);
		// $b_Content = nl2br(trim($v['b_Content']));

		$content = html_entity_decode($v["content"]);
		$content = str_replace(
			'\"',
			'',
			$content
		);
		// $b_Content = htmlspecialchars_decode(trim($b_Content));
		$r_content = htmlspecialchars_decode(trim($v['r_content']));
		$is_notice = trim($v['is_notice']);
		$reg_date = trim($v['reg_date']);
		$cur_time = date("Y.m.d H:i:s", time());
		$b_upload = trim($b_upload);
		$read_cnt  = trim($v['read_cnt']);    //조회수
	}

	$bFileUpload = $b_UploadCnt;

	if ($bFileUpload > 0) {
		# 파일첨부  조회
		$bFileQuery = " SELECT idx, board_idx, name, original_name FROM TB_BOARD_FILE  WHERE board_idx = :idx";
		$bFileQuery .= " ORDER BY idx DESC";
		$bFileStmt = $DB_con->prepare($bFileQuery);
		$bFileStmt->bindparam(":idx", $idx);
		$bFileStmt->execute();
		$bFileNum = $bFileStmt->rowCount();
	}
}



$qstr = "type=" . urlencode($type);

include "boardHead.php";  //게시판 헤더
?>

<script>
	function chkDel(b_Idx, idx) {

		//삭제시작
		if (!confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
			return;
		} else {
			var action = "/board/boardProc.php";
			$.ajax({
				type: "POST",
				url: action,
				data: 'mode=allDel&board_id=' + b_Idx + '&chk=' + idx,
				success: function(response) {
					if ($.trim(response) == 'success') {
						alert("삭제되었습니다.");
						location.replace('/board/boardList.php?type=' + b_Idx);
					} else {
						alert("에러입니다. 관리자에게 문의해 주세요.");
					}
				}
			});
		}
		//삭제끝
	}

	<? if ($b_Chk == "Y") {  ?>

		function FormCheck() {
			var message, chk;

			if ($.trim($('#b_Content').val()) == '') {
				alert("문의 하실 내용을 입력해 주세요!");
				$('#b_Content').focus();
				return;
			}
			$("#theForm").submit();

		}
	<? } ?>
</script>

<content>
	<div class="contents">

		<div>
			<ul class="title_h2">
				<li class="float_l">
					<h2><?= $titNm ?></h2>
				</li>
			</ul>
		</div>
		<?
		if ($type == "2") {  //자주 묻는 질문
		?>
			<!-- 카테고리 -->
			<div class="clear category">
				<ul class="nav">

					<? if ($b_CateChk == "Y") {  //카테고리 사용여부
					?>
						<li>
							<!-- 카테고리 옵션 -->
							<div class="float_l">
						<li <? if ($b_Part == "") { ?>class="on" <? } ?>>
							<a href="/board/boardList.php?type=<?= $type ?>">전체</a>
						</li>
						<li>
							<span class="line_gray">|</span>
						</li>
						<?

						$chk = explode("&", $b_CateName);
						foreach ($chk as $k => $v) :
							$k = $k + 1;
						?>
							<li <? if ($k == $b_Part) { ?>class="on" <? } ?>>
								<a href="/board/boardList.php?board_id=<?= $board_id ?>&amp;b_Part=<?= $k; ?>"><?= $v ?></a>
							</li>
							<li>
								<span class="line_gray">|</span>
							</li>
					<?
						endforeach;
					}
					?>

				</ul>
			</div>
		<? } ?>

		<div class="du01">

			<ul class="view_contents">
				<li class="title">
					<p>
						<span class="title"><?= $title ?></span>
					</p>

					<div class="admin_l">
						<p>
							<span class="date"><?= DateHard($reg_date, 1) ?></span>
						</p>
					</div>

					<!-- 관리자일 경우만 보임 -->

					<? if ($udev['lv'] == '0' || $udev['lv'] == '1') {   //버튼 관리자 권한 
					?>
						<div class="admin_r">
							<p>
								<? if ($_COOKIE['udev']['id'] != 'admin2') { ?>
									<span class="btn blue" onclick="location.href='/board/boardReg.php?<?= $qstr ?>&amp;idx=<?= $idx ?>&amp;mode=M'">수정</span>
									<span class="btn red" onclick="chkDel(<?= $type ?>,<?= $idx ?>)" ;>삭제</span>
								<? } ?>
							</p>
						</div>

					<? } ?>
				</li>

				<li class="m_content">
					<p>
						<?
						if ($bFileNum < 1) { //아닐경우
						} else {
							$imgUrl = "/data/" . $b_upload . "/";

							while ($i = $bFileStmt->fetch(PDO::FETCH_ASSOC)) {
								$bFName = trim($i['b_FName']);

								$fname = explode(".", $i['b_FName']);
								$fileExt = strtolower($fname[count($fname) - 1]);   //확장자 구하는것

								if ($fileExt == "gif" || $fileExt == "jpeg" || $fileExt == "jpg" || $fileExt == "png" || $fileExt == "bmp") {  //확장자 이미지 체크
						?>
									<img src="<?= $imgUrl ?><?= $i['b_FName'] ?>" class="thumb"></br></br>
						<?
								}
							}
						}
						?>
					</p>
					<p><?= $content ?></p>

				</li>
				<? if ($b_Chk == "Y") {  //카테고리 사용여부
				?>
					<p>&nbsp;</p>

					<form name="theForm" id="theForm" action="boardMProc.php" method="post" enctype="multipart/form-data" autocomplete="off">
						<input type="hidden" name="b_MemId" id="b_MemId" value="<?= $b_MemId ?>">
						<input type="hidden" name="b_Name" id="b_Name" value="<?= $b_Name ?>">
						<input type="hidden" name="board_id" id="board_id" value="<?= $board_id ?>">

						<ul class="write_contents">
							<li class="m_content">
								<textarea id="b_Content" name="b_Content" placeholder="문의 하실 내용을 입력하세요."></textarea>
							</li>
						</ul>
					</form>
				<? } ?>

				<li class="bottom">
					<div class="admin_r">
						<p>
							<? if ($b_Chk == "Y") {  //카테고리 사용여부
							?>
								<span class="btn gray" onclick="location.href='/board/boardList.php?<?= $qstr ?>'"> 목록 </span>
								<span class="btn blue" onclick="FormCheck();">상담하기</span>
							<? } else { ?>
								<span class="btn blue" onclick="location.href='/board/boardList.php?<?= $qstr ?>'"> 목록 </span>
							<? } ?>
						</p>
					</div>
				</li>
			</ul>
		</div>




	</div>
</content>

</body>

</html>

<?
dbClose($DB_con);
$bStmt = null;
$upStmt = null;
$qStmt = null;
$bFileStmt = null;
?>