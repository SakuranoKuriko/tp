<?php
include('db.php');
global $soltstr, $xorkey, $cookietime;
abstract class Regex{
    const email = "^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$";
}
abstract class LoginStatus{
    const success = 0;
    const failed = 1;
}
abstract class RegStatus{
    const success = 0;
    const failed = 1;
    const argerr = 2;
    const idused = 3;
    const emailerr = 4;
}
abstract class UserGroup{
    const normal = 0;
    const banned = 1;
}
abstract class Permission{
    const read = 0b1;
    const write = 0b10;
    const post = 0b100;
    const admin = 0b1000;
}
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
function idused($id){
    global $pdo;
    $s = $pdo->prepare("select * from usr where id=? limit 1");
    $s->execute(array($id));
    $c = $s->fetchAll(PDO::FETCH_ASSOC);
    if (count($c)==1)
        return true;
    else return false;
}
function adduser($id, $pw, $name, $email){
    $s = $pdo->prepare("insert into usr (id, pw, name, email) values (?, ?, ?, ?)");
    $s->execute(array($id, $pw, $name, $email));
}
function userchk($id, $pw){
    $s = $pdo->prepare("select * from usr where id=? and pw=? limit 1");
    $s->execute(array($id, $pw));
    if (count($s->fetchAll(PDO::FETCH_ASSOC))==0)
        return LoginStatus::failed;
    return LoginStatus::success;
}
?>