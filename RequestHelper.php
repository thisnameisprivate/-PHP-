<?php

class RequestHelper {}
abstract class ProcessRequest {
    abstract function process (RequestHelper $req);
}
class MainProcess extends ProcessRequest {
    public function process (RequestHelper $req) {
        print __CLASS__ . ": doing something useful with request \n";
    }
}
abstract class DecorateProcess extends ProcessRequest {
    protected $processRequest;
    public function __construct (ProcessRequest $pr) {
        $this->processRequest = $pr;
    }
}