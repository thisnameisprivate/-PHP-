<?php


class LogRequest extends DecorateProcess {
    public function __construct (RequestHelper $req) {
        print __CLASS__ . ": logging request \n";
        $this->processRequest->process($req);
    }
}
class AuthenticatRequest extends DecorateProcess {
    public function process (RequestHelper $req) {
        print __CLASS__ . ": authenticating request \n";
        $this->processRequest->process($req);
    }
}
class StructureRequest extends DecorateProcess {
    public function process (RequestHelper $req) {
        print __CLASS__ . ": structuring request data \n";
        $this->processRequest->process($req);
    }
}
$process = new AuthenticateRequest( new StructureRequest(
    new LogRequest(
        new MainProcess()
    )
));
$process->process(new RequestHelper());

// Request
// Authenticate
// Request: authenticating request
// StructureRequset: structuring request data
// LogRequest: logging request
// MainProcess: doing something useful with request