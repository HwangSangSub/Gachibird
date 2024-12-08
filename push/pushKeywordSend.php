<?

include "../lib/common.php";
include "../lib/functionDB.php";

$DB_con = db1();

$mem_idx = trim($idx);
$key_idx = trim($keyIdx);

if ($mem_idx !== "" && $key_idx !== "") {
    $chk_key_query = "SELECT A.name AS name, C.mem_token AS token FROM keyword A INNER JOIN member_keyword B ON A.idx = B.key_idx INNER JOIN member C ON C.idx = B.mem_idx WHERE C.idx = :mem_idx AND A.idx = :key_idx AND A.disply = 'Y' AND B.disply = 'Y' LIMIT 1";
    $chk_key_stmt = $DB_con->prepare($chk_key_query);
    $chk_key_stmt->bindParam(":key_idx", $key_idx);
    $chk_key_stmt->bindParam(":mem_idx", $mem_idx);
    $chk_key_stmt->execute();
    $chk_key_num = $chk_key_stmt->rowCount();

    if ($chk_key_num < 1) {
        $result = array("result" => false, "errorMsg" => "회원이 등록한 키워드가 아닙니다. 확인 후 다시 시도해주세요.");
    } else {
        $chk_key_row = $chk_key_stmt->fetch(PDO::FETCH_ASSOC);
        $reg_date = TIME_YMDHIS;
        $token = $chk_key_row["token"];
        $data = array("title" => $chk_key_row["name"], "content" => "키워드의 신규 소식을 확인해보세요.", "key_idx" => $key_idx);

        $rs = send_Push($token, $data);
        $result = array("result" => true);
    }
} else {
    $result = array("result" => false, "errorMsg" => "회원 고유번호 또는 키워드 고유번호가 없습니다.");
}

echo json_encode($result);