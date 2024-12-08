<?php
// ini_set("display_errors", 1);
include "../lib/common.php"; 

$DB_con = db1();

$data = [];
$data['result'] = true;

if ($cate == "") {
    $cate = "0";
}

$cQuery = "SELECT b_CateName FROM TB_BOARD_SET WHERE idx = 2";
$cStmt = $DB_con->prepare($cQuery);
$cStmt->execute();
$cNum = $cStmt->rowCount();

if ($cNum < 1) {
    $data['result'] = false;
    $data['errorMsg'] = "서버 에러입니다.";
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

$cateogry = $cStmt->fetch(PDO::FETCH_ASSOC)['b_CateName'];

$categories = explode('&', $cateogry);

array_unshift($categories, "질문 TOP");

$data['categories'] = $categories;
$query = "SELECT idx, b_Title, b_Content FROM TB_BOARD WHERE b_idx = 2 AND b_Disply = 'Y'";

if ($cate == "0") {
    $query .= " AND t_Disply = 'Y' ORDER BY t_Sort ASC";
} else {
    $query .= " AND b_Cate = :cate ORDER BY idx ASC";
}

$stmt = $DB_con->prepare($query);
if ($cate !== "0") {
    $stmt->bindParam(':cate', $cate);
}

$stmt->execute();
$num = $stmt->rowCount();

if ($num < 1) {
    $data['result'] = false;
    $data['errorMsg'] = "서버 에러입니다.";
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

$dbResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

$data['now category'] = $categories[$cate];
$data['qna'] = $dbResult;

echo json_encode($data, JSON_UNESCAPED_UNICODE);