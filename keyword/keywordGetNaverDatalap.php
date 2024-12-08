<?

/**
 * 네이버의 카카오 데이터랩 정보
 * 
 * 키워드 인덱스값을 주면 네이버 데이터랩의 그래프를 반환한다.
 */

include '../lib/common.php';

$DB_con = db1();

$key_idx = trim($key_idx);
$interval = trim($interval); // d: 하루, w: 주, m: 달

if ($key_idx !== "" && $interval !== "") {
    $now = time();
    $from = date('Ym', $now - 31622400). '01';
    $to = date('Ymd', $now - 86400);

    if (substr($to, -4) == "0229") {
        $from = (date('Y-m-', $now) - 1) . '01';
        $to = date('Y-m-d', $now - 86400);
    } else {
        $from = date('Y-m-', $now - 31622400). '01';
        $to = date('Y-m-d', $now - 86400);
    }

    $get_keyword_name_query = "SELECT name FROM keyword WHERE idx = :idx";
    $get_keyword_name_stmt = $DB_con->prepare($get_keyword_name_query);
    $get_keyword_name_stmt->bindParam(":idx", $key_idx);
    $get_keyword_name_stmt->execute();

    $keyword = urlencode($get_keyword_name_stmt->fetch(PDO::FETCH_ASSOC)["name"]);

    switch ($interval) {
        case 'd':
            $interval = "date";
            break;
        case 'w':
            $interval = "week";
            break;
        case 'm':
            $interval = "month";
            break;
    }

    $client_id = "LvwMW7aWVTOOnLS7FITq";
    $client_secret = "Pxb9rvRZtd";
    $url = "https://openapi.naver.com/v1/datalab/search";
    $body = "{\"startDate\":\"" . $from . "\",\"endDate\":\"" . $to . "\",\"timeUnit\":\"" . $interval . "\",\"keywordGroups\":[{\"groupName\":\"키워드\",\"keywords\":[\"" . $keyword . "\"]}]}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = array();
    $headers[] = "X-Naver-Client-Id: ".$client_id;
    $headers[] = "X-Naver-Client-Secret: ".$client_secret;
    $headers[] = "Content-Type: application/json";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    // SSL 이슈가 있을 경우, 아래 코드 주석 해제
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);  
    $response = curl_exec ($ch);
    $res = json_decode($response);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // echo "status_code:".$status_code." ";
    curl_close ($ch);
    if($status_code == 200) {
        $result = [
            "result" => true,
            "list" => $res
        ];
    }
} else {
    $result = [
        "result" => false,
        "list" => "키워드 고유번호 또는 주기가 없습니다."
    ];
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);