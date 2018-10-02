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
    const nochange = -1;
    const success = 0;
    const normal = 0;
    const error = 1;
    const needargs = 2;
}
abstract class UserStatus{
    const success = Status::success;
    const nochange = Status::nochange;
    const failed = Status::error;
    const needlogin = 0x11;
    const usererror = 0x12;
    const iderror = 0x13;
    const needid = 0x14;
    const notfound = 0x16;
    const needpermission = 0x17;
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
    const normalstr = '正常';
    const banned = 1;
    const bannedstr = '封禁';
    const guest = 2;
    const gueststr = '游客';
    const admin = 3;
    const adminstr = '管理员';
}
abstract class Permission{
    const read = 0b1;
    const readstr = '查看';
    const rep = 0b10;
    const repstr = '回复';
    const newpost = 0b100;
    const newpoststr = '发帖';
    const admin = 0b1000;
    const adminstr = '管理员';
}
abstract class PostStatus{
    const success = Status::success;
    const nochange = Status::nochange;
    const error = Status::error;
    const noown = 0x31;
    const needpostid = 0x32;
    const needmaster = 0x33;
    const needtitle = 0x34;
    const needtext = 0x35;
    const notfound = 0x36;
    const needpermission = 0x37;
    const iderror = 0x38;
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
function getpostarg($key, $canempty = true){
    if (isset($_POST[$key]))
        return $_POST[$key];
    else if ($canempty)
        return "";
    echo Status::needargs;
    die();
}
function getip()
{
 $ip=false;
 if(!empty($_SERVER["HTTP_CLIENT_IP"])){
  $ip = $_SERVER["HTTP_CLIENT_IP"];
 }
 if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
  if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
  for ($i = 0; $i < count($ips); $i++) {
   if (!eregi ("^(10│172.16│192.168).", $ips[$i])) {
    $ip = $ips[$i];
    break;
   }
  }
 }
 return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}
function getpos(){
    return get_position(getip());
}
function getposstr($pos){
    $p = $pos;
    if (is_string($p))
        $p = json_decode($p);
    if (strtolower($p['data']['isp_id'])=="local")
        return "内网";
    $ps = "";
    if (strtolower($p['data']['country'])!="xx")
        $ps .= $p['data']['country'];
    if (strtolower($p['data']['region'])!="xx")
        $ps .= $p['data']['region'];
    if (strtolower($p['data']['city'])!="xx")
        $ps .= $p['data']['city'];
    if ($ps!="中国"&&substr($ps, 0, 2)=="中国")
        $ps = substr($ps, 2);
    return $ps;
}
/**
 * 根据用户IP获取用户地理位置
 * $ip  用户ip
 */
function get_position($ip){
    if(empty($ip)){
        return  '缺少用户ip';
    }
    $url = 'http://ip.taobao.com/service/getIpInfo.php?ip='.$ip;
    $ipContent = file_get_contents($url);
    $ipContent = json_decode($ipContent,true); 
    return $ipContent;
}
?>
