<?

/**
 * 회원 키워드 목록 및 주목받고 있는 키워드 TOP5 조회
 */
include "../lib/common.php";
include "../lib/functionDB.php";
$DB_con = db1();

$mem_idx = trim($idx); // 회원고유번호

$query = "SELECT k.idx AS key_idx, k.name, k.img, mk.idx AS mkey_idx, sort FROM member_keyword AS mk INNER JOIN keyword AS k ON mk.key_idx = k.idx WHERE mk.disply = 'Y' AND mk.mem_idx = :mem_idx ORDER BY sort";
$stmt = $DB_con->prepare($query);
$stmt->bindparam(":mem_idx", $mem_idx);
$stmt->execute();
$num = $stmt->rowCount();

if ($num < 1) { //아닐경우
	$result = array("result" => true, "lists" => []);
} else {
	$keyword_list = [];
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$key_idx = $row['key_idx'];                        			                         // 키워드 고유번호
		$name = $row['name'];                        											// 키워드 명
		$img = $row['img'];                        													  // 키워드 이미지
		if ($img == "") {
			$img = $web_url . "/data/keyword/non_key_image.png";
		}
		$mkey_idx = $row['mkey_idx'];                        						   // 회원-키워드 고유번호
		$keyword_arr = array("idx" => $key_idx, "name" => $name, "mKeyIdx" => $mkey_idx, "image" => $img);
		array_push($keyword_list, $keyword_arr);
	}
	$result = array(
		"result" => true,
		"keywordList" => $keyword_list
	);
}

dbClose($DB_con);
$stmt = null;

echo json_encode($result, JSON_UNESCAPED_UNICODE);
