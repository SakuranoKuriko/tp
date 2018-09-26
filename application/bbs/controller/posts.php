<?php
include('vars.php');
function getpostid($id){
    if ($id==""){
        echo PostStatus::needpostid;
        die();
    }
    preg_match(Regexp::getnum, $id, $vals);
    return (int)$vals[1];
}
function getpost($postid){
    global $pdo;
    $s = $pdo->query("select * from posts where id=$postid limit 1");
    $postdata = $s->fetch(PDO::FETCH_ASSOC)[0];
    if ($s->rowCount()!=1){
        echo PostStatus::error;
        die();
    }
    $s = $pdo->query("select 1 from posts where masterid=$postid");
    $postdata['childcount'] = $s->rowCount();
    return $postdata;
}
function getchildpost($postid, $page){
    global $pdo;
    $cc = (int)getcfg('childperpage');
    $skip = --$page*$cc;
    $s = $pdo->query("select * from posts where masterid=$postid limit $skip, $cc");
    return $s->fetchAll(PDO::FETCH_ASSOC);
}
function newpost($title, $content){
    global $pdo;
    $s = $pdo->prepare("insert into posts (own, title, content) values (?, ?, ?)");
    $s->execute(array($GLOBALS['userid'], $title, $content));
    if ($s->rowCount()!=1)
        return PostStatus::error;
    return PostStatus::success;
}
function reppost($postid, $content){
    global $pdo;
    $s = $pdo->prepare("insert into posts (own, title, content, masterid) values (?, ?, ?, $postid)");
    $s->execute(array($GLOBALS['userid']), getcfg('reptitle'), $content);
    if ($s->rowCount()!=1)
        return PostStatus::error;
    return PostStatus::success;
}
function isown($postid){
    global $pdo;
    $s = $pdo->prepare("select own from posts where id=$postid and own=? limit 1");
    $s->execute(array($GLOBALS['userid']));
    if ($s->rowCount()!=1){
        echo PostStatus::noown;
        die();
    }
}
function postchk($postid){
    global $pdo;
    $s = $pdo->query("select 1 from posts where id=$postid limit 1");
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
        $s = $pdo->prepare("update posts set title=? where id=$postid");
        $s->execute(array($title));
        if ($s->rowCount()!=1)
            return PostStatus::error;
        $status++;
    }
    if ($content!=""&&$postdata['content']!=$content){
        $s = $pdo->prepare("update posts set content=? where id=$postid");
        $s->execute(array($content));
        if ($s->rowCount()!=1)
            return PostStatus::error;
        $status++;
    }
    if ($status==-1)
        return PostStatus::nochange;
    return PostStatus::success;
}
?>