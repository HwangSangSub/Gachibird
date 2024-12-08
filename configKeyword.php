<?
/**
 * 기본 키워드 조회
 */
include "./lib/common.php";

$cnt = trim($cnt);

$DB_con = db1();

$query = "SELECT name, img FROM keyword ORDER BY RAND() LIMIT ".$cnt;
$stmt = $DB_con->prepare($query);
$stmt->execute();
$num = $stmt->rowCount();

if ($num < 1) { //아닐경우
	$result = array("result" => false, "lists" => []);
} else {
    $keyword_list = [];
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$name = $row['name'];        // 게스트 키워드 등록 가능 수
		$img = $row['img'];                // 키워드 등록 가능 수
        $keyword_arr = array("name" => $name, "img" => $img);
        array_push($keyword_list, $keyword_arr);
	}
	$result = array("result" => true
		, "lists" => $keyword_list
	);
}

dbClose($DB_con);
$stmt = null;

echo json_encode($result, JSON_UNESCAPED_UNICODE);