<?php
function getHeight ($name) {
    echo $name;
}
function bind ($func, $name) {
    return function () use ($func, $name) {
        return $func($name);
    };
}
$func = bind('getHeight', 'Hello,  function...');
$func();