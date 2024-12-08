<?
/**
 * 키워드 순위 조회
 */
include '../lib/common.php';
include '../lib/functionDB.php';

$DB_con = db1();

$interval = trim($interval);
$mem_idx = trim($idx);
$limit = trim($limit);

// 현재와 5분 전의 순위 가져오기
if ($limit == "") {
    $limit = 5;
}

$now = getRanking($mem_idx, 0, 0, $interval);
$ago = getRanking($mem_idx, 5, 0, $interval);
if ($interval == "d") { // 랭킹 기준 오늘이다.
    // 오늘 등록된 키워드의 상위 n개 가져오기
    $data["date"] = date("m월 d일", time());
    
    //현재 순위만큼 반복문
    $data = getRank($now, $ago, $limit, true);
    
    $result = ["result" => true, "list" => $data];
} else if ($interval == "t") { // 랭킹 기준 전체이다.
        
    if (count($now) + count($ago) < 2) {
        $result = array("result" => false, "errorMsg" => "등록된 키워드가 없습니다.");
    } else {
        //현재 순위만큼 반복문
        $data = getRank($now, $ago, $limit, true);
    }
    
    $result = array("result" => true, "list" => $data);
} else { // 랭킹 기준 없다.
    $result = array("result" => false, "errorMsg" => "구분값이 없습니다.");
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);