<?php
require_once('db.php');
abstract class Regexp{
    const email = "/^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,6}$/";
    const usrid = "/^[a-zA-Z][a-zA-Z0-9_]+$/";
    const getid = "/([a-zA-Z][a-zA-Z0-9_]+)/";
    const getargs = "/^.*?\/.*?\/(.*)$/";
    const getcargs = "/^.*?\/.*?\/.*?\/(.*)$/";
    const numonly = "/^\d+$/";
    const getnum = "/(\d+)/";
}
abstract class Status{
    const success = 0;
    const normal = 0;
    const error = 1;
}
abstract class UserStatus{
    const success = Status::success;
    const failed = Status::error;
    const needlogin = 0x11;
    const usererror = 0x12;
    const iderror = 0x13;
    const needid = 0x14;
    const notfound = 0x16;
}
abstract class RegStatus{
    const success = Status::success;
    const failed = Status::error;
    const argerr = 0x21;
    const idused = 0x22;
    const emailerr = 0x23;
}
abstract class UserGroup{
    const normal = 0;
    const banned = 1;
    const guest = 2;
}
abstract class Permission{
    const read = 0b1;
    const rep = 0b10;
    const newpost = 0b100;
    const admin = 0b1000;
}
abstract class PostStatus{
    const success = Status::success;
    const nochange = -1;
    const error = Status::error;
    const noown = 0x31;
    const needpostid = 0x32;
    const needmaster = 0x33;
    const needtitle = 0x34;
    const needtext = 0x35;
    const notfound = 0x36;
    const iderror = 0x37;
}
function getnum($str){
    preg_match(Regexp::getnum, $str, $v);
    return count($v)>0?(int)$v[0]:0;
}
function getcfg($name){
    global $pdo;
    $s = $pdo->query("select value from cfg where name='$name' limit 1");
    return $s->fetch(PDO::FETCH_ASSOC)['value'];
}
function getargs($pathinfo = ""){
    $p = $pathinfo;
    if ($p=="")
        $p = \think\Request::instance()->pathinfo();
    preg_match(Regexp::getargs, $p, $v);
    $args = array();
    if (count($v)!=0&&$v[1]!="index.php")
        $args = explode('/', $v[1]);
    return $args;
}
?>
