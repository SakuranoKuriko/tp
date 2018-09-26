<?php
namespace app\bbs\controller;

require_once('usr.php');
require_once('posts.php');
require_once('etc.php');

class Index extends \think\Controller
{
    public function index()
    {
        $sliders = json_encode(getsliders());
        $this->assign('sliders', $sliders);
        $postlst = getlist();
        $this->assign('postlist', json_encode($postlst));
        return $this->fetch('index');
    }
}
