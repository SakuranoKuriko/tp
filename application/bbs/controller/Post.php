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
        $masterid = getmasterid($postid);
        $target = 0;
        if ($masterid!=$postid){
            $target = $postid;
            $postid = $msaterid;
        }
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
        $this->assign('targetid', $target);
        $this->assign('page', $page);
        $this->assign('childperpage', getcfg('childperpage'));
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
        $this->assign('postid', $postid);
        $this->assign('postdata', json_encode(getpost($postid)));
        return $this->fetch('edit');
    }
    public function create(){
        authchk();
        $ti = getpostarg('title', false);
        $text = getpostarg('text');
        if ($GLOBALS['permission']&\Permission::newpost)
            return (string)newpost($ti, $text);
        else return (string)\PostStatus::needpermission;
    }
    public function editit(){
        authchk();
        $ti = getpostarg('title');
        $text = getpostarg('text');
        if ($ti==""&&$text=="")
            return (string)\PostStatus::nochange;
        $postid = getpostid(getpostarg('postid', false));
        if ($GLOBALS['permission']&\Permission::admin||$GLOBALS['usrgroup']==\UserGroup::admin||isown($postid))
            return (string)editpost($postid, $ti, $text);
        else return (string)\PostStatus::needpermission;
    }
    public function del(){
        authchk();
        $postid = getpostid(getpostarg('postid', false));
        if ($GLOBALS['permission']&\Permission::admin||$GLOBALS['usrgroup']==\UserGroup::admin||isown($postid))
            return (string)delpost($postid);
        else return (string)\PostStatus::needpermission;
    }
    public function rep(){
        authchk();
        $postid = getpostid(getpostarg('postid', false));
        $text = getpostarg('text', false);
        postchk($postid);
        if ($GLOBALS['permission']&\Permission::rep)
            return (string)reppost($postid, $text);
        else return (string)\PostStatus::needpermission;
    }
    public function editrep(){
        authchk();
        $postid = getpostid(getpostarg('postid', false));
        $text = getpostarg('text', false);
        if ($GLOBALS['permission']&\Permission::admin||$GLOBALS['usrgroup']==\UserGroup::admin||isown($postid))
            return (string)editpost($postid, getcfg('reptitle'), $text);
        else return (string)\PostStatus::needpermission;
    }
}
