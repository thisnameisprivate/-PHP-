<?php

 abstract class Lesson {
    protected $duration;
    const     FIXED = 1;
    const     TIMED = 2;
    private $costtype;


    public function __construct ($duration, $consttype = 1) {
        $this->duration = $duration;
        $this->costtype = $costtype;
    }

    public function cost () {
        switch ($this->costtype) {
            CASE self::TIMED :
                return (5 * $this->duration);
                break;
            CASE self::FIXED :
                return 30;
                break;
            default:
                $this->costtype = self::FIXED;
                return 30;
        }
    }

    public function chargeType () {
        switch ($this->costtype) {
            CASE self::TIMED :
                return "hourly rate";
                break;
            CASE self::FIXED :
                return "fixed rate";
                break;
            default:
                $this->costtype = self::FIXED;
                return "fixed rate";
        }
    }
}


class Lecture extends Lesson {

}
class Seminar extens Lesson {

}


$lecture = new Lecture(5, Lesson::FIXED);
print "{$lecture->cost()} ({$lecture->chargeTYpe()})";
$seminar = new Seminar(3, Lesson::TIMED);
print "{$seminar->chargeType}\n";
// RESULT
// FIXED : 30
// HOURLY : 15