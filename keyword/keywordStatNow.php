<?

include '../lib/common.php';
include './lib/restapi.php';
$config = parse_ini_file("./lib/config.ini");

$DB_con = db1();

$get_keyword_query = "SELECT A.idx AS key_idx, A.name AS key_name, B.idx AS stat_now_idx FROM keyword A INNER JOIN member_keyword C ON C.key_idx = A.idx AND C.disply = 'Y' LEFT JOIN keyword_stat_now B ON A.idx = B.key_idx GROUP BY A.idx";
$get_keyword_stmt = $DB_con->prepare($get_keyword_query);
$get_keyword_stmt->execute();
$keywords = [];
$keywords_idx = [];
while ($keyword = $get_keyword_stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($keyword["stat_now_idx"] != NULL) continue;
    array_push($keywords_idx, $keyword["key_idx"]);
    array_push($keywords, $keyword["key_name"]);
}

$cnt = 0;

$keywordList = [];

$row_count = 0;

while(isset($keywords[$cnt])) {
    $hintKeywords = "";
    for($i = $cnt; $i < $cnt+5 && isset($keywords[$i]); $i++) {
        $hintKeywords .= $keywords[$i];
        if ($i == $cnt+4) continue; //5번째에 있는 키워드는 콤마 찍지 않기
        if (!isset($keywords[$i+1])) continue; // 5번쨰가 아니면서 가장 마지막에 있는 키워드는 콤마 찍지 않기

        $hintKeywords .= ",";
    }

    $api = new RestApi($config['BASE_URL'], $config['API_KEY'], $config['SECRET_KEY'], $config['CUSTOMER_ID']);
    $params = array(
        "hintKeywords" => $hintKeywords, // 한번에 최대 5개까지 가능, string형태로 입력 예) 가치버드,gacibird,gachi,gachita,가치타
        "showDetail" => 1 //자세히 보기 0: 끄기 1: 키기
        ,"month" => 1 //1부터 12까지 지정 가능
    );

    $response = $api->GET("/keywordstool", $params);

    $query = "INSERT INTO keyword_stat_now VALUES";

    for($i = 0; $i < 5; $i++) {
        if (!isset($response["keywordList"][$i])) break; // 조회된 키워드가 없을 시 종료
        if (!isset($keywords[$i+$cnt])) break; // 조회된 키워드가 있지만 내가 추가하지 않은 키워드일 경우 종료
        if ($i != 0) $query .= ",";

        $pc_cnt = $response["keywordList"][$i]["monthlyPcQcCnt"];
        $mobile_cnt = $response["keywordList"][$i]["monthlyMobileQcCnt"];
        $total_cnt = $pc_cnt + $mobile_cnt;
        $pc_ave_cnt = $response["keywordList"][$i]["monthlyAvePcClkCnt"];
        $mobile_ave_cnt = $response["keywordList"][$i]["monthlyAveMobileClkCnt"];
        $total_ave_cnt = $pc_ave_cnt + $mobile_ave_cnt;
        $pc_ave_rate = $response["keywordList"][$i]["monthlyAvePcCtr"];
        $mobile_ave_rate = $response["keywordList"][$i]["monthlyAveMobileCtr"];
        $pl_avg_depth = $response["keywordList"][$i]["plAvgDepth"];
        $competition = $response["keywordList"][$i]["compIdx"];

        $query .= " (NULL, {$keywords_idx[$cnt + $i]}, {$total_cnt}, {$pc_cnt}, {$mobile_cnt}, {$total_ave_cnt}, {$pc_ave_cnt}, {$mobile_ave_cnt}, {$pc_ave_rate}, {$mobile_ave_rate}, {$pl_avg_depth}, '{$competition}')";

        // array_push($keywordList, );
    }

    $stmt = $DB_con->prepare($query);
    $stmt->execute();
    $row_count += $stmt->rowCount();

    $cnt += 5;
}

if ($row_count == count($keywords)) {
    echo "success";
} else {
    echo "fail";
}