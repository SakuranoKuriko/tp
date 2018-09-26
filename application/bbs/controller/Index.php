<?php
namespace app\bbs\controller;

require_once('usr.php');
require_once('posts.php');

class Index extends \think\Controller
{
    public function index()
    {
        $this->assign('silderimg', '["/img/1.jpg","/img/1.jpg"]');
        $this->assign('slidertarget', '["","/p1"]');
        $postlst = getlist();
        $this->assign('postlist', json_encode($postlst));
        return $this->fetch('index');
    }
}
