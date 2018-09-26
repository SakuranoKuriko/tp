<?php
require_once('vars.php');
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
    $soltstr = getcfg('soltstr');
    $xorkey = getcfg('xorkey');
    $cookietime = (int)getcfg('cookietime');
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
    $k->id = $user;
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
    return RegStatus::success;
}
function userchk($id, $pw){
    $s = $pdo->prepare("select * from usr where id=? and pw=? limit 1");
    $s->execute(array($id, $pw));
    if (count($s->fetchAll(PDO::FETCH_ASSOC))==0)
        return LoginStatus::failed;
    return LoginStatus::success;
}
function authchk(){
    $own = $_COOKIE['authkey'];
    if ($own==""){
        echo LoginStatus::needlogin;
        die();
    }
    $ownd = decodeKey($own);
    if (!$ownd->vaild){
        setcookie('authkey');
        echo LoginStatus::usererror;
        die();
    }
    $GLOBALS['userid'] = $ownd->id;
}
?>