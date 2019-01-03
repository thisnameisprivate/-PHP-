<?php

$start_time = microtime();
for ($i = 0; $i < 100; $i ++) {

}
$end_time = microtime();
echo $end_time - $start_time;

class Single {
    private static $instance;`  `
    private function __construct () {

    }
    public static function instance () {
        if (self::$instance === null) return self::$instance = new Single();
        return self::$instance;
    }
}