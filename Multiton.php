<?php


namespace DesignPatterns\Createional\Multiton;

Class Multiton {
    const INSTANCE_1 = '1';
    const INSTANCE_2 = '2';

    private static $instances = [];
    private function __construct () {

    }

    public static function getInstance ($instanceName) {
        if (!array_key_exists($instanceName, self::$instances)) {
            self::$instances[$instanceName] = new self();
        }
        return self::$instances[$instanceName];
    }

    private function __clone () {

    }

    private function __wakeup () {

    }
}