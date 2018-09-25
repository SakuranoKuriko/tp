<?php
include('db.php');
include('vars.php');
function newpost($own, $title, $content){
    global $pdo;
    $s = $pdo->prepare("insert into posts (own, title, content) values (?, ?, ?)");
    $s->execute(array($own, $title, $content));
    if ($s->rowCount()!=1)
        return NewPostStatus::error;
    return NewPostStatus::success;
}
?>