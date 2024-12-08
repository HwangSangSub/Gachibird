<?
/*======================================================================================================================
* 프로그램			: DB 내용 불러올 함수
* 페이지 설명		: DB 내용 불러올 함수
========================================================================================================================*/

/**
 * 회원 등급 값 가져오기
 *
 * @param [int] $idx   // 회원고유번호
 * @return int                    // 회원등급
 */
function memLvInfo($idx)
{
    $fDB_con = db1();

    $query = "SELECT mem_lv FROM member WHERE idx = :idx";
    $stmt = $fDB_con->prepare($query);
    $stmt->bindparam(":idx", $idx);
    $stmt->execute();
    $num = $stmt->rowCount();

    if ($num < 1) { //아닐경우.
        return "";
    } else {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mem_lv = trim($row['mem_lv']);
        }
        return $mem_lv;
    }

    dbClose($fDB_con);
    $stmt = null;
}

/**
 * 게스트 키워드 등록 가능 수 조회
 *
 * @return int
 */
function guestRegKeywordCnt()
{
    $fDB_con = db1();

    $query = "SELECT guest_keyword_reg_cnt FROM config";
    $stmt = $fDB_con->prepare($query);
    $stmt->execute();
    $num = $stmt->rowCount();

    if ($num < 1) { //아닐경우.
        return 0;
    } else {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $guest_keyword_reg_cnt = trim($row['guest_keyword_reg_cnt']);             // 게스트 키워드 등록 가능 수
        }
        return $guest_keyword_reg_cnt;
    }

    dbClose($fDB_con);
    $stmt = null;
}

/**
 * 회원별 키워드 등록 가능 수
 *
 * @param [int] $idx                // 회원 고유번호
 * @return int
 */
function memberRegKeyword($idx)
{
    $fDB_con = db1();

    $query = "SELECT reg_keyword_cnt FROM member WHERE idx = :idx";
    $stmt = $fDB_con->prepare($query);
    $stmt->bindparam(":idx", $idx);
    $stmt->execute();
    $num = $stmt->rowCount();

    if ($num < 1) { //아닐경우.
        return 0;
    } else {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reg_keyword_cnt = trim($row['reg_keyword_cnt']);
        }
        return $reg_keyword_cnt;
    }

    dbClose($fDB_con);
    $stmt = null;
}

/**
 * 회원이 현재 등록한 키워드 수
 *
 * @param [int] $idx
 * @return int
 */
function memberKeywordCnt($idx)
{
    $fDB_con = db1();

    $query = "SELECT idx FROM member_keyword WHERE mem_idx = :idx AND disply = 'Y'";
    $stmt = $fDB_con->prepare($query);
    $stmt->bindparam(":idx", $idx);
    $stmt->execute();
    $num = $stmt->rowCount();

    return $num;

    dbClose($fDB_con);
    $stmt = null;
}

/**
 * 키워드 등록 여부 키워드가 등록되어 있을 경우 idx 내려보냄
 *
 * @param [string] $name
 * @return int
 */
function isKeyword($name)
{
    $fDB_con = db1();

    $query = "SELECT idx FROM keyword WHERE name = :name";
    $stmt = $fDB_con->prepare($query);
    $stmt->bindparam(":name", $name);
    $stmt->execute();

    $num = $stmt->rowCount();

    if ($num < 1) { //아닐경우.
        return 0;
    } else {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $idx = trim($row['idx']);
        }
        return $idx;
    }

    dbClose($fDB_con);
    $stmt = null;
}

/**
 * 키워드 등록
 *
 * @param [string] $name            // 키워드 명
 * @return int                                      // 키워드 고유번호
 */
