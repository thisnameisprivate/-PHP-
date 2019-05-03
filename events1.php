<?php

use \GatewayWorkerman\Lib\Gateway;

class Events1 {
    public static function onConnect ($client_id) {
        Gateway::sendToClient($client_id, json_encode([
            'type' => 'init',
            'client_id' => $client_id
        ]));
    }
    public static function onMessage ($client_id, $message) {
        $req_data = json_decode($message, true);
        if ($req_data['type'] == 'say_to_all') {
            if (array_key_exists('init', $req_data) && $req_data['init'] == 'login') {
                if (! array_key_exists($req_data['content'], self::connectRedis()->keys("*"))) {
                    self::connectRedis()->set($req_data['content'], $client_id);
                } else {
                    self::connectRedis()->del($req_data['content'], $client_id);
                    self::connectRedis()->set($req_data['content'], $client_id);
                }
                $keys = self::connectRedis()->keys('*');
                $clients_keys = implode(',', $keys);
                return Gateway::sendToAll($clients_keys . "|" . $req_data['content'] . " 上线了~");
            } else {
                return Gateway::sendToAll($req_data['content']);
            }
        }
        if ($req_data['type'] == 'say_to_one') {
            return Gateway::sendToClient($req_data['content']);
        }
    }
    public static function onClose ($client_id) {
        $redis = self::connectRedis();
        $keys_name = $redis->keys('*');
        for ($i = 0; $i < count($keys_name); $i ++) {
            $redis_collection[$keys_name[$i]] = $redis->get($keys_name[$i]);
        }
        $client_key = array_search($client_id, $redis_collection);
        $redis->del($client_key);
        if (is_string($client_key) && $client_key != '') {
            $client_keys = implode(',', $redis->keys("*"));
            Gateway::sendToAll($client_keys . '-' . $client_key . " not is on-offline~");
        } else {
            Gateway::sendToAll("Workerman get something username???");
        }
    }

}