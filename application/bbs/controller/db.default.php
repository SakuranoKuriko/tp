<?php
$dbms='mysql';     //数据库类型
$host='127.0.0.1'; //数据库主机名
$dbName='tp';     //使用的数据库
$user='root';      //数据库连接用户名
$pass='';      //对应的密码
$dsn="$dbms:host=$host;dbname=$dbName";
global $pdo;
$pdo = new PDO($dsn, $user, $pass);
$pdo->exec("set names utf8");
header('Cache-Control:no-cache,must-revalidate');
header("Expires:0");
header("Pragma:no-cache");
?>
