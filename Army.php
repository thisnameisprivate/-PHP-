<?php
abstract class Unit {
    abstract function bombardStrength();
}
class Archer extends Unit {
    public function bombardStrength()
    {
        // TODO: Implement bombardStrength() method.
        return 4;
    }
}
class LaserCannonUnit extends Unit {
    public function bombardStrength()
    {
        // TODO: Implement bombardStrength() method.
        return 44;
    }
}
class Army {
    private $units = array();
    public function addUnit (Unit $unit) {
        array_push($this->units, $unit);
    }
    public function bombardStrength () {
        $ret = 0;
        foreach ($this->units as $unit) {
            $ret += $unit->bombardStrength();
        }
        return $ret;
    }
}