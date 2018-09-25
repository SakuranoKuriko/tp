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
        authchk();
        $ti = $_POST['title'];
        if ($ti=="")
            return (string)PostStatus::needtitle;
        $text = $_POST['text'];
        return (string)newpost($ti, $text);
    }
    public function edit(){
        authchk();
        $ti = $_POST['title'];
        $text = $_POST['text'];
        if ($ti==""&&$text=="")
            return (string)PostStatus::nochange;
        $postid = getpostid();
        isown($postid);
        return (string)editpost($postid, $ti, $text);
    }
    public function del(){
        authchk();
        $postid = getpostid();
        isown($postid);
        return (string)delpost($postid);
    }
    public function rep(){
        authchk();
        $postid = getpostid();
        $text = $_POST['text'];
        if ($text=="")
            return (string)PostStatus::needtext;
        postchk($postid);
        return (string)reppost($postid, $text);
    }
    public function editrep(){
        authchk();
        $postid = getpostid();
        $text = $_POST['text'];
        if ($text=="")
            return (string)PostStatus::needtext;
        isown($postid);
        return (string)editpost($postid, getcfg('reptitle'), $text);
    }
}
