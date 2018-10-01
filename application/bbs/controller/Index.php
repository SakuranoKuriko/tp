<?php
namespace app\bbs\controller;

require_once('usr.php');
require_once('posts.php');
require_once('etc.php');

class Index extends \think\Controller
{
    public function index()
    {
        $this->assign('sliders', json_encode(getsliders()));
        $this->assign('headimg', getcfg('headimg'));
        $this->assign('needslider', getcfg('needslider'));
        $this->assign('postlist', json_encode(getlist()));
        return $this->fetch('index');
    }
}