function keywordReg($name)
{
    $fDB_con = db1();
    $reg_date = TIME_YMDHIS;                                          // 오늘날짜

    $query = "INSERT INTO keyword SET name = :name, reg_date = :reg_date";
    $stmt = $fDB_con->prepare($query);
    $stmt->bindparam(":name", $name);
    $stmt->bindparam(":reg_date", $reg_date);
    $stmt->execute();
    $key_idx = $fDB_con->lastInsertId();                      // 키워드 고유번호

    return $key_idx;

    dbClose($fDB_con);
    $stmt = null;
}

/**
 * 뉴스 북마크 등록 여부 뉴스 북마크가 등록되어 있을 경우 idx와 disply 내려보냄
 *
 * @param [int] $mem_idx
 * @param [int] $news_idx
 * @return null|array
 */
function isBookmark($mem_idx, $news_idx)
{
    $fDB_con = db1();

    $query = "SELECT idx, disply FROM member_bookmark WHERE mem_idx = :mem_idx AND news_idx = :news_idx";
    $stmt = $fDB_con->prepare($query);
    $stmt->bindParam(":mem_idx", $mem_idx);
    $stmt->bindParam(":news_idx", $news_idx);
    $stmt->execute();
    $num = $stmt->rowCount();

    if ($num < 1) {
        return null;
    } else {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $idx = trim($row['idx']);
            $disply = trim($row['disply']);
        }
        return array("idx" => $idx, "disply" => $disply);
    }

    dbClose($fDB_con);
    $stmt = null;
}

/**
 * 북마크 등록
 *
 * @param [int] $mem_idx
 * @param [int] $news_idx
 * @return int
 */
function bookmarkReg($mem_idx, $news_idx)
{
    $fDB_con = db1();
    $reg_date = TIME_YMDHIS;

    $query = "INSERT INTO member_bookmark SET mem_idx = :mem_idx, news_idx = :news_idx, reg_date = :reg_date";
    $stmt = $fDB_con->prepare($query);
    $stmt->bindparam(":mem_idx", $mem_idx);
    $stmt->bindparam(":news_idx", $news_idx);
    $stmt->bindparam(":reg_date", $reg_date);
    $stmt->execute();
    $mark_idx = $fDB_con->lastInsertId();

    return $mark_idx;

    dbClose($fDB_con);
    $stmt = null;
}

/**
 * 회원-키워드 조회
 *
 * @param [int] $mem_idx
 * @param [string] $key_name
 * @return null|array
 */
function isMemKeyword($mem_idx, $key_idx)
{
    $fDB_con = db1();

    $query = "SELECT idx, disply, sort FROM member_keyword WHERE mem_idx = :mem_idx AND key_idx = :key_idx";
    $stmt = $fDB_con->prepare($query);
    $stmt->bindParam(":mem_idx", $mem_idx);
    $stmt->bindParam(":key_idx", $key_idx);
    $stmt->execute();
    $num = $stmt->rowCount();

    if ($num < 1) {
        return null;
    } else {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return array("idx" => $row["idx"], "key_idx" => $key_idx, "disply" => $row["disply"], "sort" => $row["sort"]);
    }

    dbClose($fDB_con);
    $stmt = null;
}

/**
 * disply 수정  테이블의 idx의 값을 가진 컬럼의 disply를 변경 후 성공여부 내려보냄
 *
 * @param [string] $table
 * @param [int] $idx
 * @param [string] $disply
 * @return int
 */
