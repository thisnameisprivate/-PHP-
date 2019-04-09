<?php

interface Observer {
    public function attach (Observer $observer);
    public function detach (Observer $observer);
    public function notify ();
    // Add function
    public function update (Observerble $observerble);
}
class Login implements Observerle {
    private $observers;
    public function __construct () {
        $this->observers = array();
    }
    public function attach (Observer $observer) {
        $this->observers[] = $observer;
    }
    public function detach (Observer $observer) {
        $newobservers = array();
        foreach ($this->observers as $obs) {
            if (($obs !== $observer)) {
                $newobservers[] = $obs;
            }
        }
        $this->observers = $newobservers;
    }
    public function notify () {
        foreach ($this->observers as $obs) {
            $obs->update($this);
        }
    }
}
class SecurityMonitor implements Observer {
    public function update (Observerble $observerble) {
        $status = $observerble->getStatus();
        if ($status[0] == Login::LOGIN_WRONG_PASS) {
            print __CLASS__ . ":\tsending mail to sysadmin\n";
        }
    }
}
$login = new Login();
$login->attach(new SecurityMonitor());

abstract class LoginObserver implements Observer {
    private $login;
    public function __construct (Login $login) {
        $this->login = $login;
        $login->attach($this);
    }
    public function update (Observerable $observerable) {
        if ($observerable === $this->login) {
            $this->doUpdate($observerable);
        }
    }
    abstract function doUpdate(Login $login);
}
class SecurityMonitory extends LoginObserver {
    public function doUpdate (Login $login) {
        $status = $login->getStatus();
        if ($status[0] == Login::LOGIN_WRONG_PASS) {
            print __CLASS__ . ":\tsending mail to sysadmin\n";
        }
    }
}
class GeneralLogger extends LoginObserver {
    public function doUpdate (Login $login) {
        $status = $login->getStatus();
        print __CLASS__ . ":tadd login data to log\n";
    }
}
class PartnershipTool extends LoginObserver {
    public function doUpdate (Login $login) {
        $status = $login->getStatus();
        print __CLASS__ . ":\test cookie if IP matches a list\n";
    }
}
$login = new Login();
new SecurityMonitor($login);
new GeneralLogger($login);
new PartnershipTool($login);
class Login2 implements SplSubject {
    private $storage;
    public function __construct () {
        $this->storage = new SplObjectStorage();
    }
    public function attach (SplSubject $observer) {
        $this->storage->attach($observer);
    }
    public function detach (SplSubject $observer) {
        $this->storage->detach($observer);
    }
    public function notify () {
        foreach($this->storage as $obs) {
            $obs->update($this);
        }
    }
    // ...
}
abstract class LoginServer implements SplObserver {
    private $login;
    public function __construct (Login $login) {
        $this->login = $login;
        $login->attach($this);
    }
    public function update (SplSubject $subject) {
        if ($subject === $this->login) {
            $this->doUpdate($subject);
        }
    }
    abstract public function doUpdate(Login $login);
}