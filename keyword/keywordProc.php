<?
include '../lib/common.php';
include '../lib/functionDB.php';

$DB_con = db1();

$mem_idx = trim($idx);                                       // 회원고유번호
$mkey_idx = trim($mKeyIdx);                        // 회원-키워드고유번호
$key_name = preg_replace("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", trim($keyName));
$mod = trim($mod);                                             // mod : r(등록), m(변경), d(삭제)
$reg_date = TIME_YMDHIS;                           // 오늘날짜

$member_reg_keyword_cnt = memberRegKeyword($mem_idx);  // 회원별 키워드 등록 가능 수
$member_keyword_cnt = memberKeywordCnt($mem_idx);   // 회원별 키워드 등록 한 수

if ($mod == 'r') {
	if ($member_keyword_cnt < $member_reg_keyword_cnt) {
		if ($idx !== "" && $key_name !== "") {

			// 등록가능 키워드가 등록되어 있는지 확인하기
			$is_keywrod = isKeyword($key_name);
			if ($is_keywrod < 1) { // 키워드가 등록되어 있지 않기 때문에 신규 키워드 등록하기.
				$key_idx = keywordReg($key_name);
			} else {
				$key_idx = $is_keywrod;                     // 키워드 고유번호
			}
			// 2. 회원-키워드가 연결여부를 확인하여 연결이 되어 있다면 상태를 변경한다. 없으면 새로 등록한다.
			$chk_query = "SELECT idx, disply FROM member_keyword WHERE mem_idx = :mem_idx AND key_idx = :key_idx";
			$chk_stmt = $DB_con->prepare($chk_query);
			$chk_stmt->bindparam(":mem_idx", $mem_idx);
			$chk_stmt->bindparam(":key_idx", $key_idx);
			$chk_stmt->execute();
			$chk_num = $chk_stmt->rowCount();

			// 기존 sort값 정렬
			resetSort($mem_idx);

			// 다음 sort 값 가져오기
			$sort = getLastSort($mem_idx);
			
			if ($chk_num < 1) {
				// 3. 키워드 고유번호와 유저 고유번호를 연결
				$ins_query = "INSERT INTO member_keyword SET mem_idx = :mem_idx, key_idx = :key_idx, sort = :sort, reg_date = :reg_date";
				$ins_stmt = $DB_con->prepare($ins_query);
				$ins_stmt->bindparam(":mem_idx", $mem_idx);
				$ins_stmt->bindparam(":key_idx", $key_idx);
				$ins_stmt->bindparam(":sort", $sort);
				$ins_stmt->bindparam(":reg_date", $reg_date);
				$ins_stmt->execute();
				$ins_num = $ins_stmt->rowCount();
	
				if ($ins_num > 0) {
					$result = array("result" => true);
				} else {
					$result = array("result" => false, "errorMsg" => "키워드 등록을 할 수 없습니다. 잠시 후 다시 시도해주세요.");
				}
			} else {
				$chk_row = $chk_stmt->fetch(PDO::FETCH_ASSOC);
				$member_key_idx = $chk_row['idx'];                          // 회원- 키워드 고유번호
				$disply = $chk_row['disply'];                                          // 상태(Y: 키워드등록, N: 키워드삭제)
				if ($disply == "N") {
					$up_query = "UPDATE member_keyword SET disply = 'Y', sort = :sort WHERE idx = :member_key_idx LIMIT 1";
					$up_stmt = $DB_con->prepare($up_query);
					$up_stmt->bindparam(":sort", $sort);
					$up_stmt->bindparam(":member_key_idx", $member_key_idx);
					$up_stmt->execute();
					$up_num = $up_stmt->rowCount();
					
					if ($up_num > 0) {
						$result = array("result" => true);
					} else {
						$result = array("result" => false, "errorMsg" => "키워드 등록을 할 수 없습니다. 잠시 후 다시 시도해주세요.");
					}
				} else {
					$result = array("result" => false, "errorMsg" => "이미 등록중인 키워드입니다.");
				}
			}
		} else {
			$result = array("result" => false, "errorMsg" => "회원의 고유번호 또는 키워드가 없습니다.");
		}
	} else {
		$result = array("result" => false, "errorMsg" => "더 이상 키워드를 등록할 수 없습니다. 키워드 최대 등록 수를 확장 후 다시 시도해주세요.");
	}

	dbClose($DB_con);
	$chk_keyword_stmt = null;
	$chk_stmt = null;
	$ins_stmt = null;
	$up_stmt = null;
} else if ($mod == 'm') {

	//키워드유무확인
	$key_idx = isKeyword($key_name);
	// 키워드가 있다면
	if ($key_idx > 0) {
		$mkey = isMemKeyword($mem_idx, $key_idx);

		if ($mkey["disply"] == "Y") {//키워드가 있는데 Y false
			$result = array("result" => false, "errorMsg" => "이미 등록중인 키워드입니다. 확인 후 다시 시도해주세요.");
		} else {//키워드가 있는데 N 
			// 기존 sort 가져오기
			$key_sort_query = "SELECT sort FROM member_keyword WHERE idx = :idx LIMIT 1";
			$key_sort_stmt = $DB_con->prepare($key_sort_query);
			$key_sort_stmt->bindParam(":idx", $mkey_idx);
			$key_sort_stmt->execute();
			$key_sort = $key_sort_stmt->fetch(PDO::FETCH_COLUMN);
			// 기존 키워드 N
			$up_n_query = "UPDATE member_keyword SET disply = 'N', sort = 0, cancle_date = :cancle_date WHERE idx = :idx";
			$up_n_stmt = $DB_con->prepare($up_n_query);
			$up_n_stmt->bindParam(":cancle_date", $reg_date);
			$up_n_stmt->bindParam(":idx", $mkey_idx);
			$up_n_stmt->execute();
			$up_n_num - $up_n_stmt->rowCount();
			
			// N이던 키워드 Y로 변경
			$up_y_query = "UPDATE member_keyword SET disply = 'Y', sort = :sort, cancle_date = NULL WHERE idx = :idx";
			$up_y_stmt = $DB_con->prepare($up_y_query);
			$up_y_stmt->bindParam(":idx", $mkey["idx"]);
			$up_y_stmt->bindParam(":sort", $key_sort);
			$up_y_stmt->execute();
			$up_y_num - $up_y_stmt->rowCount();
			if ($up_n_num + $up_y_num > 1) {
				$result = array("result" => true);
			} else {
				$result = array("result" => false, "errorMsg" => "키워드 수정 할 수 없습니다. 잠시 후 다시 시도해주세요.");
			}
		}

	} else {
		// 기존 sort 가져오기
		$key_sort_query = "SELECT sort FROM member_keyword WHERE idx = :idx LIMIT 1";
		$key_sort_stmt = $DB_con->prepare($key_sort_query);
		$key_sort_stmt->bindParam(":idx", $mkey_idx);
		$key_sort_stmt->execute();
		$key_sort = $key_sort_stmt->fetch(PDO::FETCH_COLUMN);

		//없다면 키워드 등록 후 회원이랑 연결하기
		$up_n_query = "UPDATE member_keyword SET disply = 'N', sort = 0, cancle_date = :cancle_date WHERE idx = :idx";
		$up_n_stmt = $DB_con->prepare($up_n_query);
		$up_n_stmt->bindParam(":cancle_date", $reg_date);
		$up_n_stmt->bindParam(":idx", $mkey_idx);
		$up_n_stmt->execute();
		$up_n_num - $up_n_stmt->rowCount();
			
		$key_idx = keywordReg($key_name);

		$ins_query = "INSERT INTO member_keyword SET mem_idx = :mem_idx, key_idx = :key_idx, sort = :sort, reg_date = :reg_date";
		$ins_stmt = $DB_con->prepare($ins_query);
		$ins_stmt->bindparam(":mem_idx", $mem_idx);
		$ins_stmt->bindparam(":sort", $key_sort);
		$ins_stmt->bindparam(":key_idx", $key_idx);
		$ins_stmt->bindparam(":reg_date", $reg_date);
		$ins_stmt->execute();
		$ins_num = $ins_stmt->rowCount();

		if ($ins_num > 0) {
			$result = array("result" => true);
		} else {
			$result = array("result" => false, "errorMsg" => "키워드 수정 할 수 없습니다. 잠시 후 다시 시도해주세요.");
		}
	}

	dbClose($DB_con);
	$up_y_stmt = null;
	$up_n_stmt = null;
	$key_sort_stmt = null;
} else if ($mod == 'd') {
	
	$chk_query = "SELECT idx, disply FROM member_keyword WHERE idx = :mkey_idx AND mem_idx = :mem_idx";
	$chk_stmt = $DB_con->prepare($chk_query);
	$chk_stmt->bindparam(":mem_idx", $mem_idx);
	$chk_stmt->bindparam(":mkey_idx", $mkey_idx);
	$chk_stmt->execute();
	$chk_num = $chk_stmt->rowCount();
	
	if ($chk_num < 1) {
		$result = array("result" => false, "errorMsg" => "내가 등록한 키워드가 아닙니다. 확인 후 다시 시도해주세요.");
	} else {
		$chk_row = $chk_stmt->fetch(PDO::FETCH_ASSOC);
		$member_key_idx = $chk_row['idx'];                          // 회원- 키워드 고유번호
		$disply = $chk_row['disply'];                     // 상태(Y: 키워드등록, N: 키워드삭제)
		if ($disply == 'Y') {
			$up_query = "UPDATE member_keyword SET disply = 'N', sort = 0, cancle_date = :cancle_date WHERE idx = :idx";
			$up_stmt = $DB_con->prepare($up_query);
			$up_stmt->bindParam(":cancle_date", $reg_date);
			$up_stmt->bindParam(":idx", $mkey_idx);
			$up_stmt->execute();
			$up_num - $up_stmt->rowCount();

			resetSort($mem_idx);
		
			$result = array("result" => true);
		} else {
			$result = array("result" => false, "errorMsg" => "이미 삭제된 키워드입니다.");
		}
	}
	dbClose($DB_con);
	$chk_stmt = null;
	$up_stmt = null;
} else {
	$result = array("result" => false, "errorMsg" => "구분값이 없습니다.");
}
echo json_encode($result, JSON_UNESCAPED_UNICODE);
