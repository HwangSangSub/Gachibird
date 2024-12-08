<?

/**
 * 관심 키워드 등록
 * 
 * 관심키워드 수정은 추후 정해지면 작업
 */
include "../lib/common.php";
include "../lib/functionDB.php";
$DB_con = db1();

$member_keyword_cnt = memberKeywordCnt($idx);

// JSON 데이터를 받아옵니다.
$json_data = file_get_contents('php://input');
/*
예제
Array
(
    [idx] => 1
    [keyword] => Array
        (
            [0] => 1번
            [1] => 2번
        )

)
*/
// JSON 데이터를 PHP 배열로 변환합니다.
$params = json_decode($json_data, true);

$mem_idx = trim($params["idx"]);                                          // 회원 고유번호
$keywrod_arr = $params["keyword"];                               // 키워드(배열로)
$reg_date = TIME_YMDHIS;                                                       // 오늘날짜
$member_reg_keyword_cnt = memberRegKeyword($mem_idx);  // 회원별 키워드 등록 가능 수
$member_keyword_cnt = memberKeywordCnt($mem_idx);   // 회원별 키워드 등록 한 수

$keywrod_arr_cnt = count($keywrod_arr);
if ($member_reg_keyword_cnt < ($member_keyword_cnt + $keywrod_arr_cnt)) {
    $result = array("result" => false, "errorMsg" => "회원의 최대 키워드 등록 수를 초과하였습니다. 확인 후 다시 시도해주세요.");
} else {
    if ($mem_idx != "") {
        if ($keywrod_arr_cnt < 1) {
            // 등록할 키워드가 없는 경우 그냥 넘기기
            $result = array("result" => true);
        } else {
            // 선택한 키워드 입력하기 (받아온 키워드 배열 수 만큼 반복)
            for ($i = 0; $i < $keywrod_arr_cnt; $i++) {
                if ($mode == "mod") { 
                } else {
                    $keyword = $keywrod_arr[$i];
    
                    // 1. 키워드 등록하기 만약에 있는 경우는 기존 키워드 고유번호로 등록하기.
                    $chk_keyword_query = "SELECT idx FROM keyword WHERE name = :name LIMIT 1";
                    $chk_keyword_stmt = $DB_con->prepare($chk_keyword_query);
                    $chk_keyword_stmt->bindparam(":name", $keyword);
                    $chk_keyword_stmt->execute();
                    $chk_keyword_num = $chk_keyword_stmt->rowCount();
    
                    if ($chk_keyword_num < 1) { // 등록된 키워드가 없으면 키워드 등록하기.
                        $key_idx = keywordReg($keyword);
                    } else {
                        $chk_keyword_row = $chk_keyword_stmt->fetch(PDO::FETCH_ASSOC);
                        $key_idx = $chk_keyword_row['idx'];                     // 키워드 고유번호
                    }
                    // 2. 회원-키워드가 연결여부를 확인하여 연결이 되어 있다면 상태를 변경한다. 없으면 새로 등록한다.
                    $chk_query = "SELECT idx, disply FROM member_keyword WHERE mem_idx = :mem_idx AND key_idx = :key_idx";
                    $chk_stmt = $DB_con->prepare($chk_query);
                    $chk_stmt->bindparam(":mem_idx", $mem_idx);
                    $chk_stmt->bindparam(":key_idx", $key_idx);
                    $chk_stmt->execute();
                    $chk_num = $chk_stmt->rowCount();

                    // 기존 sort값 정렬
                    resetSort($mem_idx);

                    // 다음 sort 값 가져오기
                    $sort = getLastSort($mem_idx);

                    if ($chk_num < 1) {
                        // 3. 키워드 고유번호와 유저 고유번호를 연결
                        $ins_query = "INSERT INTO member_keyword SET mem_idx = :mem_idx, key_idx = :key_idx, sort = :sort, reg_date = :reg_date";
                        $ins_stmt = $DB_con->prepare($ins_query);
                        $ins_stmt->bindparam(":mem_idx", $mem_idx);
                        $ins_stmt->bindparam(":key_idx", $key_idx);
                        $ins_stmt->bindparam(":sort", $sort);
                        $ins_stmt->bindparam(":reg_date", $reg_date);
                        $ins_stmt->execute();
                    } else {
                        $chk_row = $chk_stmt->fetch(PDO::FETCH_ASSOC);
                        $mkey_idx = $chk_row['idx'];                          // 회원- 키워드 고유번호
                        $disply = $chk_row['disply'];                     // 상태(Y: 키워드등록, N: 키워드삭제)
                        if ($disply == "N") {
                            $up_query = "UPDATE member_keyword SET disply = 'Y', sort = :sort WHERE idx = :mkey_idx LIMIT 1";
                            $up_stmt = $DB_con->prepare($up_query);
                            $up_stmt->bindparam(":mkey_idx", $mkey_idx);
                            $up_stmt->bindparam(":sort", $sort);
                            $up_stmt->execute();
                        }
                    }
                }
                $result = array("result" => true);
            }
        }
    
        dbClose($DB_con);
        $chk_keyword_stmt = null;
        $ins_keyword_stmt = null;
        $chk_stmt = null;
        $ins_stmt = null;
        $up_stmt = null;
    } else { // 토큰이 없는 경우
        $result = array("result" => false, "errorMsg" => "회원 고유번호가 없습니다.");
    }
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
