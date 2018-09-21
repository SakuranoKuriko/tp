<?php
include('mydb.php');
$s = $pdo->query("select value from cfg where name='soltstr' limit 1");
$soltstr = $s->fetch(PDO::FETCH_ASSOC)["value"];
$s = $pdo->query("select value from cfg where name='xorkey' limit 1");
$xorkey = $s->fetch(PDO::FETCH_ASSOC)["value"];
$s = $pdo->query("select value from cfg where name='cookietime' limit 1");
$cookietime = (int) $s->fetch(PDO::FETCH_ASSOC)["value"];
var_dump($soltstr);
var_dump($xorkey);
function xorstr($str, $key){
    $l = strlen($str);
    $r = $str;
    for ($i=0;$i<$l;$i++){
        $r[$i] = $r[$i]^$key;
    }
    return $r;
}
function encodeKey($user){
    global $cookietime, $xorkey, $soltstr;
    $time = time();
    $time2 = $time+$cookietime*60;
    return xorstr(base64_encode("$time@$time2@$user#".md5("$time@$time2@$user@$soltstr")), $xorkey);
}
$e = encodeKey("Kuriko");
var_dump($e);
var_dump(xorstr($e, $xorkey));
?>