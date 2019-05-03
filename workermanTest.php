<?php

use Workerman\Connection\AsyncTcpConnection;
use Workerman\Worker;

require_once './Workerman\Autoloader.php';

$tcp_worker = new Worker("tcp://0.0.0.0:2345");
$tcp_worker->onConnect = function ($connection) {
    echo "New Connection " . $connection->getRemoteIp() . PHP_EOL;
    $connection_to_mysql = new AsyncTcpConnection('tcp://172.17.0.3:3306');
    $connection_to_mysql->onMessage = function ($connection_to_mysql, $data) use ($connection) {
        $connection->send($data);
    };
    $connection_to_mysql->onClose = function ($connection_to_mysql) use ($connection) {
        $connection->close();
    };
    $connection_to_mysql->connect();
    $connection->onMessage = function ($connection, $data) use ($connection_to_mysql) {
        $connection_to_mysql->send($data);
    };
    $connection_to_mysql->onCLose = function ($connection_to_mysql) use ($connection) {
        $connection->close();
    };
    $connection_to_mysql->connect();
    $connection->onMessage = function ($connection, $data) use ($connection_to_mysql) {
        $connection_to_mysql->send($data);
    };
    $connection->onClose = function ($connection) use ($connection_to_mysql) {
        $connection_to_mysql->close();
    };
};

$tcp_worker->onMessage = function ($connection, $data) {
    echo $data;
};
Worker::runAll();