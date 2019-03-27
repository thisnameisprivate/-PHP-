<?php

abstract class ApptEncoder {
    public abstract function encode ();
}
class BloggsApptEncoder extends ApptEncoder {
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
                return new MegaApptEncoder();
            default:
                return new BloggsApptEncoder();
        }
    }
}

$comms = new CommsManager(CommsMAnager::MEGA);
$apptEncoder = $comms->getApptEncdoer();
print $apptEncoder->encode();

class CommsManager3 {
    const BLOGGS = 1;
    const MEGA   = 2;
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