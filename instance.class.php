<?php
class ClassName {
    public function __construct ($param) {
        echo "Construct called with parameter:  ". $param . "</br>";
    }
}
$a = new ClassName(123);
$b = new ClassName(456);