<?php
namespace app\bbs\controller;

require_once('usr.php');
require_once('posts.php');
require_once('etc.php');

class Index extends \think\Controller
{
    public function index()
    {
        $args = getargs();
        if (count($args)>0&&$args[0]=="json")
            return json_encode(getlist());
        $this->assign('sliders', json_encode(getsliders()));
        $this->assign('headimg', getcfg('headimg'));
        $this->assign('needslider', getcfg('needslider'));
        $this->assign('postlist', json_encode(getlist()));
        return $this->fetch('index');
    }
}
