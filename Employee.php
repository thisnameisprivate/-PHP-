<?php

abstract class Employee {
    protected $name;
    public function __construct ($name) {
        $this->_name = $name;
    }
    abstract public function fire ();
}
class Minion extends Employee {
    public function fire () {
        print "{$this->name}: I'll clear any desk\n";
    }
}
class NastyBoss {
    private $employees = array();
    public function addEmployee ($employeeName) {
        $this->employees[] = new Minion($employeeName);
    }
    public function projectFails () {
        if (count($this->employees) > 0) {
            $emp = array_pop($this->employees);
            $emp->fire();
        }
    }
}

$boss = new NastyBoss();
$boss->addEmployee('harry');
$boss->addEmployee('bob');
$boss->addEmployee('mary');
$boss->projectFails();