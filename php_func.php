<?php


$mystring = 'abc';
$findme = 'a';
$pos = strpos($mystring, $findme);
if ($pos !== false) {
    echo "The string '$findme' was found in the string '$mystring'";
    echo " and exists at position $pos";
} else {
    echo "The string '$findme' was not found in the string '$mystring'";
}
$two = 'abc';
$findme = 'a';
$pos = strpos($mystring, $findme);
if ($pos === false) {
    echo "The string '$findme' was not found in the string '$mystring'";
} else {
    echo "The string '$findme' was found in the string '$mystring'";
    echo " and exists at position $pos";
}
$newstring = 'abcdef abcdef';
$pos = strpos($newstring, 'a', 1);