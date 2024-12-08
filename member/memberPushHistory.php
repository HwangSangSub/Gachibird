<?
include "../lib/common.php";

$DB_con = db1();

$idx = trim($idx);
$page = trim($page);

if ($page == "") {
    $page = 1;
} else {
    $page = (int)$page;
}

if ($idx !== "") {
    $start = ($page - 1) * 10;

    $total_query = "SELECT COUNT(*) AS total FROM push_history WHERE mem_idx = :idx LIMIT 1";
    $total_stmt = $DB_con->prepare($total_query);
    $total_stmt->bindParam(":idx", $idx);
    $total_stmt->execute();
    $row = $total_stmt->fetch(PDO::FETCH_ASSOC);
    $total = $row["total"];
    $last_page = (int)ceil($total / 10);

    $query = "SELECT B.name as name, A.title as title, A.content as content, B.img AS img, A.reg_date as reg_date FROM push_history A INNER JOIN keyword B ON A.key_idx = B.idx WHERE mem_idx = :idx ORDER BY A.reg_date DESC LIMIT {$start}, 10";
    $stmt = $DB_con->prepare($query);
    $stmt->bindParam(":idx", $idx);
    $stmt->execute();

    $num = $stmt->rowCount();
    if ($num > 0) {
        $push_list = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $push = array();
            
            $img = $row['img'];                        													  // 키워드 이미지
    		if ($img == "") {
                $img = $web_url . "/data/keyword/non_key_image.png";
            }

            $push["name"] = $row["name"];
            $push["title"] = $row["title"];
            $push["content"] = $row["content"];
            $push["image"] = $img;
            $push["regDate"] = $row["reg_date"];
            array_push($push_list, $push);
        }
        $result = array("result" => true, "totalCnt" => $total, "page" => $page, "lastPage" => $last_page, "pushList" => $push_list);
    } else {
        $result = array("result" => true, "pushList" => []);
    }
} else {
    $result = array("result" => false, "errorMsg" => "회원 고유번호가 없습니다.");
}

echo json_encode($result);