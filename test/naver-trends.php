<?php

include("./restapi.php");
$config = parse_ini_file("./config.ini");

$keywords = ["구글"]; //띄워쓰기 안됨, 콤마 안됨
$cnt = 0;

$keywordList = [];

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
        // ,"month" => 1 //1부터 12까지 지정 가능
    );

    $response = $api->GET("/keywordstool", $params);

    for($i = 0; $i < 5; $i++) {
        if (!isset($response["keywordList"][$i])) break; // 조회된 키워드가 없을 시 종료
        if (!isset($keywords[$i+$cnt])) break; // 조회된 키워드가 있지만 내가 추가하지 않은 키워드일 경우 종료

        array_push($keywordList, $response["keywordList"][$i]);
    }

    $cnt += 5;
}

echo print_r($keywordList, true);