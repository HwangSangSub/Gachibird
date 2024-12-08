<?php
include "../lib/common.php"; 

$DB_con = db1();

$contact_name = trim($contact_name);
$contact_email = trim($contact_email);
$contact_phone = trim($contact_phone);
$contact_content = sql_escape_string($contact_content);

// $contactSql = "INSERT INTO TB_COMPANY_CONTACT (contact_Name, contact_Email, contact_Tel, contact_Content, contact_Comment, reg_Date) VALUES (:contact_name , :contact_email , :contact_phone , :contact_content, NULL, now())";
$contactSql = "INSERT INTO TB_COMPANY_CONTACT SET ";
$contactSql .= "contact_Name = :contact_Name ,contact_Content = :contact_Content ";

if (isset($contact_email)) {
    $contactSql .= ",contact_Email = :contact_Email ";
}

if (isset($contact_phone)) {
    $contactSql .= ",contact_Tel = :contact_Tel ";
}

$constactStmt = $DB_con->prepare($contactSql);
$constactStmt->bindValue(":contact_Name", $contact_name);
$constactStmt->bindValue(":contact_Content", $contact_content);

if (isset($contact_email)) {
    $constactStmt->bindValue(":contact_Email", $contact_email);
}

if (isset($contact_phone)) {
    $constactStmt->bindValue(":contact_Tel", $contact_phone);
}

$constactStmt->execute();
if ($constactStmt->rowCount() > 0) {
    echo true;
} else {
    echo false;
}