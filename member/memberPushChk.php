<?
include "../lib/common.php";

$DB_con = db1();

$idx = trim($idx);

if ($idx !== "") {
    $query = "SELECT adpush_bit, keypush_bit, stoppush_bit, (CASE WHEN stoppush_bit = '1' THEN stop_end_time WHEN stoppush_bit = '0' THEN NULL ELSE stop_end_time END) as stop_end_time FROM member WHERE idx = :idx LIMIT 1";
    $stmt = $DB_con->prepare($query);
    $stmt->bindParam(":idx", $idx);
    $stmt->execute();

    $num = $stmt->rowCount();
    if ($num > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $result = array("result" => true, "adpush" => $row["adpush_bit"], "keypush" => $row["keypush_bit"], "stoppush" => $row["stoppush_bit"], "endTime" => $row["stop_end_time"]);
    } else {
        $result = array("result" => false, "errorMsg" => "회원을 찾을 수 없습니다.");
    }
} else {
    $result = array("result" => false, "errorMsg" => "회원 고유번호가 없습니다.");
}

echo json_encode($result);