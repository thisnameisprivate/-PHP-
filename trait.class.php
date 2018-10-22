<?php

trait fileLogger {
    public function logmessage ($message, $level = 'DEBUG') {
        // write $message to log file .
    }
}
trait sysLogger {
    public function logmessage ($message, $level = 'ERROR') {
        // write $message to the syslog .
    }
}
class fileStorage {
    use fileLogger, sysLogger
    {
        fileLogger::logmessage insteadof sysLogger;
        sysLogger::logmessage as private logsysmessage;
    }

    public function sotre () {
        $this->logmessage($message);
        $this->logsysmessage($message);
    }
}