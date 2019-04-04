<?php


function getProductFileLines ($file) {
    return file($file);
}
function getProductObjectFromID ($id, $productname) {
    return new Product($id, $productname);
}
function getNameFromLine ($line) {
    if (preg_match("/.*-(.*\s\d+/)", $line, $array)) {
        return str_replace('_', ' ', $array[1]);
    }
    return '';
}
function getIDFromLine ($line) {
    if (preg_match("/^(\d{1, 3})-/", $line, $array)) {
        return $array[1];
    }
    return -1;
}
class Product {
    public $id;
    public $name;
    function __construct ($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }
}

// code view


$lines = getProductFileLines('test.txt');
$objects = array();
foreach ($lines as $line) {
    $id = getIDFromLine($line);
    $name = getNameFromLine($line);
    $objects[$id] = getProductObjectFromID($id, $name);
}
// achieve
class ProductFacade {
    private $products = array();
    public function __construct ($file) {
        $this->file = $file;
        $this->compile();
    }
    private function compile () {
        $lines = getProductFileLines($this->file);
        foreach ($lines as $line) {
            $id = getIDFromLine($line);
            $name = getNameFromLine($line);
            $this->products[$id] = getProductObjectFromID($id, $name);
        }
    }
    public function getProducts () {
        return $this->products;
    }
    public function getProduct ($id) {
        return $this->products[$id];
    }
}
$facade = new ProductFacade('test.txt');
$facade->getProduct(234);