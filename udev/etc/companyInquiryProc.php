<?
// ini_set("display_errors", 1);
include "../../udev/lib/common.php";
include "../../lib/alertLib.php";
include "../../lib/thumbnail.lib.php";   //썸네일

$DB_con = db1();
if ($mode == "reg") { //등록일경우
	$Qquery = "UPDATE TB_COMPANY_CONTACT SET contact_Comment = :contact_Comment WHERE idx = :idx  LIMIT 1";
	$Stmt = $DB_con->prepare($Qquery);
	$Stmt->bindparam(":contact_Comment", $contact_Comment);
	$Stmt->bindParam(":idx", $idx);
	$Stmt->execute();

	$preUrl = "companyInquiryList.php?page=$page&$qstr";
	$message = "reg";
	proc_msg($message, $preUrl);
} else if ($mode == "mod") { //수정일경우

	$upQquery = "UPDATE TB_COMPANY_CONTACT SET contact_Comment = :contact_Comment WHERE idx = :idx  LIMIT 1";
	$upStmt = $DB_con->prepare($upQquery);
	$upStmt->bindparam(":contact_Comment", $contact_Comment);
	$upStmt->bindParam(":idx", $idx);
	$upStmt->execute();

	$preUrl = "companyInquiryList.php?page=$page&$qstr";
	$message = "mod";
	proc_msg($message, $preUrl);
}



dbClose($DB_con);
$Stmt = null;
$upStmt = null;
