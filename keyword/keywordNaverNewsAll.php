<?php
include "../lib/common.php";
date_default_timezone_set('Asia/Seoul');

$DB_con = db1();
$now = date("Y-m-d H:i:s");

// 사용중인 키워드명 가져오기
$get_name_query = "SELECT idx, name FROM keyword WHERE disply = 'Y' GROUP BY idx";
$get_name_stmt = $DB_con->prepare($get_name_query);
$get_name_stmt->execute();

// 사용중인 키워드만큼 실행
while ($row = $get_name_stmt->fetch(PDO::FETCH_ASSOC)) {
    $key_idx = $row['idx'];
    $keyword_name = $row['name'];

    // 서버에 등록되어 있는 키워드의 뉴스 링크를 가져온다.
    $keyword_news_query = "SELECT link FROM keyword_news WHERE key_idx = :key_idx";
    $keyword_news_stmt = $DB_con->prepare($keyword_news_query);
    $keyword_news_stmt->bindparam(":key_idx", $key_idx);
    $keyword_news_stmt->execute();
    $keyword_news_arr = [];
    while($keyword_news_row = $keyword_news_stmt->fetch(PDO::FETCH_ASSOC)){
        $link = $keyword_news_row["link"];
        $keyword_news_arr[] = $link; // 배열에 링크 추가
    }

    // 키워드명이 있는 뉴스 가져오기
    $client_id = "LvwMW7aWVTOOnLS7FITq"; // 클라이언트 id
    $client_secret = "Pxb9rvRZtd"; // 클라이언트 secret
    $encText = urlencode($keyword_name);
    $display = 100;
    $start = 1;
    $sort = "sim"; // sim: 정확도, date: 날짜
    $url = "https://openapi.naver.com/v1/search/news.json?query=" . $encText . "&display=" . $display . "&start=" . $start . "&sort=" . $sort; // json 결과
    $is_post = false;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, $is_post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = array();
    $headers[] = "X-Naver-Client-Id: " . $client_id;
    $headers[] = "X-Naver-Client-Secret: " . $client_secret;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    $res = json_decode($response);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // echo "status_code:".$status_code."";
    curl_close($ch);

    // 뉴스를 데이터베이스에 저장
    if ($status_code == 200) {
        $create_date = date("Y-m-d H:i:s", strtotime($res->lastBuildDate));

        // 키워드 뉴스 검색일 등록
        $update_keyword_search_date_query = "UPDATE keyword SET search_date = :search_date WHERE idx = :key_idx";
        $update_keyword_search_date_stmt = $DB_con->prepare($update_keyword_search_date_query);
        $update_keyword_search_date_stmt->bindParam(":key_idx", $key_idx);
        $update_keyword_search_date_stmt->bindParam(":search_date", $create_date);
        $update_keyword_search_date_stmt->execute();

        // 뉴스 저장
        $today = date("Y-m-d");
        foreach ($res->items as $e) {
            $link = $e->link;
            // 등록되어 있는 뉴스가 있는지 확인
            if (count($keyword_news_arr) > 0) {
                // 서버에 등록되어 있는 같은 뉴스는 등록하지 않는다.
                if (in_array($link, $keyword_news_arr)) {
                    continue;
                } else {

                    $title = addslashes($e->title);
                    $originallink = $e->originallink;
                    $content = addslashes($e->description);
                    $reg_date = date("Y-m-d H:i:s", strtotime($e->pubDate));

                    $ins_query = "INSERT INTO keyword_news SET key_idx = :key_idx, title = :title, link = :link, content = :content, reg_date = :reg_date, create_date = :create_date";
                    $ins_stmt = $DB_con->prepare($ins_query);
                    $ins_stmt->bindparam(":key_idx", $key_idx);
                    $ins_stmt->bindparam(":title", $title);
                    $ins_stmt->bindparam(":link", $link);
                    $ins_stmt->bindparam(":content", $content);
                    $ins_stmt->bindparam(":reg_date", $reg_date);
                    $ins_stmt->bindparam(":create_date", $create_date);
                    $ins_stmt->execute();
                }
            } else {
                // 등록되어 있는 뉴스가 없다면 바로 등록하기.
                $title = addslashes($e->title);
                $originallink = $e->originallink;
                $content = addslashes($e->description);
                $reg_date = date("Y-m-d H:i:s", strtotime($e->pubDate));

                $ins_query = "INSERT INTO keyword_news SET key_idx = :key_idx, title = :title, link = :link, content = :content, reg_date = :reg_date, create_date = :create_date";
                $ins_stmt = $DB_con->prepare($ins_query);
                $ins_stmt->bindparam(":key_idx", $key_idx);
                $ins_stmt->bindparam(":title", $title);
                $ins_stmt->bindparam(":link", $link);
                $ins_stmt->bindparam(":content", $content);
                $ins_stmt->bindparam(":reg_date", $reg_date);
                $ins_stmt->bindparam(":create_date", $create_date);
                $ins_stmt->execute();
            }
        }
        $result = array("result" => true);
    } else {
        $result = array("result" => false, "errorMsg" => "뉴스 조회에 실패했습니다.");
    }
}

dbClose($DB_con);
$get_name_stmt = null;
$keyword_news_stmt = null;
$ins_stmt = null;
$update_keyword_search_date_stmt = null;

echo json_encode($result, JSON_UNESCAPED_UNICODE);
