<?php

include "../lib/common.php"; 

$DB_con = db1();

$data = [];
$b_idx = 2;
$idx = trim($idx);

if ($idx == "" || $idx == NULL) {
    $data['result'] = false;
    $data['errorMsg'] = "서버 에러입니다.";
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

if ($cate == "") {
    $cate = 0;
}

$query = "SELECT b_CateName FROM TB_BOARD_SET WHERE idx = 2";
$stmt = $DB_con->prepare($query);
$stmt->execute();
$num = $stmt->rowCount();

if ($num < 1) {
    $data['result'] = false;
    $data['errorMsg'] = "서버 에러입니다.";
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

$cateogry = $stmt->fetch(PDO::FETCH_ASSOC)['b_CateName'];

$categories = explode('&', $cateogry);

array_unshift($categories, "질문 TOP");

$squery = "SELECT b_Content FROM TB_BOARD WHERE idx = :idx AND b_idx = :b_idx AND b_Disply = 'Y'";

if ($cate == 0) {
    $squery = $squery . " AND t_Disply = 'Y' ORDER BY t_Sort ASC";
}

$stmt = $DB_con->prepare($squery);
$stmt->bindParam(':idx', $idx);
$stmt->bindParam(':b_idx', $b_idx);
$stmt->execute();
$num = $stmt->rowCount();

if ($num < 1) {
    $data['result'] = false;
    $data['errorMsg'] = "서버 에러입니다.";
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$data['content'] = $result[0]["b_Content"];

echo json_encode($data, JSON_UNESCAPED_UNICODE);