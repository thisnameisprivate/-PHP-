<?php

abstract class ArmyVisitor {
    abstract function visit(Unit $node);
    public function visitArcher (Archer $node) {
        $this->visit($node);
    }
    public function visitCavalry (Cavalry $node) {
        $this->visit($node);
    }
    public function visitLaserCannonUnit (LaserCannonUnit $node) {
        $this->visit($node);
    }
    public function visitTroopCarrierUnit (TroopCarrierUnit $node) {
        $this->visit($node);
    }
    public function visitArmy (Army $node) {
        $this->visit($node);
    }
}
class TextDumpArmyVisitor extends ArmyVisitor {
    private $text = '';
    public function  visit (Unit $node) {
        $ret = "";
        $pad = 4 * $node->getDepth();
        $ret .= sprintf("%{$pad}s", "");
        $ret .= get_class($node) . ": ";
        $ret .= "bombardL " . $node->bombardStrength() . "\n";
        $this->text .= $ret;
    }
    public function getText () {
        return $this->text;
    }
}
$main_army = new Army();
$main_army->addUnit(new Archer());
$main_army->addUnit(new LaserCannonUnit());
$main_army->addUnit(new Cavalry());
$textdump = new TextDumpArmyVisitor();
$main_army->accept($textdump);
print $textdump->getText();
// result
// Army: bombard: 50
// Archer: bombard: 4
// LaserCannonUnit: bombard: 44
// Cavalry: bombard: 2