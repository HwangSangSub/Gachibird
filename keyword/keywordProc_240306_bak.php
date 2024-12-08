<?
include '../lib/common.php';

$DB_con = db1();

$mem_idx = trim($mem_idx);
$key_idx = trim($key_idx);
$key_name = preg_replace("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", trim($key_name));
$mod = trim($mod);

if ($mod == 'create') { // 회원의 최대 등록 키워드 수 확인
    $get_member_query = "SELECT A.idx, A.mem_lv, COUNT(B.idx) AS cnt, (CASE WHEN A.mem_lv = 9 THEN C.guest_keyword_reg_cnt ELSE C.keyword_max_reg_cnt END) AS max_cnt FROM member A INNER JOIN member_keyword B ON A.idx = B.mem_idx AND B.disply = 'Y' INNER JOIN config C WHERE A.idx = 2 GROUP BY A.idx LIMIT 1";
    $get_member_stmt = $DB_con->prepare($get_member_query);
    $get_member_stmt->bindParam(":idx", $mem_idx);
    $get_member_stmt->execute();
    $get_member_result = $get_member_stmt->fetch(PDO::FETCH_ASSOC);
    $mem_lv = $get_member_result["mem_lv"];
    $mem_key_cnt = $get_member_result["cnt"];
    $mem_max_cnt = $get_member_result["max_cnt"];

    if ($mem_max_cnt <= $mem_key_cnt) {
        echo "fail";
        exit();
    }
}

if ($mod == 'create' || $mod == 'modify') { // 키워드 등록

    if ($mod == 'modify') { // 수정이면 기존에 키워드 해제
        $update_keyword_query = "UPDATE member_keyword SET disply = 'N' WHERE mem_idx = :mem_idx AND key_idx = :key_idx";
        $update_keyword_stmt = $DB_con->prepare($update_keyword_query);
        $update_keyword_stmt->bindParam("mem_idx", $mem_idx);
        $update_keyword_stmt->bindParam("key_idx", $key_idx);
        $update_keyword_stmt->execute();
    }

    // 기존에 있던 키워드인지 확인
    $is_set_keyword_query = "SELECT idx, COUNT(*) AS cnt FROM keyword WHERE name = :name LIMIT 1";
    $is_set_keyword_stmt = $DB_con->prepare($is_set_keyword_query);
    $is_set_keyword_stmt->bindParam(":name", $key_name);
    $is_set_keyword_stmt->execute();

    $is_set_keyword_result = $is_set_keyword_stmt->fetch(PDO::FETCH_ASSOC);

    $keyword_idx = $is_set_keyword_result["idx"];
    $keyword_count = $is_set_keyword_result["cnt"];
    $now = date("Y-m-d H:i:s", time());

    if ($keyword_count > 0) { // 기존에 있던 키워드 등록
        // 회원이 전에 같은 키워드를 등록 후 해제하였는지 확인
        $is_set_member_keyword_query = "SELECT idx, COUNT(*) AS cnt FROM member_keyword WHERE mem_idx = :mem_idx AND key_idx = :key_idx AND disply = 'N' LIMIT 1";
        $is_set_member_keyword_stmt = $DB_con->prepare($is_set_member_keyword_query);
        $is_set_member_keyword_stmt->bindParam(":mem_idx", $mem_idx);
        $is_set_member_keyword_stmt->bindParam(":key_idx", $keyword_idx);
        $is_set_member_keyword_stmt->execute();
        
        $is_set_member_keyword_result = $is_set_member_keyword_stmt->fetch(PDO::FETCH_ASSOC);

        $member_keyword_idx = $is_set_member_keyword_result["idx"];
        $member_keyword_count = $is_set_member_keyword_result["cnt"];

        if ($member_keyword_count > 0) { // 기존에 등록했던 회원-키워드 테이블을 수정
            $query = "UPDATE member_keyword SET disply = 'Y', reg_date = :reg_date, cancle_date = NULL WHERE idx = :idx";
            $stmt = $DB_con->prepare($query);
            $stmt->bindParam(":reg_date", $now);
            $stmt->bindParam(":idx", $member_keyword_idx);
            $stmt->execute();
            $row_count = $stmt->rowCount();
        } else { // 새롭게 회원-키워드 테이블에 등록
            $query = "INSERT INTO member_keyword VALUES (NULL, :mem_idx, :key_idx, 'Y', :reg_date, NULL)";
            $stmt = $DB_con->prepare($query);
            $stmt->bindParam(":mem_idx", $mem_idx);
            $stmt->bindParam(":key_idx", $keyword_idx);
            $stmt->bindParam(":reg_date", $now);
            $stmt->execute();
            $row_count = $stmt->rowCount();
        }

        if ($row_count > 0) {
            echo "success";
        } else {
            echo "fail";
        }
    } else { // 새로운 키워드 등록
        // 키워드 테이블에 새로운 키워드 등록
        $insert_keyword_query = "INSERT INTO keyword VALUES (NULL, :name, :reg_date, NULL)";
        $insert_keyword_stmt = $DB_con->prepare($insert_keyword_query);
        $insert_keyword_stmt->bindParam(":name", $key_name);
        $insert_keyword_stmt->bindParam(":reg_date", $now);
        $insert_keyword_stmt->execute();

        $keyword_idx = $DB_con->lastInsertId();

        // 회원-키워드 테이블에 등록
        $query = "INSERT INTO member_keyword VALUES (NULL, :mem_idx, :key_idx, 'Y', :reg_date, NULL)";
        $stmt = $DB_con->prepare($query);
        $stmt->bindParam(":mem_idx", $mem_idx);
        $stmt->bindParam(":key_idx", $keyword_idx);
        $stmt->bindParam(":reg_date", $now);
        $stmt->execute();
        $row_count = $stmt->rowCount();

        if ($row_count > 0) {
            echo "success";
        } else {
            echo "fail";
        }
    }

} else if ($mod == 'delete'){
    $update_keyword_query = "UPDATE member_keyword SET disply = 'N' WHERE mem_idx = :mem_idx AND key_idx = :key_idx";
    $update_keyword_stmt = $DB_con->prepare($update_keyword_query);
    $update_keyword_stmt->bindParam("mem_idx", $mem_idx);
    $update_keyword_stmt->bindParam("key_idx", $key_idx);
    $update_keyword_stmt->execute();
    $row_count = $update_keyword_stmt->rowCount();

    if ($row_count > 0) {
        echo "success";
    } else {
        echo "fail";
    }
}