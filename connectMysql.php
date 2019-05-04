<?php

$mysql_conf = [
    'user' => 'root',
    'host' => 'localhost',
    'dbname' => 'visit',
    'password' => ''
];


$pdo = new PDO("mysql:host=" . $mysql_conf['host'] . ";dbname=" . $mysql_conf['dbname'], $mysql_conf['user'], $mysql_conf['password']) or die(mysql_error());
$pdo->query("SET NAMES 'utf8'");
$sql = "select * from nkvisit where id <  ?";
$stmt = $pdo->prepare($sql);
$id = 20;
$stmt->bindValue(1, $id);
$stmt->execute();
print_r($stmt->fetchAll());