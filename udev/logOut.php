<?
@extract($_SESSION);
@extract($_COOKIE);

session_unset(); // 모든 세션변수를 언레지스터 시켜줌
session_destroy(); // 세션해제함

setcookie("udev[id]", "", time()-3600, "/");
setcookie("udev[msid]", "", time()-3600, "/");
setcookie("udev[pw]", "", time()-3600, "/");
setcookie("udev[lv]", "", time()-3600, "/");

header("Location:/udev");
?>