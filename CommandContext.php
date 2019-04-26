<?php


class CommandContext {
    private $params = array();
    private $error = '';
    public function __construct () {
        $this->params = $_REQUEST;
    }
    public function addParam ($key, $val) {
        $this->params[$key] = $val;
    }
    public function get ($key) {
        return $this->params[$key];
    }
    public function setError ($error) {
        $this->error = $error;
    }
    public function getError () {
        return $this->error;
    }
}
class CommandNotFoundException extends Exception {}
class CommandFactory {
    private static $dir = 'commands';
    public static function getCommand ($action = 'Default') {
        if (preg_match('/\W/', $action)) {
            throw new Exception("illegal characters in action");
        }
        $class = UCFirst(strtolower($action)) . "Command";
        $file = self::$dir.DIRECTORY_SEPARATOR . "{$class}.php";
        if (!file_exists($file)) {
            throw new CommandNotFoundException("Could not find file.");
        }
        require_once($file);
        if (!class_exists($class)) {
            throw new CommandNotFOundException("no '$class' class located");
        }
        $cmd = new $class();
        return $cmd;
    }
}