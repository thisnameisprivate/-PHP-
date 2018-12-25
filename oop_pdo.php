<?php

$mysql_conf = array(
    'host'   => 'localhost',
    'pass'   => '',
    'dbname' => 'wechat',
    'user'   => 'root'
);
$pdo = new PDO("mysql:host=" . $mysql_conf['host'] . ";dbname=" . $mysql_conf['dbname'], $mysql_conf['user'], $mysql_conf['pass']);
$pdo->exec("set names 'utf8");
$sql = "select * from user where name = ?";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(1, 'joshua', PDO::PARAM_STR);
$result = $stmt->execute();
if ($result) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        var_dump($row);
    }
}
$pdo->close(); // close mysql response link.
// $pdo = null;