<?

/**
 * 회원 키워드 목록 및 주목받고 있는 키워드 TOP5 조회
 */
include "../lib/common.php";
include "../lib/functionDB.php";
$DB_con = db1();

$total_now = getRanking($mem_idx, 0, 0, "t");
$total_ago = getRanking($mem_idx, 5, 0, "t");

$total_rank = getRank($total_now, $total_ago, 5, false);

$today_now = getRanking($mem_idx, 0, 0, "d");
$today_ago = getRanking($mem_idx, 5, 0, "d");

$today_rank = getRank($today_now, $today_ago, 5, false);

$result = array(
    "result" => true,
    "totalRank" => $total_rank,
    "todayRank" => $today_rank
);

dbClose($DB_con);
$stmt = null;

echo json_encode($result, JSON_UNESCAPED_UNICODE);
