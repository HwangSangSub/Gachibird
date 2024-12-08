<?
    include "../lib/common.php";

    $DB_con = db1();
    $key_idx = trim($key_idx);

    $query = "SELECT * FROM keyword_news WHERE key_idx = :key_idx";
    $stmt = $DB_con->prepare($query);
    $stmt->bindParam(":key_idx", $key_idx);
    $stmt->execute();
    $rowCount = $stmt->rowCount();

    if ($rowCount > 0) {
        $news = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $idx = $row['idx']; // 키워드 뉴스 고유번호
            $title = $row['title']; // 키워드 뉴스 제목
            $link = $row['link']; // 키워드 뉴스 링크
            $content = $row['content']; // 키워드 뉴스 내용
            $reg_date = $row['reg_date']; // 키워드 뉴스 추가한일
            $create_date = $row['create_date']; // 키워드 뉴스 작성일
            
            $article = [
                'idx' => $idx,
                'title' => $title,
                'link' => $link,
                'content' => $content,
                'reg_date' => $reg_date,
                'create_data' => $create_date
            ];
    
            array_push($news, $article);
        }
        print_r($news);
    } else {
        print_r(["msg" => "실패"]);
    }
    

?>