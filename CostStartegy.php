<?php

abstract class CostStaregy {
    abstract public function cost (Lesson $lesson);
    abstract public function chargeType ();
}

class TimedCostStartegy extends CostStrategy {
    public function cost (Lesson $lesson) {
        return ($lesson->getDuration() * 5);
    }

    public function chargeType () {
        return "hourly rate";
    }
}

class FixedCostStrategy extends CostStaregy {
    public function cost (Lesson $lesson) {
        return 30;
    }

    public function chargeType () {
        return "fixed rate";
    }
}

$lessons[] = new Seminar(4, new TimedCoststartegy());
$lessons[] = new Lecture(4, new FixedCostStrategy());


foreach ($lessons as $lesson) {
    print "lesson charge: {$lesson->cost()}";
    print "Charge type: {$lesson->chargeType()} \n";
}

// lesson charge 20. Charge type: hourly rate