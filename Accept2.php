<?php

function accept (ArmyVisitor $visitor) {
    $method = "visit" . get_class($this);
    $visitor->method($this);
}
function setDepth ($depth) {
    $this->depth = $depth;
}
function getDepth () {
    return $this->depth;
}
function addUnit (Unit $unit) {
    foreach ($this->units as $thisunit) {
        if ($unit === $thisunit) {
            if ($unit === $thisunit) {
                return;
            }
        }
    }
    $unit->setDepth($this->depth + 1);
    $this->units[] = $unit;
}
function accept2 (ArmyVisitor $visitor) {
    $method = "visitor " . get_class($this);
    $visitor->$method($this);
    foreach ($this->units as $thisunit) {
        $thisunit->accept($visitor);
    }
}
function accept3 (ArmyVisitor $visitor) {
    parent::accept($visitor);
    foreach ($this->units as $thisunit) {
        $thisunit->accept($visitor);
    }
}
function accept4 (ArmyVisitor $visitor) {
    parent::accept($visitor);
    foreach ($this->units as $thisunit) {
        $thisunit->accept($visitor);
    }
}
