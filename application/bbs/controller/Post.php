<?php
namespace app\bbs\controller;

require_once('usr.php');
require_once('posts.php');

use \think\Request;

class Post extends \think\Controller
{
    public function _empty(){
        $postid = getpostid(Request::instance()->action());
        postchk($postid);
        $args = getargs();
        $page = 1;
        if (count($args)>0){
            $v = getnum($args[0]);
            if ($v!=0)
                $page = $v;
        }
        $postdata = getpost($postid);
        $childpost = getchildpost($postid, $page);
        $this->assign('postid', $postid);
        $this->assign('postdata', json_encode($postdata));
        $this->assign('childdata', json_encode($childpost));
        return $this->fetch('index');
    }
    //public function edit($id){
    public function edit(){
        $args = getargs();
        if (count($args)==0)
            return (string)\PostStatus::needpostid;
        $postid = getpostid($args[0]);
        $postdata = getpost($postid);
        $this->assign('postid', $postid);
        $this->assign('postdata', json_encode($postdata));
        return $this->fetch('edit');
    }
    public function new(){
        authchk();
        $ti = $_POST['title'];
        if ($ti=="")
            return (string)\PostStatus::needtitle;
        $text = $_POST['text'];
        return (string)newpost($ti, $text);
    }
    public function editit(){
        authchk();
        $ti = $_POST['title'];
        $text = $_POST['text'];
        if ($ti==""&&$text=="")
            return (string)\PostStatus::nochange;
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
            return (string)\PostStatus::needtext;
        postchk($postid);
        return (string)reppost($postid, $text);
    }
    public function editrep(){
        authchk();
        $postid = getpostid($_POST['postid']);
        $text = $_POST['text'];
        if ($text=="")
            return (string)\PostStatus::needtext;
        isown($postid);
        return (string)editpost($postid, getcfg('reptitle'), $text);
    }
}
