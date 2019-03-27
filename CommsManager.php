<?php

abstract class CommsManager {
    abstract function getHeaderText();
    abstract function getApptEncoder();
    abstract function getTtdEncoder();
    abstract function getContactEncoder();
    abstract function getFooterText();
}
class BloggsCommsManager extends CommsManager {
    public function getHeaderText () {
        return "BloggsCal header\n";
    }
    public function getApptEncoder () {
        return new BloggsApptEncoder();
    }
    public function getTtdEncoder () {
        return new BLoggsTtdEncoder();
    }
    public function getContactEncoder () {
        return new BloggsContactEncoder();
    }
    public function getFooterText () {
        return "BloggsCal footer\n";
    }
}