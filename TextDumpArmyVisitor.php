<?php


class TextDumpArmyVisitor extends ArmyVisitor {
    private $text = "";
    public function visit (Unit $node) {
        $ret = "";
        $pad = 4 * $node->getDepth();
        $ret .= sprintf("%{$pad}s%", "");
        $ret .= get_class($node) . ":";
        $ret .= "bombard: " .$node->bombardStrength(). "\n";
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