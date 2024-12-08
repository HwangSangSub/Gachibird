<?
include '../lib/common.php';

header('charset=UTF-8');

$key_idx = trim($key_idx); // 키워드 인덱스값
$DB_con = db1();

// 뉴스 인덱스값과 링크 가져오기
$get_news_link_query = "SELECT idx, link FROM keyword_news WHERE key_idx = :key_idx AND image IS NULL AND fail_cnt < 2";
$get_news_link_stmt = $DB_con->prepare($get_news_link_query);
$get_news_link_stmt->bindParam(":key_idx", $key_idx);
$get_news_link_stmt->execute();

$rmetas = array();
// 다시 선언 안함으로써 기존 html이 남지만 속도가 빠름
$doc = new DOMDocument(); 
$query = '//*/meta[starts-with(@property, \'og:image\')] | //*/meta[starts-with(@name, \'twitter:image\')] | //*/meta[starts-with(@property, \'og:site_name\')] | //*/meta[starts-with(@property, \'og:article:author\')] | //*/meta[starts-with(@name, \'twitter:site\')] | //*/meta[starts-with(@name, \'author\')]';
$exceptionImage = ["썸네일 이미지 절대 경로"];
$exceptionAgency = ["", "jndn.com", "@GameFocus_Twit", "jnilbo"];
// if ()

while($row = $get_news_link_stmt->fetch(PDO::FETCH_ASSOC)) { 
    $idx = $row['idx'];
    $url = trim($row['link']);
  
    // 소프트웨어 식별 정보 입력
    $config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';

    // 링크의 html 가져오기
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    curl_close($ch);

    // 가져온 html을 dom document로 로딩하기 
    @$doc->loadHTML("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">" . $response);
    $xpath = new DOMXPath($doc);
    $metas = $xpath->query($query);

    $image = null;
    $agency = null;

    // 헤더의 open graph의 이미지 가져오기
    foreach ($metas as $meta) { 
        $property = $meta->getAttribute('property');
        $content = $meta->getAttribute('content');
        $name = $meta->getAttribute('name');
        echo in_array($image, $exceptionImage);
        if ($property == "og:image" && end($rmetas) !== $content && !isset($image)) { 
            $image = $content;
        } else if ($property == "og:image" && !isset($image)) {
            $image = "None image";
        } else if ($name == "twitter:image" && !isset($image)) {
            $image = $content;
        } else if ($property == "og:site_name") {
            $agency = $content; 
        } else if ($property == "og:article:author" && !isset($agency)) {
            if (strpos($content, "네이버") == 0) {
                $agency = explode(" | ", $content)[1];
            } else if (strpos($content, "네이버") > 0) {
                $agency = explode(" | ", $content)[0];
            } else {
                $agency = $content;
            }
        } else if ($name == "twitter:site" && !isset($agency)) {
            if (strpos($content, "http")) {
                $agency = null;
            } else {
                $agency = $content;
            }
        } else if ($name == "author" && !isset($agency)) {
            $agency = $content;
        }
    }

    if (!isset($agency) or in_array($agency, $exceptionAgency)) {
        if (strpos($url, "kbench.com")) {
            $agency = "케이벤치";
        } else if (strpos($url, "gamevu.co.kr")) {
            $agency = "게임뷰";
        } else if (strpos($url, "gamechosun.co.kr")) {
            $agency = "게임조선";
        } else if (strpos($url, "ttlnews.com")) {
            $agency = "티티엘뉴스";
        } else if (strpos($url, "skyedaily.com")) {
            $agency = "스카이데일리";
        } else if (strpos($url, "jndn.com")) {
            $agency = "전남매일";
        } else if (strpos($url, "raonnews.com")) {
            $agency = "라온신문";
        } else if (strpos($url, "game.donga")) {
            $agency = "게임동아";
        } else if (strpos($url, "lawtimes.co.kr")) {
            $agency = "법률신문";
        } else if (strpos($url, "newsmaker.or.kr")) {
            $agency = "NewsMaker";
        } else if (strpos($url, "cnbnews.com")) {
            $agency = "CNB뉴스";
        } else if (strpos($url, "mdilbo.com")) {
            $agency = "무등일보";
        } else if (strpos($url, "vegannews.co.kr")) {
            $agency = "비건뉴스";
        } else if (strpos($url, "jnilbo.com")) {
            $agency = "전남일보";
        } else if (strpos($url, "gamefocus.co.kr")) {
            $agency = "게임포커스";
        } else if (strpos($url, "kgnews.co.kr")) {
            $agency = "경기신문";
        } 
    }

    if (strpos($url, "asiatoday.co.kr")) {
        $agency = "아시아투데이";
        // $rmetas[$idx]["agency"] = $agency;
    } else if (strpos($url, "mbnmoney.mbn.co.kr")) {
        $agency = "매일경제TV";
    } else if (strpos($url, "game.donga")) {
        $image = "https://game.donga.com" . $image;
        // $rmetas[$idx]["agency"] = $agency;
    } else if (strpos($url, "kookbang")) {
        $image = "https:" . $image;
    } else if (strpos($url, "metroseoul.co.kr")) {
        $image = "https:" . $image;
    } else if (strpos($url, "upinews.kr")) {
        $agency = "UPI뉴스";
    } 
    $rmetas[$idx]["agency"] = $agency;
    $rmetas[$idx]["image"] = $image;
}

$cnt = count($rmetas);

if ($cnt > 0) {
    $s_cnt = 0;
    $f_cnt = 0;
    foreach($rmetas as $key => $data) {
        if (isset($data["image"])) {
            $s_cnt++;
            $update_news_query = "UPDATE keyword_news SET agency = :agency, image = :image WHERE idx = :idx";
        } else {
            $f_cnt++;
            $update_news_query = "UPDATE keyword_news SET agency = :agency, fail_cnt = fail_cnt + 1 WHERE idx = :idx";
        }

        $update_news_stmt = $DB_con->prepare($update_news_query);
        $update_news_stmt->bindParam(":agency", $data["agency"]);
        $update_news_stmt->bindParam(":idx", $key);
        
        if (isset($data["image"])) {
            $update_news_stmt->bindParam(":image", $data["image"]);
        }

        $update_news_stmt->execute();
    }

    $result = [
        "result"=>true,
        "list" => [
            "success_cnt" => $s_cnt,
            "fail_cnt" => $f_cnt,
            "total" => $cnt
        ]
    ];
} else {
    $result = [
        "result"=>false,
        "list" => []
    ];
}

echo json_encode($result);
