<?php
namespace app\bbs\controller;

include('usr.php');

class User extends \think\Controller
{
    public function login(){

    }
    public function logout(){

    }
    public function reg($id, $pw){
        echo "$id<br/>$pw";
    }
    public function idused($id){
        return chkid($id)?'1':'0';
    }
}
