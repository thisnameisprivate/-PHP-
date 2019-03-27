<?php

abstract class CommsManager2 {
    const APPT = 1;
    const TTD  = 2;
    const CONTACT = 3;
    abstract function getHeaderText();
    abstract function make($flag_int);
    abstract function getFooterText();
}
class BloggsCommsManager extends CommsManager2 {
    public function getHeaderText () {
        return "BloggsCal header\n";
    }
    public function make ($flag_int) {
        switch ($flag_int) {
            case self::APPT:
                return new BloggsApptEncoder();
            case self::CONTACT:
                return new BloggsContactEncoder();
            case self::TTD:
                return new BloggsTtdEncoder();
        }
    }
    public function getFooterText () {
        return "BloggsCal footer\n";
    }
}