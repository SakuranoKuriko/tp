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
    return $s->rowCount()!=0;
}
function userexist($uid){
    global $pdo;
    $s = $pdo->prepare("select 1 from usr where id=? limit 1");
    $s->execute(array($uid));
    return $s->rowCount()!=0;
}
function updateuser($id, $pw, $usrid, $email, $permission, $usrgroup, $name, $hp, $github, $steam){
    global $pdo;
    $query = "update usr set ";
    $args = array();
    if ($pw!=""){
        $query .= "pw=?,";
        array_push($args, $pw);
    }
    if ($usrid!=""){
        $query .= "usrid=?,";
        array_push($args, $usrid);
    }
    if ($email!=""){
        $query .= "email=?,";
        array_push($args, $email);
    }
    if ($permission!=""){
        $query .= "permission=?,";
        array_push($args, $permission);
    }
    if ($usrgroup!=""){
        $query .= "usrgroup=?,";
        array_push($args, $usrgroup);
    }
    if ($name!=""){
        $query .= "name=?,";
        array_push($args, $name);
    }
    if ($hp!=""){
        $query .= "homepage=?,";
        array_push($args, $hp);
    }
    if ($github!=""){
        $query .= "github=?,";
        array_push($args, $github);
    }
    if ($steam!=""){
        $query .= "steam=?,";
        array_push($args, $steam);
    }
    if (count($args)==0)
        return UserStatus::nochange;
    $query = substr($query, 0, -1) . " where id=?";
    array_push($args, $id);
    $s = $pdo->prepare($query);
    $s->execute($args);
    if ($s->rowCount()==1)
        return UserStatus::success;
    else return UserStatus::failed;
}
function adduser($id, $pw, $name, $email){
    global $pdo;
    $s = $pdo->prepare("insert into usr (usrid, pw, permission, usrgroup, name, email) values (?, ?, ?, ?, ?, ?)");
    $s->execute(array($id, $pw, Permission::read|Permission::rep|Permission::newpost, UserGroup::guest, $name, $email));
    if ($s->rowCount()==1)
        return UserStatus::success;
    else return UserStatus::failed;
}
function userchk($id, $pw){
    global $pdo;
    $s = $pdo->prepare("select id from usr where usrid=? and pw=? limit 1");
    $s->execute(array($id, $pw));
    if ($s->rowCount()==1)
        return UserStatus::success;
    else return UserStatus::failed;
}
function authchk(){
    global $pdo;
    $own = $_COOKIE['authkey'];
    if ($own==""){
        echo UserStatus::needlogin;
        die();
    }
    $ownd = decodeKey($own);
    if (!$ownd->valid){
        setcookie('authkey','',time()-3600,'/');
        setcookie('id','',time()-3600,'/');
        echo UserStatus::usererror;
        die();
    }
    $s = $pdo->prepare("select id from usr where usrid=? limit 1");
    $s->execute(array($ownd->id));
    if ($s->rowCount()==0){
        setcookie('authkey','',time()-3600,'/');
        setcookie('id','',time()-3600,'/');
        echo UserStatus::notfound;
        die();
    }
    $GLOBALS['userid'] = $ownd->id;
    $GLOBALS['useruid'] = $s->fetch(PDO::FETCH_ASSOC)['id'];
    $s = $pdo->prepare("select permission,usrgroup from usr where id=? limit 1");
    $s->execute(array($GLOBALS['useruid']));
    $res = $s->fetch(PDO::FETCH_ASSOC);
    $GLOBALS['permission'] = (int)$res['permission'];
    $GLOBALS['usrgroup'] = (int)$res['usrgroup'];
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
        preg_match(Regexp::getnum, $userid, $idn);
    }
    else{
        preg_match(Regexp::getid, $userid, $idn);
    }
    if (count($idn)!=0){
        $usrid = $idn[0];
        $s = $pdo->query("select id,usrid from usr where $from='$usrid'");
        return $s->fetch(PDO::FETCH_ASSOC)[$query];
    }
    echo UserStatus::iderror;
    die();
}
?>
