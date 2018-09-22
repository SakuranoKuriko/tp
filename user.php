<?php
include('mydb.php');
global $soltstr, $xorkey, $cookietime;
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
    global $pdo;
    $s = $pdo->query("select value from cfg where name='soltstr' limit 1");
    $soltstr = $s->fetch(PDO::FETCH_ASSOC)["value"];
    $s = $pdo->query("select value from cfg where name='xorkey' limit 1");
    $xorkey = $s->fetch(PDO::FETCH_ASSOC)["value"];
    $s = $pdo->query("select value from cfg where name='cookietime' limit 1");
    $cookietime = (int) $s->fetch(PDO::FETCH_ASSOC)["value"];
    $time = time();
    $time2 = $time+$cookietime*60;
    return base64_encode(xorstr(base64_encode("$time@$time2@$user#".md5("$time@$time2@$user@$soltstr")), $xorkey));
}
function decodeKey($key){
    global $pdo;
    $s = $pdo->query("select value from cfg where name='soltstr' limit 1");
    $soltstr = $s->fetch(PDO::FETCH_ASSOC)["value"];
    $s = $pdo->query("select value from cfg where name='xorkey' limit 1");
    $xorkey = $s->fetch(PDO::FETCH_ASSOC)["value"];
    $ks = base64_decode(xorstr(base64_decode($key), $xorkey));
    $ks1 = explode("#", $ks);
    $k1 = explode("@", $ks1[0]);
    $k = new stdClass();
    $time = $k1[0];
    $time2 = $k1[1];
    $user = $k1[2];
    $k->createtime = $time;
    $k->exp = $time2;
    $k->user = $user;
    $k->md5 = $ks1[1];
    $k->vmd5 = md5("$time@$time2@$user@$soltstr");
    $k->valid = $k->md5 == $k->vmd5;
    return $k;
}
$e = encodeKey("Kuriko");
decodeKey($e);
?>