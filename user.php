<?php
include('mydb.php');
global $soltstr, $xorkey, $cookietime;
$s = $pdo->query("select value from cfg where name='soltstr' limit 1");
$soltstr = $s->fetch(PDO::FETCH_ASSOC)["value"];
$s = $pdo->query("select value from cfg where name='xorkey' limit 1");
$xorkey = $s->fetch(PDO::FETCH_ASSOC)["value"];
$s = $pdo->query("select value from cfg where name='cookietime' limit 1");
$cookietime = (int) $s->fetch(PDO::FETCH_ASSOC)["value"];
function xorstr($str, $key){
    $l = strlen($str);
    $xl = strlen($key);
    $r = $str;
    for ($i=0;$i<$l;$i++){
        $r[$i] = $r[$i]^$key[$i%$xl];
    }
    return $r;
}
function encodeKey($user){
    global $cookietime, $xorkey, $soltstr;
    echo "$cookietime, $xorkey, $soltstr";
    $time = time();
    $time2 = $time+$cookietime*60;
    return base64_encode(xorstr(base64_encode("$time@$time2@$user#".md5("$time@$time2@$user@$soltstr")), $xorkey));
}
function decodeKey($key){
    global $xorkey;
    $ks = base64_decode(xorstr(base64_decode($key), $xorkey));
    
}
$e = encodeKey("Kuriko");
xorstr(base64_decode($e), $xorkey);
?>