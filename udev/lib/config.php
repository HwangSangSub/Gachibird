<?
define('PATH', $_SERVER["DOCUMENT_ROOT"]);
// define('PATH', $_SERVER["DOCUMENT_ROOT"].'/');
define('DATA_DIR', 'data');
define('DATA_PATH', PATH.'/'.DATA_DIR);

define('UDEV_DIR', '/udev');
define('UDEV_ADMIN', '/udev/admin');
define('FRD', '/frd');
define('COM', $_SERVER['DOCUMENT_ROOT'].'/lib');
define('UPATH', $_SERVER['DOCUMENT_ROOT'].'/'.UDEV_DIR);

define('EXTEND_DIR',  'extend');
define('SESSION_DIR', 'session');

//구글 fcm키
define("GOOGLE_API_KEY", "AAAAQ5PRua4:APA91bHIqpvIHy5sm_Av5GYw1o3qO3gZxorKjfHnbXN_G17YiEf_qnaH-5n34dsbUJ1YmqBNjAaGAAY6hrJ4VmL2ntidTTMF_FXOYh_xcH4X-od_bdHVmj5iyqmAeYnLXqprP_FWA1mD");
define("GOOGLE_OAUTH_TOKEN", "114108121020516755401");


define('EXTEND_PATH', PATH.'/'.EXTEND_DIR);
define('SESSION_PATH', PATH.'/'.DATA_DIR.'/'.SESSION_DIR);


define('DOMAIN', '');

if (DOMAIN) {
    define('URL', DOMAIN);
} else {
    if (isset($path['url']))
        define('URL', $path['url']);
    else
        define('URL', '');
}

/*
www.aa.com 과 aa.com 도메인은 서로 다른 도메인으로 인식합니다. 쿠키를 공유하려면 .aa.com 과 같이 입력하세요.
이곳에 입력이 없다면 www 붙은 도메인과 그렇지 않은 도메인은 쿠키를 공유하지 않으므로 로그인이 풀릴 수 있습니다.
*/

define('COOKIE_DOMAIN',  '');

/********************
    시간 상수
********************/
// 서버의 시간과 실제 사용하는 시간이 틀린 경우 수정하세요.
// 하루는 86400 초입니다. 1시간은 3600초
// 6시간이 빠른 경우 time() + (3600 * 6);
// 6시간이 느린 경우 time() - (3600 * 6);
define('SERVER_TIME',    time());
define('TIME_YMDHIS',    date('Y-m-d H:i:s', SERVER_TIME));
define('TIME_YMD',       substr(TIME_YMDHIS, 0, 10));
define('TIME_HIS',       substr(TIME_YMDHIS, 11, 8));

// 퍼미션
define('DIR_PERMISSION',  0755); // 디렉토리 생성시 퍼미션
define('FILE_PERMISSION', 0775); // 파일 생성시 퍼미션

// 모바일 인지 결정 $_SERVER['HTTP_USER_AGENT']
define('MOBILE_AGENT',   'phone|samsung|lgtel|mobile|[^A]skt|nokia|blackberry|BB10|android|sony');

// 암호화 함수 지정
// 사이트 운영 중 설정을 변경하면 로그인이 안되는 등의 문제가 발생합니다.
define('STRING_ENCRYPT_FUNCTION', 'sql_password');

// escape string 처리 함수 지정
// addslashes 로 변경 가능
define('ESCAPE_FUNCTION', 'sql_escape_string');

// sql_escape_string 함수에서 사용될 패턴
//define('ESCAPE_PATTERN',  '/(and|or).*(union|select|insert|update|delete|from|where|limit|create|drop).*/i');
//define('ESCAPE_REPLACE',  '');

// 썸네일 jpg Quality 설정
define('THUMB_JPG_QUALITY', 90);

// 썸네일 png Compress 설정
define('THUMB_PNG_COMPRESS', 5);

// 모바일 기기에서 DHTML 에디터 사용여부를 설정합니다.
define('IS_MOBILE_DHTML_USE', false);

// MySQLi 사용여부를 설정합니다.
define('MYSQLI_USE', true);

// Browscap 사용여부를 설정합니다.
define('BROWSCAP_USE', true);

// 접속자 기록 때 Browscap 사용여부를 설정합니다.
define('VISIT_BROWSCAP_USE', false);

// ip 숨김방법 설정
/* 123.456.789.012 ip의 숨김 방법을 변경하는 방법은
\\1 은 123, \\2는 456, \\3은 789, \\4는 012에 각각 대응되므로
표시되는 부분은 \\1 과 같이 사용하시면 되고 숨길 부분은 ♡등의
다른 문자를 적어주시면 됩니다.
*/
define('IP_DISPLAY', '\\1.♡.\\3.\\4');

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') {   //https 통신일때 daum 주소 js
    define('POSTCODE_JS', '<script src="https://spi.maps.daum.net/imap/map_js_init/postcode.v2.js"></script>');
} else {  //http 통신일때 daum 주소 js
    define('POSTCODE_JS', '<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>');
}





?>