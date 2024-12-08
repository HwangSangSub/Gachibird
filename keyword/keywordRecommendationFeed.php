<?
/**
 * 추천 피드 목록
 */
include "../lib/common.php";
include '../lib/functionDB.php';

$mem_idx = trim($idx); // 회원고유번호
$key_limit = trim($keyLimit); // 키워드 최대 반환 개수 (기본: 5)
$news_limit = trim($newsLimit); // 뉴스 최대 반환 개수 (기본: 3)
$tops = array();

if ($key_limit == "") {
    $key_limit = 5;
} 
if ($news_limit == "") {
    $news_limit = 5;
} 

$keywords = topKeyword($key_limit, $mem_idx);

foreach ($keywords as $keyword) {
    $news = getNewsLink($keyword["idx"], $news_limit);

    $keyword["news"] = $news;

    array_push($tops, $keyword);
}

if (count($tops) > 0) {
    $result = array("result" => true, "list" => $tops);
} else {
    $result = array("result" => false, "errorMsg" => "추천피드가 없습니다.");
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>