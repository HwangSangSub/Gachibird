<?
/**
 * 나의 키워드 순서 변경
 */

include '../lib/common.php'; 

$DB_con = db1();

// 회원 키워드 테이블 배열로 입력받기
$json_data = file_get_contents('php://input');
/*

*/

$param = json_decode($json_data, true);

$mem_idx = $param["idx"];
$mkey_list = $param["mKeyList"];

if (isset($mem_idx) && isset($mkey_list)) {
    if (count($mkey_list) > 0) { // 키워드 배열이 있다면
        // 배열 개수만큼 반복문
        foreach($mkey_list as $key => $value) {
            $sort = $key + 1;
            // 배열 순서에 맞게 sort 업데이트
            $query = "UPDATE member_keyword SET sort = :sort WHERE idx = :idx AND mem_idx = :mem_idx AND disply = 'Y'";
            $stmt = $DB_con->prepare($query);
            $stmt->bindParam(":sort", $sort);
            $stmt->bindParam(":idx", $value);
            $stmt->bindParam(":mem_idx", $mem_idx);
            $stmt->execute();
            $num = $stmt->rowCount();
        }
        $result = array("result" => true);
    } else { // 키워드 배열이 없다면
        $result = array("result" => false, "errorMsg" => "키워드가 없습니다. 확인 후 다시 시도해주세요.");
    }
} else {
    $result = array("result" => false, "errorMsg" => "키워드 또는 회원 고유번호가 없습니다. 확인 후 다시 시도해주세요.");
}


// 성공 또는 실패 출력
echo json_encode($result);