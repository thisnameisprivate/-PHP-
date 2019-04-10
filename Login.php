<?php



class Login {
    const LOGIN_USER_UNKNOWN = 1;
    const LOGIN_WRONG_PASS = 2;
    const LOGIN_ACCESS = 3;
    private $status = array();
    public function handleLogin ($user, $pass, $ip) {
        switch (rand(1, 3)) {
            case 1:
                $this->setStatus(self::LOGIN_ACCESS, $user, $ip);
                $ret = true;
                break;
            case 2:
                $this->setStatus(self::LOGIN_WRONG_PASS, $user, $ip);
                $ret = false;
                break;
            case 3:
                $this->setStatus(self::LOGIN_USER_UNKNOWN, $user, $ip);
                $ret = false;
                break;
        }
        return $ret;
    }
    private function setStatus ($status, $user, $ip) {
        $this->status = array($status, $user, $ip);
    }
    public function getStatus () {
        return $this->status;
    }
}
function handleLogin ($user, $pass, $ip) {
    switch (rand(1, 3)) {
        case 1:
            $this->setStatus(self::LOGIN_ACCESS, $user, $ip);
            $ret = true;
            break;
        case 2:
            $this->setStatus(self::LOGIN_WRONG_PASS, $user, $ip);
            $ret = false;
            break;
        case 3:
            $this->setStatus(self::LOGIN_USER_UNKNOWN, $user, $ip);
            $ret = false;
            break;
    }
    Logger::logIP($user, $ip, $this->getStatus());
    return $ret;;
}