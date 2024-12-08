<?

include "../lib/common.php";
date_default_timezone_set('Asia/Seoul');

$DB_con = db1();

$date = trim($date);

// 기존에 저장돼있는 뉴스가 있으면 삭제
$news_delete_query = "DELETE FROM keyword_news WHERE idx NOT IN (SELECT idx FROM (SELECT A.idx FROM keyword_news A INNER JOIN member_bookmark B ON A.idx = B.news_idx WHERE create_date < :date) a) AND create_date < :date";
$news_delete_stmt = $DB_con->prepare($news_delete_query);
$news_delete_stmt->bindParam(":date", date('Y-m-d', strtotime($date)));
$news_delete_stmt->execute();
$news_delete_row_count = $news_delete_stmt->rowCount();

echo $news_delete_row_count;