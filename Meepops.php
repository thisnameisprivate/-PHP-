<?php


require_once('MeepoPS/index.php');

$telnet19910 = new \MeepoPS\Api\Telnet('0.0.0.0', '19910');
$telnet19911 = new \MeepoPS\Api\Telnet('0.0.0.0', '19911');
$telnet19912 = new \MeepoPS\Api\Telnet('0.0.0.0', '19912');

$telnet19910->childProcessCount = 1;
$telnet19911->childProcessCount = 4;
$telnet19912->childProcessCount = 8;

$telnet19910->instanceName = 'MeepoPS-Telnet-19910';
$telnet19911->instanceName = 'MeepoPS-Telnet-19911';
$telnet19912->instanceName = 'MeepoPS-Telnet-19912';

$telnet19910->callbackStartInstance = function ($instance) {
    var_dump('instance : ' . $instance->instanceName . 'starting');
};
$telnet19910->callbackConnect = function ($connect) {
    var_dump('get new link, link id is :' . $connect->id . "\n");
};
$telnet19910->callbackNewData = function ($connect, $data) {
    var_dump('get new Message, link id is :' . $connect->id . 'user speak :' . $data . "\n");
};

\MeepoPS\runMeepoPS();