<?php

abstract class Printer {
    abstract public function printMe(Person $person);
}
class PersonWriter extends Printer {
    public function printMe (Person $person) {
        echo PHP_EOL . '--------------PersonWrite--------------' . PHP_EOL;
        echo 'Person\'s name is ' . $person->getName() . PHP_EOL;
    }
}
abstract class Person {
    protected $name = 'None';
    protected $printer;
    public function __construct (Printer $printer) {
        $this->printer = $printer;
    }
    public function getName () {
        return $this->name;
    }
}
class Doctor extends Person {
    public function __construct ($name, Printer $printer) {
        $this->name = $name;
        parent::__construct($printer);
    }
    public function __call ($methodName, $arguments) {
        if (method_exists($this->printer, $methodName)) {
            return $this->printer->$methodName($this);
        }
    }
}
$doctor = new Doctor('Zhangsan', new PersonWriter());
$doctor->printMe();