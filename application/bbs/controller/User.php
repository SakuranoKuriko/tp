<?php
namespace app\bbs\controller;

require_once('usr.php');

use \think\Request;

class User extends \think\Controller
{
    public function _empty(){
        $id = Request::instance()->action();
        $userdata = getusr($id);
        $this->assign('userdata', json_encode($userdata));
        return $this->fetch('index');
    }
    public function login(){
        $id = $_POST['id'];
        $pw = $_POST['pw'];
        if (userchk($id, $pw)==\UserStatus::success){
            $cookieexp = time()+3600*24*30;
            setcookie('authkey', encodeKey($id), $cookieexp);
            return (string)\UserStatus::success;
        }
        return (string)\UserStatus::failed;
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
        return (string)adduser($id, $pw, $name, $email);
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
