<?php
namespace app\bbs\controller;

require_once('usr.php');
require_once('posts.php');

use \think\Request;

class Post extends \think\Controller
{
    public function _empty(){
        $req = Request::instance();
        $postid = getpostid($req->action());
        $pathinfo = $req->pathinfo();
        preg_match(\Regexp::getargs, $pathinfo, $v);
        $args = array();
        var_dump($args);
        var_dump($v);
        if ($v[1]!="index.php"){
            $args = explode('/', $v[1]);
        }
        var_dump($args);
        postchk($postid);
        $postdata = getpost($postid);
        $childpost = getchildpost($postdata, $page);
        $this->assign('postid', $postid);
        $this->assign('title', $title);
        return $this->fetch('index');
    }
    public function edit($id){
        $postid = getpostid($id);
        $this->assign('postid', $postid);
        return $this->fetch('edit');
    }
    public function new(){
        authchk();
        $ti = $_POST['title'];
        if ($ti=="")
            return (string)PostStatus::needtitle;
        $text = $_POST['text'];
        return (string)newpost($ti, $text);
    }
    public function editit(){
        authchk();
        $ti = $_POST['title'];
        $text = $_POST['text'];
        if ($ti==""&&$text=="")
            return (string)PostStatus::nochange;
        $postid = getpostid($_POST['postid']);
        isown($postid);
        return (string)editpost($postid, $ti, $text);
    }
    public function del(){
        authchk();
        $postid = getpostid($_POST['postid']);
        isown($postid);
        return (string)delpost($postid);
    }
    public function rep(){
        authchk();
        $postid = getpostid($_POST['postid']);
        $text = $_POST['text'];
        if ($text=="")
            return (string)PostStatus::needtext;
        postchk($postid);
        return (string)reppost($postid, $text);
    }
    public function editrep(){
        authchk();
        $postid = getpostid($_POST['postid']);
        $text = $_POST['text'];
        if ($text=="")
            return (string)PostStatus::needtext;
        isown($postid);
        return (string)editpost($postid, getcfg('reptitle'), $text);
    }
}
