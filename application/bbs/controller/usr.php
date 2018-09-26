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
    $soltstr = getcfg('soltstr');
    $xorkey = getcfg('xorkey');
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
    $s = $pdo->prepare("select 1 from usr where usrid=? limit 1");
    $s->execute(array($id));
    if ($s->rowCount()==0)
        return false;
    return true;
}
function adduser($id, $pw, $name, $email){
    $s = $pdo->prepare("insert into usr (usrid, pw, name, email) values (?, ?, ?, ?)");
    $s->execute(array($id, $pw, $name, $email));
    return RegStatus::success;
}
function userchk($id, $pw){
    $s = $pdo->prepare("select id from usr where usrid=? and pw=? limit 1");
    $s->execute(array($id, $pw));
    if ($s->rowCount()==0)
        return UserStatus::failed;
    return UserStatus::success;
}
function authchk(){
    $own = $_COOKIE['authkey'];
    if ($own==""){
        echo UserStatus::needlogin;
        die();
    }
    $ownd = decodeKey($own);
    if (!$ownd->vaild){
        setcookie('authkey');
        echo UserStatus::usererror;
        die();
    }
    $s = $pdo->prepare("select id from usr where usrid=? limit 1");
    $s->execute(array($own->id));
    if ($s->rowCount()==0){
        setcookie('authkey');
        echo UserStatus::notfound;
        die();
    }
    $GLOBALS['useruid'] = $s->fetch(PDO::FETCH_ASSOC);
    $GLOBALS['userid'] = $ownd->id;
}
function getusr($id){
    global $pdo;
    $query = "id,usrid,permission,usrgroup,name,homepage,steam,github";
    preg_match(Regexp::numonly, $id, $idn);
    if (count($idn)>0){
        $u = $idn[0];
        $s = $pdo->query("select $query from usr where id='$u' limit 1");
        return $s->fetch(PDO::FETCH_ASSOC);
    }
    else{
        preg_match(Regexp::usrid, $id, $idv);
        if (count($idv)>=0){
            $u = $idv[0];
            $s = $pdo->query("select $query from usr where usrid='$u' limit 1");
            return $s->fetch(PDO::FETCH_ASSOC);
        }
    }
    echo UserStatus::iderror;
    die();
}
function getid($userid, $isuid = true){
    global $pdo;
    $query = "id";
    $from = "usrid";
    if ($isuid){
        $query = "usrid";
        $from = "id";
        preg_match(\Regexp::getnum, $userid, $idn);
    }
    else{
        preg_match(\Regexp::getid, $userid, $idn);
    }
    if (count($idn)!=0){
        $usrid = $idn[0];
        $s = $pdo->query("select id,usrid from usr where $from='$usrid'");
        return $s->fetch(PDO::FETCH_ASSOC)[$query];
    }
    die();
}
?>