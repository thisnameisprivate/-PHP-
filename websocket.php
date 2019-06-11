<?php

require_once "../../MeepoPS/index.php";


$telnet = new \MeepoPS\Api\WebSocket("0.0.0.0", 19910);

$telnet->instanceName = "MeepoPS-WebSocket";
$telnet->callbackStartInstance = "callbackStartInstance";
$telnet->callbackNewData = "callbackNewData";
$telnet->callbackConnect = "callbackConnect";
$telnet->callbackSendBackBufferEmpty = "callbackSendBackBufferEmpty";
$telnet->callbackInstanceStop = "callbackInstanceStop";
$telnet->callbackConnectClose = "callbackConnectClose";


function callbackStartInstance ($instance) {
    print "Instance : " . $instance->instanceName . "start" . "\n";
}
function callbackConnect ($connect) {
    foreach ($connect->instance->clientList as $client) {
        if ($connect->id != $client->id) {
            $client->send("new client" . $connect->id . "online");
        }
    }
    print_r("Get new link -  Unqiued " . $connect->id . "\n");
}
function callbackNewData ($connect, $data) {
    print_r("UnqiueId = " . $connect->id . "speak: " . $data . "\n");
    foreach ($connect->instance->clientList as $client) {
        if ($connect->id != $client->id) {
            $client->send("clients push user: " . $connect->id . "speak: " . $data  . "\n");
        }
    }
}
function callbackSendBufferEmpty ($connect) {
    var_dump("user: " . $connect->id . "push message list is null ... \n");
}
function callbackInstanceStop ($instance) {
    foreach ($instance->clientList as $client) {
        $client->send("Service stop!~~~");
    }
}