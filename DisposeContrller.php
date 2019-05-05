<?php

use Controller;


class DisposeController extends Controller {
    public static function onConnect ($client_id) {
        Gateway::sendToClient($client_id, json_encode(array(
            'type' => 'init',
            'client_id' => $client_id
        )));
    }
    public static function onMessage ($client_id, $message) {
        $req_data = json_decode($message, true);
        if ($req_data['type'] == 'say_to_all') {
            if (array_key_exists('init', $req_data) && $req_data['init'] == 'login') {

            }
        }
    }
}