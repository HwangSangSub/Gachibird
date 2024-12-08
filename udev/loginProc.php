<?
include "./lib/common.php";

//암호화할 때:  
$user_id = trim($user_id);
$password = trim($user_pw);

$DB_con = db1();
/*, login_cnt*/
$query = "SELECT idx, mem_id, mem_pwd, mem_lv  from member  WHERE mem_id = :mem_id AND disply = 'N' AND mem_lv IN (0,1,2)";
$stmt = $DB_con->prepare($query);
$stmt->bindparam(":mem_id", $user_id);
$user_id = trim($user_id);
$stmt->execute();
$num = $stmt->rowCount();
if ($num < 1) { //아닐경우
	echo "error";
} else {

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

		$idx = $row['idx'];
		$hash = $row['mem_pwd'];
		if (password_verify($password, $hash)) { // 비밀번호가 일치하는지 비교합니다. 

			echo "success";  // 비밀번호가 맞음 

			# 마지막 로그인 시간을 업데이트 한다.
			$upQquery = "UPDATE member SET login_date = now() WHERE  mem_id = :mem_id AND idx = :idx /* AND login_cnt = :login_cnt */ LIMIT 1";
			$upStmt = $DB_con->prepare($upQquery);
			$upStmt->bindparam(":idx", $idx);
			$upStmt->bindparam(":mem_id", $user_id);
			$upStmt->execute();

			$mem_id = $user_id;									   // 아이디
			$mem_pwd = $row['mem_pwd'];	           // 비밀번호
			$mem_lv = $row['mem_lv'];

			setcookie("udev[id]", $mem_id, false, "/");
			setcookie("udev[midx]", $idx, false, "/");
			setcookie("udev[pw]", $mem_pwd, false, "/");
			setcookie("udev[lv]", $mem_lv, false, "/");
		} else {
			echo "error";  // 비밀번호가 틀림 
		}
	}

	dbClose($DB_con);
	$stmt = null;
	$upStmt = null;
	$upStmt2 = null;
}
