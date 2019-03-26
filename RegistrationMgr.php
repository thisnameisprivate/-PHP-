<?php


// RegistrationMgr 作为 Notifier 类的客户端
class RegistrationMgr {
    public function register (Lesson $lesson) {
        $notifier = Notifier::getNotifier();
        $notifier->inform("new lesson: cost ({$lesson->cost()})");
    }
}
abstract class Notifier {
    static public function getNotifer () {
        if (rand(1, 2) == 1) {
            return new MailNotifier();
        } else {
            return new TextNotifier();
        }
    }

    abstract public function inform ($message);
}
class MailNotifier extends Notifier {
    public function inform ($message) {
        print "MAIL notification: {$message}\n";
    }
}
class TextNotifier extends Notifier {
    public function inform ($message) {
        print "TEXT notification: {$message}\n";
    }
}

$lessons1 = new Seminar(4, new TimedCostStrategy());
$lessons2 = new Lecture(4, new FixedCostStrategy());
$mgr = new RegistrationMgr();
$mgr1->register($lessons1);
$mgr2->register($lessons2);