<?
/**
 * 회원의 키워드 목록 
 */
    include "../lib/common.php";

    $DB_con = db1();
    $mem_idx = trim($mem_idx); // 회원 인덱스값

    $query = "SELECT * FROM member_keyword A INNER JOIN keyword B ON A.key_idx = B.idx WHERE mem_idx = :mem_idx AND disply = 'Y'";
    $stmt = $DB_con->prepare($query);
    $stmt->bindParam(":mem_idx", $mem_idx);
    $stmt->execute();
    $rowCount = $stmt->rowCount();
    $result = array();

    if ($rowCount > 0) {
        $keyword_list = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $idx = $row['idx'];
            $keyword = $row['name'];
            
            $keywords = [
                'idx' => $idx,
                'keyword' => $keyword
            ];
    
            array_push($keyword_list, $keywords);
        }
        print_r($keyword_list);
    } else {
        ;
    }
    

?>