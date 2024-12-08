<?
include "../lib/common.php";
header("Content-Type: text/html");
$DB_con = db1();

$idx = trim($idx);
$query = "SELECT title, content, reg_date FROM board WHERE idx = :idx LIMIT 1";
$stmt = $DB_con->prepare($query);
$stmt->bindParam(":idx", $idx);
$stmt->execute();
$num = $stmt->rowCount();
if ($num < 1) {
} else {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $reg_date = date("Y.m.d", strtotime($row["reg_date"]));
?>
<!DOCTYPE html>
<html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../common/css/main/notice.css">
    <link rel="stylesheet" href="../common/css/main/font/pretendard/pretendard.css">
    <title>공지사항</title>
</head>

<body>
    <section class="wrapper">
        <div>
            <div class="return_btn"><img src="../common/img/main/back_icon.svg" alt=""></div>
            <p class="notice_title"><?= $row["title"] ?></p>
            <p class="notice_date"><?= $reg_date ?></p>
        </div>
        <div class="line"></div>
        
        <div class="sub_title">짹짹, 안녕하세요. 가치버드입니다.</div>

        <div class="sub_content">
            <?= $row["content"] ?>
        </div>
    </section>
</body>
</html>
<?
}
?>