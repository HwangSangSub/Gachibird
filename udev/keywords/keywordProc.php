<?

include "../../udev/lib/common.php";
include "../../lib/alertLib.php";

$DB_con = db1();

$idx = trim($idx);
$kwd_nm = trim($kwd_nm);

if ($mode == "reg") {
	$ins_query = "INSERT INTO keyword SET name = :name ";
	$ins_stmt = $DB_con->prepare($ins_query);
	$ins_stmt->bindParam(":name", $kwd_nm);
	$ins_stmt->execute();

	$pre_url = "keywordsList.php?page=$page&$qstr";
	$message = "reg";
	proc_msg($message, $pre_url);
} else if ($mode == "mod") { //수정일경우

	//키워드 기본 수정
	$up_query = "UPDATE keyword SET name = :name WHERE idx = :idx LIMIT 1";
	$up_stmt = $DB_con->prepare($up_query);
	$up_stmt->bindParam(":name", $kwd_nm);
	$up_stmt->bindParam(":idx", $idx);
	$up_stmt->execute();

	//변경된 키워드의 뉴스와 정보 다시 저장

	$pre_url = "keywordsList.php?page=$page&$qstr";
	$message = "mod";
	proc_msg($message, $pre_url);
} else {  //삭제일경우

	//키워드 기본 수정
	$up_query = "UPDATE keyword SET name = :name WHERE idx = :idx LIMIT 1";
	$up_stmt = $DB_con->prepare($up_query);
	$up_stmt->bindParam(":name", $kwd_nm);
	$up_stmt->bindParam(":idx", $idx);
	$up_stmt->execute();


	$pre_url = "keywordsList.php?page=$page&$qstr";
	$message = "del";
	proc_msg($message, $pre_url);
}


dbClose($DB_con);
$ins_stmt = null;
$up_stmt = null;