<?php
require_once('usr.php');
function getpostid($id){
    if ($id==""){
        echo PostStatus::needpostid;
        die();
    }
    return getnum($id);
}
function getpost($postid){
    global $pdo;
    $s = $pdo->query("select * from posts where id='$postid' limit 1");
    $postdata = $s->fetch(PDO::FETCH_ASSOC);
    if ($s->rowCount()!=1){
        echo PostStatus::error;
        die();
    }
    $s = $pdo->query("select 1 from posts where masterid='$postid'");
    $postdata['childcount'] = $s->rowCount();
    unset($postdata['pos']);
    return $postdata;
}
function getlist($page = 1){
    global $pdo;
    $cc = (int)getcfg('postperpage');
    $skip = ($page-1)*$cc;
    $s = $pdo->query("select id,own,title,createtime,updatetime,lastrep from posts where masterid is null order by updatetime desc limit $skip, $cc");
    return $s->fetchAll(PDO::FETCH_ASSOC);
}
function getchildpost($postid, $page = 1){
    global $pdo;
    $cc = (int)getcfg('childperpage');
    $skip = ($page-1)*$cc;
    $s = $pdo->query("select * from posts where masterid='$postid' limit $skip, $cc");
    $postdata = $s->fetchAll(PDO::FETCH_ASSOC);
    foreach($postdata as $key=>$val){
        unset($postdata[$key]['pos']);
    }
    return $postdata;
}
function getmasterid($postid){
    global $pdo;
    $s = $pdo->query("select masterid from posts where id='$postid' limit 1");
    $r = $s->fetch(PDO::FETCH_ASSOC)['masterid'];
    if ($s->rowCount()==1){
        if ($r!="") return (int)$r;
        else return $postid;
    }
    echo PostStatus::error;
    die();
}
function newpost($title, $content){
    global $pdo;
    $pos = getpos();
    $s = $pdo->prepare("insert into posts (own, title, content, pos, posstr) values (?, ?, ?, ?, ?)");
    $s->execute(array($GLOBALS['useruid'], $title, $content, json_encode($pos), getposstr($pos)));
    if ($s->rowCount()==1)
        return PostStatus::success;
    else return PostStatus::error;
}
function reppost($postid, $content){
    global $pdo;
    $pos = getpos();
    $s = $pdo->prepare("insert into posts (own, title, content, masterid, pos, posstr) values (?, ?, ?, '$postid', ?, ?)");
    $s->execute(array($GLOBALS['useruid'], getcfg('reptitle'), $content, json_encode($pos), getposstr($pos)));
    if ($s->rowCount()!=1)
        return PostStatus::error;
    $s = $pdo->prepare("update posts set lastrep=? where id='$postid'");
    $s->execute(array($GLOBALS['useruid']));
    return PostStatus::success;
}
function isown($postid){
    global $pdo;
    $s = $pdo->prepare("select own from posts where id='$postid' and own=? limit 1");
    $s->execute(array($GLOBALS['useruid']));
    if ($s->rowCount()==1)
        return true;
    return false;
}
function postchk($postid){
    global $pdo;
    $s = $pdo->query("select 1 from posts where id='$postid' limit 1");
    if ($s->rowCount()!=1){
        echo PostStatus::notfound;
        die();
    }
}
function editpost($postid, $title, $content){
    global $pdo;
    $postdata = getpost($postid);
    $status = -1;
    if ($title!=""&&$postdata['title']!=$title){
        $s = $pdo->prepare("update posts set title=? where id='$postid'");
        $s->execute(array($title));
        if ($s->rowCount()!=1)
            return PostStatus::error;
        $status++;
    }
    if ($content!=""&&$postdata['content']!=$content){
        $s = $pdo->prepare("update posts set content=? where id='$postid'");
        $s->execute(array($content));
        if ($s->rowCount()!=1)
            return PostStatus::error;
        $status++;
    }
    if ($status==-1)
        return PostStatus::nochange;
    return PostStatus::success;
}
function delpost($postid){
    global $pdo;
    if ($pdo->exec("delete from posts where id=$postid")!=1)
        return PostStatus::error;
    $pdo->exec("delete from posts where masterid=$postid");
    return PostStatus::success;
}
?>
