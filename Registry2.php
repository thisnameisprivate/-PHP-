<?php

class Registry2 {
    private static $instance;
    private $values = array();
    private function __construct () {

    }
    public static function instance () {
        if (! isset(sefl::$instasnce)) return self::$instance = new Registry2();
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