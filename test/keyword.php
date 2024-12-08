<?php
  // 네이버 데이터랩 통합검색어 트렌드 Open API 예제
  $client_id = "LvwMW7aWVTOOnLS7FITq"; // 네이버 개발자센터에서 발급받은 CLIENT ID
  $client_secret = "Pxb9rvRZtd";// 네이버 개발자센터에서 발급받은 CLIENT SECRET
  $url = "https://openapi.naver.com/v1/datalab/search";
  $body = "{
    \"startDate\":\"2023-02-01\",
    \"endDate\":\"2024-02-29\",
    \"timeUnit\":\"date\",
    \"keywordGroups\":[{\"groupName\":\"황상섭\",\"keywords\":[\"구글\"]}]
}
    ";

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
  $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  echo "status_code:".$status_code." ";
  curl_close ($ch);
  if($status_code == 200) {
      echo $response;
  } else {
      echo "Error 내용:".$response;
  }
?>