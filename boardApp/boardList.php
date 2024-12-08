<?
	include "../lib/common.php";

	
	$type = trim($type);  //게시판 ID
	$page = trim($page);  // 페이징

	if ($page == "") {
		$page = 1;
	} else {
		$page = (int)$page;
	}
	
	if ($type == "") {
		$type = 1;
	}

	if ($type == "1") {  //공지사항
		$titNm = "공지사항";
	} else 	if ($type == "2") {  //자주 묻는 질문
		$titNm = "자주 묻는 질문";
	} else 	if ($type == "3") {  //이용가이드
		$titNm = "이용가이드";
	}

	$DB_con = db1();

	include "boardSetting.php";  //게시판 환경설정

	if ($type == "4")	{ //기타접근제어
	    $result = array("result" => false, "errorMsg" => "잘못된 접근입니다. 해당하는 게시판이 없습니다." );
	} else {
	    $result = array("result" => false, "errorMsg" => "게시판 권한이 없습니다. 로그인 후 이용해 주세요!" );
    }

	########## 기본 설정 시작 ##########
	//	$url_etc = "?page=";
	//$base_url = $PHP_SELF."?board_id=".$board_id;
	$base_url = $PHP_SELF;
	
	if($part != "")  {
	    $sql_search = " AND type = :type";
	}
	
	
	//전체 카운트
	$cntQuery = "";
	$cntQuery = "SELECT COUNT(idx)  AS cntRow FROM board WHERE type = :type AND disply = 'Y' {$sql_search} " ;
	//$cntQuery = "SELECT COUNT(idx)  AS cntRow FROM TB_BOARD WHERE b_Idx = :board_id AND b_Not = '' AND b_Disply = 'Y' {$sql_search} " ;
	$cntStmt = $DB_con->prepare($cntQuery);
	$cntStmt->bindparam(":type",$type);
	
	if($part != "")  {
	    $cntStmt->bindparam(":type",$part);
	}
	
	$cntStmt->execute();
	$row = $cntStmt->fetch(PDO::FETCH_ASSOC);
	$totalCnt = $row['cntRow'];
	$totalCnt = (int)$totalCnt;

	$start = ($page - 1) * 10;
	$rows = 10;
	$total_page  = ceil($totalCnt / $rows);  // 전체 페이지 계산

	if ($type == "1" || $type == "5") {  //일반 게시물
		## 공지글

		$nquery = " SELECT idx, mem_idx, type, title, content, r_content ,reg_date, read_cnt, is_notice, disply";
		$nquery .= "  , ( SELECT COUNT(board_idx) FROM board_file WHERE board_file.board_idx = board.idx ) AS fileNCnt  ";
		$nquery .= "  FROM board WHERE  is_notice = 'Y' AND type = :type ";
		$nquery .= " AND  disply = 'Y'" ;    //사용여부 체크
		$nquery .= " {$sql_search} ORDER BY is_notice DESC, idx DESC LIMIT {$start}, 10";
		$nqStmt = $DB_con->prepare($nquery);
		$nqStmt->bindparam(":type",$type);
		
		if($part != "")  {
		    $nqStmt->bindparam(":type",$part);
		}
		
		$nqStmt->execute();
		$Ncounts = $nqStmt->rowCount();

	}

	## 공지글이 아닌 게시물
	$query = "";
	$query = " SELECT idx, mem_idx, type, mem_name, title, content, reg_date, read_cnt";

	if ($b_Type == "1") {  //일반게시판
		$query .= "  , ( SELECT COUNT(idx) FROM board_file WHERE board_file.board_idx = board.idx) AS fileCnt  ";
	}
	
	if ($b_Type == "3" || $b_Type == "8" ) { //웹진게시판, 이벤트게시판
		$query .= "  , ( SELECT name FROM board_file WHERE board_file.board_idx = board.idx AND ORDER BY board_file.idx ASC  limit 1 ) AS fileNm  ";
		$query .= "  , ( SELECT idx FROM board_file WHERE board_file.board_idx = board.idx ORDER BY board_file.idx ASC  limit 1 ) AS fileIdx ";
	}

	if ($b_Type == "4") {  //FAQ게시판 
		$query .= "  , r_content ";
	}

	$query .= "  FROM board WHERE idx = type ";
	$query .= " AND  is_notice = '' AND  disply = 'Y'";    //사용여부 체크
	// $query .= " AND  b_Not = '' AND  b_Disply = 'Y'";    //사용여부 체크
	$query .= "  {$sql_search} ORDER BY idx DESC LIMIT {$start}, 10";
//echo $query;
	$qStmt = $DB_con->prepare($query);
	$qStmt->bindparam(":type",$type);
	
	if($part != "")  {
	    $qStmt->bindparam(":type",$part);
	}
	
	$qStmt->execute();
	$counts = $qStmt->rowCount();
	
	
	
// 	if ($board_id == "2") {  //자주 묻는 질문
// 	 if ($b_cate_chk == "Y") {  //카테고리 사용여부 		
			
// 	   $bcate  = [];
// 	   $chk = explode("&",$b_cate);
// 	   foreach($chk as $k=>$v):
// 		$k = $k +1;
		
// 		$mCate = array("part"=> $k, "typeNm" => $v);
// 		array_push($bcate, $mCate);
		
// 	   endforeach;
	   

// 	 }
//    } 
  
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
			
