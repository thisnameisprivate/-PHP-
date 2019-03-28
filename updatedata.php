<?php


class Gettarget {
    private $flag = '';
    public function __set ($property, $flag) {
        if ($property === 'flag') $this->flag = $flag;
    }
    public function __get ($property) {
        if ($property === 'flag') return $this->flag;
    }
}
$instance = new Gettarget();
$instance->flag = 'This is test data~';
print_r($instance->flag);