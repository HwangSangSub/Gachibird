<?

/*************************************************************************
 **
 **  일반 함수 모음
 **
 *************************************************************************/

// 마이크로 타임을 얻어 계산 형식으로 만듦
function get_microtime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

// 한페이지에 보여줄 행, 현재페이지, 총페이지수, URL
function get_paging($write_pages, $cur_page, $total_page, $url, $add = "")
{
    //$url = preg_replace('#&amp;page=[0-9]*(&amp;page=)$#', '$1', $url);
    $url = preg_replace('#&amp;page=[0-9]*#', '', $url) . '&amp;page=';

    $str = '';
    if ($cur_page > 1) {
        $str .= '<a href="' . $url . '1' . $add . '" class="pg_page pg_start">처음</a>' . PHP_EOL;
    }

    $start_page = (((int)(($cur_page - 1) / $write_pages)) * $write_pages) + 1;
    $end_page = $start_page + $write_pages - 1;

    if ($end_page >= $total_page) $end_page = $total_page;

    if ($start_page > 1) $str .= '<a href="' . $url . ($start_page - 1) . $add . '" class="pg_page pg_prev">이전</a>' . PHP_EOL;

    if ($total_page > 1) {
        for ($k = $start_page; $k <= $end_page; $k++) {
            if ($cur_page != $k)
                $str .= '<a href="' . $url . $k . $add . '" class="pg_page">' . $k . '<span class="sound_only">페이지</span></a>' . PHP_EOL;
            else
                $str .= '<span class="sound_only">열린</span><strong class="pg_current">' . $k . '</strong><span class="sound_only">페이지</span>' . PHP_EOL;
        }
    }

    if ($total_page > $end_page) $str .= '<a href="' . $url . ($end_page + 1) . $add . '" class="pg_page pg_next">다음</a>' . PHP_EOL;

    if ($cur_page < $total_page) {
        $str .= '<a href="' . $url . $total_page . $add . '" class="pg_page pg_end">맨끝</a>' . PHP_EOL;
    }

    if ($str)
        return "<nav class=\"pg_wrap\"><span class=\"pg\">{$str}</span></nav>";
    else
        return "";
}

// 페이징 코드의 <nav><span> 태그 다음에 코드를 삽입
function page_insertbefore($paging_html, $insert_html)
{
    if (!$paging_html)
        $paging_html = '<nav class="pg_wrap"><span class="pg"></span></nav>';

    return preg_replace("/^(<nav[^>]+><span[^>]+>)/", '$1' . $insert_html . PHP_EOL, $paging_html);
}

// 페이징 코드의 </span></nav> 태그 이전에 코드를 삽입
function page_insertafter($paging_html, $insert_html)
{
    if (!$paging_html)
        $paging_html = '<nav class="pg_wrap"><span class="pg"></span></nav>';

    if (preg_match("#" . PHP_EOL . "</span></nav>#", $paging_html))
        $php_eol = '';
    else
        $php_eol = PHP_EOL;

    return preg_replace("#(</span></nav>)$#", $php_eol . $insert_html . '$1', $paging_html);
}

// 변수 또는 배열의 이름과 값을 얻어냄. print_r() 함수의 변형
function print_r2($var)
{
    ob_start();
    print_r($var);
    $str = ob_get_contents();
    ob_end_clean();
    $str = str_replace(" ", "&nbsp;", $str);
    echo nl2br("<span style='font-family:Tahoma, 굴림; font-size:9pt;'>$str</span>");
}


// 메타태그를 이용한 URL 이동
// header("location:URL") 을 대체
function goto_url($url)
{
    $url = str_replace("&amp;", "&", $url);
    //echo "<script> location.replace('$url'); </script>";

    if (!headers_sent())
        header('Location: ' . $url);
    else {
        echo '<script>';
        echo 'location.replace("' . $url . '");';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url=' . $url . '" />';
        echo '</noscript>';
    }
    exit;
}

// 쿠키변수 생성
function set_cookie($cookie_name, $value, $expire)
{
    global $g5;

    setcookie(md5($cookie_name), base64_encode($value), SERVER_TIME + $expire, '/', COOKIE_DOMAIN);
}


// 쿠키변수값 얻음
function get_cookie($cookie_name)
{
    $cookie = md5($cookie_name);
    if (array_key_exists($cookie, $_COOKIE))
        return base64_decode($_COOKIE[$cookie]);
    else
        return "";
}

// url에 http:// 를 붙인다
function set_http($url)
{
    if (!trim($url)) return;

    if (!preg_match("/^(http|https|ftp|telnet|news|mms)\:\/\//i", $url))
        $url = "http://" . $url;

    return $url;
}


// 파일의 용량을 구한다.
//function get_filesize($file)
function get_filesize($size)
{
    //$size = @filesize(addslashes($file));
    if ($size >= 1048576) {
        $size = number_format($size / 1048576, 1) . "M";
    } else if ($size >= 1024) {
        $size = number_format($size / 1024, 1) . "K";
    } else {
        $size = number_format($size, 0) . "byte";
    }
    return $size;
}

// 폴더의 용량 ($dir는 / 없이 넘기세요)
function get_dirsize($dir)
{
    $size = 0;
    $d = dir($dir);
    while ($entry = $d->read()) {
        if ($entry != '.' && $entry != '..') {
            $size += filesize($dir . '/' . $entry);
        }
    }
    $d->close();
    return $size;
}

function is_mobile()
{
    return preg_match('/'.MOBILE_AGENT.'/i', $_SERVER['HTTP_USER_AGENT']);
}