<?php


class TaxCollectionVisitor extends ArmyVisitor {
    private $due = 0;
    private $report = '';
    public function visit (Unit $node) {
        $this->levy($node, 1);
    }
    public function visitArcher (Archer $node) {
        $this->levy($node, 2);
    }
    public function visitCavalry (Cavalry $node) {
        $this->levy($node, 3);
    }
    public function visitTroopCarrierUnit (TroopCarrierUnit $node) {
        $this->levy($node, 5);
    }
    private function levy (Unit $unit, $amount) {
        $this->report .= "Tax levied for " . get_class($unit);
        $this->report .= ": $amount\n";
        $this->due += $amount;
    }
    public function getReport () {
        return $this->report;
    }
    public function getTax () {
        return $this->due;
    }
}
$main_army = new Army();
$main_army->addUnit(new Archer());
$main_army->addUnit(new LaserCannonUnit());
$main_army->addUnit(new Cavalry());
$taxcollector = new TaxCollectionVisitor();
$main_army->accept($taxcollector);
print "TOTAL: ";
print $taxcollector->getTax() . "\n";