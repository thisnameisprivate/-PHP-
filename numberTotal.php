<?php

function isPrime($n) {
        if ($n <= 3) {
        return $n > 1;
        } else if ($n % 2 === 0 || $n % 3 === 0) {
                return false;
        } else {
        for ($i = 5; $i * $i <= $n; $i += 6) {
            if ($n % $i === 0 || $n % ($i + 2) === 0) {
                return false;
            }
        }
        return true;
    }
}