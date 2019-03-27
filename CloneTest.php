<?php

class Contained {}
class Container {
    public $contained;
    public function __construct () {
        $this->contained = new Contained();
    }
    public function __clone () {
        $this->contained = clone $this->contained;
    }
}