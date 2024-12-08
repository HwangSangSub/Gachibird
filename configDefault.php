<?
/**
 * 기본 설정 조회
 */
include "./lib/common.php";
$DB_con = db1();


$query = "SELECT guest_keyword_reg_cnt, keyword_reg_cnt, keyword_max_reg_cnt, keyword_add_interval_cnt, keyword_search_time FROM config";
$stmt = $DB_con->prepare($query);
$stmt->execute();
$num = $stmt->rowCount();

if ($num < 1) { //아닐경우
	$result = array("result" => false);
} else {
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$guest_keyword_reg_cnt = $row['guest_keyword_reg_cnt'];                        			  // 게스트 키워드 등록 가능 수
		$keyword_reg_cnt = $row['keyword_reg_cnt'];                        											// 키워드 등록 가능 수
		$keyword_max_reg_cnt = $row['keyword_max_reg_cnt'];                                          // 키워드 최대 등록 가능 수
		$keyword_add_interval_cnt = $row['keyword_add_interval_cnt'];                         // 키워드 확장 추가 수
		$keyword_search_time = $row['keyword_search_time'];                                             // 키워드 뉴스 자동 검색 시간(분)
	}
	$result = array("result" => true
		, "guest_keyword_reg_cnt" => (int)$guest_keyword_reg_cnt
		, "keyword_reg_cnt" => (int)$keyword_reg_cnt
		, "keyword_max_reg_cnt" => (int)$keyword_max_reg_cnt
		, "keyword_add_interval_cnt" => (int)$keyword_add_interval_cnt
		, "keyword_search_time" => (int)$keyword_search_time
	);
}

dbClose($DB_con);
$stmt = null;

echo json_encode($result, JSON_UNESCAPED_UNICODE);