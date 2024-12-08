<?
include "../udev/lib/common.php";

$type = trim($type);  //게시판 ID
$mem_idx  = trim($idx);
$memLv = $memLv;

if ($type == "1") {  //공지사항
	$titNm = "공지사항";
} else 	if ($type == "2") {  //자주 묻는 질문
	$titNm = "자주 묻는 질문";
} else 	if ($type == "3") {  //이용가이드
	$titNm = "이용가이드";
}

$DB_con = db1();

include "boardHead.php";  //게시판 헤더
include "boardSetting.php";  //게시판 환경설정

if ($type == "4") { //기타접근제어
	$message = "잘못된 접근 방식입니다.";
	proc_msg3($message);
} else {

	if ($b_list_lv < $memLv) {  //게시판 리스트 권한 
		$message = $altMessage;
		loginUChk($message);
	}
}

########## 기본 설정 시작 ##########
//	$url_etc = "?page=";
//$base_url = $PHP_SELF."?board_id=".$board_id;
$base_url = $PHP_SELF;

// if ($b_Part != "") {
// 	$sql_search = " AND cate = :cate";
// }

//전체 카운트
$cntQuery = "";
$cntQuery = "SELECT COUNT(idx)  AS cntRow FROM board WHERE type = :type AND disply = 'Y' "; //{$sql_search} 
$cntStmt = $DB_con->prepare($cntQuery);
$cntStmt->bindparam(":type", $type);

// if ($b_Part != "") {
// 	$cntStmt->bindparam(":b_Cate", $b_Part);
// }

$cntStmt->execute();
$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
$totalCnt = $row['cntRow'];

$rows = 10;
$total_page  = ceil($totalCnt / $rows);  // 전체 페이지 계산
if ($page == "") {
	$page = 1;
} // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


if ($type == "1" || $type == "5") {  //일반 게시물
	## 공지글
	$nquery = "";
	$nquery = " SELECT idx, mem_idx, type, title, content, r_content ,reg_date, read_cnt, is_notice, disply";
	$nquery .= "  , ( SELECT COUNT(board_idx) FROM board_file WHERE board_file.board_idx = board.idx) AS fileNCnt  ";
	$nquery .= "  FROM board WHERE  is_notice = 'Y' AND type = :type ";
	$nquery .= " AND  disply = 'Y'";    //사용여부 체크
	$nquery .= " ORDER BY is_notice DESC, idx DESC"; //  {$sql_search}
	$nqStmt = $DB_con->prepare($nquery);
	$nqStmt->bindparam(":type", $type);

	// if ($b_Part != "") {
	// 	$nqStmt->bindparam(":b_Cate", $b_Part);
	// }

	$nqStmt->execute();
	$Ncounts = $nqStmt->rowCount();
}

## 공지글이 아닌 게시물
$query = "";
$query = " SELECT idx, mem_idx, type, title, content, r_content ,reg_date, read_cnt, is_notice, disply  ";

if ($type == "1") {  //일반게시판
	$query .= "  , ( SELECT COUNT(board_idx) FROM board_file WHERE board_file.board_idx = board.idx) AS fileCnt  ";
}

if ($type == "2") {  //갤러리게시판
	$query .= "  , ( SELECT name FROM board_file WHERE board_file.board_idx = board.idx ORDER BY board_file.idx ASC  limit 1 ) AS fileNm  ";
	$query .= "  , ( SELECT idx FROM board_file WHERE board_file.board_idx = board.idx ORDER BY board_file.idx ASC  limit 1 ) AS fileIdx ";
	$query .= "  , ( SELECT COUNT(board_idx) FROM board_file WHERE board_file.board_idx = board.idx) AS fileCnt  ";
} else if ($type == "3" || $type == "8") { //웹진게시판, 이벤트게시판
	$query .= "  , ( SELECT name FROM board_file WHERE board_file.board_idx = board.idx ORDER BY board_file.idx ASC  limit 1 ) AS fileNm  ";
	$query .= "  , ( SELECT idx FROM board_file WHERE board_file.board_idx = board.idx ORDER BY board_file.idx ASC  limit 1 ) AS fileIdx ";
}

if ($type == "4") {  //FAQ게시판 
	$query .= "  , r_content ";
}

$query .= "  FROM board WHERE type = :type ";
$query .= " AND  is_notice = '' AND  disply = 'Y'";    //사용여부 체크

$query .= "  ORDER BY idx DESC limit  {$from_record}, {$rows}"; //  {$sql_search}
$qStmt = $DB_con->prepare($query);
$qStmt->bindparam(":type", $board_id);

// if ($b_Part != "") {
// 	$qStmt->bindparam(":b_Cate", $b_Part);
// }

$qStmt->execute();
$counts = $qStmt->rowCount();

$qstr = "?type=" . urlencode($type);
?>

<div class="contents">
	<!-- 앱일 경우 상단 타이틀이 있으므로 해당 영역을 안보이게 해야 합니다.
	<div style="display:none;">
-->
	<div>
		<ul class="title_h2">
			<li class="float_l">
				<h2><?= $titNm ?></h2>
			</li>
			<li class="float_r">
				<p>
					<? if ($_COOKIE['udev']['id'] != 'admin2') { ?>
						<span class="btn gray" onclick="location.href='/board/boardReg.php?type=<?= $type ?>'">글쓰기</span>
					<? } ?>
				</p>
			</li>
		</ul>
	</div>

	<?
	if ($board_id == "2") {  //자주 묻는 질문
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
						<li <? if ($k == $type) { ?>class="on" <? } ?>>
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

	<?

	if ($type == "1" || $type == "5") {  //일반게시판, 기타게시판
		include "boardNomalList.php";
	} elseif ($type == "2") {  //갤러리게시판
		include "boardPhoto.php";
	} elseif ($type == "4") {  //FAQ게시판
		include "boardFaq.php";
	}


	dbClose($DB_con);
	$nqStmt = null;
	$cntStmt = null;
	$qStmt = null;

	?>


	<? if ($totalCnt < 1) { ?>
	<? } else { ?>
		<div class="page">
			<ul class="pagination pagination-sm">
				<?= get_fpaging($rows, $page, $total_page, "$base_url$qstr"); ?>
			</ul>
		</div>
	<? } ?>

</div>
</content>


</body>