function upDisply($table, $idx, $disply, $cancle_date = null)
{
    $fDB_con = db1();
    $cancle_query = "";

    if ($cancle_date) {
        $cancle_query = ", cancle_date = :cancle_date";
    }

    $query = "UPDATE {$table} SET disply = :disply {$cancle_query} WHERE idx = :idx";
    $stmt = $fDB_con->prepare($query);
    $stmt->bindParam(":disply", $disply);
    if ($cancle_date) {
        if ($cancle_date == "NULL") {
            $stmt->bindParam(":cancle_date", $cancle_date, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(":cancle_date", $cancle_date);
        }
    }
    $stmt->bindParam(":idx", $idx);
    $stmt->execute();

    return $stmt->rowCount();

    dbClose($fDB_con);
    $stmt = null;
}

/**
 * 뉴스 조회
 *
 * @param [int] $news_idx
 * @return int
 */
function isNews($news_idx)
{
    $fDB_con = db1();

    $query = "SELECT * FROM keyword_news WHERE idx = :idx";
    $stmt = $fDB_con->prepare($query);
    $stmt->bindParam(":idx", $news_idx);
    $stmt->execute();
    $num = $stmt->rowCount();

    if ($num > 0) {
        return $news_idx;
    } else {
        return 0;
    }

    dbClose($fDB_con);
    $stmt = null;
}

/**
 * 상위 키워드 n개 조회
 *
 * @param integer $limit
 * @return array|boolean
 */
function topKeyword($limit = 0, $mem_idx)
{
    $fDB_con = db1();

    $query = "SELECT A.idx AS idx
                            , name AS name
                            , img AS img
                            , (SELECT COUNT(idx) FROM member_keyword WHERE key_idx = A.idx AND disply = 'Y') AS cnt
                        FROM keyword A 
                        WHERE (SELECT COUNT(idx) FROM keyword_news WHERE key_idx = A.idx) > 2
                            AND A.idx NOT IN (SELECT idx FROM member_keyword WHERE mem_idx = :mem_idx AND disply = 'Y')
                        ORDER BY RAND(), NAME ASC";
                        // ORDER BY cnt DESC, NAME ASC"; // 랜덤으로 뽑기 위해서 cnt 정렬 제거
    if ($limit > 0) {
        $query .= " LIMIT " . $limit;
    }
    $stmt = $fDB_con->prepare($query);
    $stmt->bindParam(":mem_idx", $mem_idx);
    $stmt->execute();
    $num = $stmt->rowCount();
    $datas = array();

    if ($num > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data = array();
            $data["idx"] = $row["idx"];
            $data["name"] = $row["name"];
            $data["img"] = $row["img"];
            if ($row["img"] == "") {
                $data["img"] = "https://" . $_SERVER['HTTP_HOST'] . "/data/keyword/non_key_image_text.png";
            } else {
                $data["img"] = $row["img"];
            }
            $data["cnt"] = $row["cnt"];

            array_push($datas, $data);
        }

        return $datas;
    } else {
        return false;
    }


    dbClose($fDB_con);
    $stmt = null;
}

/**
 * 키워드의 링크 n개 조회
 *
 * @param [int] $key_idx
 * @param integer $limit
 * @return array|boolean
 */
function getNewsLink($key_idx, $limit = 0)
{
    $fDB_con = db1();

    $query = "SELECT title, link FROM keyword_news WHERE key_idx = :key_idx ORDER BY create_date DESC";
    if ($limit > 0) {
        $query .= " LIMIT " . $limit;
    }
    $stmt = $fDB_con->prepare($query);
    $stmt->bindParam(":key_idx", $key_idx);
    $stmt->execute();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $datas = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data = array();
            $data["title"] = str_replace("&quot;", "\"", str_replace("&amp;", "&", strip_tags(stripslashes($row["title"]))));
            $data["link"] = $row["link"];

            array_push($datas, $data);
        }

        return $datas;
    } else {
        $datas = array();
        return $datas;
    }

    dbClose($fDB_con);
    $stmt = null;
}
/**
 * 키워드 등록 랭킹  min분전의 키워드가 가장 많이 등록된 순으로 limit개 조회
 *
 * @param integer $mem_idx
 * @param integer $min
 * @param integer $limit
 * @param string $interval
 * @return array|boolean
 */
