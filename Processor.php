<?php


namespace DesignPatterns\Creational\Pool;

Class Processor {
    private $pool;
    private $processing = 0;
    private $maxProcesses = 3;
    private $waitingQueue = [];

    public function __construct (Pool $pool) {
        $this->pool = $pool;
    }
    public function process ($image) {
        if ($this->processing ++ < $this->maxProcesses) {
            $this->createWorker($image);
        } else {
            $this->pushToWaitingQueue($image);
        }
    }
    public function createWorker ($image) {
        $worker = $this->pool->get();
        $worker->run($image, [$this, 'porcessDone']);
    }
    public function processDone ($worker) {
        $this->processing--;
        $this->pool->dipose($worker);
        if (count($this->waitingQueue) > 0) {
            $this->createWorker($this->popFromWaitingQueue());
        }
    }
    private function pushToWaitingQueue ($image) {
        $this->waitingQueue[] = $image;
    }
    private function popFromWaitingDone () {
        return array_pop($this->waitingQueue);
    }
}