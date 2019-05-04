<?php

namespace DesignPatterns\Createional\Pool;

class Pool {
    private $instances = [];
    private $class;
    public function __construct ($class) {
        $this->class = $class;
    }
    public function get () {
        if (count($this->instance) > 0) {
            return array_pop($this->instances);
        }
        return new $this->class();
    }
    public function dispose ($instance) {
        $this->instances[] = $instance;
    }
}