<?php
include_once('db.php');
abstract class Regexp{
    const email = "^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$";
    const getargs = "/^.*?\/.*?\/(.*)$/i";
    const noarg = "/^.*?\/.*?\/index\.php/i";
    const numonly = "^\d+$";
    const getnum = "(\d+)";
}
abstract class Status{
    const success = 0;
    const normal = 0;
    const error = 1;
}
abstract class LoginStatus{
    const success = Status::success;
    const failed = Status::error;
    const needlogin = 0x11;
    const usererror = 0x12;
}
abstract class RegStatus{
    const success = Status::success;
    const failed = Status::error;
    const argerr = 0x21;
    const idused = 0x22;
    const emailerr = 0x23;
}
abstract class UserGroup{
    const normal = 0xf0;
    const banned = 0xf1;
}
abstract class Permission{
    const read = 0b1;
    const write = 0b10;
    const post = 0b100;
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
function getcfg($name){
    return $GLOBALS['pdo']->query("select value from cfg where name=$name limit 1")->fetch(PDO::FETCH_ASSOC)[0]['value'];
}
?>