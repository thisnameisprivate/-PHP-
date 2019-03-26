<?php


abstract class Lesson3 {
    private $duration;
    private $costStrategy;


    public function __construct ($duration, CostStrategy $startegy) {
        $this->duration = $duration;
        $this->costStrategy = $startegy;
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


class Lecture extends Lesson3 {

}

class Seminar extends Lesson3 {

}