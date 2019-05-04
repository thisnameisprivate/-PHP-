<?php


namespace DesignPatterns\Createional\Pool;

Class Worker1 {
    public function __construct () {

    }
    public function run ($image, array $callback) {
        call_user_func($callback, $this);
    }
}