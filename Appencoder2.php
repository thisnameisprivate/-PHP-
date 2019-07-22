<?php

abstract class Appencoder2 {
    public abstract function encode();
}
class BloggsApptEncoder extends Appencoder2 {
    public function encode () {
        return "Appointment data encoded in MegaCal format\n";
    }
}
abstract class CommsManger {
    abstract function getHeaderText();
    abstract function getApptEncoder();
    abstract function getFooterText();
}
class CommsManager {
    public function getApptEncoder () {
        return new BloggsApptEncoder();
    }
}
class CommsManager2 {
    const BLOGGS = 1;
    const MEGA = 2;
    private $mode = 1;
    public function __construct ($mode) {
        $this->mode = $mode;
    }
    public function getApptEncoder () {
        switch ($this->mode) {
            case (self::MEGA):
                return new MageApptEncoder();
            default:
                return new BloggsAppEncoder();
        }
    }
}
$comms = new CommsManager(CommsManager::MEGA);
$apptEncoder = $coms->getApptEncoder();
print $apptEncoder->encode();

class CommsManger3 {
    const BLOGGS = 1;
    const MEGA = 2;
    private $mode;
    public function __construct () {
        $this->mode = $mode;
    }
    public function getHeaderText () {
        switch ($this->mode) {
            case (self::MEGA):
                return "MegaCal header\n";
            default:
                return "BloggsCal Header\n";
        }
    }
    public function getApptEncoder () {
        switch ($this->mode) {
            case (self::MEGA):
                return new MegaApptEncoder();
            default:
                return new BloggsApptEncoder();
        }
    }
}