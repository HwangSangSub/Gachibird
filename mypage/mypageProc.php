<?
include '../lib/common.php';
include '../lib/functionDB.php';

$DB_con = db1();

$mem_idx = trim($idx);                                       // 회원고유번호
$mod = trim($mod);                                             // push : 이벤트 및 소식 알림, keypush : 마이 키워드 알림, stoppush : 방해금지 시간 설정
$hour = trim($hour);                                          // 방해금지 종료 시간
$reg_date = TIME_YMDHIS;                           // 오늘 날짜

if ($mod == 'push') {

    $up_query = "UPDATE member SET adpush_bit = CASE WHEN adpush_bit = '0' THEN '1' WHEN adpush_bit = '1' THEN '0' ELSE adpush_bit END WHERE idx = :mem_idx";
    $up_stmt = $DB_con->prepare($up_query);
    $up_stmt->bindParam(':mem_idx', $mem_idx);
    $up_stmt->execute();

    $chk_query = "SELECT adpush_bit FROM member WHERE idx = :mem_idx LIMIT 1";
    $chk_stmt = $DB_con->prepare($chk_query);
    $chk_stmt->bindParam(':mem_idx', $mem_idx);
    $chk_stmt->execute();
    
    $chk_row = $chk_stmt->fetch(PDO::FETCH_ASSOC);

	$result = array("result" => true, "adpush_bit" => $chk_row["adpush_bit"]);

	dbClose($DB_con);
	$up_stmt = null;
} else if ($mod == 'keypush') {

    $up_query = "UPDATE member SET keypush_bit = CASE WHEN keypush_bit = '0' THEN '1' WHEN keypush_bit = '1' THEN '0' ELSE keypush_bit END WHERE idx = :mem_idx";
    $up_stmt = $DB_con->prepare($up_query);
    $up_stmt->bindParam(':mem_idx', $mem_idx);
    $up_stmt->execute();
    
    $chk_query = "SELECT keypush_bit FROM member WHERE idx = :mem_idx LIMIT 1";
    $chk_stmt = $DB_con->prepare($chk_query);
    $chk_stmt->bindParam(':mem_idx', $mem_idx);
    $chk_stmt->execute();
    
    $chk_row = $chk_stmt->fetch(PDO::FETCH_ASSOC);

	$result = array("result" => true, "keypush_bit" => $chk_row["keypush_bit"]);

	dbClose($DB_con);
	$up_stmt = null;
} else if ($mod == 'stoppush') {
    $end_time = time() + (3600 * $hour);
    $stop_end_time = date('Y-m-d H:i:s', $end_time);
    
    $up_query = "UPDATE member SET stoppush_bit = CASE WHEN stoppush_bit = '0' THEN '1' WHEN stoppush_bit = '1' THEN '0' ELSE stoppush_bit END, stop_end_time = :stop_end_time WHERE idx = :mem_idx";
    $up_stmt = $DB_con->prepare($up_query);
    $up_stmt->bindParam(':stop_end_time', $stop_end_time);
    $up_stmt->bindParam(':mem_idx', $mem_idx);
    $up_stmt->execute();

    $chk_query = "SELECT stoppush_bit FROM member WHERE idx = :mem_idx LIMIT 1";
    $chk_stmt = $DB_con->prepare($chk_query);
    $chk_stmt->bindParam(':mem_idx', $mem_idx);
    $chk_stmt->execute();
    
    $chk_row = $chk_stmt->fetch(PDO::FETCH_ASSOC);
    
	$result = array("result" => true, "stoppush_bit" => $chk_row["stoppush_bit"], "endTime" => $chk_row["stoppush_bit"] == '1' ? $stop_end_time : NULL);

	dbClose($DB_con);
	$up_stmt = null;
} else {
	$result = array("result" => false, "errorMsg" => "구분값이 없습니다.");
}
echo json_encode($result, JSON_UNESCAPED_UNICODE);
