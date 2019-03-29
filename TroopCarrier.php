<?php

class TroopCarrier {
    public function addUnit (Unit $unit) {
        if ($unit instanceof Cavalry) {
            throw new UnitException("Can't get a horse on the vehicle");
        }
        super::addUnit($unit);
    }
    public function bombardStrength () {
        return 0;
    }
}
abstract class Tile {
    abstract function getWealthFactor();
}
class Plains extends Tile {
    private $wealthfactor = 2;
    public function getWEalthFactor () {
        return $this->wealthfactor;
    }
}