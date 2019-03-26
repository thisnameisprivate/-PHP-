<?php

class NastyBoss {
    private $employees = array();
    public function addEmployee (Employee $employee) {
        $this->employees[] = $employee;
    }
    public function projectFails () {
        if (count($this->employees)) {
            $emp = array_pop($this->employees);
            $emp->fire();
        }
    }
}
class CluedUp extends Employee {
    public function fire () {
        print "{$this->name}: I'll call my lawyer\n";
    }
}

$boss = new NastyBoss();
$boss->addEmployee(new Minion('harry'));
$boss->addEmployee(new CluedUp('bob'));
$boss->addEmployee(new Minion('mary'));
$boss->projectFails();
$boss->projectFails();
$boss->projectFails();


// Employee
abstract class Employee {
    protected $name;
    private static $types = array('minion', 'cluedup', 'wellconnected');
    public static function recruit ($name) {
        $num = rand(1, count(self::$types)) - 1;
        $class = self::$types[$num];
        return new $class($name);
    }
    public function __construct ($name) {
        $this->name = $name;
    }
    abstract public function fire();
}
class WellConnected extends Employee {
    public function fire () {
        print "{$this->name} : I'll call my data\n";
    }
}
$boss2 = new NastyBoss();
$boss2->addEmployee(Employee::recruit('harry'));
$boss2->addEmployee(Employee::recruit('bob'));
$boss2->addEMployee(Employee::recruit('mary'));