<?php

class Registry {
    private static $instance;
    private $request;
    private function __construct () {

    }
    public static function instance () {
        if (!isset(self::$instance)) return self::$instance = new Registry();
    }
    public function getRequest () {
        return $this->request;
    }
    public function setRequest (Request $request) {
        $this->request = $request;
    }
}
class Request {}
$reg = Registry::instance();
$reg = Registry::instance();
print_r($reg->getRequest());