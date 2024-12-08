<?

/**
 * 키워드별 뉴스 목록 가져오기
 */
include "../lib/common.php";

$DB_con = db1();
$key_idx = trim($idx);                                      // 회원고유번호
$sort = trim($sort);                                           //  정렬방식(new: 최신 순, old: 오래된 순)
if ($sort == 'old') {
    $order = "reg_date ASC";
} else {
    $order = "reg_date DESC";
}
$is_now = trim($isNow);                                // Y: 오늘, N: 어제
if ($is_now == 'N') {
    $date_where = "DATE_FORMAT(reg_date, '%Y-%m-%d') BETWEEN DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 3 DAY), '%Y-%m-%d') AND DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 DAY), '%Y-%m-%d')";
} else {
    $date_where = "DATE_FORMAT(reg_date, '%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')";
}
$reg_date = TIME_YMDHIS;                         // 오늘날짜

$query = "SELECT IFNULL(agency,'') AS agency, IFNULL(title,'') AS title, IFNULL(image,'') AS image, IFNULL(content,'') AS content, IFNULL(link,'') AS link, reg_date FROM keyword_news WHERE key_idx = :key_idx AND {$date_where} ORDER BY $order LIMIT 5";
$stmt = $DB_con->prepare($query);
$stmt->bindparam(":key_idx", $key_idx);
$stmt->execute();
$num = $stmt->rowCount();
if ($num < 1) {
    $result = array("result" => false, "errorMsg" => "등록된 뉴스가 없습니다.");
} else {
    $news_list = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $agency = $row['agency'];                                  // 뉴스사
        $title = str_replace("&quot;", "\"", str_replace("&amp;", "&",strip_tags(stripslashes($row['title']))));                                                    // 제목
        $image = $row['image'];                                          // 이미지링크
        if($image == ""){
            $image = $web_url."/data/keyword/non_news.png";
        }
        $content = str_replace("&quot;", "\"", str_replace("&amp;", "&",strip_tags(stripslashes($row['content']))));                                    // 내용
        $link = $row['link'];                                                   // 뉴스 링크
        $reg_date = $row['reg_date'];                            // 뉴스 등록일
        $news_arr = array("agency" => $agency, "title" => $title, "image" => $image, "content" => $content, "link" => $link, "regDate" => $reg_date);
        array_push($news_list, $news_arr);
    }
    $result = array("result" => true, "lists" => $news_list);
}

dbClose($DB_con);
$stmt = null;

echo json_encode($result, JSON_UNESCAPED_UNICODE);
