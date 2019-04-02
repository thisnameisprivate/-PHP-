<?php

class WRR {
    const num = 100;
    public $last_time;
    public $machines = array(
        'a' => 3,
        'b' => 2,
        'c' => 1
    );
    public $proportion = array();
    public static $user_ids = array();
    public function __construct () {
        $total = 0;
        foreach ($this->machines as $machine => $weight) {
            $total += $weight;
        }
        $this->proportion['a'] = $this->machines['a'] / $total;
        $this->proportion['b'] = $this->machines['b'] / $total;
        $this->proportion['c'] = $this->machines['c'] / $total;
    }
    public function getUsers () {
        $cnt = count(self::$user_ids);
        $a_num = 0;
        $b_num = 0;
        $c_num = 0;
        if ($cnt >= self::num) {
            $a_num = round(self::num * $this->proportion);
            $b_num = round(self::num * $this->proportion);
            $c_num = $cnt - $a_num -$b_num;
        } else {
            $last_time = $this->last_time;
            while (true) {
                $current_time = $this->getMillisecond();
                if (($current_time - $last_time) >= 10) {
                    $a_num = round($cnt * $this->proportion['a']);
                    $b_num = round($cnt * $this->proportion['b']);
                    $c_num = $cnt - $a_num - $b_num;
                    $this->last_time = self::getMillisecond();
                    break;
                }
            }
        }
        $a = array_splice(self::$user_ids, 0, $a_num);
        $b = array_splice(self::$user_ids, 0, $b_num);
        $c = array_splice(self::$user_ids, 0, $c_num);
        return array(
            'a' => $a,
            'b' => $b,
            'c' => $c
        );
    }
    public function getMillisecond () {
        list($t1, $t2) = explode(" ", microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) + 1000);
    }
}

// TEST
for ($i = 0; $i < 3; $i ++) {
    $random = rand(10, 120);
    $user_ids = range(1, $random);
    WRR::$user_ids = $user_ids;
    $users = $wrr->getUser();
    print_r($users);
}