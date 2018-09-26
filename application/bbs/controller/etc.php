<?php
require_once('vars.php');
function getsliders(){
    global $pdo;
    $s = $pdo->query("select id,val1,val2 from etc where tag='slider' limit 10");
    return $s->fetchAll(PDO::FETCH_ASSOC);
}
?>