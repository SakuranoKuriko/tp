<?php
namespace app\bbs\controller;

require_once('usr.php');

use \think\Request;

class User extends \think\Controller
{
    public function _empty(){
        $args = getargs();
        $id = Request::instance()->action();
        $userdata = getusr($id);
        if (count($args)>0&&$args[0]=="json")
            return json_encode($userdata);
        $this->assign('userdata', json_encode($userdata));
        return $this->fetch('index');
    }
    public function login(){
        $id = getpostarg('id', false);
        $pw = getpostarg('pw', false);
        if (userchk($id, $pw)==\UserStatus::success){
            $cookieexp = time()+3600*24*30;
            setcookie('authkey', encodeKey($id), $cookieexp, '/');
            setcookie('id', getid($id, false), $cookieexp, '/');
            return (string)\UserStatus::success;
        }
        return (string)\UserStatus::failed;
    }
    public function edit(){
        authchk();
        $id = getpostarg('id', false);
        $pw = getpostarg('pw');
        $usrid = getpostarg('usrid');
        $email = getpostarg('email');
        $name = getpostarg('name');
        $permission = "";
        $usrgroup = "";
        if ($GLOBALS['permission']&\Permission::admin&&$GLOBALS['usrgroup']==\UserGroup::admin){
            $permission = getpostarg('permission');
            $usrgroup = getpostarg('usrgroup');
        }
        $hp = getpostarg('hp');
        $github = getpostarg('github');
        $steam = getpostarg('steam');
        if (!userexist($id))
            return (string)\UserStatus::notfound;
        if ($GLOBALS['permission']&\Permission::admin||$GLOBALS['usrgroup']==\UserGroup::admin||$GLOBALS['useruid']==$_COOKIE['id'])
            return updateuser($id, $pw, $usrid, $email, $permission, $usrgroup, $name, $hp, $github, $steam);
        else return \UserStatus::needpermission;
    }
    /*public function logout(){

    }*/
    public function reg(){
        $id = getpostarg('id', false);
        $pw = getpostarg('pw', false);
        $name = getpostarg('name');
        $email = getpostarg('email');
        if ($id==''||$pw=='')
            return (string)\RegStatus::argerr;
        if ($name=='')
            $name = $id;
        if (idused($id))
            return (string)\RegStatus::idused;
        if ($email!=''&&!preg_match(\Regexp::email, $email))
            return (string)\RegStatus::emailerr;
        $addst = adduser($id, $pw, $name, $email);
        if ($addst==\RegStatus::success){
            $cookieexp = time()+3600*24*30;
            setcookie('authkey', encodeKey($id), $cookieexp, '/');
            setcookie('id', getid($id, false), $cookieexp, '/');
        }
        return (string)$addst;
    }
    //public function idused($id){
    public function idused(){
        $args = getargs();
        if (count($args)==0)
            return (string)\UserStatus::needid;
        return idused($args[0])?'1':'0';
    }
    //public function uid($id){
    public function uid(){
        $args = getargs();
        if (count($args)>0)
            return (string)getid($args[0], false);
        return "";
    }
    //public function id($uid){
    public function id(){
        $args = getargs();
        if (count($args)>0)
            return getid($args[0]);
        return "";
    }
}
