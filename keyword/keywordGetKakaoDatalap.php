<?
/**
 * 키워드의 카카오 데이터랩 정보
 * 
 * 키워드 인덱스값을 주면 카카오 데이터랩의 성별,나이,기기,지역,그래프를 반환한다.
 */
include '../lib/common.php';

$DB_con = db1();

$key_idx = trim($key_idx); // 키워드 인덱스값
$interval = trim($interval); // 주기 (d: 하루, w: 주, m: 달)

if ($key_idx !== "" && $interval !== "") {
    $curl = curl_init();
    $now = time();
    $from = date('Ym', $now - 31622400). '01';
    $to = date('Ymd', $now - 86400);

    if (substr($to, -4) == "0229") {
        $from = (date('Ym', $now) - 1) . '01';
    }

    $get_keyword_name_query = "SELECT name FROM keyword WHERE idx = :idx";
    $get_keyword_name_stmt = $DB_con->prepare($get_keyword_name_query);
    $get_keyword_name_stmt->bindParam(":idx", $key_idx);
    $get_keyword_name_stmt->execute();

    $keyword = urlencode($get_keyword_name_stmt->fetch(PDO::FETCH_ASSOC)["name"]);


    $url = 'https://datatrend.kakao.com/api/search/trend?q=' . $keyword . '&from=' . $from . '&to=' . $to . '&interval=' . $interval;

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);

    $res = json_decode($response);
    // print_r($res);

    $date_list = $res->date;
    $gender = $res->list[0]->gender;
    $age = $res->list[0]->age;
    $device = $res->list[0]->device;
    $local = $res->list[0]->local;
    $qc = $res->list[0]->qc;

    $date = array();

    foreach($date_list as $d) {
        array_push($date, date('Y-m-d', strtotime($d)));
    }
    $list = [
        "key_idx" => $key_idx,
        "keyword" => $keyword,
        "interval" => $interval,
        "date" => $date, // 날짜
        "gender" => $gender, // 성별
        "age" => $age, // 나이
        "device" => $device, // 기기
        "local" => $local, // 지역
        "qc" => $qc // 그래프 
    ];
    $result = [
        "result" => true,
        "list" =>$list
    ];
} else {
    $result = [
        "result" => false,
        "errorMsg" => "키워드 고유번호 또는 주기가 없습니다."
    ];
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
