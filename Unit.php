<?php

abstract class Unit {
    abstract function addUnit (Unit $unit);
    abstract function removeUnit (Unit $unit);
    abstract function bombardStrength();
}
class Army extends Unit {
    private $units = array();
    public function addUnit (Unit $unit) {
        if (in_array($unit, $this->units, true)) {
            return;
        }
        $this->units[] = $unit;
    }
    public function removeUnit (Unit $unit) {
        $this->units = array_udiff($this->units, array($unit), function ($a, $b) {
            return ($a === $b) ? 0 : 1;
        });
        // 5.3以下兼容写法
        $this->units = array_udiff($this->units, array($unit), function ($a, $b) {
            create_function('$a, $b', 'return ($a === $b) ? 0 : 1');
        });
    }
    public function bombardStrength () {
        $ret = 0;
        foreach ($this->units as $unit) {
            $ret += $unit->bombardStrength();
        }
        return $ret;
    }
}