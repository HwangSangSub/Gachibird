<?php
include "../lib/common.php";

$DB_con = db1();

$key_idx = trim($key_idx);

if ($key_idx !== "") {
    // 키워드명 가져오기
    $get_name_query = "SELECT name FROM keyword WHERE idx = :idx LIMIT 1";
    $get_name_stmt = $DB_con->prepare($get_name_query);
    $get_name_stmt->bindParam(":idx", $key_idx);
    $get_name_stmt->execute();

    $get_news_query = "SELECT link FROM keyword_news WHERE key_idx = :key_idx";
    $get_news_stmt = $DB_con->prepare($get_news_query);
    $get_news_stmt->bindParam(":key_idx", $key_idx);
    $get_news_stmt->execute();
    $news_list = $get_news_stmt->fetchAll(PDO::FETCH_COLUMN);

    $keyword_name = $get_name_stmt->fetch(PDO::FETCH_COLUMN);
    
    // 키워드명이 있는 뉴스 가져오기
    $client_id = "LvwMW7aWVTOOnLS7FITq"; // 클라이언트 id
    $client_secret = "Pxb9rvRZtd"; // 클라이언트 secret
    $encText = urlencode($keyword_name);
    $display = 100; // 가져오는 뉴스의 개수
    $start = 1; // 
    $sort = "date"; // sim: 정확도, date: 날짜
    $url = "https://openapi.naver.com/v1/search/news.json?query=".$encText . "&display=" . $display . "&start=" . $start . "&sort=" . $sort; // json 결과
    $is_post = false;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, $is_post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = array();
    $headers[] = "X-Naver-Client-Id: ".$client_id;
    $headers[] = "X-Naver-Client-Secret: ".$client_secret;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec ($ch);
    $res = json_decode($response);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // echo "status_code:".$status_code."";
    curl_close ($ch);

    // 뉴스를 데이터베이스에 저장
    if($status_code == 200) {
        $create_date = date("Y-m-d H:i:s", strtotime($res->lastBuildDate)); 

        // 뉴스 저장
        $query = "INSERT INTO keyword_news VALUES";
        $last_week = date("Y-m-d", time() - 604800);
        $data = array();
        foreach($res->items as $e) {
            $title = addslashes($e->title);
            $originallink = $e->originallink;
            $link = $e->link;
            $content = addslashes($e->description);
            $reg_date = date("Y-m-d H:i:s", strtotime($e->pubDate));

            if (in_array($link, $news_list)) {
                if (!next($res->items)) {
                    $query = substr($query, 0, -1);
                    }
                continue;
            }

            if ($reg_date >= $last_week) {
                $query .= " (NULL, ?, NULL, ?, ?, NULL, ?, ?, ?, 0)";
                $data[] = $key_idx;
                $data[] = $title;
                $data[] = $link;
                $data[] = $content;
                $data[] = $reg_date;
                $data[] = $create_date;
    
                if (next($res->items)) {
                    $query .= ",";
                }
            } else {
                $query = substr($query, 0, -1);
                break;
            }

        }

        if (count($data)) {
            $stmt = $DB_con->prepare($query);
            $stmt->execute($data);
            $row_count = $stmt->rowCount();
    
            $update_keyword_search_date_query = "UPDATE keyword SET search_date = :search_date WHERE idx = :key_idx";
            $update_keyword_search_date_stmt = $DB_con->prepare($update_keyword_search_date_query);
            $update_keyword_search_date_stmt->bindParam(":key_idx", $key_idx);
            $update_keyword_search_date_stmt->bindParam(":search_date", $create_date);
            $update_keyword_search_date_stmt->execute();
            $update_keyword_search_date_row_count = $update_keyword_search_date_stmt->rowCount();
            $result = [
                "result" => true,
                "list" => [
                    "success_cnt" => (count($data)/6)
                ]
            ];
        } else {
            $result = [
                "result" => false,
                "errorMsg" => "키워드에 관한 뉴스가 없습니다."
            ];
        }
    } else {
        $result = [
            "result" => false,
            "errorMsg" => "키워드 고유번호가 없습니다."
        ];
    }
} else {
    $result = [
        "result" => false,
        "errorMsg" => "키워드 고유번호가 없습니다."
    ];
}
echo json_encode($result);