function getRanking($mem_idx, $min = 0, $limit = 0, $interval = "t")
{
    $fDB_con = db1();

    if ($interval == "t") {
        $date_format = "";
    } else {
        $date_format = "AND DATE_FORMAT(NOW(), '%Y-%m-%d') = DATE_FORMAT(B.reg_date, '%Y-%m-%d')";
    }

    $date = date("Y-m-d H:i:s", time() - ($min * 60));
    $query = "SELECT A.idx as idx, A.name as name, SUM(IF(B.disply = 'Y' {$date_format} AND B.reg_date < :reg_date,1,0)) AS cnt, SUM(IF(B.mem_idx = :mem_idx,1,0)) AS mykey, A.img FROM keyword A LEFT JOIN member_keyword B ON A.idx = B.key_idx WHERE A.disply = 'Y' aND B.disply='Y' GROUP BY A.idx ORDER BY cnt DESC, A.name ASC";
    if ($limit > 0) {
        $query .= " LIMIT " . $limit;
    }
    $stmt = $fDB_con->prepare($query);
    $stmt->bindParam(":mem_idx", $mem_idx);
    $stmt->bindParam(":reg_date", $date);
    $stmt->execute();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    } else {
        return false;
    }
    dbClose($fDB_con);
    $stmt = null;
}

/**
 * 키워드 순위 및 편차 조회
 *
 * @param array $now
 * @param array $ago
 * @param integer $limit
 * @param boolean $image
 * @return array|boolean
 */
function getRank($now, $ago, $limit = 5, $image = true)
{
    $data = array();
    for ($i = 0; $i < count($now); $i++) {
        // 5분전 순위만큼 반복문
        for ($j = 0; $j < count($ago); $j++) {
            // 같은 키워드를 찾는다면
            if ($ago[$j]["idx"] == $now[$i]["idx"]) {
                $change = $j - $i; // 변동된 순위(상승: 상수, 변동없음: 0, 감소: 음수)
                $data[$i]["idx"] = $now[$i]["idx"];
                $data[$i]["name"] = $now[$i]["name"];
                $data[$i]["rank"] = $i + 1;
                $data[$i]["change"] = $change;
                $data[$i]["cnt"] = $now[$i]["cnt"];
                $data[$i]["use"] = $now[$i]["mykey"];
                if ($image) {
                    $img = $now[$i]['img'];                        													  // 키워드 이미지
                    if ($img == "") {
                        $img = "https://".$_SERVER['HTTP_HOST'] . "/data/keyword/non_key_image.png";
                    }
                    $data[$i]["image"] = $img;
                }
                break;
            }
        }
        // 기존에 없던 키워드가 5분전에 급상승 했다면
        if (!isset($data[$i]["idx"])) {
            $change = count($now) - $i - 1;
            $data[$i]["idx"] = $now[$i]["idx"];
            $data[$i]["name"] = $now[$i]["name"];
            $data[$i]["rank"] = $i + 1;
            $data[$i]["change"] = $change;
            $data[$i]["cnt"] = $now[$i]["cnt"];
            $data[$i]["use"] = $now[$i]["mykey"];
            if ($image) {
                $img = $now[$i]['img'];                        													  // 키워드 이미지
                if ($img == "") {
                    $img = "https://".$_SERVER['HTTP_HOST'] . "/data/keyword/non_key_image.png";
                }
                $data[$i]["image"] = $img;
            }
        }
        // limit만큼 실행
        if ($limit == $i + 1) {
            break;
        }
    }

    if (count($data)) {
        return $data;
    } else {
        return false;
    }
}

/**
 * 회원이 등록한 키워드의 마지막 순서 조회
 *
 * @param int $mem_idx
 * @return int
 */
function getLastSort($mem_idx) {
    $fDB_con = db1();
    
    $query = "SELECT sort AS sort FROM member_keyword WHERE mem_idx = :mem_idx AND disply = 'Y' ORDER BY sort DESC LIMIT 1";
    $stmt = $fDB_con->prepare($query);
    $stmt->bindParam(":mem_idx", $mem_idx);
    $stmt->execute();
    $num = $stmt->rowCount();
    
    if ($num > 0) {
        $sort = $stmt->fetch(PDO::FETCH_COLUMN);
        return (int)$sort + 1;
    } else {
        return 1;
    }
    
    dbClose($fDB_con);
    $stmt = null;
}

