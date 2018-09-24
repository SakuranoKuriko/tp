<?php
namespace app\bbs\controller;

include('usr.php');

class User extends \think\Controller
{
    public function index(){
        return $this->fetch('index');
    }
    public function login(){
        $id = $_POST['id'];
        $pw = $_POST['pw'];
        $s = $pdo->prepare("select * from usr where id=? and pw=? limit 1");
        $s->execute(array($id, $pw));
        if (count($s->fetchAll(PDO::FETCH_ASSOC))==0)
            return (string)LoginStatus::failed;
        $cookieexp = time()+3600*24*30;
        setcookie('authkey', encodeKey($id), $cookieexp);
        return (string)LoginStatus::success;
    }
    public function logout(){

    }
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
        if ($email!=''&&!preg_match(Regex::email, $email))
            return (string)RegStatus::emailerr;
        adduser($id, $pw, $name, $email);
        return (string)RegStatus::success;
    }
    public function idused($id){
        return idused($id)?'1':'0';
    }
}
