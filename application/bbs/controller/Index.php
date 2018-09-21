<?php
namespace app\bbs\controller;

class Index extends \think\Controller
{
    public function index()
    {
        $this->assign('silderimg', '["/img/1.jpg","/img/1.jpg"]');
        $this->assign('slidertarget', '["","/1"]');
        $postlst = array();
        $p = json_decode('[{id: "f0", ti: "title 0", url: "/link0"},{id: "f1", ti: "title 1", url: "/link1"},{id: "f2", ti: "title 2", url: "/link2"}]');
        $this->assign('postlist', '["a0","a1"]');
        return $this->fetch('index');
    }
}
