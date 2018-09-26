<?php
namespace app\bbs\controller;

include_once('usr.php');
include_once('posts.php');

class Index extends \think\Controller
{
    public function index()
    {
        $this->assign('silderimg', '["/img/1.jpg","/img/1.jpg"]');
        $this->assign('slidertarget', '["","/p1"]');
        $postlst = array();
        $p = json_decode('[{"id": "f0", "ti": "title p0", "url": "/link0"},{"id": "f1", "ti": "title p1", "url": "/link1"},{"id": "f2", "ti": "title p2", "url": "/link2"}]', true);
        foreach($p as $value){
            array_push($postlst, $value);
        }
        $this->assign('postlist', json_encode($postlst));
        return $this->fetch('index');
    }
}
