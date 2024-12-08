<?
	include "../lib/alertLib.php";

	// 회원이이디 체크
	if ( $udev['lv']  != "" ) {
		$mem_id = $udev['id'];
	} else {
		$mem_id = $mem_id;
	}

	//회원레벨 체크
	if ( $udev['lv']  != "" ) {
		$memLv = $udev['lv'];
	} else {
		$memLv = $memLv;
	}

	
	//게시판 환경설정
	$bquery = "SELECT type, upload, cate_chk, cate, coment, list_lv, write_lv, comment_lv, is_disply, reg_date, (SELECT COUNT(board_idx) FROM board_file INNER JOIN board ON board_file.board_idx = board.idx WHERE board.type = :type) as b_UploadCnt  FROM  board_info  WHERE idx = :type LIMIT 1";
	
	$bqStmt = $DB_con->prepare($bquery);
	$bqStmt->bindparam(":type",$type);
	$bqStmt->execute();
	$bqNum = $bqStmt->rowCount();
	if($bqNum < 1)  { //아닐경우
		$message = "etc";
		$preUrl = "/board/boardList.php?type=1";
		proc_msg($message, $preUrl);
	} else {
		while($brow=$bqStmt->fetch(PDO::FETCH_ASSOC)) {
			$b_type = trim($brow['type']);
			$b_upload = trim($brow['upload']);
			$b_cate_chk = trim($brow['cate_chk']);
			$b_cate = trim($brow['cate']);
			$b_coment = trim($brow['coment']);
			$b_list_lv = trim($brow['list_lv']);
			$b_write_lv = trim($brow['write_lv']);
			$b_comment_lv = trim($brow['comment_lv']);
			$b_is_disply = trim($brow['is_disply']);
			$b_reg_date = trim($brow['reg_date']);
			$b_UploadCnt = trim($brow['b_UploadCnt']);
		}
	}


	 if ( $memLv == "") {  //레벨이 없을 경우 비회원 권한 부여함.
		$memLv = "9";
	 }

	 $altMessage = "게시판 권한이 없습니다. 로그인 후 이용해 주세요!";

?>