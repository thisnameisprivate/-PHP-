<?php

class Controller {
    private $context;
    public function __construct () {
        $this->context = new CommandContext();
    }
    public function getContext () {
        return $this->context;
    }
    public function process () {
        $cmd = CommandFactory::getCommand($this->context->get('action'));
        if (! $cmd->execute($this->context)) {
            // handler success...
        } else {
            // handler error...
        }
    }
}