<?
/**
 * 사용자의 뉴스 북마크 목록 조회
 */
include "../lib/common.php";

$DB_con = db1();

$mem_idx = trim($mem_idx);

if ($mem_idx !== "") {
    $query = "SELECT C.name, B.title, B.agency, B.link, B.image, B.content, B.reg_date, B.create_date FROM member_bookmark A INNER JOIN keyword_news B ON A.news_idx = B.idx LEFT JOIN keyword C ON C.idx = B.key_idx WHERE mem_idx = 2 AND A.disply = 'Y'";
    $stmt = $DB_con->prepare($query);
    $stmt->bindParam(":mem_idx", $mem_idx);
    $stmt->execute();
    $row_count = $stmt->rowCount();

    if ($row_count > 0) {
        $news = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $keyword = $row['name'];
            $title = $row['title'];
            $agency = $row['agency'];
            $link = $row['link'];
            $image = $row['image'];
            $content = $row['content'];
            $reg_date = $row['reg_date'];
            $search_date = $row['create_date'];

            $article = [
                "keyword" => $keyword,
                "title" => $title,
                "agency" => $agency,
                "link" => $link,
                "image" => $image,
                "content" => $content,
                "reg_date" => $reg_date,
                "search_date" => $search_date
            ];

            array_push($news, $article);
        }

        $result = [
            "result" => true,
            "list" => $news
        ];
    } else {
        $result = [
            "result" => false,
            "errorMsg" => "등록된 뉴스 북마크가 없습니다."
        ];
    }
} else {
    $result = [
        "result" => false,
        "errorMsg" => "회원 고유번호가 없습니다."
    ];
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
