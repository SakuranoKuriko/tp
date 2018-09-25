<?php
namespace app\bbs\controller;

include('usr.php');
include('posts.php');

class Post extends \think\Controller
{
    public function index($id){
        
        $this->assign('title', $title);
    }
    public function new(){
        $auth = authchk();
        if ($auth != LoginStatus::success)
            return $auth;
        $ti = $_POST['title'];
        if ($ti=="")
            return (string)NewPostStatus::needtitle;
        $text = $_POST['text'];
        return (string)newpost($GLOBALS['userid'], $ti, $text);
    }
    public function edit(){
        $auth = authchk();
        if ($auth != LoginStatus::success)
            return $auth;
    }
    public function del(){
        $auth = authchk();
        if ($auth != LoginStatus::success)
            return $auth;
    }
    public function rep(){
        $auth = authchk();
        if ($auth != LoginStatus::success)
            return $auth;
    }
    public function editrep(){
        $auth = authchk();
        if ($auth != LoginStatus::success)
            return $auth;
    }
}
