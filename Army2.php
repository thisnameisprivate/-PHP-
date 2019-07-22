<?php


abstract class Unit {
    abstract function bombardStrength();
}
class Archer extends Unit {
    /**
     * @return int
     */
    public function bombardStrength () {
        // TODO: Implement bombardStrength() method.
        return 4;
    }
}
class LaserCannonUnit extends Unit {
    /**
     * @return int
     */
    public function bombardStrength () {
        // TODO: Implement borbardStrength() method.
        return 44;
    }
}
class Army {
    private $units = [];
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