<?php

class Registry2 {
    private static $instance;
    private $values = array();
    private function __construct () {

    }
    public static function instance () {
        if (! isset(self::$instasnce)) return self::$instance = new Registry2();
    }
    public function get ($key) {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }
    public function set ($key, $value) {
        $this->values[$key] = $value;
    }
}

class Registry3 {
    private static $instance;
    private $values = [];
    private function __construct () {

    }
    public static function instance () {
        if (! isset(self::$instance)) return self::$instance = new Registry3();
    }
    public function get ($key) {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }
    public function set ($key, $value) {
        $this->values[$key] = $value;
    }
}