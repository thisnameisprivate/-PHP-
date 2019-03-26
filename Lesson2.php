<?php

abstract class Lesson2 {
    private $duration;
    private $costStrategy;


    public function __construct ($duration, CostStrategy $strategy) {
        $this->duration = $duration;
        $this->costStartegy = $strategy;
    }

    public function cost () {
        return $this->costStrategy->cost($this);
    }

    public function chargeType () {
        return $this->costStrategy->chargeType();
    }

    public function getDuration () {
        return $this->duration;
    }
}

class Lecture extends Lesson2 {

}

class Seminar extends Lesson2 {

}
