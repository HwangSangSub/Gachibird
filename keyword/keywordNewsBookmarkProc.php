<?
    include "../lib/common.php";
    include '../lib/functionDB.php';

    $DB_con = db1();
    $news_idx = trim($news_idx); // 뉴스 고유번호
    $mem_idx = trim($mem_idx); // 회원 고유번호
    $mark_idx = trim($mark_idx); // 
    $mod = trim($mod);
    $reg_date = TIME_YMDHIS;

    if ($mod == "r") { // 뉴스 북마크 등록
        // 사용중인 북마크가 있는지 체크
        $bookmark = isBookmark($mem_idx, $news_idx);
        if (is_array($bookmark)) { // 사용중인 북마크가 있다
            if ($bookmark["disply"] == "Y") { // Y 이라면 false
                $result = array("result" => false, "errorMsg" => "이미 사용중인 뉴스 북마크입니다.");
            } else { // N 이라면 Y로 변경
                $num = upDisply("member_bookmark", $bookmark["idx"], "Y");
                
                if ($num > 0) {
                    $result = array("result" => true);
                } else {
                    $result = array("result" => false, "errorMsg" => "뉴스 북마크 등록을 할 수 없습니다. 잠시 후 다시 시도해주세요.");
                }
            }
            
        } else {// 사용중인 북마크가 없다
            // 북마크 생성
            $mark_idx = bookmarkReg($mem_idx, $news_idx);
            
            if ($mark_idx > 0) {
                $result = array("result" => true);
            } else {
                $result = array("result" => false, "errorMsg" => "뉴스 북마크 등록을 할 수 없습니다. 잠시 후 다시 시도해주세요.");
            }
        }
    } else if ($mod == "m"){ // 뉴스 북마크 수정

        // 뉴스가 있는지 체크
        $news_idx = isNews($news_idx);

        if ($news_idx > 0) { // 뉴스가 있다
            // 사용중인 북마크가 있는지 확인
            $bookmark = isBookmark($mem_idx, $news_idx);

            if (is_array($bookmark)) { // 사용중인 북마크가 있다
                    if ($bookmark["disply"] == "Y") { // Y 이라면 false
                        $result = array("result" => false, "errorMsg" => "이미 북마크 되어있는 뉴스입니다. 확인 후 다시 시도해주세요.");
                    } else { // N 이라면 Y로 변경
                        $up_num = 0;
                        $up_num += upDisply("member_bookmark", $bookmark["idx"], "Y");
                        $up_num += upDisply("member_bookmark", $mark_idx, "N");
                        
                        if ($up_num > 1) {
                            $result = array("result" => true);
                        } else {
                            $result = array("result" => false, "errorMsg" => "뉴스 북마크 수정 할 수 없습니다. 잠시 후 다시 시도해주세요.");
                        }
                    }

            } else { // 사용중인 북마크가 없다
                // 북마크 새로 등록 후 기존 북마크 N
                $up_num = upDisply("member_bookmark", $mark_idx, "N");
                $mark_idx = bookmarkReg($mem_idx, $news_idx);

                if ($up_num > 0 && $mark_idx > 0) {
                    $result = array("result" => true);
                } else {
                    $result = array("result" => false, "errorMsg" => "뉴스 북마크 수정 할 수 없습니다. 잠시 후 다시 시도해주세요.");
                }
            }
        } else { // 뉴스가 없다
            // false
            $result = array("result" => false, "errorMsg" => "존재하지 않는 뉴스입니다. 확인 후 다시 시도해주세요.");
        }

    } else if ($mod == "d") { // 뉴스 북마크 해제

        // 사용중인 북마크가 있는지 확인
        $bookmark = isBookmark($mem_idx, $news_idx);

        if ($bookmark["disply"] == "Y") { // 사용중인 북마크가 있다

        } else { // 사용중인 북마크가 없다

        }
    } else {
        $result = array("result" => false, "errorMsg" => "구분값이 없습니다.");
    }

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>