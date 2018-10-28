<?php

$id = 2;
$user = 'root';
$pass = '';
$host = 'localhost';
$dbname = 'visit';
$dsn = "mysql:host=$host;dbname=$dbname";
try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->query("SET NAMES 'utf8'");
    $sql = "select * from status where id > ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue('1', $id);
    $stmt->execute();
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($result);
    }
} catch (PDOException $e) {
    echo "Connect Error: " . $e->getMessage();
}