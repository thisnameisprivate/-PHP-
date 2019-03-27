<?php

class UnitException extends Exception {}
class Archer extends Unit {
    public function addUnit (Unit $unit) {
        throw new UnitException(get_class($this) . ' is a leaf');
    }
    public function removeUnit (Unit $unit) {
        throw new UnitException(get_class($this) . ' is a leaf');
    }
    public function bombardStrength () {
        return 4;
    }
}


// abstract
abstract class Unit {
    abstract function bombardStrength ();
    public function addUnit (Unit $unit) {
        throw new UnitException(get_class($this) . ' is a leaf');
    }
    public function removeUnit (Unit $unit) {
        throw new UnitException(get_class($this) . ' is a leaf');
    }
}
class Archer extends Unit {
    public function bombardStrength () {
        return 4;
    }
}
$main_army = new Army();
$main_army->addUnit(new Archer());
$main_army->addUnit(new LaserCannonUnit());
$sub_army = new Army();
$sub_army->addUnit(new Archer());
$sub_army->addUnit(new Archer());
$sub_army->addUnit(new Archer());
$main_army->addUnit($sub_army);
print "attacking with strength: {$main_army->bombardStrength()}\n";