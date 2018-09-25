<?php
include('db.php');
include('vars.php');
function newpost($own, $title, $content){
    global $pdo;
    $s = $pdo->prepare("insert into posts (own, title, content) values (?, ?, ?)");
    $s->execute(array($own, $title, $content));
    if ($s->rowCount()!=1)
        return PostStatus::error;
    return PostStatus::success;
}
function isown($postid){
    global $pdo;
    $s = $pdo->prepare("select own from posts where id=? and own=? limit 1");
    $s->execute(array($postid, $GLOBALS['userid']));
    if (count($s->fetch(PDO::FETCH_ASSOC))==0){
        echo PostStatus::noown;
        die();
    }
}
?>