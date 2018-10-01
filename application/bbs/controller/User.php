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
        $id = $_POST['id'];
        $pw = $_POST['pw'];
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
        if (isset($_POST['id']))
            $id = $_POST['id'];
        else return (string)\UserStatus::needid;
        $usrid = "";
        if (isset($_POST['usrid']))
            $usrid = $_POST['usrid'];
        $name = "";
        if (isset($_POST['name']))
            $name = $_POST['name'];
        $permission = "";
        if (isset($_POST['permission']))
            $permission = $_POST['permission'];
        $usrgroup = "";
        if (isset($_POST['usrgroup']))
            $usrgroup = $_POST['usrgroup'];
        $hp = "";
        if (isset($_POST['hp']))
            $hp = $_POST['hp'];
        $github = "";
        if (isset($_POST['github']))
            $github = $_POST['github'];
        $steam = "";
        if (isset($_POST['steam']))
            $steam = $_POST['steam'];
        if (!userexist($id))
            return (string)\UserStatus::notfound;
        return updateuser($id, $usrid, $permission, $usrgroup, $name, $hp, $github, $steam);
    }
    /*public function logout(){

    }*/
    public function reg(){
        $id = $_POST['id'];
        $pw = $_POST['pw'];
        $name = $_POST['name'];
        $email = $_POST['email'];
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
