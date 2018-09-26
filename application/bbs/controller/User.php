<?php
namespace app\bbs\controller;

require_once('usr.php');

use \think\Request;

class User extends \think\Controller
{
    public function _empty(){
        $id = Request::instance()->action();
        return $id;
        return $this->fetch('index');
    }
    public function login(){
        $id = $_POST['id'];
        $pw = $_POST['pw'];
        if (userchk($id, $pw)==LoginStatus::success){
            $cookieexp = time()+3600*24*30;
            setcookie('authkey', encodeKey($id), $cookieexp);
            return (string)LoginStatus::success;
        }
        return LoginStatus::failed;
    }
    /*public function logout(){

    }*/
    public function reg(){
        $id = $_POST['id'];
        $pw = $_POST['pw'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        if ($id==''||$pw=='')
            return (string)RegStatus::argerr;
        if ($name=='')
            $name = $id;
        if (idused($id))
            return (string)RegStatus::idused;
        if ($email!=''&&!preg_match(Regexp::email, $email))
            return (string)RegStatus::emailerr;
        return (string)adduser($id, $pw, $name, $email);
    }
    public function idused($id){
        return idused($id)?'1':'0';
    }
}