/**
 * 회원 키워드 테이블 sort 리셋
 *
 * @param int $mem_idx
 * @return boolean
 */
function resetSort($mem_idx) {
    $fDB_con = db1();
    
    $query = "SELECT idx, sort FROM member_keyword WHERE mem_idx = :mem_idx ORDER BY sort ASC";
    $stmt = $fDB_con->prepare($query);
    $stmt->bindParam(":mem_idx", $mem_idx);
    $stmt->execute();
    $num = $stmt->rowCount();
    if ($num > 0) {
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        for($i = 0; $i < count($row); $i++) {
            $sort = $i + 1;
            $up_query = "UPDATE member_keyword SET sort = :sort WHERE idx = :idx";
            $up_stmt = $fDB_con->prepare($up_query);
            $up_stmt->bindParam(":sort", $sort);
            $up_stmt->bindParam(":idx", $mem_idx);
            $up_stmt->execute();
        }

        return true;
    } else {
        return false;
    }

    dbClose($fDB_con);
    $stmt = null;
}

function memIdxInfoToken($tokens)
{

    $fDB_con = db1();

    $memTQuery = "SELECT idx FROM member WHERE mem_token = :mem_token AND disply = 'N' LIMIT 1";
    $memTStmt = $fDB_con->prepare($memTQuery);
    $memTStmt->bindparam(":mem_token", $tokens);
    $memTStmt->execute();
    $memTNum = $memTStmt->rowCount();

    if ($memTNum < 1) { //주 ID가 없을 경우 회원가입 시작
    } else {  //등록된 회원이 있을 경우
        while ($memTRow = $memTStmt->fetch(PDO::FETCH_ASSOC)) {
            $mem_Idx = $memTRow['idx'];           //체크 랜덤아이디
        }
        return $mem_Idx;
    }

    dbClose($fDB_con);
    $memTStmt = null;
}

function pushHistoryReg($tokens, $data)
{
    $fDB_con = db1();

    $mem_idx = memIdxInfoToken($tokens);   //회원 주아이디
    $key_idx = $data["key_idx"];
    if($data["title"] != ""){
        $title = $data["title"];
    }else{
        $title = "키워드의 신규 소식을 확인해보세요.";
    }
    $content = ($data["content"] == "" ? "" : $data["content"]);

    $insPsQuery = "INSERT INTO push_history (mem_idx, key_idx, title, content, reg_date) VALUES (:mem_idx, :key_idx, :title, :content, NOW())";
    
    $insPsStmt = $fDB_con->prepare($insPsQuery);
    $insPsStmt->bindparam(":mem_idx", $mem_idx);
    $insPsStmt->bindparam(":key_idx", $key_idx);
    $insPsStmt->bindparam(":title", $title);
    $insPsStmt->bindparam(":content", $content);
    $insPsStmt->execute();
    dbClose($fDB_con);
    $insPsStmt = null;
}

function send_Push($tokens, $data)
{
    pushHistoryReg($tokens, $data);
    if($data["title"] != ""){
        $title = $data["title"];
    }else{
        $title = "키워드의 신규 소식을 확인해보세요.";
    }
    $pushUrl = "https://fcm.googleapis.com/fcm/send";
    $headers = [];
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization:key=' . GOOGLE_API_KEY;
    //푸시데이터에서 위경도값이 있으면 같이 보내기.
    $notification = [
        'title' => $title,
        'body' => $data["content"]
    ];
    $extraNotificationData = ["message" => $notification];
    $data = array(
        "data" => $notification,
        "notification" => $notification,
        "to"  => $tokens //token get on my ipad with the getToken method of cordova plugin,
    );
    $json_data =  json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $pushUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

    $result = curl_exec($ch);

    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }
    curl_close($ch);

    sleep(1);

    return $result;
}