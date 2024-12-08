<?
/**
 * 회원가입
 * 
 * 차후 회원 가입이 필요할 경우  로직 변경하여 사용한다.
 */

include "../lib/common.php";
include "../lib/functionDB.php";
$DB_con = db1();

$mem_token = trim($token);                    		              // 회원 토큰
$reg_date = TIME_YMDHIS;				 	                     // 오늘날짜
$reg_keyword_cnt = guestRegKeywordCnt();  // 게스트 키워드 등록 가능 수

if ($mem_token != "") {

	$mem_chk_query = "SELECT idx, disply, login_cnt FROM member WHERE mem_token = :mem_token";
	$mem_chk_stmt = $DB_con->prepare($mem_chk_query);
	$mem_chk_stmt->bindparam(":mem_token", $mem_token);
	$mem_chk_stmt->execute();
	$mem_chk_num = $mem_chk_stmt->rowCount();

	if ($mem_chk_num < 1) { // 중복토큰이 없으면 우선 GUEST 권한으로 회원 가입 진행. guest : 9

		// 회원 등록
		$ins_query = "INSERT INTO member SET mem_token = :mem_token, reg_date = :reg_date, reg_keyword_cnt = :reg_keyword_cnt";
		$ins_stmt = $DB_con->prepare($ins_query);
		$ins_stmt->bindparam(":mem_token", $mem_token);
		$ins_stmt->bindparam(":reg_date", $reg_date);
		$ins_stmt->bindparam(":reg_keyword_cnt", $reg_keyword_cnt);
		$ins_stmt->execute();
        $m_idx = $DB_con->lastInsertId();  		// 저장된 idx 값

		if($m_idx != ""){
			$result = array("result" => true, "idx" => (int)$m_idx);
		}else{
			$result = array("result" => false);
		}
	} else {  // 등록된 정보가 있을 경우
		while ($mem_chk_row = $mem_chk_stmt->fetch(PDO::FETCH_ASSOC)) {
			$mem_idx = $mem_chk_row['idx'];                 	// 회원고유번호
			$disply = $mem_chk_row['disply'];               	// 상태
			$login_cnt = $mem_chk_row['login_cnt'];    // 로그인 횟수
		}

		$add_login_cnt = $login_cnt + 1;

		# 마지막 로그인 시간을 업데이트 한다. 로그인 횟수 증가.
		$up_query = "UPDATE member SET login_Date = now(), login_cnt = :login_cnt WHERE mem_token = :mem_token LIMIT 1";
		$up_stmt = $DB_con->prepare($up_query);
		$up_stmt->bindparam(":login_cnt", $add_login_cnt);
		$up_stmt->bindparam(":mem_token", $mem_token);
		$up_stmt->execute();

		$result = array("result" => true, "idx" => (int)$mem_idx);
	}

	dbClose($DB_con);
	$mem_chk_stmt = null;
	$ins_stmt = null;
	$up_stmt = null;
} else { // 토큰이 없는 경우
	$result = array("result" => false, "errorMsg" => "회원 토큰이 없습니다.");
}
echo json_encode($result, JSON_UNESCAPED_UNICODE);