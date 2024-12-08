<?

	// 회원이이디 체크
    $mem_Id = $mem_Id;

	//회원레벨 체크
	$memLv = $memLv;


	//게시판 환경설정
	$bquery = "SELECT type, upload, cate_chk, cate, coment, list_lv, write_lv, comment_lv, is_disply, reg_date, (SELECT COUNT(board_idx) FROM board_file INNER JOIN board ON board_file.board_idx = board.idx WHERE board.type = :type) as b_UploadCnt FROM  board_info  WHERE idx = :type LIMIT 1";

	$bqStmt = $DB_con->prepare($bquery);
	$bqStmt->bindparam(":type",$type);
	$bqStmt->execute();
	$bqNum = $bqStmt->rowCount();

	if($bqNum < 1)  { //아닐경우
	    $result = array("result" => "error", "errorMsg" => "잘못된 접근입니다. 해당하는 게시판이 없습니다." );
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


?>