<?

include '../lib/common.php'; 

$DB_con = db1();

$get_keyword_idx_query = "SELECT idx, name FROM keyword";
$get_keyword_idx_stmt = $DB_con->prepare($get_keyword_idx_query);
$get_keyword_idx_stmt->execute();

while($row = $get_keyword_idx_stmt->fetch(PDO::FETCH_ASSOC)) {
    $key_idx = $row["idx"];
    $keyword = $row["name"];
    $url = "https://news.google.com/rss/search?q={$keyword}%20when%3A7d&hl=ko&gl=KR&ceid=KR:ko";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $json_res = json_encode(simplexml_load_string($response));
    
    $res = json_decode($json_res);
    
    $is_set_news_query = "SELECT link FROM keyword_news WHERE key_idx = :key_idx";
    $is_set_news_stmt = $DB_con->prepare($is_set_news_query);
    $is_set_news_stmt->bindParam(":key_idx", $key_idx);
    $is_set_news_stmt->execute();
    $link_list = $is_set_news_stmt->fetchALL(PDO::FETCH_COLUMN);
    
    $create_date = $res->channel->lastBuildDate;
    $items = $res->channel->item;
    
    $sql = "INSERT INTO keyword_news VALUES";
    $date = array();
    
    $cnt = 0;
    
    foreach ($items as $item) {
        if (in_array($item->link, $link_list)) {
            if (!next($items)) {
                $sql = substr($sql, 0, -1);
            }
            continue;
        }
    
        $cnt++;
    
        $sql .= " (NULL, ?, ?, ?, ?, ?, ?)";
        if (next($items)) {
            $sql .= ",";
        }
    
        $data[] = $key_idx;
        $data[] = $item->title;
        $data[] = $item->link;
        $data[] = $item->description;
        $data[] = date('Y-m-d H:i:s', strtotime($item->pubDate));
        $data[] = date('Y-m-d H:i:s', strtotime($create_date));
    }
    
    if ($cnt > 0) {
        $stmt = $DB_con->prepare($sql);
        $ex = $stmt->execute($data);

        $update_keyword_search_date_query = "UPDATE keyword SET search_date = :search_date WHERE idx = :key_idx";
        $update_keyword_search_date_stmt = $DB_con->prepare($update_keyword_search_date_query);
        $update_keyword_search_date_stmt->bindParam(":key_idx", $key_idx);
        $update_keyword_search_date_stmt->bindParam(":search_date", $create_date);
        $update_keyword_search_date_stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            echo "fail";
            die();
        }
    }
}



// print_r($data);
