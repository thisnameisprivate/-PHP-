<?php

$string = 'thisnameisprivate@163.com';
$boolean = preg_match('/^\w+\@\d{1,3}\.\w{1,3}$/', $string, $matches);
$boolean = strpos($string, '163');
print_r($boolean);
print_r($matches);