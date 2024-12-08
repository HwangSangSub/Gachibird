#!/usr/bin/php -q
<?php
/*======================================================================================================================

* 프로그램			: 유효시간이 지난 매칭중 노선 취소(삭제)처리  (매시간마다 적용)
* 페이지 설명		: 유효시간이 지난 매칭중 노선 취소(삭제)처리
* 파일명          : taxiSharingChk.php

========================================================================================================================*/

// register_globals off 처리
if (isset($_GET)) {
	@extract($_GET);
}
if (isset($_POST)) {
	@extract($_POST);
}
if (isset($_SERVER)) {
	@extract($_SERVER);
}
if (isset($_ENV)) {
	@extract($_ENV);
}
if (isset($_SESSION)) {
	@extract($_SESSION);
}
if (isset($_COOKIE)) {
	@extract($_COOKIE);
}
if (isset($_REQUEST)) {
	@extract($_REQUEST);
}
if (isset($_FILES)) {
	@extract($_FILES);
}

ob_start();

header('Content-Type: text/html; charset=utf-8');
$gmnow = gmdate('D, d M Y H:i:s') . ' GMT';
header('Expires: 0'); // rfc2616 - Section 14.21
header('Last-Modified: ' . $gmnow);
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
header('Pragma: no-cache'); // HTTP/1.0
include '../lib/dbcon.php';

$DB_con = db1();
$now = date("Y-m-d H:i:s");

// 사용중인 키워드명 가져오기
$get_name_query = "SELECT DISTINCT B.idx, B.name FROM member_keyword A INNER JOIN keyword B ON A.key_idx = B.idx WHERE disply = 'Y' GROUP BY B.idx";
$get_name_stmt = $DB_con->prepare($get_name_query);
$get_name_stmt->execute();

// 사용중인 키워드만큼 실행
while ($row = $get_name_stmt->fetch(PDO::FETCH_ASSOC)) {
	$key_idx = $row['idx'];
	$keyword_name = $row['name'];

	// 키워드명이 있는 뉴스 가져오기
	$client_id = "LvwMW7aWVTOOnLS7FITq"; // 클라이언트 id
	$client_secret = "Pxb9rvRZtd"; // 클라이언트 secret
	$encText = urlencode($keyword_name);
	$display = 100;
	$start = 1;
	$sort = "date"; // sim: 정확도, date: 날짜
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

		// 뉴스 저장
		$query = "INSERT INTO keyword_news VALUES";
		$today = date("Y-m-d");
		foreach ($res->items as $e) {
			$title = addslashes($e->title);
			$originallink = $e->originallink;
			$link = $e->link;
			$content = addslashes($e->description);
			$reg_date = date("Y-m-d H:i:s", strtotime($e->pubDate));

			if ($reg_date >= $today) {
				$query .= " (NULL, {$key_idx}, '{$title}', '{$link}', '{$content}', '{$reg_date}', '{$create_date}')";

				if (next($res->items)) {
					$query .= ",";
				}
			} else {
				$query = substr($query, 0, -1);
				break;
			}
		}
		$stmt = $DB_con->prepare($query);
		$stmt->execute();
		$row_count = $stmt->rowCount();

		$update_keyword_search_date_query = "UPDATE keyword SET search_date = :search_date WHERE idx = :key_idx";
		$update_keyword_search_date_stmt = $DB_con->prepare($update_keyword_search_date_query);
		$update_keyword_search_date_stmt->bindParam(":key_idx", $key_idx);
		$update_keyword_search_date_stmt->bindParam(":search_date", $create_date);
		$update_keyword_search_date_stmt->execute();
	} else {
		echo "Error 내용:" . $response;
	}
}
echo "success";
